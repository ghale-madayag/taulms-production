<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\Lesson;
use App\Models\Section;
use App\Models\Subject;
use App\Models\Term;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Spatie\Permission\Models\Permission;

class AnnouncementController extends Controller
{

    public function index(Subject $subject, Section $section){
        
        $announcement = Announcement::where('subject_id', $subject->id)
            ->where('section_id', $section->id)
            ->filter(request(['search']))
            ->orderBy('created_at','DESC')
            ->paginate(5)
            ->withQueryString();

        foreach ($announcement as $data){
            $data['month'] = date_format($data['created_at'],"M");
            $data['day'] = date_format($data['created_at'],"d");
        }

        $user =  User::select(DB::raw("lname + ', ' + fname + ISNULL(' ' + SUBSTRING(mname, 1, 1) + '.', '') + ISNULL(' ' + extname, '') as full_name"),'users.id')->find(auth()->user()->id);

        return Inertia::render('Announcement/Index',[
            'announs' => $announcement,
            'subjects' => $subject,
            'section' => $section,
            'user' =>  $user,
            'total' => $announcement->count(),
            'filters' => request(['search']),
        ]);
    }

    public function listView(Subject $subject, Section $section, User $user){

        $announcement = Announcement::where('subject_id', $subject->id)
            ->where('section_id', $section->id)
            ->filter(request(['search']))
            ->orderBy('created_at','DESC')
            ->paginate(5)
            ->withQueryString();

        foreach ($announcement as $data){
            $data['month'] = date_format($data['created_at'],"M");
            $data['day'] = date_format($data['created_at'],"d");
        }

        $user =  User::select(DB::raw("lname + ', ' + fname + ISNULL(' ' + SUBSTRING(mname, 1, 1) + '.', '') + ISNULL(' ' + extname, '') as full_name"),'users.id')->find($user->id);

        return Inertia::render('Announcement/Index',[
            'announs' => $announcement,
            'subjects' => $subject,
            'section' => $section,
            'user' =>  $user,
            'total' => $announcement->count(),
            'filters' => request(['search']),
        ]);
    }

    public function create(Subject $subject, Section $section){

        $user =  User::select(DB::raw("lname + ', ' + fname + ISNULL(' ' + SUBSTRING(mname, 1, 1) + '.', '') + ISNULL(' ' + extname, '') as full_name"),'users.id')->find(auth()->user()->id);
       
        return Inertia::render('Announcement/Create',[
            'subjects' => $subject,
            'section' => $section,
            'user' => $user,
        ]);
    }

    public function createAll(Subject $subject, Section $section, $user){
    
        $user =  User::select(DB::raw("lname + ', ' + fname + ISNULL(' ' + SUBSTRING(mname, 1, 1) + '.', '') + ISNULL(' ' + extname, '') as full_name"),'users.id')->find($user);

        return Inertia::render('Announcement/Create',[
            'subjects' => $subject,
            'section' => $section,
            'user' => $user,
        ]);
    }

    public function edit(Announcement $announcement){

        $announ = $announcement->with('subject')
        ->with('section')
        ->with('user', function($query){
            $query->select(DB::raw("lname + ', ' + fname + ISNULL(' ' + SUBSTRING(mname, 1, 1) + '.', '') + ISNULL(' ' + extname, '') as full_name"),'users.id');
        })
        ->where('id', $announcement->id)
        ->first();

        return Inertia::render('Announcement/Edit',[
            'announcement' => $announ,
        ]);
    }

    public function update(Announcement $announcement){
        $attributes = $this->validateData($announcement);
        $announcement->update($attributes);

        return Redirect::to('/announcement/'.$announcement->slug.'/edit')->with('message', 'Announcement has been updated successfully.');
    }

    public function store(){

        $user = Auth::user();
        $permission = Permission::findByName('create all announcement');

        if ($user->hasPermissionTo($permission)) {
            $user_id = request()->user_id;
        } else {
            $user_id = request()->user()->id;
        }

        $result = Announcement::create(array_merge($this->validateData(), [
            'user_id' => $user_id,
        ]));

        return Redirect::to('/announcement/'.$result->slug.'/edit')->with('message', 'Announcement has been created successfully.');
    }

    protected function validateData(?Announcement $announcement = null): array
    {
        $announcement ??= new Announcement();
        return request()->validate([
            'title' => 'required',
            'full_text' => 'required',
            'subject_id' => ['required', Rule::exists('subjects', 'id')],
            'section_id' => 'required',
        ]);
    }

    public function destroy($id){
        $url = Announcement::where('id',$id)->first();
        Announcement::where('id', '=', $id)->delete();

        $user = Auth::user();
        $permission = Permission::findByName('delete all announcement');

        if ($user->hasPermissionTo($permission)) {
            return redirect('/announcement/'.$url->subject->slug.'/'.$url->section_id.'/'.$url->user->id)->with('message', 'Announcement has been deleted successfully.');
        } else {
            return redirect('/announcement/'.$url->subject->slug.'/'.$url->section_id)->with('message', 'Announcement has been deleted successfully.');
        }
        
    }
}
