<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;

class UpdateUserOnlineStatus
{
    public function handle($event)
    {
        $user = $event->user;

        if ($event instanceof Login) {
            $user->is_online = true;
        }

        if ($event instanceof Logout) {
            $user->is_online = false;
        }

        $user->save();
    }
}
