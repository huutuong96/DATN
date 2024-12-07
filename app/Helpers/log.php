<?php

use Illuminate\Support\Facades\Http;

if (!function_exists('log_debug')) {
    function log_debug($message)
    {
        $botToken = env('TELEGRAM_BOT_TOKEN_AISHA_BOT');
        $chatId = env('TELEGRAM_CHAT_ID');
        $url = "https://api.telegram.org/bot{$botToken}/sendMessage";

        Http::post($url, [
            'chat_id'    => $chatId,
            'text'       => "Có lỗi xảy ra nè: " . $message,
            'parse_mode' => 'HTML', // Hỗ trợ định dạng HTML trong tin nhắn
        ]);
    }
}
