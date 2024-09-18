<?php

namespace App\Http\Controllers;

use App\Http\Resources\StudentResource;
use App\Models\Lesson;
use App\Models\Registration;
use App\Models\Role;
use App\Models\Section;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Request;
use Inertia\Inertia;

class StudentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Inertia::render('Student/StudentList',[
            'student' => StudentResource::collection(User::select('users.id',
                'users.fname',
                'users.lname',
                'users.mname',
                'users.initial',
                'users.year_lvl',
                'users.extname',
                'registrations.validation_date',
                'role_user.role_id')
                ->join('role_user','users.id','=','role_user.user_id')
                ->join('registrations','users.id','registrations.user_id')
                ->orderBy('lname')
                ->where('role_user.role_id',1)
                ->with('registration')
                ->filter(request(['search','selected']))
                ->paginate(10)),
            'filters' => Request::only(['search','selected']),
            'validated' => Registration::whereNotNull('validation_date')->count(),
            'notvalidated' => Registration::whereNull('validation_date')->count(),
            'displayTotal' => User::select('year_lvl',DB::raw('count(year_lvl) as cnt'))
                ->where('year_lvl','!=',0)
                ->groupBy('year_lvl')
                ->get(),
        ]);
    }

    public function changePassword(){
        $id = auth()->user()->id;

        return Inertia::render('ProfilePassword',[
            'user' => User::find($id),
        ]);
    }

    public function viewStudents(Subject $subject, Section $section){

        $student = User::select(DB::raw("lname + ', ' + fname + ISNULL(' ' + SUBSTRING(mname, 1, 1) + '.', '') + ISNULL(' ' + extname, '') as full_name"), 
        DB::raw("SUBSTRING(fname,1,1) + SUBSTRING(lname,1,1) as fl"),'id')
        ->whereHas('registration', function($query) use ($subject, $section){
            $query->whereHas('registration_details', function($query) use ($subject, $section){
                $query->whereHas('schedule', function($query) use ($subject, $section){
                    $query->where('section_id', $section->id)->where('subject_id', $subject->id);
                });
            });
        })
        ->withCount(['student_lesson_statuses' => function($query){
            $query->where('finished', 1);
        }])
        ->with('student_quizzes', function($query){
            $query->with('quiz');
        })
        ->orderBy('lname', 'asc')
        ->filterStudent(request(['search','status']))
        ->paginate(10);

        $lesson = Lesson::selectRaw('*, CONVERT(varchar(19), created_at, 100) as formatted_date')
        ->where('subject_id',$subject->id)
        ->where('section_id', $section->id)
        ->get();

        $user =  User::select(DB::raw("lname + ', ' + fname + ISNULL(' ' + SUBSTRING(mname, 1, 1) + '.', '') + ISNULL(' ' + extname, '') as full_name"),'users.id')->find(auth()->user()->id);

        return Inertia::render('Student/Index',[
            'students' => $student,
            'user' => $user,
            'total' => $student->count(),
            'subjects' => $subject,
            'section' => $section,
            'lesson' => $lesson->count(),
            'filters' => Request::only(['search','status']),
        ]);
    }

    public function viewAllStudents(Subject $subject, Section $section, User $user){

        $student = User::select(DB::raw("lname + ', ' + fname + ISNULL(' ' + SUBSTRING(mname, 1, 1) + '.', '') + ISNULL(' ' + extname, '') as full_name"), 
        DB::raw("SUBSTRING(fname,1,1) + SUBSTRING(lname,1,1) as fl"),'id')
        ->whereHas('registration', function($query) use ($subject, $section){
            $query->whereHas('registration_details', function($query) use ($subject, $section){
                $query->whereHas('schedule', function($query) use ($subject, $section){
                    $query->where('section_id', $section->id)->where('subject_id', $subject->id);
                });
            });
        })
        ->withCount(['student_lesson_statuses' => function($query){
            $query->where('finished', 1);
        }])
        ->with('student_quizzes', function($query){
            $query->with('quiz');
        })
        ->orderBy('lname', 'asc')
        ->filterStudent(request(['search','status']))
        ->paginate(10);

        $lesson = Lesson::selectRaw('*, CONVERT(varchar(19), created_at, 100) as formatted_date')
        ->where('subject_id',$subject->id)
        ->where('section_id', $section->id)
        ->get();

        $user =  User::select(DB::raw("lname + ', ' + fname + ISNULL(' ' + SUBSTRING(mname, 1, 1) + '.', '') + ISNULL(' ' + extname, '') as full_name"),'users.id')->find($user->id);
       
        return Inertia::render('Student/Index',[
            'students' => $student,
            'user' => $user,
            'total' => $student->count(),
            'subjects' => $subject,
            'section' => $section,
            'lesson' => $lesson->count(),
            'filters' => Request::only(['search','status']),
        ]);
    }

    public function show($id)
    {
        
        return Inertia::render('Student/Profile',[
            'user' => User::find($id),
        ]);
    }


    public function edit($id)
    {
        $owner = false;
        $auth = auth()->user()->id;

        if($auth==$id){
            $owner = true;
        }
        return Inertia::render('ProfileEdit',[
            'user' => User::find($id),
            'owner' => $owner,
        ]);
    }

}
