<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Log;

class Kernel extends ConsoleKernel
{
    /**
     * Constructor to log kernel initialization.
     */
    public function __construct()
    {
        parent::__construct();

        // Log when Kernel.php is loaded
        Log::info('Kernel.php loaded successfully!');
    }

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // Log when the scheduler runs
        Log::info('Scheduler is running!');

        // Example scheduled command
        $schedule->command('leaderboard:update')
                 ->everyMinute()
                 ->appendOutputTo(storage_path('logs/scheduler.log'));
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');
        require base_path('routes/console.php');

        // Log when commands are registered
        Log::info('Commands registered successfully in Kernel.php!');
    }
}
