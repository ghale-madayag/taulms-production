<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use App\Models\User;
use Request;
use Spatie\Permission\Models\Role;

class FacultyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $options = Role::all()
        ->where('name', '<>', 'student')
        ->pluck('name', 'id')->toArray();
        
        $roles = Role::where('name', '<>', 'student')->get();
        $faculty = User::select('users.email','users.email_verified_at',DB::raw("lname + ', ' + fname + ISNULL(' ' + SUBSTRING(mname, 1, 1) + '.', '') + ISNULL(' ' + extname, '') as full_name"),'users.id','roles.id as role_id',  
        DB::raw("UPPER(LEFT(roles.name, 1)) + SUBSTRING(roles.name, 2, LEN(roles.name)) as role_name"), DB::raw("SUBSTRING(fname,1,1) + SUBSTRING(lname,1,1) as fl"))
        ->whereHas('roles', function ($query) use ($roles) {
            $query->whereIn('name', $roles->pluck('name'));
        })
        ->leftJoin('model_has_roles', 'model_has_roles.model_uuid', '=', 'users.id')
        ->leftJoin('roles', 'roles.id', '=', 'model_has_roles.role_id')
        ->where('users.id', '<>', auth()->id())
        ->orderBy('lname', 'asc')
        ->filter(request(['search','roles']))
        ->paginate(10);

        $roleName = 'student';
        $total = User::whereHas('roles', function ($query) use ($roleName) {
                    $query->where('name', '!=', $roleName);
                })
                ->count();

        return Inertia::render('Faculty/FacultyList',[
            'faculty' => $faculty,
            'filters' => Request::only(['search','selected']),
            'select_roles' => $options,
            'total' => $total
        ]);
    }

    public function changePassword(){
        $id = auth()->user()->id;

        return Inertia::render('ProfilePassword',[
            'user' => User::find($id),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
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

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
