<?php

namespace App\Console\Commands;

use App\Models\Schedule;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ScheduleCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:schedule';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync Schedule';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $oldDb = DB::connection('sqlsrvlms')
            ->table('ES_ClassSchedules')
            ->whereExists(fn ($query) =>
            Schedule::select(DB::raw(1))
                    ->from('schedules')
                    ->whereColumn('schedules.id', 'ES_ClassSchedules.ScheduleID'))
        ->orderBy('ScheduleID');

        $oldDb->chunkById(100, function ($records) {
            foreach ($records as $key => $value){
                    $query = new Schedule();
                    $query->upsert([
                        'id' => $value->ScheduleID,
                        'term_id' => $value->TermID,
                        'section_id' => $value->SectionID,
                        'subject_id' => $value->SubjectID,
                        'user_id' => $value->FacultyID,
                        'room_type' => $value->Days1_EventID,
                        'sched_date' => $value->Sched_1,
                    ],
                    ['id'],
                    ['term_id','section_id','subject_id','user_id','sched_date']
                );
            }
        }, $column = 'ScheduleID');
        

        return 0;
    }
}
