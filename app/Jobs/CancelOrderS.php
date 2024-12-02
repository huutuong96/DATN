<?php

namespace App\Jobs;

use App\Models\OrdersModel;
use App\Models\Shop;
use App\Models\UsersModel;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class CancelOrderS implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    public function __construct()
    {

    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        DB::table('log_jobs')->insert([
            'log' => 'CancelOrderS ',
            'date' => Carbon::now(),
        ]);
        $ordersPrepareCancel = OrdersModel::where('order_status', 0)->where('created_at', '<', Carbon::now()->subDays(4))->get();
        $shopsHasOrderPrepareCancel = Shop::whereIn('id', $ordersPrepareCancel->pluck('shop_id'))->get();
        $orders = OrdersModel::where('order_status', 0)->where('created_at', '<', Carbon::now()->subDays(5))->get();
        $shops = Shop::whereIn('id', $orders->pluck('shop_id'))->get();
        $users = UsersModel::whereIn('id', $orders->pluck('user_id'))->get();
        foreach ($shopsHasOrderPrepareCancel as $shop) {
            sendNotiPrepareCancelOrderForSeller::dispatch($shop->owner_id);
            DB::table('log_jobs')->insert([
                'log' => 'sendNotiPrepareCancelOrderForSeller ',
            ]);
        }
        foreach ($users as $user) {
            dd($user->email);
            sendNotiWhenCanceledOrder::dispatch($user->id, $user->email);
            DB::table('log_jobs')->insert([
                'log' => 'sendNotiWhenCanceledOrder ',
                'date' => Carbon::now(),
            ]);
        }
        foreach ($shops as $shop) {
            sendNotiWhenCanceledOrderForSeller::dispatch($shop->owner_id);
            DB::table('log_jobs')->insert([
                'log' => 'sendNotiWhenCanceledOrderForSeller ',
                'date' => Carbon::now(),
            ]);
        }
 
        foreach ($orders as $order) {
            autoCancelOrder::dispatch($order);
            DB::table('log_jobs')->insert([
                'log' => 'autoCancelOrder ',
                'date' => Carbon::now(),
            ]);
        }
    }
}
