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
    
    protected $orders;
    protected $total_amount;
    protected $carts;
    protected $orderDetails;
    protected $shipFee;
    protected $products;
    protected $variants;
    protected $email;
    protected $paymentMethod;
    protected $user;
    protected $disscount;

    public function __construct($orders, $total_amount, $carts, $orderDetails, $shipFee, $products, $variants, $email, $paymentMethod, $user, $disscount)
    {
        $this->orders = $orders;
        $this->total_amount = $total_amount;
        $this->carts = $carts;
        $this->orderDetails = $orderDetails;
        $this->shipFee = $shipFee;
        $this->products = $products;
        $this->variants = $variants;
        $this->email = $email;
        $this->paymentMethod = $paymentMethod;
        $this->user = $user;
        $this->disscount = $disscount ?? 0;
        $this->handle();
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
            Mail::to($this->email)->send(new ConfirmOderToCart($this->orders, $this->total_amount, $this->carts, $this->orderDetails, $this->shipFee, $this->products, $this->variants, $this->email, $this->paymentMethod, $this->user, $this->disscount));
    }
}
