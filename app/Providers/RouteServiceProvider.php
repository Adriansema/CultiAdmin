<?php

namespace App\Providers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Route;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{

    /**
     * Esta es la ruta a la que los usuarios son redirigidos despues del login que no tienen roles.
     */
    public const HOME = './no-role-assigned';

    /**
     * Define tus rutas aqui.
     */
    public function boot(): void
    {
        /* Route::middleware('role:administrador'); */

        $this->routes(function () {
            Route::middleware('web')
                ->group(base_path('routes/web.php'));
        });
    }

    /**
     * Configure the rate limiters for the application.
     *
     * @return void
     */
    protected function configureRateLimiting()
    {
        // Esta es la definición del limitador 'login'
        RateLimiter::for('login', function (Request $request) {
            return Limit::perMinute(3) // 3 intentos
                ->by($request->email ?: $request->ip())
                ->response(function () use ($request) {
                    // Puedes personalizar la respuesta cuando se excede el límite
                    return redirect()->back()->withInput($request->only('email'))
                        ->withErrors(['email' => __('auth.throttle', [
                            'seconds' => RateLimiter::availableIn($request->email ?: $request->ip()),
                        ])]);
                });
        });

        // Este es para 'two-factor' si lo necesitas también
        // También puedes usar 'use ($request)' si personalizas la respuesta aquí.
        RateLimiter::for('two-factor', function (Request $request) {
            return Limit::perMinute(5)->by($request->ip());
        });
    }
}
