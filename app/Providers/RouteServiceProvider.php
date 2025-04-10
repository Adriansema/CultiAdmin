<?php

//actualizacion 09/04/2025

namespace App\Providers;

use Illuminate\Support\Facades\Route;
use Spatie\Permission\Models\Role;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{

    /**
     * Esta es la ruta a la que los usuarios son redirigidos después del login.
     */
    public const HOME = '/dashboard';

    /**
     * Define tus rutas aquí.
     */
    public function boot(): void
    {
        Route::middleware('role:administrador');

        $this->routes(function () {
            Route::middleware('web')
                ->group(base_path('routes/web.php'));
        });
    }
}
