<?php

namespace App\Http\Responses\Fortify;

use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Contracts\FailedLoginResponse as FailedLoginResponseContract;
use Laravel\Fortify\Fortify;
use Symfony\Component\HttpFoundation\Response;
use App\Models\User; // Asegurate de importar el modelo User

class FailedLoginResponse implements FailedLoginResponseContract
{
    /**
     * Renderiza la respuesta cuando el login falla.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function toResponse($request): Response
    {
        $usernameField = Fortify::username();
        $user = User::where($usernameField, $request->$usernameField)->first();

        // Si el usuario fue encontrado (email/username correcto)
        // Y aqui asumimos que NO es inactivo, porque tu AttemptToAuthenticate
        // ya habria redirigido a los inactivos antes de llegar a este punto.
        if ($user) {
            throw ValidationException::withMessages([
                'password' => __('auth.password'), // Mensaje "La contrasena es incorrecta."
            ])->redirectTo(route('login'))->withInput($request->only($usernameField));
        }

        // Si el usuario NO fue encontrado (email/username incorrecto o no existe)
        // Usar el mensaje generico 'auth.failed'.
        throw ValidationException::withMessages([
            $usernameField => [trans('auth.failed')], // Mensaje "El correo o la contrasena no son correctos."
        ])->redirectTo(route('login'))->withInput($request->only($usernameField));
    }
}