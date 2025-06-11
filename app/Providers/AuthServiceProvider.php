<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use App\Models\Producto;
use App\Policies\ProductoPolicy;
use App\Models\Boletin;
use App\Policies\BoletinPolicy;
use App\Models\User;
use App\Policies\UserPolicy;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // Mapeos de Modelo a Policy
        Producto::class => ProductoPolicy::class,
        Boletin::class => BoletinPolicy::class,
        User::class => UserPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function register(): void
    {
        // Este método suele estar vacío en AuthServiceProvider a menos que
        // haya necesidades de registro muy específicas de autenticación/autorización.
        // Las políticas se registran automáticamente por la propiedad $policies.
    }

    /**
     * Bootstrap any authentication / authorization services.
     */
    public function boot(): void
    {
        // Este método suele estar vacío en AuthServiceProvider a menos que
        // haya necesidades de arranque muy específicas de autenticación/autorización,
        // como registrar Gates o ciertas directivas Blade personalizadas.
        // Las políticas se registran automáticamente por la propiedad $policies.
    }
}
