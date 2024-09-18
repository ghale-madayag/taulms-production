<?php

namespace App\Console\Commands;

use App\Models\RegistrationDetails;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class RegistrationDetailsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:registrationdetails';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync Registration Details';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        $oldDb = DB::connection('sqlsrvlms')
        ->table('ES_RegistrationDetails')
        ->whereExists(fn ($query) =>
        RegistrationDetails::select(DB::raw(1))
                ->from('registration_details')
                ->whereColumn('registration_details.id', 'ES_RegistrationDetails.ReferenceID'))
                ->orderBy('ReferenceID');

        $oldDb->chunkById(100, function ($records) {
            foreach ($records as $key => $value){
                $query = new RegistrationDetails();
                $query->upsert([
                    'id' => $value->ReferenceID,
                    'registration_id' => $value->RegID,
                    'schedule_id' => $value->ScheduleID,
                    ],
                    ['id'],
                    ['registration_id','schedule_id']
                );
            }
        }, $column = 'ReferenceID');
        

        return 0;
    }
}
