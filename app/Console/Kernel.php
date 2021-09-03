<?php

namespace App\Console;

use App\Jobs\Transactions\ProcessPending;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\App;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        Commands\ForeignExchangeRateUpdate::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param Schedule $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('every_four_hours:foreign_exchange_rates')->everyFourHours()->appendOutputTo(storage_path('logs/scheduler_foreign_exchange_rates.log'));
        $this->setupJobs($schedule);
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

    protected function setupJobs(Schedule $schedule)
    {
        $connection = config('queue.default');
        $queue = config("queue.connections.$connection.queue");

        $processAllPending = $schedule->job(new ProcessPending(), $queue);
        if (App::environment('local')) {
            $processAllPending->everyMinute();
        } else {
            $processAllPending->timezone('Asia/Manila')->dailyAt('1:00');
        }
    }
}
