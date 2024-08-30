<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ConfirmOder extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct($order, $orderDetail, $product, $quantity, $totalPrice)
    {
        $this->order = $order;
        $this->orderDetail = $orderDetail;
        $this->product = $product;
        $this->quantity = $quantity;
        $this->totalPrice = $totalPrice;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Đặt Hàng Thành Công - VN Shop',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.confirm_oder',
            with: [
                'order' => $this->order,
                'orderDetail' => $this->orderDetail,
                'product' => $this->product,
                'quantity' => $this->quantity,
                'totalPrice' => $this->totalPrice,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
