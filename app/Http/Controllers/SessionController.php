<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\Lesson;
use App\Models\Quiz;
use App\Models\StudentCourse;
use App\Models\StudentQuiz;
use App\Models\Subject;
use App\Models\Term;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use App\Models\User;
use App\Rules\MatchOldPassword;
use Illuminate\Support\Facades\Password;

class SessionController extends Controller
{
    public function dashboard()
    {
        $user = auth()->user()->id;
        $user_role = auth()->user()->roles->first()->id;

        if($user_role == 1){
            $user = auth()->user()->id;
            $term = getSelectedTerm();

            $subject = StudentCourse::where('user_id',$user)
                ->where('finished',0)
                ->with(['subject' => function ($query) {
                    $query->with('lesson');
                }])
                ->limit(5)
                ->get();

            $quiz = StudentQuiz::where('user_id', $user)
                ->where('finished',0)
                ->with(['quiz' => function ($query) {
                    $query->selectRaw('*, CONVERT(varchar(19), end_date, 100) as formatted_date')->with('student_quiz_status_pending');
                }])
                ->orderBy('updated_at','desc')
                ->paginate(2);
        

            $announ = Subject::select('subjects.id','subjects.title','subjects.slug','subjects.thumbnail','subjects.code','schedules.user_id','schedules.section_id','schedules.sched_date','schedules.room_type')
                ->join('schedules','subjects.id','schedules.subject_id')
                ->join('registration_details','schedules.id','registration_details.schedule_id')
                ->join('registrations','registration_details.registration_id','registrations.id')
                ->join('users','registrations.user_id','users.id')
                ->where('schedules.term_id', $term->id)
                ->where('users.id',$user)
                ->orderBy('subjects.title')
                ->get();
            
            $announcements = [];
            
            foreach($announ as $an){
                
                $queryAnn = Announcement::where('subject_id', $an->id)
                    ->where('section_id', $an->section_id)
                    ->get();
                
                foreach ($queryAnn as $data){
                    $data['month'] = date_format($data['updated_at'],"M");
                    $data['day'] = date_format($data['updated_at'],"d");
                }
        

                if($queryAnn){
                    $announcements[] = $queryAnn;
                }
                
            }

            //dd($quiz);
            return Inertia::render('Student/Profile',[
                'user' => User::find(auth()->user()->id),
                'subject' => $subject,
                'quiz' => $quiz,
                'announ' => $announcements,
            ]);
        }else{
            
            $term = getSelectedTerm();//Term::orderBy('id', 'desc')->first();
            
            $subject = Subject::select('subjects.id','subjects.title','subjects.slug','subjects.thumbnail','subjects.code','schedules.section_id','schedules.sched_date')
                ->join('schedules','subjects.id','schedules.subject_id')
                ->where('schedules.user_id',$user)
                ->where('schedules.term_id', $term->id)
                ->with(['section' => function($query){
                    $query->with(['meet' => function ($query) {
                        $query->select('id','subject_id','section_id','link');
                    }]);
                }])
                ->filter(request(['search']))
                ->orderBy('subjects.title');

            $lesson = Lesson::where('user_id', $user)
                ->with('subject')
                ->orderBy('id','desc')
                ->limit(4)->get();

            $quiz = Quiz::where('user_id', $user)->limit(2)->get();
        
            return Inertia::render('Faculty/Profile',[
                'subjectTotal' => $subject->count(),
                'subject' => $subject->paginate(2),
                'lessons' => $lesson,
                'lessonTotal' => $lesson->count(),
                'quizzes' => $quiz,
                'quizTotal'=> $quiz->count(),
            ]);

            
        }


    }

    public function create(){
        $id = auth()->user()->id;

        return Inertia::render('ProfileEmail',[
            'user' => User::find($id),
        ]);
    }

    public function facultyEmail($faculty){
        return Inertia::render('Administrator/ChangeEmail',[
            'user' => User::find($faculty),
        ]);
    }

    public function store(Request $request){
    
        $request->validate([
            'email' => ['required', Rule::unique('users','email')],
        ]);

        $query = new User();
        $check = User::where('id', $request->id)->first();
        $check['email'] = $request->email;

        if ($check) {
            
            $query->upsert([
                'id' => $request->id,
                'email' => $request->email
            ],
                ['id'],
                ['email']
            );

            $check->sendEmailVerificationNotification();
        }

        return back()->with('status','success');
    }

    public function validatePasswordRequest(Request $request){
        $credentials = $request->validate(['email' => 'required|email']);
        Password::sendResetLink($credentials);
        return redirect()->back();
    }

    public function resetPasswordRequest(Request $request){
        $request->validate([
            'current_password' => ['required', new MatchOldPassword],
            'password' => [
                'required',
                'min:6',
                'regex:/[a-z]/',
                'regex:/[A-Z]/',
                'regex:/[0-9]/',
            ],
            'password_confirmation' => ['same:password'],
        ]);

        User::find(auth()->user()->id)->update(['password'=> bcrypt($request->password)]);
        return redirect()->back();
    }
}
