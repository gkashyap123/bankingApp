<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('birthday:greeting')->dailyAt('13:50')->withoutOverlapping()
        ->appendOutputTo(storage_path('logs/greet.log'));
        
        $schedule->command('greet:birthdays')->dailyAt('13:50')
                                             ->withoutOverlapping()
                                             ->appendOutputTo(storage_path('logs/greet.log'));
        $schedule->command('app:send-festival-greetings')->dailyAt('13:50')->withoutOverlapping()
        ->appendOutputTo(storage_path('logs/festivalgreet.log'));

        $schedule->command('events:remind')->dailyAt('13:50');

        $schedule->call(function () {
            \Log::info('CRON WORKING ' . now());
        })->everyMinute();

    }
}
