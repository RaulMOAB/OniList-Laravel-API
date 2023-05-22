<?php

namespace App\Console;

use App\Models\ForgotPassword;
use App\Models\Verify;
use Carbon\Carbon;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        //$schedule->command('inspire')->hourly();
        //$schedule->command('php init.php')->dailyAt('02:00');
        // $schedule->call(function () {
        //     // Delete created tokens since 1 hora
        //     $expiredTokens = ForgotPassword::where('created_at', '<=', Carbon::now()->subHour())->get();
        //     $expiredTokens->each->delete();
        // })->hourly();
        // $schedule->call(function () {
        //     // Delete created tokens since 1 hora
        //     $expiredTokens = Verify::where('created_at', '<=', Carbon::now()->subHour())->get();
        //     $expiredTokens->each->delete();
        // })->hourly();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');
        

        require base_path('routes/console.php');
    }
}
