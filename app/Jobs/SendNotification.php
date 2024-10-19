<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Http\Controllers\NotificationController;
use App\Models\Notification;
use App\Models\Notification_to_mainModel;
use Illuminate\Http\Request;

class SendNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $title;
    protected $description;
    protected $user_id;

    public function __construct($title, $description, $user_id)
    {
        $this->title = $title;
        $this->description = $description;
        $this->user_id = $user_id;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $notificationData = [
            'type' => 'main',
            'title' => $this->title,
            'description' => $this->description,
            'user_id' => $this->user_id,
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
