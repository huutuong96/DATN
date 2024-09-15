<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ConfirmOderToCart extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct($allOrders, $allOrderDetails, $allProduct, $allQuantity, $totalQuantity, $grandTotalPrice)
    {
        $this->allOrders = $allOrders;
        $this->allOrderDetails = $allOrderDetails;
        $this->allProduct = $allProduct;
        $this->allQuantity = $allQuantity;
        $this->totalQuantity = $totalQuantity;
        $this->grandTotalPrice = $grandTotalPrice;
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
            view: 'emails.confirm_oder_tocart',
            with: [
                'allOrders' => $this->allOrders,
                'allOrderDetails' => $this->allOrderDetails,
                'allProduct' => $this->allProduct,
                'allQuantity' => $this->allQuantity,
                'totalQuantity' => $this->totalQuantity,
                'grandTotalPrice' => $this->grandTotalPrice,
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
