<?php

namespace App\Actions\Fortify;

use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;
use App\Providers\RouteServiceProvider;

class LoginResponse implements LoginResponseContract
{
    public function toResponse($request)
    {
        $user = $request->user();

        // Redirección para SuperAdmin
        if ($user->hasRole('SuperAdmin')) {
            return redirect()->route('dashboard'); // Define una ruta específica para SuperAdmin
        }

        // Redirección para Administrador
        if ($user->hasRole('Administrador')) {
            return redirect()->route('dashboard'); // Define una ruta específica para Administrador
        }

        // Redirección para Operario
        if ($user->hasRole('Operario')) {
            return redirect()->route('dashboard'); // Define una ruta específica para Operario
        }

        // Redirección para Funcionario
        if ($user->hasRole('Funcionario')) {
            return redirect()->route('dashboard'); // Define una ruta específica para Funcionario
        }

        // Si por alguna razón un usuario no tiene un rol esperado aqui esta su ruta por defecto
        return redirect()->intended(RouteServiceProvider::HOME);
    }
}
