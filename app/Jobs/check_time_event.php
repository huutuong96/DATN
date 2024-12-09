<?php

namespace App\Jobs;

use App\Models\Banner;
use App\Models\Event;
use App\Models\voucherToMain;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class check_time_event implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $today = Carbon::now()->format('Y-m-d');
            $events = Event::where('to', $today)->get();
            foreach ($events as $event) {
                $event->status = 5;
                $event->save();
                check_var($event->event_title. " Đã kết thúc", 201);
            }
            $event_is_active_banner = Banner::where('status', 2)->update(['status' => 16]);
            $rollback_banner = Banner::where('status', 15)->update(['status' => 2]);
            $voucher_is_active = voucherToMain::where('is_event', 1)->delete();            
        } catch (\Throwable $th) {
            check_var($th->getMessage(), 400);
        }

    }
}
