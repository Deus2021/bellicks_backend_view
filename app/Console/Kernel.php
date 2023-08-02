<?php

namespace App\Console;

use App\Jobs\MidnightJob;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.

     */

    protected $scheduleTimezone = 'Africa/Dar_es_Salaam';


    protected function schedule(Schedule $schedule): void
    {
     
        $schedule->job(new MidnightJob)->dailyAt('11:30');
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}