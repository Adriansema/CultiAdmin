<?php

//actualizacion 09/04/2025

namespace App\Providers;

use Carbon\Carbon;
use Illuminate\Support\ServiceProvider;
use App\Actions\Fortify\LoginResponse;
use Illuminate\Pagination\Paginator;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Inyectamos la respuesta personalizada al login
        $this->app->singleton(LoginResponseContract::class, LoginResponse::class);
         // Registrar el IDE Helper solo en entorno local
         if ($this->app->environment('local')) {
            $this->app->register(\Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider::class);
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useTailwind();
        Carbon::setLocale('es');
        setlocale(LC_TIME, 'es_ES.UTF-8');

    }
}
