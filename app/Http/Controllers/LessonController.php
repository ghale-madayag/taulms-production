<?php

namespace App\Http\Controllers;

use App\Http\Resources\LessonResource;
use App\Models\Answer;
use App\Models\Lesson;
use App\Models\LessonAttachement;
use App\Models\Question;
use App\Models\Quiz;
use App\Models\Section;
use App\Models\StudentCourse;
use App\Models\StudentLessonStatus;
use App\Models\StudentQuiz;
use App\Models\Subject;
use App\Models\Term;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Request;
use Spatie\Permission\Models\Permission;

class LessonController extends Controller
{
    public function ismidterm(Lesson $lesson){
        
        $lesson->is_midterm_completed = request()->is_midterm_completed;
        $lesson->save();

        redirect()->back();
    }

    public function isfinals(Lesson $lesson){
       // dd(request()->is_finals_completed);
        $lesson->is_finals_completed = request()->is_finals_completed;
        $lesson->save();

        redirect()->back();
    }

    public function start(Subject $subject){
        
        $lesson = $subject->lesson()->orderBy('position')
            ->where('section_id', request()->section)
            ->where('term',request()->term)->first();

        $user = request()->user()->id;

        if(request()->term == 1){
            $query = new StudentCourse();
            $query->user_id = $user;
            $query->subject_id = $subject->id;
            $query->term = request()->term;
            $query->save();

            return redirect('/subject/'.$subject->slug.'/lesson/'.$lesson->slug.'/term/'.request()->term.'/'.request()->section);

        }else{

            $query = $subject->student_lesson_statuses_pending()
                ->select('lesson_id')
                ->where('user_id',$user)
                ->first();

            if($query==null){
                return redirect('/subject/'.$subject->slug.'/lesson/'.$lesson->slug.'/term/'.request()->term.'/'.$lesson->section_id);
            }else{
                $lesson = $subject->lesson()->find($query->lesson_id);
                $term = $subject->lessonTerm->first();

                return redirect('/subject/'.$subject->slug.'/lesson/'.$lesson->slug.'/term/'.request()->term.'/'.$lesson->section_id);
            }

        }


    }

    public function getActive(Subject $subject)
    {
        $user = request()->user()->id;
        $query = $subject->student_lesson_statuses_pending()->select('lesson_id')->where('user_id',$user)->first();

        $lesson = $subject->lesson()->find($query->lesson_id);
        $term = $subject->lessonTerm->first();

        return redirect('/subject/'.$subject->slug.'/lesson/'.$lesson->slug.'/term/'.request()->term.'/'.$lesson->section_id);
    }

    public function take(Subject $subject, $lesson, $term, $section){

        $user = request()->user()->id;
        $user_role = auth()->user()->roles->first()->id;

        $finished_quiz = 0;

        if($user_role==1){
            $query = $subject->lesson()->select('slug')
                ->where('term',$term)
                ->where('section_id', $section)
                ->orderBy('position')->get();
        }else{
            $query = $subject->lesson()->select('slug')
                ->where('section_id', $section)
                ->orderBy('term','ASC')
                ->orderBy('position','ASC')
                ->get();
        }

        $lesson_result = Lesson::where('slug',$lesson)
            ->with('subject')
            ->with('attachement')
            ->with(['quiz' => function ($query) use ($user) {
                $query->with('student_quiz_status_pending')
                ->where('published','1')
                ->where('quiz_schedule', '<=', Carbon::now())
                ->where('end_date', '>=', Carbon::now())
                ->whereRaw("excluded NOT LIKE '%,{$user},%'")
                ->whereRaw("excluded NOT LIKE '{$user},%'")
                ->whereRaw("excluded NOT LIKE '%,{$user}'")
                ->whereRaw("excluded NOT LIKE '{$user}'");
            }])
            ->first();

        $quiz = $lesson_result->quiz->first();

        if($quiz!=null){
            $finished_quiz = StudentQuiz::where('quiz_id',$quiz->id)->where('finished',1)->where('user_id',$user)->first();
        }

        

        if($finished_quiz){
            $finished_quiz['question'] = $quiz->question;

            $checkQuiz = Answer::where('student_quiz_id',$finished_quiz->id)
               ->where('points', -1)
               ->count();
            
            $finished_quiz['submitted_score'] = $checkQuiz;
        }
        
        return Inertia::render('Lesson/Take',[
            'lesson' => $lesson_result,
            'links' => $query,
            'active' => $lesson,
            'finished' => $subject->student_lesson_stats($term)->get(),
            'finished_quiz' => $finished_quiz,
        ]);
    }

    public function finished(Lesson $lesson){


        $user = request()->user()->id;

        $student = new StudentLessonStatus();

        $store = $student->where('lesson_id',$lesson->id)
            ->where('subject_id',$lesson->subject->id)
            ->where('user_id', $user);


        if($store->count() != 0){
            $store->update(['finished' => 1]);
        }
        
        if($lesson->term == 1){
            $data = ['term' => $lesson->term +1];
        }else{
            $data = ['term' => $lesson->term +1,'finished' => 1];
        }

        if(request()->complete == true){
            StudentCourse::where('user_id',$user)
                ->where('subject_id',$lesson->subject->id)
                ->update($data);
        }

    }

    public function active(Lesson $lesson){
        $user = request()->user()->id;

        $student = new StudentLessonStatus();

        $store = $student->where('lesson_id',$lesson->id)
            ->where('subject_id',$lesson->subject->id)
            ->where('user_id', $user);

        if($store->count() == 0){
            $student->lesson_id = $lesson->id;
            $student->subject_id = $lesson->subject->id;
            $student->user_id = $user;
            $student->term = request()->term;
            $student->active = 1;
            $student->finished = 0;
            $student->save();
        }

    }

    public function index(Subject $subject, Section $section){
        $user =  User::select(DB::raw("lname + ', ' + fname + ISNULL(' ' + SUBSTRING(mname, 1, 1) + '.', '') + ISNULL(' ' + extname, '') as full_name"),'users.id')->find(auth()->user()->id);

        $lesson = Lesson::selectRaw('*, CONVERT(varchar(19), created_at, 100) as formatted_date')
        ->where('subject_id',$subject->id)
        ->where('section_id', $section->id)
        ->withCount('attachement')
        ->with(['user' => function($query){
                $query->select(DB::raw("lname + ', ' + fname + ISNULL(' ' + SUBSTRING(mname, 1, 1) + '.', '') + ISNULL(' ' + extname, '') as full_name"),'users.id',  
            );
        }])
        ->filterAll(request(['lesson', 'status']))
        ->get();

        $lessonTerm['lesson_mid'] = $lesson->where('term',1)->sortBy('position')->values();
        $lessonTerm['lesson_fin'] = $lesson->where('term',2)->sortBy('position')->values();

        $total = $lesson->count();
        $pending = $lesson->where('published',0)->count();
        $published = $lesson->where('published',1)->count();


        return Inertia::render('Lesson/Index',[
            'subjects' => $subject,
            'section' => $section,
            'user' => $user,
            'lessons' => $lessonTerm,
            'filters' => Request::only(['lesson','status']),
            'total' => $total,
            'pending' => $pending,
            'published' => $published
        ]);
    }

    public function listView(Subject $subject, Section $section, User $user){
        $user =  $user->select(DB::raw("lname + ', ' + fname + ISNULL(' ' + SUBSTRING(mname, 1, 1) + '.', '') + ISNULL(' ' + extname, '') as full_name"),'users.id')->find($user->id);
        
        $lesson = Lesson::selectRaw('*, CONVERT(varchar(19), created_at, 100) as formatted_date')
        ->where('subject_id',$subject->id)
        ->where('section_id', $section->id)
        ->withCount('attachement')
        ->with(['user' => function($query){
                $query->select(DB::raw("lname + ', ' + fname + ISNULL(' ' + SUBSTRING(mname, 1, 1) + '.', '') + ISNULL(' ' + extname, '') as full_name"),'users.id',  
            );
        }])
        ->filterAll(request(['lesson', 'status']))
        ->get();

        $lessonTerm['lesson_mid'] = $lesson->where('term',1)->sortBy('position')->values();
        $lessonTerm['lesson_fin'] = $lesson->where('term',2)->sortBy('position')->values();

        $total = $lesson->count();
        $pending = $lesson->where('published',0)->count();
        $published = $lesson->where('published',1)->count();


        return Inertia::render('Lesson/Index',[
            'subjects' => $subject,
            'section' => $section,
            'user' => $user,
            'lessons' => $lessonTerm,
            'filters' => Request::only(['lesson','status']),
            'total' => $total,
            'pending' => $pending,
            'published' => $published
        ]);
    }

    public function createWithSubject(Request $request,$subject){
        $user = auth()->user()->id;

        $term = getSelectedTerm();

        $subject_id = Subject::select('id')->where('slug', $subject)->first();

        $subject = Subject::select('subjects.id as id', 'subjects.title as text')
                ->join('schedules','subjects.id','schedules.subject_id')
                ->where('schedules.user_id',$user)
                ->where('schedules.term_id', $term->id)
                ->with('section')
                ->filter(request(['search']))
                ->orderBy('subjects.title')
                ->distinct()
                ->get();
        
        return Inertia::render('Lesson/Create', [
            'subjects' => $subject,
            'selected' => $subject_id->id,
        ]);
    }

    public function create(Subject $subject, Section $section){
        
        $user =  User::select(DB::raw("lname + ', ' + fname + ISNULL(' ' + SUBSTRING(mname, 1, 1) + '.', '') + ISNULL(' ' + extname, '') as full_name"),'users.id')->find(auth()->user()->id);

        return Inertia::render('Lesson/Create', [
            'user' => $user,
            'subjects' => $subject,
            'section' => $section,
        ]);
    }

    public function createAll(Subject $subject, Section $section, $user){
        
        $user =  User::select(DB::raw("lname + ', ' + fname + ISNULL(' ' + SUBSTRING(mname, 1, 1) + '.', '') + ISNULL(' ' + extname, '') as full_name"),'users.id')->find($user);

        return Inertia::render('Lesson/Create', [
            'user' => $user,
            'subjects' => $subject,
            'section' => $section,
        ]);
    }

    public function getSection($subject){
        $user = auth()->user()->id;
        $term = getSelectedTerm();

        $section = Section::select('sections.id as id','sections.name as text')
            ->join('schedules','sections.id', 'schedules.section_id')
            ->where('schedules.user_id',$user)
            ->where('schedules.subject_id', $subject)
            ->where('schedules.term_id', $term->id)
            ->get();

        return redirect()->back()->with('status', $section->values());
    }

    public function sort(){

        $lessons = Lesson::all();

        foreach ($lessons as $lesson){
            $id = $lesson['id'];
            foreach (request()->position as $post){
                if($post['id']==$id){
                    $lesson->update(['position' => $post['position'],'term' => $post['term']]);
                }
            }
        }

        return redirect()->back();
    }

    public function store(){
    
        $user = Auth::user();
        $permission = Permission::findByName('create all lesson');

        if ($user->hasPermissionTo($permission)) {
            $user_id = request()->user_id;
        } else {
            $user_id = request()->user()->id;
        }

        $file = "";

        if(request()->hasFile('thumbnail')){
            $filename = request()->file('thumbnail')->getClientOriginalName();
            $file = request()->file('thumbnail')->storeAs('/lesson', $filename);
        }

        $checkPosition = Lesson::where('subject_id',request()->subject_id)
            ->where('section_id',request()->section_id)
            ->orderBy('id','desc')->first();

        if($checkPosition != null){
            $setPos = $checkPosition->position + 1;
        }else{
            $setPos =  1;
        }


        $result = Lesson::create(array_merge($this->validateData(), [
            'user_id' => $user_id,
            'position' => $setPos,
            'thumbnail' => $file,
        ]));

        $data = [];

        if(request()->hasFile('attachement')){
            foreach(request()->attachement as $file){
                $file_name = time().''.rand(10,100).'-'. $file->getClientOriginalName();
                $file->storeAs('/attachement/lessons', $file_name);
                $data['lesson_id'] = $result->id;
                $data['attachement'] = $file_name;
                LessonAttachement::create($data);
            }
        }


       return Redirect::to('/lesson/'.$result->slug.'/edit')->with('message', 'Lesson has been created successfully.');
    }

    public function edit(Lesson $lesson){
        
        $lesson = $lesson
        ->with('subject')
        ->with('section')
        ->with(['user' => function($query){
                $query->select(DB::raw("lname + ', ' + fname + ISNULL(' ' + SUBSTRING(mname, 1, 1) + '.', '') + ISNULL(' ' + extname, '') as full_name"),'users.id',  
            );
        }])
        ->with('attachement')
        ->find($lesson->id);

        return Inertia::render('Lesson/Edit',[
            'lesson' => $lesson,
        ]);

    }

    public function update(Lesson $lesson){

        $attributes = $this->validateData($lesson);

        if ($attributes['thumbnail'] ?? false) {
            $filename = time().''.rand(10,100).'-'.request()->file('thumbnail')->getClientOriginalName();
            $attributes['thumbnail'] = request()->file('thumbnail')->storeAs('/lesson', $filename);
        }

        if(request()->oldThumb != null){
            Storage::disk('public')->delete('/lesson/'.request()->oldThumb);
        }
        if(request()->oldAttachement != null){
            foreach(request()->oldAttachement as $file){
                LessonAttachement::where('attachement', $file)->delete();
                Storage::disk('public')->delete('/attachement/lessons/'.$file);
            }
        }

        if(request()->hasFile('attachement')){
            foreach(request()->attachement as $file){
                $file_name = time().''.rand(10,100).'-'. $file->getClientOriginalName();
                $file->storeAs('/attachement/lessons', $file_name);

                $data['lesson_id'] = $lesson->id;
                $data['attachement'] = $file_name;
                LessonAttachement::create($data);
            }
        }

        if($attributes['thumbnail'] == null && request()->oldThumb == null){
            $attributes['thumbnail'] = $lesson->thumbnail;
        }

        $lesson->update($attributes);

        return Redirect::to('/lesson/'.$lesson->slug.'/edit')->with('message', 'Lesson has been updated successfully.');

    }

    public function destroy($id){

        $url = Lesson::where('id',$id)->first();
        Lesson::where('id', '=', $id)->delete();

        $user = Auth::user();
        $permission = Permission::findByName('delete all lesson');

        if ($user->hasPermissionTo($permission)) {
            return redirect('/lesson/'.$url->subject->slug.'/'.$url->section_id.'/'.$url->user->id)->with('message', 'Announcement has been deleted successfully.');
        } else {
            return redirect('/lesson/'.$url->subject->slug.'/'.$url->section_id)->with('message', 'Announcement has been deleted successfully.');
        }

       
    }

    public function destroyLesson(Subject $subject, Section $section, $id){
        $url = Lesson::where('id',$id)->first();
        Lesson::where('id', '=', $id)->delete();

        $user = Auth::user();
        $permission = Permission::findByName('delete all lesson');

        if($user->hasPermissionTo($permission)) {
            return redirect('/lesson/'.$url->subject->slug.'/'.$url->section_id.'/'.$url->user->id)->with('message', 'Announcement has been deleted successfully.');
        } else {
            return redirect('/lesson/'.$url->subject->slug.'/'.$url->section_id)->with('message', 'Announcement has been deleted successfully.');
        }

    }

    public function destroyList($id){

        Lesson::where('id', '=', $id)->delete();

        return redirect()->back();
    }

    protected function validateData(?Lesson $lesson = null): array
    {
        
        $lesson ??= new Lesson();
        $rules = [
            'title' => 'required',
            'thumbnail' => $lesson->exists ? ['nullable', 'image'] : ['nullable', 'image'],
            'published' => 'required',
            'term' => 'required',
            'position' => 'nullable',
            'subject_id' => ['required', Rule::exists('subjects', 'id')],
            'section_id' => 'required',
            'user_id' => 'nullable',
        ];
    
        // Conditionally add 'required' rule for 'short_text' based on button click
        if (request()->published == 1) {
            $rules['short_text'] = 'required';
            $rules['full_text'] = 'required';
        }else{
            $rules['short_text'] = 'nullable';
            $rules['full_text'] = 'nullable';
        }

        return request()->validate($rules);

    }

    public function showAll(){
        $lessons = Lesson::with(['subject' => function($query){
                        $query->select('id','title','slug');
                    }])
                    ->with(['section' => function($query){
                        $query->select('id','name');
                    }])
                    ->with(['user' => function($query){
                            $query->select(DB::raw("lname + ', ' + fname + ISNULL(' ' + SUBSTRING(mname, 1, 1) + '.', '') + ISNULL(' ' + extname, '') as full_name"),'users.id',  
                        );
                    }])
                    ->filterAll(request(['subject','faculty','section','lesson', 'status']))
                    ->paginate(10);

        $total = Lesson::count();
        $pending = Lesson::where('published',0)->count();
        $published = Lesson::where('published',1)->count();

        return Inertia::render('Lesson/All',[
            'lessons' => $lessons,
            'filters' => Request::only(['subject','faculty','section','lesson','status']),
            'total' => $total,
            'pending' => $pending,
            'published' => $published
        ]);
    }

    public function editAll(Lesson $lesson, $user){
    
        $term = getSelectedTerm();

        $section = Section::select('sections.id as id','sections.name as text')
            ->join('schedules','sections.id', 'schedules.section_id')
            ->where('schedules.term_id', $term->id)
            ->where('schedules.user_id',$user)
            ->get();

        $subject = Subject::select('subjects.id as id', 'subjects.title as text')
            ->join('schedules','subjects.id','schedules.subject_id')
            ->where('schedules.user_id',$user)
            ->where('schedules.term_id', $term->id)
            ->with('section')
            ->filter(request(['search']))
            ->orderBy('subjects.title')
            ->distinct()
            ->get();

    
        return Inertia::render('Lesson/EditAll',[
            'subjects' => $subject,
            'section' => $section,
            'lesson' => $lesson->where('slug', $lesson->slug)->with('attachement')->first(),
            'createUrl' => $lesson->subject,
        ]);

    }

    public function getSectionAll($subject, $user){
        $term = getSelectedTerm();

        $section = Section::select('sections.id as id','sections.name as text')
            ->join('schedules','sections.id', 'schedules.section_id')
            ->where('schedules.user_id',$user)
            ->where('schedules.subject_id', $subject)
            ->where('schedules.term_id', $term->id)
            ->get();

        return redirect()->back()->with('status', $section->values());
    }

    public function duplicate(){
        
        $subjectId = request()->subject;
        $sectionId = (int) request()->section;
        $currentSection = request()->currentSection;

        $lessonQuiz = Lesson::where('subject_id', $subjectId)
            ->where('section_id', $currentSection)
            ->with(['quiz' => function ($query) {
                    $query->with('question');
                }])
            ->with('attachement')
            ->get();

        foreach ($lessonQuiz as $lesson) {
            $lesResult = new Lesson();
            $lesResult->user_id = $lesson->user_id;
            $lesResult->title = $lesson->title;
            $lesResult->short_text = $lesson->short_text;
            $lesResult->thumbnail = $lesson->thumbnail;
            $lesResult->full_text = $lesson->full_text;
            $lesResult->term = $lesson->term;
            $lesResult->position = $lesson->position;
            $lesResult->published = 0;
            $lesResult->subject_id = $lesson->subject_id;
            $lesResult->section_id = $sectionId;

            $lesResult->save();

            $quizResult = $lesson->quiz;

            foreach($quizResult as $quiz){
                $newQuiz = new Quiz($quiz->toArray());
                $newQuiz->lesson_id = $lesResult->id;
                $newQuiz->published = 0;
                $newQuiz->save();

                $questResult = $quiz->question;

                foreach($questResult as $question){
                    $newQuestion = new Question($question->toArray());
                    $newQuestion->quiz_id = $newQuiz->id;

                    $newQuestion->save();
                }
            }

            $attachments = $lesson->attachement;

            foreach($attachments as $attachment){
               
                $newAttachment = new LessonAttachement([
                    'lesson_id' => $lesResult->id,
                    'attachement' => $attachment->attachement, 
                ]);
                $newAttachment->save();

            }
        }

        //return redirect()->back()->with('success', 'Lessons are  successfully copied.');

    }
}
