<?php

namespace App\Http\Controllers;

use App\Http\Resources\SubjectLessonResource;

use App\Models\Announcement;
use App\Models\Lesson;
use App\Models\Meet;
use App\Models\Question;
use App\Models\Quiz;
use App\Models\Schedule;
use App\Models\Section;
use App\Models\StudentCourse;
use App\Models\StudentGradeCat;
use App\Models\StudentGradeMark;
use App\Models\StudentQuiz;
use App\Models\Subject;
use App\Models\Term;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Request;

class SubjectController extends Controller
{
    public function report(){
        
        $term = getSelectedTerm();
        $user = auth()->user()->id;
        $user_role = auth()->user()->roles->first()->id;
        if($user_role == 1){
            $subject = Subject::select('subjects.id','subjects.title','subjects.slug','subjects.thumbnail','subjects.code','schedules.section_id','schedules.sched_date')
                ->join('schedules','subjects.id','schedules.subject_id')
                ->join('registration_details','schedules.id','registration_details.schedule_id')
                ->join('registrations','registration_details.registration_id','registrations.id')
                ->join('users','registrations.user_id','users.id')
                ->where('users.id',$user)
                ->with('student_courses')
                ->with(['lesson' => function($query){
                    $query->with(['quiz' => function ($query) {
                        $query->with(['student_quiz' => function($query){
                            $query->where('user_id', auth()->user()->id);
                        }]);
                    }]);
                }])
                ->with('student_lesson_statuses')
                ->orderBy('subjects.title')
                ->filter(request(['search']))
                ->get();
        }

        $sectionIds = $subject->pluck('section_id')->unique()->toArray();
        $subjectIds = $subject->pluck('id')->unique()->toArray();

        $categoriesMid = StudentGradeCat::with(['student_grade_sub' => function($query) use ($user){
            $query->with(['student_grade_score' => function ($query) use ($user){
                $query->where('user_id', $user);
            }]);
        }])
        ->whereIn('section_id', $sectionIds)
        ->whereIn('subject_id', $subjectIds)
        ->where('tabs', 'midterm')
        ->orderBy('name', 'ASC')
        ->get();

        $categoriesFin = StudentGradeCat::with(['student_grade_sub' => function($query) use ($user){
            $query->with(['student_grade_score' => function ($query) use ($user){
                $query->where('user_id', $user);
            }]);
        }])
        ->whereIn('section_id', $sectionIds)
        ->whereIn('subject_id', $subjectIds)
        ->where('tabs', 'finals')
        ->orderBy('name', 'ASC')
        ->get();



        //dd($categories);

        $gradesData = [];
    

        foreach ($subject as $sub ){

            $filteredResultsMid = [];
            $filteredResultsFin = [];

            $midterm = $this->filterdTerm($categoriesMid,$subject,$term->id);
            $final = $this->filterdTerm($categoriesFin,$subject,$term->id);

            foreach ($midterm as $item) {
                // Check if the "code" key matches the desired criteria
                if ($item['code'] === $sub->code) {
                   
                    // If it matches, add the item to the filteredResults array
                    $filteredResultsMid[] = $item;
                }
            }

            foreach ($final as $item) {
                // Check if the "code" key matches the desired criteria
                if ($item['code'] === $sub->code) {
                    // If it matches, add the item to the filteredResults array
                    $filteredResultsFin[] = $item;
                }
            }

            $gradesData[] = [
                'code' => $sub->code,
                'name' => $sub->title,
                'midterm' => $filteredResultsMid[0],
                'finals' => $filteredResultsFin[0],
            ];
            
        }

        $result = StudentQuiz::select('user_id',DB::raw('SUM(score) as score'),DB::raw('SUM(total) as total'))
            ->where('user_id',$user)
            ->groupBy('user_id')->first();

        if($result != null){
            if($result['score'] != 0 && $result['total'] != 0){
                $total = $result['score']/$result['total'] * 100;
            }else{
                $total = 0;
            }
        }else{
            $total = 0;
        }
        
        $totalSubject = Subject::select('subjects.id','subjects.title','subjects.slug','subjects.thumbnail','subjects.code','schedules.section_id','schedules.sched_date')
           ->join('schedules','subjects.id','schedules.subject_id')
           ->join('registration_details','schedules.id','registration_details.schedule_id')
           ->join('registrations','registration_details.registration_id','registrations.id')
           ->join('users','registrations.user_id','users.id')
           ->where('users.id',$user);
        
        $quiz = StudentQuiz::where('user_id', $user)->get();

       $complete = StudentCourse::where('user_id',$user)->where('finished', 1);

        return Inertia::render('Report/Index',[
            'subject' => $subject,
            'subjects' => $gradesData,
            'total' => $total,
            'total_subject' => $totalSubject->count(),
            'completed' => $complete->count(),
            'student_result' => $quiz->count(),
        ]);
    }


    public function filterdTerm($categories, $subject,$term){

        $gradesData = [];
    

        foreach ($subject as $sub ){

            $studentGrades = [];
            $categoryWeights = [];

            foreach($categories as $cat){
                $totalScore = 0;

                
                if($sub->id == $cat->subject_id && $sub->section_id == $cat->section_id){
                    
                    foreach ($cat->student_grade_sub as $subItem) {

                        $totalScore = $subItem->student_grade_score->sum('score');
                        
                        $perfectScore = $subItem->over;
                        $percentage = $cat->percentage;
        
                        $averageScore = ($perfectScore > 0) ? (($totalScore / $perfectScore) * 100) : 0;
        
                        $formattedAverageScore = number_format($averageScore, 2);
                        
                        $studentGrades[$subItem->name] = [
                            'name' => $subItem->name,
                            'category_id' => $subItem->student_grade_cat_id,
                            'percentage' => $percentage,
                            'total_score' => $totalScore,
                            'average_score' => $formattedAverageScore,
                            'over' => $subItem->over,
                        ];
    
                        if (!isset($categoryWeights[$subItem->student_grade_cat_id])) {
                            $categoryWeights[$subItem->student_grade_cat_id] = 0;
                        }
    
                        $categoryWeights[$subItem->student_grade_cat_id] = $percentage;
                    }
                }

            }

            $categoryAverages = [];
            foreach ($categoryWeights as $categoryId => $weight) {
                $catAvg = 0;
                $categoryTotalScore = 0;
                $categoryTotalPercentage = 0;
                //dd($studentGrades);
                foreach ($studentGrades as $grade) {
                    //dd($categoryId);
                    if ((int)$grade['category_id'] === $categoryId) {
                        //dd('true');
                        $categoryTotalScore += ($grade['average_score'] * $grade['percentage']);
                        $categoryTotalPercentage += $grade['percentage'];
                    }
                }

                $categoryAverage = ($categoryTotalPercentage > 0) ? ($categoryTotalScore / $categoryTotalPercentage) : 0;
                $catAvg = $categoryAverage * ($weight / 100);
                $categoryAverages[$categoryId] = number_format($catAvg, 2);

            }

            foreach ($categories as $ctr) {
                if($sub->id == $cat->subject_id && $sub->section_id == $cat->section_id){
                    foreach ($cat->student_grade_sub as $subItem) {
                        if (!isset($studentGrades[$subItem->name])) {
                            $studentGrades[$subItem->name] = 0;
                        }
                    }
                }
                
            }

            $totalSum = 0;

            foreach ($categoryAverages as $categoryId => $formattedValue) {
                $numericValue = (float) str_replace(',', '', $formattedValue);
                $totalSum += $numericValue;
            }

            $mark = StudentGradeMark::where('subject_id',$sub->id)
                    ->where('section_id', $sub->section_id)
                    ->where('term_id', $term)
                    ->first();

            $passing = ($mark) ? $mark->passing : 60;
            $cutoff = ($mark) ? $mark->cutoff : 75;

            //dd($passing);

            if($totalSum < $passing){
                $percentage = 75 - (25*($passing-$totalSum)/$passing);
            }else{
                $percentage = 100 - (25*(100-$totalSum)/(100-$passing));
            }

            $readableGrade = 1 + 2 *((100-$percentage)/(100-$cutoff));

            $roundedGrade = 0;
            
            if ($readableGrade > 3) {
                $roundedGrade = round($readableGrade * 2) / 2;
            } else {
                $roundedGrade = round($readableGrade * 4) / 4;
            }

            $gradesData[] = [
                'code' => $sub->code,
                'criteria' => $studentGrades,
                'grade' => number_format($roundedGrade, 2),
            ];
            
        }

        return $gradesData;
    }

    public function index()
    {
        $user = auth()->user()->id;
        $user_role = auth()->user()->roles->first()->id;
        $term = getSelectedTerm();

        if($user_role == 1){
           $subject = Subject::select('subjects.id','subjects.title','subjects.slug','subjects.thumbnail','subjects.code','schedules.user_id','schedules.section_id','schedules.sched_date','schedules.room_type')
               ->join('schedules','subjects.id','schedules.subject_id')
               ->join('registration_details','schedules.id','registration_details.schedule_id')
               ->join('registrations','registration_details.registration_id','registrations.id')
               ->join('users','registrations.user_id','users.id')
               ->where('schedules.term_id', $term->id)
               ->where('users.id',$user)
               ->with('section')
               ->with('student_courses')
               ->with('lessonTerm')
               ->with('student_lesson_statuses')
               ->orderBy('subjects.title')
               ->filter(request(['search']))
               ->paginate(8);
               
        }else{

            $subject = Subject::select('subjects.id','subjects.title','subjects.slug','subjects.thumbnail','subjects.code','schedules.section_id','schedules.sched_date','schedules.room_type')
                ->join('schedules','subjects.id','schedules.subject_id')
                ->where('schedules.user_id',$user)
                ->where('schedules.term_id', $term->id)
                ->with('section')
                ->filter(request(['search']))
                ->orderBy('subjects.title')
                ->paginate(8);
        }

        //dd($subject);
        return Inertia::render('Subject/SubjectList',[
            'subject' => $subject,
            'filters' => request(['search']),
       ]);
    }

    public function all()
    {   
        $term = getSelectedTerm();

        $subjects = Schedule::with('subject')
        ->with('section')
        ->with(['user' => function($query){
                $query->select(DB::raw("lname + ', ' + fname + ISNULL(' ' + SUBSTRING(mname, 1, 1) + '.', '') + ISNULL(' ' + extname, '') as full_name"),'users.id',  
                );
            }])
        ->filter(request(['subject','faculty', 'section']))
        ->paginate(10);

        $total = Subject::count();

        return Inertia::render('Subject/All',[
            'subjects' => $subjects,
            'filters' => Request::only(['subject','faculty','section']),
            'total' => $total,
        ]);
    }


    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        //
    }

    public function show(Subject $subject, Section $section)
    {
        $user = auth()->user()->id;
        
        $subjectVal = $subject->ownSubject($user, $subject->id)
            ->with(array('lesson' => function($query) use ($section){
            $query->with('attachement')
                ->where('section_id',$section->id);
        }))->where('slug', $subject->slug)->first();


        $subjectVal['lesson_mid'] = $subjectVal->lesson->where('term',1)->sortBy('position')->values();
        $subjectVal['lesson_fin'] = $subjectVal->lesson->where('term',2)->sortBy('position')->values();
        $announcement = Announcement::where('subject_id', $subject->id)->where('section_id',$section->id)->orderBy('updated_at','DESC')->paginate(2);
        $meeting = Meet::where('subject_id', $subject->id)
        ->where('section_id',$section->id)
        ->first();

        foreach ($announcement as $data){
            $data['month'] = date_format($data['updated_at'],"M");
            $data['day'] = date_format($data['updated_at'],"d");
        }

        $user =  User::select(DB::raw("lname + ', ' + fname + ISNULL(' ' + SUBSTRING(mname, 1, 1) + '.', '') + ISNULL(' ' + extname, '') as full_name"),'users.id')->find(auth()->user()->id);

        return Inertia::render('Subject/SubjectIndex',[
            'subjects' => $subjectVal,
            'section' => $section,
            'user' => $user,
            'announcement' => $announcement,
            'meeting' => $meeting,
        ]);
    }

    public function showAll(Subject $subject, Section $section, $user)
    {
        $subjectVal = $subject->ownSubject($user, $subject->id)
            ->with(array('lesson' => function($query) use ($section){
            $query->with('attachement')
                ->where('section_id',$section->id);
        }))->where('slug', $subject->slug)->first();

        $subjectVal['lesson_mid'] = $subjectVal->lesson->where('term',1)->sortBy('position')->values();
        $subjectVal['lesson_fin'] = $subjectVal->lesson->where('term',2)->sortBy('position')->values();
        $announcement = Announcement::where('subject_id', $subject->id)->where('section_id',$section->id)->orderBy('updated_at','DESC')->paginate(2);
        $meeting = Meet::where('subject_id', $subject->id)
        ->where('section_id',$section->id)
        ->first();

        foreach ($announcement as $data){
            $data['month'] = date_format($data['updated_at'],"M");
            $data['day'] = date_format($data['updated_at'],"d");
        }

        $user =  User::select(DB::raw("lname + ', ' + fname + ISNULL(' ' + SUBSTRING(mname, 1, 1) + '.', '') + ISNULL(' ' + extname, '') as full_name"),'users.id')->find($user);

        return Inertia::render('Subject/SubjectIndex',[
            'subjects' => $subjectVal,
            'section' => $section,
            'user' => $user,
            'announcement' => $announcement,
            'meeting' => $meeting,
        ]);
    }

    public function edit($id)
    {
        //
    }


    public function update(Request $request, $id)
    {
        $subject = new Subject();
        $attributes = request()->validate([
            'thumbnail' => $subject->exists ? ['nullable','image'] : ['nullable','image'],
        ]);

        if(request()->oldThumb!=null){
            $subject->where('id',$id)->update($attributes);
            $files = Storage::disk('public')->delete('/subject'.request()->oldThumb);
        }

        if ($attributes['thumbnail'] !=null) {
            $filename = time().''.rand(10,100).'-'.$attributes['thumbnail']->getClientOriginalName();
            $attributes['thumbnail'] = request()->file('thumbnail')->storeAs('/subject', $filename);
            $subject->where('id',$id)->update($attributes);
        }

        return back()->with('message','Featured image is now updated');
    }


    public function destroy($id)
    {
            Subject::where('id', $id)
                    ->where('thumbnail', '=', request()->thumbnail)
                    ->update(['thumbnail' => NULL]);

            return back()->with('message','Image has been removed');
    }


    public function course(Subject $subject, Section $section){
        $user =  User::select(DB::raw("lname + ', ' + fname + ISNULL(' ' + SUBSTRING(mname, 1, 1) + '.', '') + ISNULL(' ' + extname, '') as full_name"),'users.id')->find(auth()->user()->id);

        $lesson = Lesson::selectRaw('*, CONVERT(varchar(19), created_at, 100) as formatted_date')
        ->where('subject_id',$subject->id)
        ->where('section_id', $section->id)
        ->where('term',1)
        ->where('position',1)
        ->first();


        return redirect()->to('/subject/'.$subject->slug.'/lesson/'.$lesson->slug.'/term/1/'.$section->id);
    }

}
