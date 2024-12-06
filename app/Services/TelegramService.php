<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class TelegramService
{
    protected $botToken;
    protected $chatId;

    public function __construct()
    {
        $this->botToken = env('TELEGRAM_BOT_TOKEN');
        $this->chatId = env('TELEGRAM_CHAT_ID');
    }

    public function sendMessage($message)
    {
        $url = "https://api.telegram.org/bot{$this->botToken}/sendMessage";

        $response = Http::post($url, [
            'chat_id' => $this->chatId,
            'text'    => $message,
            'parse_mode' => 'HTML', // Hỗ trợ định dạng HTML trong tin nhắn
        ]);

        if ($response->successful()) {
            return "Message sent successfully!";
        }

        return "Failed to send message: " . $response->body();
    }
}
