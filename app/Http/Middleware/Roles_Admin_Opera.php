<?php

//actualizacion 16/06/2025

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use Symfony\Component\HttpFoundation\Response;

class Roles_Admin_Opera
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Verifica si el usuario tiene alguno de los roles necesarios para acceder a esta ruta.
        // Ahora incluye 'SuperAdmin' para asegurar que el administrador principal siempre tenga acceso.
        // También se ha actualizado 'operador' a 'Operario' para consistencia con el seeder.
        if (!Auth::User()?->hasAnyRole(['SuperAdmin', 'Administrador', 'Operario'])) {
            // Si el usuario no tiene ninguno de los roles requeridos, deniega el acceso.
            abort(403, 'Acceso no autorizado');
        }

        return $next($request);
    }
}
