<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ConfirmOderToCart extends Mailable
{
    use Queueable, SerializesModels;

    
    public $orders;
    public $total_amount;
    public $carts;
    public $totalQuantity;
    public $shipFee;
    public $orderDetails;
    public $products;
    public $variants;
    public $email;
    public $paymentMethod;
    public $user;
    public $disscount;
    // public $typeCheckout;

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
        // $this->typeCheckout = $typeCheckout;
    }

    public function build()
    {
        // dd($this->ordersByShop);
        return $this->view('emails.confirm_oder_tocart')
                    ->subject('Xác nhận đơn hàng của bạn');
    }
}
