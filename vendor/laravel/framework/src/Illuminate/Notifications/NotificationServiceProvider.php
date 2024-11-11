<?php

namespace Illuminate\Notifications;
use Illuminate\Support\Facades\View;
use App\Models\Notification;
use App\Models\Notification_to_mainModel;
use Illuminate\Contracts\Notifications\Dispatcher as DispatcherContract;
use Illuminate\Contracts\Notifications\Factory as FactoryContract;
use Illuminate\Support\ServiceProvider;

class NotificationServiceProvider extends ServiceProvider
{
    /**
     * Boot the application services.
     *
     * @return void
     */
    public function boot()
    {
        
        View::composer('*', function ($view) {
            $user = auth()->user();
            if ($user) {
            $notification = Notification::where('user_id', $user->id)->get();
            $notificationIds = $notification->pluck('id_notification'); // Lấy danh sách các ID từ collection
            $notifyMain = Notification_to_mainModel::whereIn('id', $notificationIds)->orderBy('created_at', 'desc')->take(5)->get();
            $view->with('notifyMain', $notifyMain);
            }
        });
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(ChannelManager::class, fn ($app) => new ChannelManager($app));

        $this->app->alias(
            ChannelManager::class, DispatcherContract::class
        );

        $this->app->alias(
            ChannelManager::class, FactoryContract::class
        );
    }
}
