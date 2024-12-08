<?php

namespace App\Jobs;

use App\Mail\sendMailWhenOrderCanceledForUser;
use App\Models\Notification;
use App\Models\Notification_to_mainModel;
use App\Models\OrdersModel;
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
    protected $order_id;

    /**
     * Create a new job instance.
     *
     * @param int $user_id
     * @param string $email
     * @param int $order_id
     */
    public function __construct($user_id, $email, $order_id)
    {
        $this->user_id = $user_id;
        $this->email = $email;
        $this->order_id = $order_id; 
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $order = OrdersModel::find($this->order_id);
        // dd($order);
        if ($order) {
            $notificationData = [
                'type' => 'main',
                'title' => 'Đơn hàng đã bị hủy',
                'description' => 'Đơn hàng đã bị hủy tự động vì cửa hàng không xác nhận đơn hàng trong thời gian quy định, số tiền sẽ được hoàn lại trong vòng 5 ngày làm việc',
                'user_id' => $this->user_id,
                'image' => 'Hình chưa thiết kế',
            ];
            $notification = Notification_to_mainModel::create($notificationData);
            Notification::create([
                'type' => 'main',
                'user_id' => $this->user_id,
                'id_notification' => $notification->id,
            ]);
          return ($order);
            Mail::to($this->email)->send(new sendMailWhenOrderCanceledForUser($order));
        }
    }
}
