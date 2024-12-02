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

    public function __construct($user_id, $email)
    {
        $this->user_id = $user_id;
        $this->email = $email;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $notificationData = [
            'type' => 'main',
            'title' => 'Đơn hàng đã bị hủy',
            'description' => 'Đơn hàng đã bị hủy tự động vì cửa hàng không xác nhận đơn hàng trong thời gian quy định, số tiền sẽ được hoàn lại trong vòng 5 ngày làm việc',
            'user_id' => $this->user_id,
            'image' => 'Hình chưa thiết kế',
        ];
        $notification = Notification_to_mainModel::create($notificationData);
        // dd($notification->id);
        Notification::create([
            'type' => 'main',
            'user_id' => $this->user_id,
            'id_notification' => $notification->id,
        ]);

        Mail::to($this->email)->send(new sendMailWhenOrderCanceledForUser());

    }
}
