<?php

namespace App\Console\Commands;

use App\Models\Subject;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SubjectCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:subject';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync Subject';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $oldDb = DB::connection('sqlsrvlms')
        ->table('ES_Subjects')
        ->whereExists(fn ($query) =>
           Subject::select(DB::raw(1))
                 ->from('subjects')
                 ->whereColumn('subjects.id', 'ES_Subjects.SubjectID'))
       ->orderBy('SubjectID');

       $oldDb->chunkById(100, function ($records) {
            foreach ($records as $key => $value){
                $query = new Subject();
                $query->upsert([
                        'id' => $value->SubjectID,
                        'code' => $value->SubjectCode,
                        'title' => $value->SubjectTitle,
                        'slug' => Str::slug($value->SubjectTitle),
                        'description' => $value->SubjectDesc,
                        'isLab' => $value->LabUnits,
                    ],
                    ['id'],
                    ['code','title','slug','description']
                );
            }
        }, $column = 'SubjectID');
       
       return 0;
    }
}
