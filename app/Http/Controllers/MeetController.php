<?php

namespace App\Http\Controllers;

use App\Models\Meet;
use App\Models\Section;
use App\Models\Subject;
use App\Models\Term;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Spatie\GoogleCalendar\Event;
use Spatie\Permission\Models\Permission;

class MeetController extends Controller
{

    public function index(Subject $subject, Section $section)
    {

        $query = Meet::selectRaw('*, CONVERT(varchar(19), created_at, 100) as formatted_date')
            ->with(['subject' => function($quer){
                $quer->select('id','code','title');
            }])
            ->with(['section' => function($quer){
                $quer->select('id','name');
            }])
            ->where('subject_id', $subject->id)
            ->where('section_id', $section->id)
            ->orderBy('created_at', 'desc')
            ->paginate(5);
        
        $user =  User::select(DB::raw("lname + ', ' + fname + ISNULL(' ' + SUBSTRING(mname, 1, 1) + '.', '') + ISNULL(' ' + extname, '') as full_name"),'users.id')->find(auth()->user()->id);

        return Inertia::render('Meet/Create',[
            'subjects' => $subject,
            'section' => $section,
            'user' =>  $user,
            'meet' => $query,
        ]);
    }

    public function indexAll(Subject $subject, Section $section, User $user)
    {

        $query = Meet::selectRaw('*, CONVERT(varchar(19), created_at, 100) as formatted_date')
            ->with(['subject' => function($quer){
                $quer->select('id','code','title');
            }])
            ->with(['section' => function($quer){
                $quer->select('id','name');
            }])
            ->where('subject_id', $subject->id)
            ->where('section_id', $section->id)
            ->orderBy('created_at', 'desc')
            ->paginate(5);
        
        $user =  User::select(DB::raw("lname + ', ' + fname + ISNULL(' ' + SUBSTRING(mname, 1, 1) + '.', '') + ISNULL(' ' + extname, '') as full_name"),'users.id')->find($user->id);

        return Inertia::render('Meet/Create',[
            'subjects' => $subject,
            'section' => $section,
            'user' =>  $user,
            'meet' => $query,
        ]);
    }

    public function store()
    {  

        //dd(request());
        $query = Meet::where('subject_id',request()->subject_id)
                ->where('section_id', request()->section_id)
                ->count();


        if ($query > 0) {
            return redirect()->back()->with('error', 'The link for this class is already exist!');
        } else {
            
            $meetID = uniqid();

            $user = Auth::user();
            $permission = Permission::findByName('create all conference');

            if ($user->hasPermissionTo($permission)) {
                $user_id = request()->user_id;
            } else {
                $user_id = request()->user()->id;
            }

            $result = Meet::create(array_merge($this->validateData(), [
                'user_id' => $user_id,
                'subject_id' => request()->subject_id,
                'section_id' => request()->section_id,
                'startDate' => Carbon::now(),
                'endDate' => Carbon::now()->addHour(),
                'meetID' => request()->link,
            ]));

            // $client = new \GuzzleHttp\Client();
            // $response = $client->post('https://hooks.zapier.com/hooks/catch/14479021/3ymhg5n/', [
            //     'json' => [
            //         'meetID' => $meetID,
            //         'startDate' => Carbon::now(),
            //         'endDate' => Carbon::now()->addHour(),
            //     ]
            // ]);

            //$statusCode = $response->getStatusCode();

            // if ($statusCode == 200) {
            //     $result = Meet::create(array_merge($this->validateData(), [
            //         'user_id' => request()->user()->id,
            //         'subject_id' => request()->subject_id,
            //         'section_id' => request()->section_id,
            //         'startDate' => Carbon::now(),
            //         'endDate' => Carbon::now()->addHour(),
            //         'meetID' => $meetID,
            //     ]));

            //     sleep(5);

            //     $this->getEvent($meetID);
                
            //     return redirect()->back()->with('success', 'Google meet was successfully created');
            // } else {
            //     return redirect()->back()->with('error', 'There is a problem creating the link');
            // }
            
            
        }
       
        
      
    }


    protected function validateData(?Meet $meet = null): array
    {
        
        $meet ??= new Meet();
        return request()->validate([
            'user_id' => 'nullable',
            'subject_id' => 'required',
            'section_id' => 'required',
            'startDate' => 'nullable',
            'endDate' => 'nullable',
            'meetID'=> 'nullable',
            'link' => 'nullable',
        ]);
    }

    public function getEvent($meetID){
       
        $events = Event::get();
       // dd($events);
        foreach ($events as $event) {
            if($event->name == $meetID){
                $hangoutLink = $event->hangoutLink;
                
                $this->update($meetID, $hangoutLink, $event->id);
            }
            //echo $event->name . "\n";
        }

        //dd($events);
    }

    public function update($meetID, $link, $id){
        $query = Meet::where('meetID', $meetID)->update([
            'link' => $link,
            'meetID' => $id,
        ]);
    }


    public function destroy($id){

        // $meetID = Meet::where('id',$id)->first();


        // $event = Event::find($meetID->meetID);
        // $event->delete();
        $url = Meet::with('section')->with('subject')->where('id',$id)->first();
        Meet::where('id', '=', $id)->delete();

        $user = Auth::user();
        $permission = Permission::findByName('delete all conference');

        if ($user->hasPermissionTo($permission)) {
            return redirect('/meet/'.$url->subject->slug.'/'.$url->section->id.'/'.$url->user_id)->with('message', 'Google Meet has been deleted successfully.');
        } else {
            return redirect('/meet/'.$url->subject->slug.'/'.$url->section->id)->with('message', 'Google Meet has been deleted successfully.');
        }

        //return redirect('/meet/create')->with('message', 'Google Meet has been deleted successfully.');
    }


}

