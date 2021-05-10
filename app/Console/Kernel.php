<?php

namespace App\Console;

use App\Console\Commands\EntrataGetCustomers;
use App\Console\Commands\EntrataGetMistLeases;
use App\Console\Commands\PostInstallNginx;
use App\Console\Commands\PostInstallRadius;
use App\Console\Commands\RadiusCleanUp;
use App\Console\Commands\SendWelcomeEmail;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        PostInstallRadius::class,
        PostInstallNginx::class,
        RadiusCleanUp::class,
        EntrataGetCustomers::class,
        EntrataGetMistLeases::class,
        SendWelcomeEmail::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param \Illuminate\Console\Scheduling\Schedule $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('radius:cleanup')->daily();
        $schedule->command('entrata:getCustomers')->daily()->emailWrittenOutputTo(config('app.support.email'))
            ->then(function () {
                $this->call('entrata:getMitsLeases')->emailWrittenOutputTo(config('app.support.email'))->then(function () {
                    $this->call('email:welcome')->emailWrittenOutputTo(config('app.support.email'));
                });
            });
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
    }
}
