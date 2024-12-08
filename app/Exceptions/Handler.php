<?php

namespace App\Exceptions;

use App\Services\TelegramService;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{ protected $telegramService;

    public function __construct(TelegramService $telegramService)
    {
        $this->telegramService = $telegramService;
    }
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    public function report(Throwable $exception)
    {
        $message = $exception->getMessage();
        $this->telegramService->sendMessage($message);
        // parent::report($exception);
        return $exception;
    }


    protected function formatExceptionMessage(\Throwable $exception)
    {
        return "⚠️ <b>Exception occurred:</b>\n" .
               "Message: {$exception->getMessage()}\n" .
               "File: {$exception->getFile()}\n" .
               "Line: {$exception->getLine()}\n" .
               "Trace: {$exception->getTraceAsString()}";
    }
    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function () {
            //
        });
    }

    
}
