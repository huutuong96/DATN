<?php

namespace App\Jobs;

use App\Mail\mailEvent;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendMailEvent implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

   
    protected $users;
    protected $eventTitle;
    protected $code;

    public function __construct($users, $eventTitle, $code)
    {
        $this->users = $users;
        $this->eventTitle = $eventTitle;
        $this->code = $code;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        foreach ($this->users as $user) {
            Mail::to($user)->send(new mailEvent($this->eventTitle, $this->code, $user));
        }
    }
}
