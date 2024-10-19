<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Mail\ConfirmOderToCart;
use App\Mail\ConfirmOder;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Tymon\JWTAuth\Facades\JWTAuth;

class SendMail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $ordersByShop;
    protected $total_amount;
    protected $carts;
    protected $totalQuantity;
    protected $shipFee;
    protected $email;

    public function __construct($ordersByShop, $total_amount, $carts, $totalQuantity, $shipFee, $email)
    {
        $this->ordersByShop = $ordersByShop;
        $this->total_amount = $total_amount;
        $this->carts = $carts;
        $this->totalQuantity = $totalQuantity;
        $this->shipFee = $shipFee;
        $this->email = $email;
        $this->handle();
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
            Mail::to($this->email)->send(new ConfirmOderToCart($this->ordersByShop, $this->total_amount, $this->carts, $this->totalQuantity, $this->shipFee));
    }
}
