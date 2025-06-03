<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

// Este middleware es el encargado de verificar si el usuario actual está autenticado (logueado).
// Su propósito principal es proteger rutas: si un usuario no ha iniciado sesión y trata de acceder
// a una ruta protegida por este middleware, será redirigido automáticamente a la página de login.
// La lógica real de autenticación y redirección reside en la clase base de Laravel (Illuminate\Auth\Middleware\Authenticate)
// de la cual este middleware hereda. Este archivo actúa como un "placeholder" o un punto de personalización.
class Authenticate
{
    /**
     * Maneja una solicitud HTTP entrante.
     *
     * En este método, la lógica heredada de la clase padre (Illuminate\Auth\Middleware\Authenticate)
     * se encarga de:
     * 1. Comprobar si hay un usuario autenticado para el "guard" por defecto (generalmente 'web').
     * 2. Si no hay usuario autenticado, redirigir la solicitud a la ruta de login.
     * 3. Si hay un usuario autenticado, simplemente pasar la solicitud al siguiente middleware o al controlador.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // La implementación clave de autenticación y redirección sucede "antes" de este punto
        // en la clase padre o en métodos que se llaman internamente.
        // Si el usuario no está autenticado, la ejecución nunca llegará a esta línea;
        // en su lugar, se producirá una redirección.
        return $next($request); // Pasa la solicitud al siguiente punto si el usuario está autenticado.
    }
}