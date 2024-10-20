<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ConfirmOderToCart extends Mailable
{
    use Queueable, SerializesModels;

    public $ordersByShop;
    public $total_amount;
    public $carts;
    public $totalQuantity;
    public $shipFee;
    public $typeCheckout;
    public function __construct($ordersByShop, $total_amount, $carts, $totalQuantity, $shipFee, $typeCheckout)
    {
        $this->ordersByShop = $ordersByShop;
        $this->total_amount = $total_amount;
        $this->carts = $carts;
        $this->totalQuantity = $totalQuantity;
        $this->shipFee = $shipFee;
        $this->typeCheckout = $typeCheckout;
    }

    public function build()
    {
        // dd($this->ordersByShop);
        return $this->view('emails.confirm_oder_tocart')
                    ->subject('Xác nhận đơn hàng của bạn');
    }
}
