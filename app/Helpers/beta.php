<?php

use Illuminate\Support\Facades\Http;

if (!function_exists('log_host')) {
    function log_host($message, $statusCode, $urlAPI, $statusCodeClient, $urlClient)
    {
        $botToken = env('TELEGRAM_BOT_TOKEN_BETA_DEVOPS');
        $chatId = env('TELEGRAM_CHAT_ID');
        $url = "https://api.telegram.org/bot{$botToken}/sendMessage";

        if ($statusCode == 200) {
            $message = "🚀 Host: " . $message . "\n🔗 URL API: " . $urlAPI . "\n🔗 URL Client: " . $urlClient . "\n🔖 Status Code: " . $statusCode . "\n🔖 Status Code Client: " . $statusCodeClient;
        } else {
            $message = "🔥 Host: " . $message . "\n🔗 URL API: " . $urlAPI . "\n🔗 URL Client: " . $urlClient . "\n🔖 Status Code: " . $statusCode . "\n🔖 Status Code Client: " . $statusCodeClient;
        }

        Http::post($url, [
            'chat_id'    => $chatId,
            'text'       => $message,
        ]);
    }
}
