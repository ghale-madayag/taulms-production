<?php

namespace App\Console\Commands;

use App\Models\Registration;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class RegistrationsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:registrations';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync Registration';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $oldDb = DB::connection('sqlsrvlms')
        ->table('ES_Registrations')
        ->whereExists(fn ($query) =>
           Registration::select(DB::raw(1))
                 ->from('registrations')
                 ->whereColumn('registrations.id', 'ES_Registrations.RegID'))
       ->orderBy('RegID');
    
       $oldDb->chunkById(100, function ($records) {
            foreach ($records as $key => $value){
                    $query = new Registration();
                    $query->upsert([
                        'id' => $value->RegID,
                        'user_id' => $value->StudentNo,
                        'term_id' => $value->TermID,
                        'validation_date' => $value->ValidationDate
                    ],
                    ['id'],
                    ['user_id','term_id','validation_date']
                );
            }
        }, $column = 'RegID');
       

       return 0;
    }
}
