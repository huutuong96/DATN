<?php

namespace App\Listeners;

use App\Events\UserLoggedOut;
use App\Models\UsersModel;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class UpdateUserCountOnLogout
{
    public function handle(UserLoggedOut $event)
    {
        // Tăng số lượng người dùng đăng nhập
        UsersModel::where('id', $event->user->id)->update([
            'is_login' => 0
        ]);
    }
}
