<?php

namespace App\Console\Commands;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Console\Command;
use Spatie\Permission\Models\Role;

class StudentCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:student';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync Student to user';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        $oldDb = DB::connection('sqlsrvlms')
            ->table('ES_Students')
            ->whereExists(fn ($query) =>
               User::select(DB::raw(1))
                     ->from('users')
                     ->whereColumn('users.id', 'ES_Students.StudentNo'))
                     ->orderBy('StudentNo');

        $oldDb->chunkById(10, function ($records) {
            foreach ($records as $key => $value) {
                $query = new User();
                $query->upsert(
                    [
                        'id' => $value->StudentNo,
                        'email' => $value->Email,
                        'password' => Hash::make('password'),
                        'fname' => $value->FirstName,
                        'mname' => $value->Middlename,
                        'lname' => $value->LastName,
                        'initial' => $value->MiddleInitial,
                        'extname' => $value->ExtName,
                        'year_lvl' => $value->YearLevelID,
                        'date_admitted' => $value->DateAdmitted,
                    ],
                    ['id'],
                    ['fname','mname','lname','initial','extname','year_lvl','date_admitted']
                );
    
                $user = User::find($value->StudentNo);

                if($user != null){
                    $rolesToAssign = Role::whereIn('name', ['student'])->get();
                    $user->syncRoles($rolesToAssign);
                }
            }
        }, $column = 'StudentNo');

        $this->call('sync:faculty');
        $this->call('sync:term');
        $this->call('sync:registrations');
        $this->call('sync:subject');
        $this->call('sync:section');
        $this->call('sync:schedule');
        $this->call('sync:registrationdetails');

        return 0;
    }
}




