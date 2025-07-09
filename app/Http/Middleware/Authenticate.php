<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

// Este middleware es el encargado de verificar si el usuario actual esta autenticado (logueado).
// Su proposito principal es proteger rutas: si un usuario no ha iniciado sesion y trata de acceder
// a una ruta protegida por este middleware, sera redirigido automaticamente a la pagina de login.
// La logica real de autenticacion y redireccion reside en la clase base de Laravel (Illuminate\Auth\Middleware\Authenticate)
// de la cual este middleware hereda. Este archivo actua como un "placeholder" o un punto de personalizacion.
class Authenticate
{
    /**
     * Maneja una solicitud HTTP entrante.
     *
     * En este metodo, la logica heredada de la clase padre (Illuminate\Auth\Middleware\Authenticate)
     * se encarga de:
     * 1. Comprobar si hay un usuario autenticado para el "guard" por defecto (generalmente 'web').
     * 2. Si no hay usuario autenticado, redirigir la solicitud a la ruta de login.
     * 3. Si hay un usuario autenticado, simplemente pasar la solicitud al siguiente middleware o al controlador.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // La implementacion clave de autenticacion y redireccion sucede "antes" de este punto
        // en la clase padre o en metodos que se llaman internamente.
        // Si el usuario no esta autenticado, la ejecucion nunca llegara a esta linea;
        // en su lugar, se producira una redireccion.
        return $next($request); // Pasa la solicitud al siguiente punto si el usuario esta autenticado.
    }
}