<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Redirect;

class RolesController extends Controller
{
    public function index(){
        $options = Role::all()
        ->where('name', '<>', 'student')
        ->pluck('name', 'id')->toArray();
            
        $roles = Role::where('name', '<>', 'student')->get();
        $users = User::select(DB::raw("lname + ', ' + fname + ISNULL(' ' + SUBSTRING(mname, 1, 1) + '.', '') + ISNULL(' ' + extname, '') as full_name"),'users.id','roles.id as role_id',  
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

        $excludedRoles = 'student';
        $roles_count = Role::where('name', '<>', $excludedRoles)
            ->withCount('users')
            ->get();


        //dd($roles_count);
        return Inertia::render("Roles/Role", [
            'users' => $users,
            'select_roles' => $options,
            'role_cnt' => $roles_count,
            'filters' => request(['search','roles']),
        ]);
    }

    public function update($role, $user){
        
        $rolesString = $role;
        $rolesArray = explode(',', $rolesString);

        $user = User::find($user);
        $roles = Role::whereIn('id',$rolesArray)->get();

        $user->syncRoles($roles);
    }

    public function create(){
        $permissions = Permission::all();
        $roles = Role::orderBy('name','asc')->get();

        return Inertia::render('Roles/Create',[
            'permissions' => $permissions,
            'roles' => $roles,
        ]);
    }

    public function store(){


        $validatedData = request()->validate([
            'title' => 'required',
            'selectedItems' => 'required',
        ]);

        $roleName = $validatedData['title'];

        $roleCheck = Role::where('name', $roleName)->first();

        if ($roleCheck) {
            return redirect()->back()->with('error', 'Role with the given name already exists.');
        }else{
            $role = Role::create(['name' => strtolower($roleName)]);
            $permissionIds = request()->selectedItems;
            $permissions = Permission::whereIn('id', $permissionIds)->get();
            $role->syncPermissions($permissions);
    
            return redirect('roles/'.strtolower($validatedData['title']).'/edit')->with('success', 'Role has been updated successfully.');
        }

        
    }

    public function edit($role){

        $roles = Role::where('name', $role)->first();
        $selected = $roles->permissions->pluck('name')->toArray();

        $permissions = Permission::all();

        $permissions = $permissions->map(function ($permission) use ($selected) {
            $check =  in_array($permission->name,$selected); 
        
            return [
                'id' => $permission->id,
                'name' => $permission->name,
                'check' => $check,
            ];
        });
        
        //dd($role);

        return Inertia::render('Roles/Edit',[
            'role' => $roles,
            'permissions' => $permissions,
        ]);
    }

    public function editRole(Role $role){

        $validatedData = request()->validate([
            'title' => 'required',
            'selectedItems' => 'required',
        ]);

        $newRoleName = $validatedData['title'];

        if($newRoleName == request()->oldname){
            $role->name = strtolower($newRoleName);
            $role->save();
    
            $permissionIds = request()->selectedItems;
            $permissions = Permission::whereIn('id', $permissionIds)->get();
            $role->syncPermissions($permissions);
    
            return redirect('roles/'.strtolower($newRoleName).'/edit')->with('success', 'Role has been updated successfully.');
        }else{

            $roleCheck = Role::where('name', $newRoleName)->first();

            if ($roleCheck) {
                return redirect()->back()->with('error', 'Role with the given name already exists.');
            }else{
                 $role->name = strtolower($newRoleName);
                $role->save();
        
                $permissionIds = request()->selectedItems;
                $permissions = Permission::whereIn('id', $permissionIds)->get();
                $role->syncPermissions($permissions);
        
                return redirect('roles/'.strtolower($newRoleName).'/edit')->with('success', 'Role has been updated successfully.');
            }
        } 
    }

    public function destroy($id){

        $role = Role::find($id);

        if ($role) {
            if ($role->users()->exists()) {
                return redirect()->back()->withErrors(['message' => 'Cannot delete role because it is still assigned to one or more users. Please reassign the users to a different role before deleting this role.']);
            } else {
                $role->delete();
            }
        }

        return redirect('/roles/create')->with('message', 'Role has been deleted successfully.');
    }
}
