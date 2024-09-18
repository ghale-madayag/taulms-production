<?php

namespace App\Http\Controllers;

use App\Models\SchedulingConfiguration;
use App\Models\Term;
use Carbon\Carbon;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class DatabaseResetController extends Controller
{
    public function index(){
        $query = SchedulingConfiguration::first();

        if ($query !== null) {
            $interval = $query->interval;
            $msg = '';
        
            switch ($interval) {
                case 'daily':
                    $msg = 'The database scheduled daily sync at ' . $query->schedule_time;
                    break;
                case 'weekly':
                    $days = $this->days_of_week($query->days_of_week);
                    $msg = 'The database scheduled weekly sync at ' . $days . ', ' . $query->schedule_time;
                    break; 
                case 'monthly':
                    $msg = "The database scheduled monthly sync every " . $query->day_of_month . ' of the month at ' . $query->schedule_time;
                    break; 
                default:
                   
                    break;
            }
        } else {
            // Handle the case where $query is null.
            $msg = 'No scheduling configuration found.';
        }

        $term = Term::orderBy('id','asc')->get();

        return Inertia::render('Maintenance/Index',[
            'msg' => $msg,
            'term' => $term
        ]);
    }

    public function term(){
        
        try {

            $validatedData = request()->validate([
                'isTerm' => 'required|exists:terms,id' 
            ]);
    
            Term::where('id', '<>', $validatedData['isTerm'])->update(['isTerm' => 0]);

            Term::where('id', $validatedData['isTerm'])->update(['isTerm' => 1]);
    
            return redirect()->back()->with('success', 'The School Year and Terms are now updated');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred during the operation.');
        }

    }

    public function days_of_week($d){
        switch ($d) {
            case '1':
                return 'Monday';
                break;
            case '2':
                return 'Tuesday';
                break;
            case '3':
                return 'Wednesday';
                break;
            case '4':
                return 'Thursday';
                break;
            case '5':
                return 'Friday';
                break;
            case '6':
                return 'Saturday';
                break;
            case '7':
                return 'Sunday';
                break;
            default:
                # code...
                break;
        }
    }

    public function reset(){

        try {
            Artisan::call('reset-database:reset');
            return redirect()->back()->with('success', 'The database reset operation finished without errors.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred during the database reset operation.');
        }
    }

    public function maintenance(Request $request){
        //dd($request);

        $data = $request->validate([
            'name' => 'required|string',
            'command' => 'required|string',
            'interval' => 'required|in:daily,weekly,monthly,yearly',
            'schedule_time' => 'required|regex:/^\d{2}:\d{2}$/',
            'days_of_week' => 'nullable|string', 
            'day_of_month' => 'nullable|integer', 
            'month' => 'nullable|string', 
        ]);

        SchedulingConfiguration::truncate();
        $configuration = SchedulingConfiguration::create($data);

        return redirect()->back()->with('success', 'Scheduling configuration saved successfully');
        
    }
}
