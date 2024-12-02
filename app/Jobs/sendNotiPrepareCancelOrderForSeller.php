<?php

namespace App\Jobs;

use App\Models\Notification;
use App\Models\Notification_to_mainModel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class sendNotiPrepareCancelOrderForSeller implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $user_id;

    public function __construct($user_id)
    {
        $this->user_id = $user_id;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $notificationData = [
            'type' => 'main',
            'title' => 'Bạn có đơn hàng sắp bị hủy',
            'description' => 'Đơn hàng của bạn sắp bị hủy vì cửa hàng không xác nhận đơn hàng trong thời gian quy định',
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
    }
}
