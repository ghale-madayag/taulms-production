<?php

namespace App\Console;

use Carbon\Carbon;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * 
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected $commands = [
        Commands\ResetDatabase::class,
    ];

    protected function schedule(Schedule $schedule)
    {   
        
        $schedulingConfigurations = $this->getSchedulingConfigurations();

        if ($schedulingConfigurations) {
            foreach ($schedulingConfigurations as $configuration) {
                $this->scheduleTask($schedule, $configuration);
            }
        }
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */

    private function getSchedulingConfigurations()
    {
        // Query the scheduling configuration data from the database
        return DB::table('scheduling_configurations')->get();
    }
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }

    private function scheduleTask($schedule, $configuration)
    {
        // Determine the scheduling interval (daily, weekly, etc.)
        $interval = $configuration->interval;

        // Define the task based on the interval and configuration parameters
        switch ($interval) {
            case 'daily':
                $schedule->command('sync:database')
                        ->dailyAt($configuration->schedule_time);
                break;

            case 'weekly':
                $schedule->command('sync:database')
                        ->weeklyOn($configuration->days_of_week, $configuration->schedule_time);
                break;

            case 'monthly':
                $schedule->command('sync:database')
                        ->monthlyOn($configuration->day_of_month, $configuration->schedule_time);
                break;

            case 'yearly':
                $schedule->command('sync:database')
                        ->yearly()
                        ->on($configuration->month, $configuration->day_of_month)
                        ->at($configuration->schedule_time);
                break;

            // Add more cases for other scheduling intervals as needed

            default:
                // Handle unsupported intervals or invalid configurations
                break;
        }
    }
}
