<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

// Este middleware es el encargado de encriptar y desencriptar las cookies de tu aplicacion.
// Es una capa de seguridad crucial para proteger la informacion sensible que se guarda en las cookies
// (como el ID de sesion) y para asegurar que no sean manipuladas por el cliente.
// Se ejecuta para cada solicitud HTTP que pasa por el grupo de middleware 'web'.
class EncryptCookies
{
    /**
     * Maneja una solicitud entrante.
     * Este es el punto de entrada principal del middleware.
     * En este archivo base, el metodo `handle` simplemente pasa la solicitud al siguiente middleware.
     * Sin embargo, la logica real de encriptacion y desencriptacion de cookies
     * se implementa en la clase base de Laravel de la que este middleware hereda.
     *
     * La clase base (Illuminate\Cookie\Middleware\EncryptCookies) contiene los metodos
     * `encrypt()` y `decrypt()` y el arreglo `$except` para excluir ciertas cookies
     * de la encriptacion.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // En este punto del ciclo de vida de la solicitud, antes de que llegue al controlador,
        // la clase padre de EncryptCookies ya habria desencriptado las cookies entrantes.

        $response = $next($request); // Pasa la solicitud al siguiente middleware o al controlador.
                                    // Aqui es donde tu aplicacion genera su respuesta.

        // Una vez que la respuesta ha sido generada por la aplicacion,
        // la clase padre de EncryptCookies se encargara de encriptar
        // las cookies que se van a enviar al navegador antes de que la respuesta sea devuelta.

        return $response; // Retorna la respuesta, ahora con las cookies encriptadas (si aplica).
    }
}