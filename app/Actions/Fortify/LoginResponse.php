<?php

//actualizacion 09/04/2025

namespace App\Actions\Fortify;

use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;
use App\Providers\RouteServiceProvider;

class LoginResponse implements LoginResponseContract
{
    public function toResponse($request)
    {
        $user = $request->user();

        if ($user->hasRole('administrador')) {  //Ruta para el administrador
            return redirect()->route('dashboard');
        }

        if ($user->hasRole('operador')) {
            return redirect()->route('dashboard'); //Ruta para el operador
        }

        return redirect()->intended(RouteServiceProvider::HOME); //Ruta por defecto
        // Puedes personalizar la redirección según tus necesidades
    }
}
