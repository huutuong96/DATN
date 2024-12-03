<?php

namespace App\Jobs;

use App\Mail\mailEvent;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class sendMailBirthDay implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $userMail;
    protected $eventTitle;
    protected $code;

    public function __construct($userMail, $eventTitle, $code)
    {
        $this->userMail = $userMail;
        $this->eventTitle = $eventTitle;
        $this->code = $code;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        
            Mail::to($this->userMail)->send(new mailEvent($this->eventTitle, $this->code, "Bạn"));
       
    }
}
