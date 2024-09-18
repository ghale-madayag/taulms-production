<?php

namespace App\Console\Commands;

use App\Models\Term;
use DateTime;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class TermCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:term';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync the Term table';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $oldDb = DB::connection('sqlsrvlms')
            ->table('ES_AYTerm')
            ->whereExists(fn ($query) =>
                Term::select(DB::raw(1))
                    ->from('terms')
                    ->whereColumn('terms.id', 'ES_AYTerm.TermID'))
                    ->orderBy('TermID');;
        
        $oldDb->chunkById(10, function ($records) {
            foreach ($records as $value){
                $query = new Term();
                $newStart = date("Y-m-d h:m:s", strtotime($value->StartofAY));
                $newEnd = date("Y-m-d h:m:s", strtotime($value->EndofAY));
                $query->upsert(
                    [
                        'id' => $value->TermID,
                        'academic_year' => $value->AcademicYear,
                        'term' => $value->SchoolTerm,
                        'start_ay' => $newStart,
                        'end_ay' => $newEnd,
                    ],
                    ['id'],
                    ['academic_year','term','start_ay','end_ay']
                );
               
            }

            // Get the last inserted ID
            $lastInsertedId = $query->orderBy('id', 'desc')->value('id');

            // Update the 'isTerm' column to 1 for the last inserted ID
            if ($lastInsertedId) {
                Term::where('id', $lastInsertedId)->update(['isTerm' => 1]);
            }

        }, $column = 'TermID');
        

        return 0;
    }
}
