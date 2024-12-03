<?php

namespace App\Listeners;

use App\Events\UserLoggedIn;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Models\UsersModel;

class UpdateUserCount
{
    public function handle(UserLoggedIn $event)
    {
        // Tăng số lượng người dùng đăng nhập
        // dd($event);
        // UsersModel::where('id', $event->user->id)->update([
        //     'is_login' => 1
        // ]);
    }
}

