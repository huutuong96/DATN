<?php

namespace App\Jobs;

use GuzzleHttp\Client;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class checkWebDie implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $urlAPI = 'http://vnshop.top/';
            $urlClient = 'https://test.vnshop.top/';
            $client = new Client();
            $responseAPI = $client->request('GET', $urlAPI);
            $responseClient = $client->request('GET', $urlClient);
            $statusCode = $responseAPI->getStatusCode();
            $statusCodeClient = $responseClient->getStatusCode();
            if ($statusCode == 200 && $statusCodeClient == 200) {
                $message = 'Cả API và Client đều hoạt động bình thường';
                log_host($message, $statusCode, $urlAPI, $statusCodeClient, $urlClient);
            } else {
                $message = 'WEBSITE KHÔNG HOẠT ĐỘNG';
                log_host($message, $statusCode, $urlAPI, $statusCodeClient, $urlClient);
            }
        } catch (\Throwable $th) {
            log_debug($th->getMessage());
        } 
    }
}
