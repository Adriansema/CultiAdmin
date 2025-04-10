<?php

//actualizacion 09/04/2025

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

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


        // Verifica si el usuario tiene alguno de los roles necesarios
        if (!auth()->user()?->hasAnyRole(['administrador', 'operador'])) {
            abort(403, 'Acceso no autorizado');
        }

        return $next($request);
    }
}
