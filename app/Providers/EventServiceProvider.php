<?php

namespace App\Providers;

use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use App\Listeners\UpdateUserOnlineStatus;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        Login::class => [
            UpdateUserOnlineStatus::class,
        ],
        Logout::class => [
            UpdateUserOnlineStatus::class,
        ],
    ];

    public function boot(): void
    {
        //
    }
}
