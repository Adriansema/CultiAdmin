<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\RateLimiter; // Necesario para RateLimiter
use Illuminate\Auth\AuthenticationException; // Opcional: para un manejo más robusto de excepciones

class ResetLoginAttempts
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  \Illuminate\Auth\Events\Login  $event
     * @return void
     */
    public function handle(Login $event)
    {
        // Aseguramos que el usuario está presente y tiene una propiedad 'email' o 'username'
        // Fortify usa 'email' por defecto para el username.
        $email = $event->user->email ?? null;

        if ($email) {
            // Genera la misma clave de throttle que usamos en LogFailedLogin
            $key = strtolower($email) . '|' . request()->ip();

            // Resetea el contador de intentos fallidos para esta clave
            RateLimiter::clear($key);

            // Opcional: Loguear para depuración
            // \Log::info("Login exitoso para: {$email}. Contador de intentos reseteado.");
        }
    }
}