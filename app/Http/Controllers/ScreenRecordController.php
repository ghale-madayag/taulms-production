<?php

namespace App\Http\Controllers;

use App\Models\ScreenRecord;
use App\Models\Subject;
use App\Models\Term;
use App\Models\User;
use Carbon\Carbon;
use FFMpeg\Coordinate\TimeCode;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Validation\Rule;
use ProtoneMedia\LaravelFFMpeg\Support\FFMpeg;
use Illuminate\Support\Facades\Storage;
use FFMpeg\Format\Video\X264;

class ScreenRecordController extends Controller
{
    public function index(){
        
        $user = auth()->user()->id;
        $user_role = auth()->user()->roles->first()->id;
        
        if($user_role!=1){

            $query = ScreenRecord::selectRaw('*, CONVERT(varchar(19), created_at, 100) as formatted_date')
                ->with(['subject' => function($quer){
                    $quer->select('id','code','title');
                }])
                ->with(['section' => function($quer){
                    $quer->select('id','name');
                }])
                ->where('user_id', $user)
                ->orderBy('created_at')
                ->paginate(5);

            $model = ScreenRecord::where('user_id', $user)
                    ->orderBy('created_at')
                    ->get();
            
            
            foreach ($query as $data){
                $data['totalHrs'] = $this->calculateHrs($data);
            }
           
            $modelsArray = $model->toArray();
            $totalHours = $this->calculateTotalHours($modelsArray);
            $totalWeek = $this->calculateTotalTimeThisWeek($modelsArray);
            $totalMonth = $this->calculateTotalTimeThisMonth($modelsArray);
           // dd($totalWeek);

        }


        return Inertia::render('Recorder/Recorder',[
            'recorded' => $query,
            'totalDay' => $totalHours,
            'totalWeek' => $totalWeek,
            'totalMonth' => $totalMonth,
        ]);
    }

    public function indexAll(User $user){


        $user_role = $user->roles->first();
        //dd($user->id);
        if($user_role->id!=1){

            $query = ScreenRecord::selectRaw('*, CONVERT(varchar(19), created_at, 100) as formatted_date')
                ->with(['subject' => function($quer){
                    $quer->select('id','code','title');
                }])
                ->with(['section' => function($quer){
                    $quer->select('id','name');
                }])
                ->where('user_id', $user->id)
                ->orderBy('created_at')
                ->paginate(5);

            $model = ScreenRecord::where('user_id', $user->id)
                    ->orderBy('created_at')
                    ->get();
            
            
            foreach ($query as $data){
                $data['totalHrs'] = $this->calculateHrs($data);
            }
           
            $modelsArray = $model->toArray();
            $totalHours = $this->calculateTotalHours($modelsArray);
            $totalWeek = $this->calculateTotalTimeThisWeek($modelsArray);
            $totalMonth = $this->calculateTotalTimeThisMonth($modelsArray);
           // dd($totalWeek);

        }


        return Inertia::render('Recorder/Recorder',[
            'recorded' => $query,
            'totalDay' => $totalHours,
            'totalWeek' => $totalWeek,
            'totalMonth' => $totalMonth,
        ]);
    }

    public function store(ScreenRecord $screenrecord){

        $timestamp = request()->end;
        $readable_time = date('Y-m-d H:i:s', $timestamp / 1000);

        if(request()->hasFile('video')){

            $video = request()->file('video');

            if($video->isValid()){
                $attributes = $this->validateData($screenrecord);

                $filename = request()->name;
                $file = request()->file('video')->storeAs('/screenrecord', $filename);
           
                $extension = pathinfo($file, PATHINFO_EXTENSION);
                $fileWithoutExtension = str_replace(".$extension", '', $filename);

                $attributes['end'] = $readable_time;
                $attributes['thumbnail'] = $fileWithoutExtension.'.png';
                $attributes['video'] = $fileWithoutExtension.'.mp4';

                $screenrecord->update($attributes);

                FFMpeg::fromDisk('public')
                ->open($file)
                ->export()
                ->toDisk('public')
                ->inFormat(new \FFMpeg\Format\Video\X264('libmp3lame','libx264'))
                ->save('/screenrecord/converted/'.$fileWithoutExtension.'.mp4');

                FFMpeg::fromDisk('public')
                ->open($file)
                ->export()
                ->frame(TimeCode::fromSeconds(3))
                ->toDisk('public')
                ->save('/screenrecord/thumbnail/'.$fileWithoutExtension.'.png');

                Storage::disk('public')->delete($file);

            }else{
                // Return an error message
                return back()->withErrors(['video' => 'The video file is not valid']);
            }


            
        }
    }

    public function startRecording(){
        //dd(request()->startTime);
        $timestamp = request()->startTime;
        $readable_time = date('Y-m-d H:i:s', $timestamp / 1000);
        
        $result = ScreenRecord::create(array_merge($this->validateData(), [
            'user_id' => request()->user()->id,
            'start_at' => $readable_time,
            'end_at' => $readable_time,
        ]));

        return redirect()->back()->with('message', $result->id);

    }

    public function update(ScreenRecord $screenrecord){
       

        $timestamp = request()->end;
        $readable_time = date('Y-m-d H:i:s', $timestamp / 1000);

        $attributes = $this->validateData($screenrecord);

        $attributes['end_at'] = $readable_time;

        $screenrecord->update($attributes);
    }

    protected function validateData(?ScreenRecord $screenrecord = null): array
    {
        
        $screenrecord ??= new ScreenRecord();

        return request()->validate([
            'user_id' => 'nullable',
            'subject_id' => 'nullable',
            'section_id' => 'nullable',
            'start_at' => 'nullable',
            'video' => 'nullable',
            'thumbnail' => 'nullable',
            'end' => 'nullable'
        ]);
    }

    public function getSubject(){
        $user = auth()->user()->id;

        $user_role = auth()->user()->roles->first()->id;
        $term = getSelectedTerm();

        $subject = Subject::select('subjects.id','subjects.title','subjects.slug','subjects.thumbnail','subjects.code','schedules.section_id','schedules.sched_date','schedules.room_type')
                ->join('schedules','subjects.id','schedules.subject_id')
                ->where('schedules.user_id',$user)
                ->where('schedules.term_id', $term->id)
                ->with('section')
                ->orderBy('subjects.title')
                ->get();
        
        $value = ['subject' => $subject];

        return response()->json($value);

    }

    function calculateHrs($model){

        $totalTime = [];
        
        $start = new Carbon($model['created_at']);
        $end = new Carbon($model['updated_at']);

        $totalSeconds = $start->diffInSeconds($end);
        $hours = (int) floor($totalSeconds / 3600);
        $minutes = (int) floor(($totalSeconds % 3600) / 60);
        $seconds = (int) ($totalSeconds % 60);
        $totalTime[] = sprintf('%d:%02d:%02d', $hours, $minutes, $seconds);

        return sprintf('%d:%02d:%02d', $hours, $minutes, $seconds);
    }
    function calculateTotalHours(array $models) {

        $totalTime = [];
    
        foreach ($models as $model) {
            
            $start = new Carbon($model['created_at']);
            $end = new Carbon($model['updated_at']);
            if ($start->isToday($end)) {
                $totalSeconds = $start->diffInSeconds($end);
                $hours = (int) floor($totalSeconds / 3600);
                $minutes = (int) floor(($totalSeconds % 3600) / 60);
                $seconds = (int) ($totalSeconds % 60);
                $totalTime[] = sprintf('%d:%02d:%02d', $hours, $minutes, $seconds);
            }
        }
       
        $hours = 0;
        $minutes = 0;
        $seconds = 0;

        foreach ($totalTime as $total) {
            list($h, $m, $s) = explode(':', $total);
            $hours += (int) $h;
            $minutes += (int) $m;
            $seconds += (int) $s;
        }
        
        $minutes += (int) floor($seconds / 60);
        $seconds = (int) ($seconds % 60);
        $hours += (int) floor($minutes / 60);
        $minutes = (int) ($minutes % 60);
        
        return sprintf('%d:%02d:%02d', $hours, $minutes, $seconds);
    }

    function calculateTotalTimeThisWeek(array $models) {
        $totalTime = [];
    
        foreach ($models as $model) {
            $start = new Carbon($model['created_at']);
            $end = new Carbon($model['updated_at']);
            if ($start->isSameWeek($end)) {
                $totalSeconds = $start->diffInSeconds($end);
                $hours = (int) floor($totalSeconds / 3600);
                $minutes = (int) floor(($totalSeconds % 3600) / 60);
                $seconds = (int) ($totalSeconds % 60);
                $totalTime[] = sprintf('%d:%02d:%02d', $hours, $minutes, $seconds);
            }
        }

        $hours = 0;
        $minutes = 0;
        $seconds = 0;

        foreach ($totalTime as $total) {
            list($h, $m, $s) = explode(':', $total);
            $hours += (int) $h;
            $minutes += (int) $m;
            $seconds += (int) $s;
        }
        
        $minutes += (int) floor($seconds / 60);
        $seconds = (int) ($seconds % 60);
        $hours += (int) floor($minutes / 60);
        $minutes = (int) ($minutes % 60);
        
        return sprintf('%d:%02d:%02d', $hours, $minutes, $seconds);
    }
    
    function calculateTotalTimeThisMonth(array $models) {
        $totalTime = [];
    
        foreach ($models as $model) {
            $start = new Carbon($model['created_at']);
            $end = new Carbon($model['updated_at']);
            if ($start->isSameMonth($end)) {
                $totalSeconds = $start->diffInSeconds($end);
                $hours = (int) floor($totalSeconds / 3600);
                $minutes = (int) floor(($totalSeconds % 3600) / 60);
                $seconds = (int) ($totalSeconds % 60);
                $totalTime[] = sprintf('%d:%02d:%02d', $hours, $minutes, $seconds);
            }
        }

        $hours = 0;
        $minutes = 0;
        $seconds = 0;

        foreach ($totalTime as $total) {
            list($h, $m, $s) = explode(':', $total);
            $hours += (int) $h;
            $minutes += (int) $m;
            $seconds += (int) $s;
        }
        
        $minutes += (int) floor($seconds / 60);
        $seconds = (int) ($seconds % 60);
        $hours += (int) floor($minutes / 60);
        $minutes = (int) ($minutes % 60);
        
        return sprintf('%d:%02d:%02d', $hours, $minutes, $seconds);
    }

    public function destroy(ScreenRecord $screenrecord){
        ScreenRecord::where('id', '=', $screenrecord->id)->delete();

        return redirect()->back();
    }
}
