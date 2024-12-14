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
        ]);
        $orders = OrdersModel::where('order_status', 0)->where('created_at', '<', Carbon::now()->subDays(5))->get();

        foreach ($orders as $order) {
            $user = UsersModel::find($order->user_id);
            if ($user) {
                sendNotiWhenCanceledOrder::dispatch($user->id, $user->email, $order->id); 
                DB::table('log_jobs')->insert([
                    'log' => 'sendNotiWhenCanceledOrder for Order ID: '
                ]);
            }
        
            $shop = Shop::find($order->shop_id); 
            if ($shop) {
                sendNotiWhenCanceledOrderForSeller::dispatch($shop->owner_id, $order->id); 
                $shop->cancel_order_count = $shop->cancel_order_count + 1;
                $shop->save();
                DB::table('log_jobs')->insert([
                    'log' => 'sendNotiWhenCanceledOrderForSeller for Order  '
                ]);
            }
            autoCancelOrder::dispatch($order);
            DB::table('log_jobs')->insert([
                'log' => 'autoCancelOrder for Order ID:'
            ]);
           
        }
        check_var('Đ', 200);
        
 
    }
}