<?php

namespace App\Jobs;

use App\Models\Notification;
use App\Models\Notification_to_mainModel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendNotiEvent implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $users;
    protected $eventTitle;
    protected $description;
    protected $image;

    public function __construct($users, $eventTitle, $description, $image)
    {
        $this->users = $users;
        $this->eventTitle = $eventTitle;
        $this->description = $description;
        $this->image = $image;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        foreach ($this->users as $user) {
            $notificationData = [
                'type' => 'main',
                'title' => $this->eventTitle,
                'description' => $this->description,
                'user_id' => $user->id,
                'image' => $this->image,
              
            ];
            $notification = Notification_to_mainModel::create($notificationData);
            // dd($notification->id);
            Notification::create([
                'type' => 'main',
                'user_id' => $user->id,
                'id_notification' => $notification->id,
            ]);
        }   
        
    }
}
