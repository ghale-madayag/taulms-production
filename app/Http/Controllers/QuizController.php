<?php

namespace App\Http\Controllers;

use App\Http\Resources\QuizResource;
use App\Models\Lesson;
use App\Models\Question;
use App\Models\Quiz;
use App\Models\Section;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Illuminate\Support\Facades\Validator;
use Request;
use Spatie\Permission\Models\Permission;
use App\Models\StudentQuiz;
use App\Models\StudentQuizStatus;
use App\Models\Answer;
use App\Models\StudentGradeCat;
use App\Models\StudentGradeScore;
use App\Models\StudentGradeSub;
use Carbon\Carbon;

class QuizController extends Controller
{
    public function index(Subject $subject, Section $section){

        $quiz = Quiz::selectRaw('*, CONVERT(varchar(19), created_at, 100) as formatted_date, CONVERT(varchar(19), quiz_schedule, 100) as quiz_date')
            ->whereHas('lesson', function($query) use ($subject,$section){
                $query->where('subject_id', $subject->id)
                ->where('section_id', $section->id);
            })
            ->withCount('question')
            ->with('question')
            ->filterAll(request(['quiz', 'status']))
            ->paginate(10);

        $user =  User::select(DB::raw("lname + ', ' + fname + ISNULL(' ' + SUBSTRING(mname, 1, 1) + '.', '') + ISNULL(' ' + extname, '') as full_name"),'users.id')->find(auth()->user()->id);

        $total = $quiz->count();
        $pending = $quiz->where('published',0)->count();
        $published = $quiz->where('published',1)->count();

        return Inertia::render('Quiz/Index',[
            'quizzes' => $quiz,
            'user' => $user,
            'subjects' => $subject,
            'section' => $section,
            'filters' => Request::only(['quiz','status']),
            'total' => $total,
            'pending' => $pending,
            'published' => $published
        ]);
    }

    public function indexAll(Subject $subject, Section $section, User $user){
        $user =  $user->select(DB::raw("lname + ', ' + fname + ISNULL(' ' + SUBSTRING(mname, 1, 1) + '.', '') + ISNULL(' ' + extname, '') as full_name"),'users.id')->find($user->id);
        
        $quiz = Quiz::selectRaw('*, CONVERT(varchar(19), created_at, 100) as formatted_date, CONVERT(varchar(19), quiz_schedule, 100) as quiz_date')
            ->whereHas('lesson', function($query) use ($subject,$section){
                $query->where('subject_id', $subject->id)
                ->where('section_id', $section->id);
            })
            ->withCount('question')
            ->filterAll(request(['quiz', 'status']))
            ->paginate(10);

        
        $total = $quiz->count();
        $pending = $quiz->where('published',0)->count();
        $published = $quiz->where('published',1)->count();

        return Inertia::render('Quiz/Index',[
            'quizzes' => $quiz,
            'user' => $user,
            'subjects' => $subject,
            'section' => $section,
            'filters' => Request::only(['quiz','status']),
            'total' => $total,
            'pending' => $pending,
            'published' => $published
        ]);
    }

    public function show(Quiz $quiz, Question $question){
        $user = request()->user()->id;

        $query = $question->with('quiz')->find($question->id);
        
        $activeAnswer = StudentQuiz::where('finished',0)->where('user_id', $user)->where('quiz_id',$question->quiz_id)->orderBy('id')->first();

        if($activeAnswer == null){
            $activeAnswer = StudentQuiz::where('finished',1)->where('user_id', $user)->where('quiz_id',$question->quiz_id)->orderBy('id')->first();
        }

        $query['data'] = json_decode($query['data']);

        $end_date = new Carbon($query['quiz']['end_date']);

        if($end_date->gte(Carbon::now())){
            $seconds_remaining = $end_date->diffInSeconds(Carbon::now());
        }else{
            $seconds_remaining = 0;
        }


        $query['quiz']['duration'] = $seconds_remaining;
       // $query['question'] = htmlspecialchars($query->question);

        $includedIds = $quiz->student_quiz_stat->where('user_id', $user)->pluck('question_id'); 
        
        $includedQuestions = $quiz
        ->question()
        ->select('id','quiz_id')
        ->whereIn('id', $includedIds->toArray())
        ->get()
        ->sortBy(function ($question) use ($includedIds) {
            return array_search($question->id, $includedIds->toArray());
        })->values();

        $excludedQuestions = $quiz
        ->question()
        ->select('id','quiz_id')
        ->whereNotIn('id', $includedIds->toArray())
        ->inRandomOrder()
        ->get();

        $combinedQuestions = $includedQuestions->concat($excludedQuestions);
        $answer = Answer::where('question_id',$question->id)->where('student_quiz_id', $activeAnswer->id)->first();
        $finish = $quiz->student_quiz_status->where('user_id', $user)->toArray();
        $finish = array_values($finish);

        return Inertia::render('Question/Index',[
            'question' => $query,
            'links' => $combinedQuestions,
            'active' => $question->id,
            'finished_quiz' => $finish,
            'activeAnswer' => $answer
        ]);
    }

    public function showAll(){

        $quiz = Quiz::with(['lesson' => function($query){
            $query->select('id','title','slug','section_id')
            ->with(['section' => function($query){
                $query->select('id','name');
            }]);
        }])
        ->with(['user' => function($query){
                $query->select(DB::raw("lname + ', ' + fname + ISNULL(' ' + SUBSTRING(mname, 1, 1) + '.', '') + ISNULL(' ' + extname, '') as full_name"),'users.id',  
            );
        }])
        ->orderBy('title')
        ->filterAll(request(['subject','faculty','section','lesson','quiz','status']))
        ->paginate(5)
        ->withQueryString();

        $total = Quiz::count();
        $pending = Quiz::where('published',0)->count();
        $published = Quiz::where('published',1)->count();

        return Inertia::render('Quiz/All',[
            'quizzes' => $quiz,
            'total' => $total,
            'pending' => $pending,
            'published' => $published,
            'filters' => Request::only(['subject','faculty','section','lesson', 'quiz','status']),
        ]);
    }

    public function create(Subject $subject, Section $section){

        $user =  User::select(DB::raw("lname + ', ' + fname + ISNULL(' ' + SUBSTRING(mname, 1, 1) + '.', '') + ISNULL(' ' + extname, '') as full_name"),'users.id')->find(auth()->user()->id);
        $lessons = Lesson::select('id','title as text','position')
        ->where('subject_id', $subject->id)
        ->where('section_id', $section->id)
        ->where('user_id', auth()->user()->id)
        ->orderby('position','ASC')
        ->get();

        $student = User::select(DB::raw("lname + ', ' + fname + ISNULL(' ' + SUBSTRING(mname, 1, 1) + '.', '') + ISNULL(' ' + extname, '') as label"),'id as value')
        ->whereHas('registration', function($query) use ($subject, $section){
            $query->whereHas('registration_details', function($query) use ($subject, $section){
                $query->whereHas('schedule', function($query) use ($subject, $section){
                    $query->where('section_id', $section->id)->where('subject_id', $subject->id);
                });
            });
        })
        ->orderBy('lname', 'asc')
        ->get();

        $cnt = 1;
        $lessonNumbers = [];
        foreach ($lessons as $lesson) {
            $lessonNumbers[] = [
                'value' => $lesson->id,
                'label' => 'Lesson ' . $cnt .': '. $lesson->text,
            ];

            $cnt++;
        }

        return Inertia::render('Quiz/Create',[
            'user' => $user,
            'student' => $student,
            'subjects' => $subject,
            'section' => $section,
            'lesson' => $lessonNumbers,
        ]);
    }

    public function createAll(Subject $subject, Section $section, User $user){
        $user =  $user->select(DB::raw("lname + ', ' + fname + ISNULL(' ' + SUBSTRING(mname, 1, 1) + '.', '') + ISNULL(' ' + extname, '') as full_name"),'users.id')->find($user->id);

        $lessons = Lesson::select('id', 'title as text')->where('user_id', $user->id)->get();

        $student = User::select(DB::raw("lname + ', ' + fname + ISNULL(' ' + SUBSTRING(mname, 1, 1) + '.', '') + ISNULL(' ' + extname, '') as label"),'id as value')
        ->whereHas('registration', function($query) use ($subject, $section){
            $query->whereHas('registration_details', function($query) use ($subject, $section){
                $query->whereHas('schedule', function($query) use ($subject, $section){
                    $query->where('section_id', $section->id)->where('subject_id', $subject->id);
                });
            });
        })
        ->orderBy('lname', 'asc')
        ->get();

        $cnt = 1;
        $lessonNumbers = [];
        foreach ($lessons as $lesson) {
            $lessonNumbers[] = [
                'value' => $lesson->id,
                'label' => 'Lesson ' . $cnt .': '. $lesson->text,
            ];

            $cnt++;
        }

        return Inertia::render('Quiz/Create',[
            'user' => $user,
            'student' => $student,
            'subjects' => $subject,
            'section' => $section,
            'lesson' => $lessonNumbers,
        ]);
    }

    public function edit(Quiz $quiz){

        $query = $quiz
                ->with(['user' => function($query){
                        $query->select(DB::raw("lname + ', ' + fname + ISNULL(' ' + SUBSTRING(mname, 1, 1) + '.', '') + ISNULL(' ' + extname, '') as full_name"),'users.id',  
                    );
                }])
                ->with(['lesson' => function($query){
                    $query->with('subject')->with('section');
                }])
                ->where('slug', $quiz->slug)->with('question')->first();
        $hours = (new \DateTime($query['duration']))->format('H');
        $min = (new \DateTime($query['duration']))->format('i');
        $query['hours'] = $hours;
        $query['min'] = $min;

        $startDate = new \DateTime($query['quiz_schedule'], new \DateTimeZone('UTC'));
        $startDate->setTimezone(new \DateTimeZone('Etc/GMT-8'));

        $endDate = new \DateTime($query['end_date'], new \DateTimeZone('UTC'));
        $endDate->setTimezone(new \DateTimeZone('Etc/GMT-8'));
        
        $query['quiz_schedule'] = ['startDate' => $startDate->format('Y-m-d H:i'), 'endDate' => $endDate->format('Y-m-d H:i')];

        foreach ($query['question'] as $data){
            $data['data'] = json_decode($data['data']);
        }

        $subject = $query->lesson->subject;
        $section = $query->lesson->section;

        $student = User::select(DB::raw("lname + ', ' + fname + ISNULL(' ' + SUBSTRING(mname, 1, 1) + '.', '') + ISNULL(' ' + extname, '') as label"),'id as value')
        ->whereHas('registration', function($query) use ($subject, $section){
            $query->whereHas('registration_details', function($query) use ($subject, $section){
                $query->whereHas('schedule', function($query) use ($subject, $section){
                    $query->where('section_id', $section->id)->where('subject_id', $subject->id);
                });
            });
        })
        ->orderBy('lname', 'asc')
        ->get();

        $lessons = Lesson::select('id','title as text')
        ->where('subject_id', $subject->id)
        ->where('section_id', $section->id)
        ->where('user_id', auth()->user()->id)
        ->where('user_id', $quiz->user_id)
        ->orderby('position','ASC')
        ->get();

        $cnt = 1;
        $lessonNumbers = [];
        foreach ($lessons as $lesson) {
            $lessonNumbers[] = [
                'value' => $lesson->id,
                'label' => 'Lesson ' . $cnt .': '. $lesson->text
            ];

            $cnt++;
        }

        $exclude = explode(',',$query->excluded);

        //dd($lessonNumbers);

        return Inertia::render('Quiz/Edit',[
            'quiz' => $query,
            'student' => $student,
            'exclude' => $exclude,
            'lesson' => $lessonNumbers
        ]);
    }

    public function update(Quiz $quiz){

        $tags = request()->lesson_tags;

        //dd(request());

        $lesson_id = Lesson::whereIn('id', $tags)
        ->orderByDesc('position')
        ->pluck('id')
        ->first(); 

        $lessonTagsString = implode(',', $tags);

        request()->merge(['lesson_id' => $lesson_id]);
        request()->merge(['lesson_tags' => $lessonTagsString]);

        $attributes = $this->validateData($quiz);
        $exclude = implode(',',$attributes['excluded']); 
        
        $startDate = $attributes['quiz_schedule'][0];
        $endDate = $attributes['quiz_schedule'][1];

        $attributes['quiz_schedule'] = $startDate;
        $attributes['end_date'] = $endDate;
        $attributes['excluded'] = $exclude;

        if ($attributes['thumbnail'] ?? false) {
            $filename = time().''.rand(10,100).'-'.request()->file('thumbnail')->getClientOriginalName();
            $attributes['thumbnail'] = request()->file('thumbnail')->storeAs('/quiz', $filename);
        }

        //$attributes['duration'] = request()->duration['hours'].':'.request()->duration['minutes'];

        $existingIds = $quiz->question()->pluck('id')->toArray();

        $newIds = Arr::pluck($attributes['questions'], 'id');

        $toDelete = array_diff($existingIds, $newIds);

        $toAdd = array_diff($newIds, $existingIds);

        Question::destroy($toDelete);

        foreach ($attributes['questions'] as $question) {
            if (in_array($question['id'], $toAdd)) {
                $question['quiz_id'] = $quiz->id;
                $this->createQuestion($question);
            }
        }

        $questionMap = collect($attributes['questions'])->keyBy('id');

        foreach ($quiz->question()->get() as $question) {
            if (isset($questionMap[$question->id])) {
                $this->updateQuestion($question, $questionMap[$question['id']]);
            }
        }

        if($attributes['thumbnail'] == null && request()->oldThumb == null){
            $attributes['thumbnail'] = $quiz->thumbnail;
        }

        $quiz->update($attributes);
        return Redirect::to('/quiz/'.$quiz->slug.'/edit')->with('message', 'Quiz has been updated successfully.');
    }

    public function store(){

        $tags = request()->lesson_tags;

        $lesson_id = Lesson::whereIn('id', $tags)
        ->orderByDesc('position')
        ->pluck('id')
        ->first(); 

        $lessonTagsString = implode(',', $tags);

        request()->merge(['lesson_id' => $lesson_id]);
        request()->merge(['lesson_tags' => $lessonTagsString]);


        if(request()->excluded){
            $exclude = implode(',',request()->excluded); 
        }else{
            $exclude = ""; 
        }

        $user = Auth::user();
        $permission = Permission::findByName('create all lesson');

        if ($user->hasPermissionTo($permission)) {
            $user_id = request()->user_id;
        } else {
            $user_id = request()->user()->id;
        }
       
        $file = "";

        $startDate = request()->quiz_schedule ? request()->quiz_schedule[0] : '';
        $endDate = request()->quiz_schedule ? request()->quiz_schedule[1] : '';

        if(request()->hasFile('thumbnail')){
            $filename = request()->file('thumbnail')->getClientOriginalName();
            $file = request()->file('thumbnail')->storeAs('/quiz', $filename);
        }

        $data = $this->validateData();

        $tabs = '';
        $resultCat = '';
        $lesson = Lesson::find($lesson_id);
        
        if($lesson->term == 1){
            $tabs = 'Midterm';
        }else{
            $tabs = 'Finals';
        }

        $attributes = [
                'name' => 'Quiz',
                'section_id' => $lesson->section_id,
                'subject_id' => $lesson->subject_id,
                'percentage' => 30,
                'tabs' => $tabs,
                'category' => 'Lecture',
                'user_id' => request()->user_id,
        ];

        $conditions = [
            'name' => $attributes['name'],
            'section_id' => $attributes['section_id'],
            'subject_id' => $attributes['subject_id'],
            'tabs' => $attributes['tabs'],
            'user_id' => request()->user_id,
        ];

         // Check for duplicate name (case-insensitive)
        $existingCat = StudentGradeCat::where($conditions)
        ->first();

        $points = collect($data['questions'])->sum('points');
        $catId = 0;
        $catCnt = 0;

        if(!$existingCat){
            $resultCat = StudentGradeCat::create($attributes);
            $catId = $resultCat->id;
            $catCnt = 0;

        }else{
            $catId = $existingCat->id;
            $catCnt = StudentGradeSub::where('student_grade_cat_id', $catId)->count();
        }

        $subCat = [
            'name' => 'Quiz '.$catCnt+1,
            'student_grade_cat_id' => $catId,
            'over' => $points,
        ];

        $resultID = StudentGradeSub::create($subCat);

        $result = Quiz::create(array_merge($this->validateData(), [
            'user_id' => $user_id,
            'excluded' => $exclude,
            'quiz_schedule' => $startDate,
            'end_date' => $endDate,
            'thumbnail' => $file,
            'student_grade_sub_id' => $resultID->id
        ]));

        if(isset($data['questions'])){
            foreach ($data['questions'] as $question) {
                $question['quiz_id'] = $result->id;
                $this->createQuestion($question);
            }
        }

        return Redirect::to('/quiz/'.$result->slug.'/edit')->with('message', 'Quiz has been created successfully.');
    }

    public function duplicate(Quiz $quiz){

        $quizWithQuestions = $quiz->with('question')
                            ->where('id', $quiz->id)
                            ->first();

        $result = new Quiz($quizWithQuestions->toArray());
        $result->published = 0;
        $result->save();
        
        // Get the associated questions
        $questions = $quizWithQuestions->question;
        
        // Duplicate the questions
        foreach ($questions as $question) {
            $newQuestion = new Question($question->toArray());
            //$newQuestion->id = null;
            $newQuestion->quiz_id = $result->id;
            //dd($newQuestion);
            // Modify the question attributes if needed
            //$newQuestion->quiz_id = $result->id; // Assign the new quiz_id
            $newQuestion->save();
        }
        

    }

    private function createQuestion($data)
    {

        if(isset($data['data'])){
            if (is_array($data['data'])) {
                $data['data'] = json_encode($data['data']);
            }
        }

        if(isset($data['answer'])){
            if (is_array($data['answer'])) {
                $data['answer'] = json_encode($data['answer']);
            }
        }

        $validator = Validator::make($data, [
            'question' => 'required|string',
            'type' => ['required', Rule::in([
                Quiz::TYPE_TEXT,
                Quiz::TYPE_TEXTAREA,
                Quiz::TYPE_RADIO,
                Quiz::TYPE_CHECKBOX,
                Quiz::TYPE_FILE_UPLOAD,
            ])],
            'description' => 'nullable|string',
            'data' => 'present',
            'answer' => 'required',
            'points' => 'required',
            'quiz_id' => Rule::exists('quizzes', 'id')
        ]);


        return Question::create($validator->validated());
    }

    private function updateQuestion(Question $question, $data)
    {
        if (isset($data['data'])) {
            $data['data'] = json_encode($data['data']);
        }

        if(isset($data['answer'])){
            if (is_array($data['answer'])) {
                $data['answer'] = json_encode($data['answer']);
            }
        }

        $validator = Validator::make($data, [
            'id' => 'exists:App\Models\Question,id',
            'question' => 'required|string',
            'type' => ['required', Rule::in([
                Quiz::TYPE_TEXT,
                Quiz::TYPE_TEXTAREA,
                Quiz::TYPE_RADIO,
                Quiz::TYPE_CHECKBOX,
                Quiz::TYPE_FILE_UPLOAD,
            ])],
            'description' => 'nullable|string',
            'data' => 'present',
            'answer' => 'required',
            'points' => 'required',
            'quiz_id' => Rule::exists('quizzes', 'id')
        ]);

        $question->update($validator->validated());
    }


    protected function validateData(?Quiz $quiz = null): array
    {
      
        $quiz ??= new Quiz();
        return request()->validate([
            'title' => 'required',
            'thumbnail' => $quiz->exists ? ['nullable','image'] : ['nullable','image'],
            'description' => 'required',
            'excluded' => 'nullable',
            'published' => 'required',
            'date_copy' => 'nullable',
            'position' => 'nullable',
            'quiz_schedule' => 'required',
            'questions' => 'required',
            'lesson_tags' => 'nullable',
            'lesson_id' => ['required', Rule::exists('lessons', 'id')]
        ]);
    }

    public function destroy($id){

        $url = Quiz::where('id',$id)->first();
        
        StudentGradeSub::where('id', $url->student_grade_sub_id)->delete();
        Quiz::where('id', '=', $id)->delete();

        $user = Auth::user();
        $permission = Permission::findByName('delete all quiz');

        $sub = $url->lesson->with('subject')->with('section')->where('id', $url->lesson_id)->first();

        if ($user->hasPermissionTo($permission)) {
            return redirect('/quiz/'.$sub->subject->slug.'/'.$sub->section->id.'/'.$url->user->id)->with('message', 'Quiz has been deleted successfully.');
        } else {
            return redirect('/quiz/'.$sub->subject->slug.'/'.$sub->section->id)->with('message', 'Quiz has been deleted successfully.');
        }

    }

    public function destroyList($id){
        
        $result = Quiz::where('id',$id)->first();
        //dd($result->student_grade_sub_id);

        StudentGradeSub::where('id', $result->student_grade_sub_id)->delete();
        Quiz::where('id', '=', $id)->delete();

        return redirect()->back();
    }

    public function review(StudentQuiz $studentquiz){
        
        $quiz = Quiz::select('*')
        ->with('question', function($query) use ($studentquiz){
            $query->select('*',DB::raw("answer as final_answer"))->with('answer', function($query) use ($studentquiz){
                $query->where('student_quiz_id', $studentquiz->id);
            });
        })
        ->with('lesson', function($query){
            $query->with('subject');
        })
        ->where('id', $studentquiz->quiz_id)
        ->first();

       $score = StudentQuiz::find($studentquiz->id);

        $student = User::select(DB::raw("lname + ', ' + fname + ISNULL(' ' + SUBSTRING(mname, 1, 1) + '.', '') + ISNULL(' ' + extname, '') as full_name"), 
        DB::raw("SUBSTRING(fname,1,1) + SUBSTRING(lname,1,1) as fl"),'id')
        ->where('id', $studentquiz->user_id)
        ->first();

        //dd($quiz);
        
        return Inertia::render('Review/Index',[
            'quiz' => $quiz,
            'student' => $student,
            'score' => $score
        ]);
    }

   

    public function faculty_review(Quiz $quiz){

        $quizzes = $quiz->with('question')->with('lesson', function($query){
            $query->with('subject');
        })->find($quiz->id);

        $quizzes['description'] = strip_tags($quizzes['description']);
        $quizzes['quiz_schedule'] = Carbon::parse($quizzes['quiz_schedule'])->format('F j, Y, g:i A');
        $quizzes['end_date'] = Carbon::parse($quizzes['end_date'])->format('F j, Y, g:i A');

        $points = $quizzes->question->sum('points');

        $excludedStudentNumbers = explode(',', $quizzes['excluded']);

        $students = User::whereIn('id', $excludedStudentNumbers)->get();

        $studentNames = $students->map(function ($student) {
            return $student->lname . ', ' . $student->fname;
        });

        
        return Inertia::render('Review/FacultyReview',[
            'quiz' => $quizzes,
            'points' => $points,
            'excluded' => $studentNames
        ]);
    }

    public function actual_review(Quiz $quiz, Question $question){

        $query = $question->with('quiz.lesson.subject')->find($question->id);
        $query['data'] = json_decode($query['data']);

        $links = $quiz
        ->question()
        ->select('id','quiz_id')
        ->get();

        return Inertia::render('Review/ActualQuizReview',[
            'question' => $query,
            'links' => $links,
            'active' => $question->id,
        ]);
    }
}
