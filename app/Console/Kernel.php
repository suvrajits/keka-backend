<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // Schedule the leaderboard update command to run every minute (for testing purposes)
        $schedule->command('leaderboard:update')
                 ->everyMinute()
                 ->appendOutputTo(storage_path('logs/scheduler.log'));

        // Example of scheduling a simple inline task (for debugging/testing purposes)
        $schedule->call(function () {
            \Log::info('Scheduler is working!');
        })->everyMinute()
          ->appendOutputTo(storage_path('logs/test_scheduler.log'));
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        // Load custom commands from the Commands directory
        $this->load(__DIR__ . '/Commands');

        // Include console routes for inline Artisan commands
        require base_path('routes/console.php');
    }
}

