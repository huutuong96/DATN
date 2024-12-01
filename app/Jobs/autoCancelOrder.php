<?php
namespace App\Jobs;

use App\Models\OrdersModel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class autoCancelOrder implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $order;
 
    public function __construct($order)
    {
        $this->order = $order;

    }

    public function handle(): void
    {
            $order = OrdersModel::find($this->order->id);
            if ($order) {
                $order->update(['order_status' => 10]);
            }
    }
}