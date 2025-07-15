<?php

namespace App\Providers;

use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Auth\Events\Failed;
use App\Listeners\LogFailedLogin;
use Illuminate\Support\Facades\Event;
use App\Listeners\ResetLoginAttempts;
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
        Failed::class => [
            LogFailedLogin::class,
        ],
        Login::class => [
            ResetLoginAttempts::class,
        ],
    ];

    public function boot(): void
    {
        //
    }
}
