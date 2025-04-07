<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('courier:balance-update')->everyMinute()
        ->runInBackground()->withoutOverlapping();

        $schedule->command('subscription:reminder')->daily()
            ->runInBackground()->withoutOverlapping();

        $schedule->command('courier:status')->daily()
            ->runInBackground()->withoutOverlapping();

        $schedule->command('orders:delete-unverified')->daily()
            ->runInBackground()->withoutOverlapping();

        $schedule->command('redx:status-update')->daily()
            ->runInBackground()->withoutOverlapping();

        // $schedule->command('order:delete')->daily()
        //     ->runInBackground()->withoutOverlapping();

        // $schedule->command('shoplessorder:delete')->monthly()
        //     ->runInBackground()->withoutOverlapping();
        
        $schedule->command('domain-ssl:request-and-verify')->everyMinute();

        $schedule->command('telescope:prune')->weekly()
        ->runInBackground()
        ->withoutOverlapping();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');
    }
}
