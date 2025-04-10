<?php

//actualizacion 09/04/2025

namespace App\Actions\Fortify;

use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;

class LoginResponse implements LoginResponseContract
{
    public function toResponse($request)
    {
        $user = $request->user();

        if ($user->hasRole('administrador')) {  //Ruta para el administrador
            return redirect()->route('productos.index');
        }

        if ($user->hasRole('operador')) {
            return redirect()->route('operador.productos.operador.pendientes'); //Ruta para el operador
        }

        return redirect()->intended('/dashboard'); //Ruta por defecto
        // Puedes personalizar la redirección según tus necesidades
    }
}
