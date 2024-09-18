<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class FacultyCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:faculty';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync Faculty';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        $oldDb = DB::connection('sqlsrvlms')
        ->table('HR_Employees')
        ->whereExists(fn ($query) =>
           User::select(DB::raw(1))
                 ->from('users')
                 ->whereColumn('users.id', 'HR_Employees.EmployeeID'))
                 ->orderBy('EmployeeID');
        
        $oldDb->chunkById(100, function ($records) {
            foreach ($records as $key => $value){
                $query = new User();
                $query->upsert(
                    [
                        'id' => $value->EmployeeID,
                        'email' => $value->Email,
                        'password' => Hash::make('password'),
                        'fname' => $value->FirstName,
                        'mname' => $value->MiddleName,
                        'lname' => $value->LastName,
                        'initial' => $value->MiddleInitial,
                        'extname' => $value->ExtName,
                    ],
                    ['id'],
                    ['fname','mname','lname','initial','extname']);
    
                    $user = User::find($value->EmployeeID);

                    if($user != null){
                        if($user->id == 'TCA-502'){
                            $rolesToAssign = Role::whereIn('name', ['administrator'])->get();
                        }else{
                            $rolesToAssign = Role::whereIn('name', ['faculty'])->get();
                        }

                        $user->syncRoles($rolesToAssign);
                    }
            }
        }, $column = 'EmployeeID');
       

        return 0;
    }
}
