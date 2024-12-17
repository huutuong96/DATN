<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        $schedule->command('queue:work --stop-when-empty')
        ->everyMinute()
        ->withoutOverlapping();

        // // Add this method to your Kernel.php file to schedule the cancel_order_auto method to run every minute
        // $schedule->call(function () {
        //     app(\App\Http\Controllers\OrdersController::class)->cancel_order_auto();
        // })->daily();

        // $schedule->call(function () {
        //     app(\App\Http\Controllers\NotificationController::class)->send_mail_event();
        // })->daily();

        // $schedule->call(function () {
        //     app(\App\Http\Controllers\NotificationController::class)->check_time_event();
        // })->daily();

        // $schedule->call(function () {
        //     app(\App\Http\Controllers\VnshopController::class)->checkWebDie();
        // })->everyMinute();
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
