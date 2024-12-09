<?php

use Illuminate\Support\Facades\Http;

if (!function_exists('check_var')) {
    function check_var($message, $status)
    {
        $botToken = env('TELEGRAM_BOT_TOKEN_ALPHA_CHECK');
        $chatId = env('TELEGRAM_CHAT_ID');
        $url = "https://api.telegram.org/bot{$botToken}/sendMessage";

        if ($status == 200) {
            $message = "🚀 Sự kiện của sàn đã bắt đầu rồi nè: " . $message;
        }
        if ($status == 201) {
            $message = "❤️" . $message;
        }
        if ($status == 404) {
            $message = "🎉" . $message;
        }
        if($status == 400) {
            $message = "🔥 Oh! Sự kiện không chạy nè: " . $message;
        }

        Http::post($url, [
            'chat_id'    => $chatId,
            'text'       => $message,
        ]);
    }
}
