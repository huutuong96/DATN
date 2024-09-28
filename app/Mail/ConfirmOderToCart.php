<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ConfirmOderToCart extends Mailable
{
    use Queueable, SerializesModels;

    public $ordersByShop;
    public $grandTotalPrice;
    public $carts;
    public $totalQuantity;
    public $shipFee;
    public function __construct($ordersByShop, $grandTotalPrice, $carts, $totalQuantity, $shipFee)
    {
        $this->ordersByShop = $ordersByShop;
        $this->grandTotalPrice = $grandTotalPrice;
        $this->carts = $carts;
        $this->totalQuantity = $totalQuantity;
        $this->shipFee = $shipFee;
    }

    public function build()
    {
        // dd($this->ordersByShop);
        return $this->view('emails.confirm_oder_tocart')
                    ->subject('Xác nhận đơn hàng của bạn');
    }
}
