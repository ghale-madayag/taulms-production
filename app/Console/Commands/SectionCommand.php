<?php

namespace App\Console\Commands;

use App\Models\Section;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SectionCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:section';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync Section';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $oldDb = DB::connection('sqlsrvlms')
        ->table('ES_ClassSections')
        ->whereExists(fn ($query) =>
           Section::select(DB::raw(1))
                 ->from('sections')
                 ->whereColumn('sections.id', 'ES_ClassSections.SectionID'))
       ->orderBy('SectionID');

       $oldDb->chunkById(100, function ($records) {
            foreach ($records as $key => $value){
                    $query = new Section();
                    $query->upsert([
                        'id' => $value->SectionID,
                        'name' => $value->SectionName,
                        'term_id' => $value->TermID
                    ],
                    ['id'],
                    ['name','term_id']
                );
            }
        }, $column = 'SectionID');
       

       return 0;
    }
}
