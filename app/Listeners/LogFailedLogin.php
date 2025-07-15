<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Failed;
use Illuminate\Support\Facades\RateLimiter; // Para el contador de intentos
use App\Models\User; // Ajusta según tu modelo de usuario

class LogFailedLogin
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
     * @param  \Illuminate\Auth\Events\Failed  $event
     * @return void
     */
    public function handle(Failed $event)
    {
        $email = $event->credentials['email'] ?? null;

        // Incrementa el contador de intentos fallidos para el email
        RateLimiter::hit($this->throttleKey($email));

        // Si el usuario intentó iniciar sesión
        if ($email) {
            $user = User::where('email', $email)->first();

            if ($user) {
                // Si el email existe pero la contraseña es incorrecta
                session()->flash('auth.error', __('auth.password_mismatch'));
            } else {
                // Si el email no existe
                session()->flash('auth.error', __('auth.email_not_found'));
            }
        } else {
            // Para otros tipos de fallos (ej. si el campo email no estaba presente)
            session()->flash('auth.error', __('auth.failed'));
        }
    }

    /**
     * Get the rate limiting throttle key for the given email.
     *
     * @param  string  $email
     * @return string
     */
    protected function throttleKey(string $email): string
    {
        return strtolower($email) . '|' . request()->ip();
    }
}