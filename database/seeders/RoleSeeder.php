<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $student = Role::create(['name' => 'student']);
        $faculty = Role::create(['name' => 'faculty']);
        $administrator = Role::create(['name' => 'administrator']);
        
        $permissions = [
            ['name' => 'view subject'],
            ['name' => 'view all subject'],

            ['name' => 'create lesson'],
            ['name' => 'edit lesson'],
            ['name' => 'delete lesson'],
            ['name' => 'view lesson'],
            ['name' => 'create all lesson'],
            ['name' => 'edit all lesson'],
            ['name' => 'delete all lesson'],
            ['name' => 'view all lesson'],
            ['name' => 'view lesson dashboard'],

            ['name' => 'create quiz'],
            ['name' => 'edit quiz'],
            ['name' => 'delete quiz'],
            ['name' => 'review quiz'],
            ['name' => 'create all quiz'],
            ['name' => 'edit all quiz'],
            ['name' => 'delete all quiz'],
            ['name' => 'review all quiz'],
            ['name' => 'take quiz'],
            ['name' => 'view quiz dashboard'],
            
            ['name' => 'reset password'],
            ['name' => 'reset student password'],
            ['name' => 'reset faculty password'],
            
            ['name' => 'add email'],
            ['name' => 'add all email'],

            ['name' => 'change email'],
          
            ['name' => 'create announcement'],
            ['name' => 'edit announcement'],
            ['name' => 'delete announcement'],
            ['name' => 'create all announcement'],
            ['name' => 'edit all announcement'],
            ['name' => 'delete all announcement'],
            ['name' => 'view announcement dashboard'],

            ['name' => 'create conference'],
            ['name' => 'delete conference'],
            ['name' => 'create all conference'],
            ['name' => 'delete all conference'],
            ['name' => 'view conference dashboard'],

            ['name' => 'view students'],
            ['name' => 'view all students'],
            ['name' => 'view student report'],
          
            ['name' => 'add time records'],
            ['name' => 'delete all time records'],
            ['name' => 'view all time records'],
            ['name' => 'view all video'],
          
            ['name' => 'view all faculty'],

            ['name' => 'manage roles'],
            ['name' => 'manage grade'],
            ['name' => 'manage database'],

        ];

       
        foreach ($permissions as $permission) {
            $assign = Permission::create($permission);
            //$administrator->givePermissionTo($assign);
        }

        $administrator->givePermissionTo([
            'view subject',
            'create lesson',
            'edit lesson',
            'delete lesson',
            'view lesson dashboard',
            'create quiz',
            'edit quiz',
            'delete quiz',
            'review quiz',
            'view quiz dashboard',
            'create announcement',
            'edit announcement',
            'delete announcement',
            'view announcement dashboard',
            'view all subject',
            'create all lesson',
            'edit all lesson',
            'delete all lesson',
            'view all lesson',
            'view lesson dashboard',
            'create all quiz',
            'edit all quiz',
            'delete all quiz',
            'view quiz dashboard',
            'review all quiz',
            'add all email',
            'create all announcement',
            'edit all announcement',
            'delete all announcement',
            'view announcement dashboard',
            'create all conference',
            'delete all conference',
            'view conference dashboard',
            'create conference',
            'delete conference',
            'add time records',
            'view students',
            'delete all time records',
            'view all time records',
            'view all video',
            'view all students',
            'view all faculty',
            'reset student password',
            'reset faculty password',
            'manage roles',
            'reset password',
            'add email',
            'change email',
            'manage grade',
            'manage database',
        ]);

        $faculty->givePermissionTo([
            'view subject',
            'create lesson',
            'edit lesson',
            'delete lesson',
            'view lesson dashboard',
            'create quiz',
            'edit quiz',
            'delete quiz',
            'review quiz',
            'view quiz dashboard',
            'create announcement',
            'edit announcement',
            'delete announcement',
            'view announcement dashboard',
            'create conference',
            'delete conference',
            'view conference dashboard',
            'view students',
            'reset password',
            'add email',
            'change email',
            'manage grade',
        ]);

        $student->givePermissionTo([
            'view subject',
            'view lesson',
            'take quiz',
            'reset password',
            'add email',
            'change email',
            'view student report',
        ]);

    }
}
