<?php

namespace App\Jobs;

use App\Mail\sendMailWhenOrderCanceledForUser;
use App\Models\Notification;
use App\Models\Notification_to_mainModel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class sendNotiWhenCanceledOrder implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $user_id;
    protected $email;
    protected $order; // Chỉ một đơn hàng
    
    public function __construct($user_id, $email, $order)
    {
        $this->user_id = $user_id;
        $this->email = $email;
        $this->order = $order; 
    }
    
    public function handle(): void
    {
        
        $notificationData = [
            'type' => 'main',
            'title' => 'Đơn hàng đã bị hủy',
            'description' => 'Đơn hàng #' . $this->order->id . ' đã bị hủy tự động...',
            'user_id' => $this->user_id,
            'image' => 'Hình chưa thiết kế',
        ];
    
        $notification = Notification_to_mainModel::create($notificationData);
    
        Notification::create([
            'type' => 'main',
            'user_id' => $this->user_id,
            'id_notification' => $notification->id,
        ]);
        $this->order->load('orderDetails.product');
       dd(  $this->email);
        Mail::to('hoangtlvps31622@gmail.com')->send(new sendMailWhenOrderCanceledForUser($this->order));
    }
    
}
