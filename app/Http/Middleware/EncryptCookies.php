<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

// Este middleware es el encargado de encriptar y desencriptar las cookies de tu aplicación.
// Es una capa de seguridad crucial para proteger la información sensible que se guarda en las cookies
// (como el ID de sesión) y para asegurar que no sean manipuladas por el cliente.
// Se ejecuta para cada solicitud HTTP que pasa por el grupo de middleware 'web'.
class EncryptCookies
{
    /**
     * Maneja una solicitud entrante.
     * Este es el punto de entrada principal del middleware.
     * En este archivo base, el método `handle` simplemente pasa la solicitud al siguiente middleware.
     * Sin embargo, la lógica real de encriptación y desencriptación de cookies
     * se implementa en la clase base de Laravel de la que este middleware hereda.
     *
     * La clase base (Illuminate\Cookie\Middleware\EncryptCookies) contiene los métodos
     * `encrypt()` y `decrypt()` y el arreglo `$except` para excluir ciertas cookies
     * de la encriptación.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // En este punto del ciclo de vida de la solicitud, antes de que llegue al controlador,
        // la clase padre de EncryptCookies ya habría desencriptado las cookies entrantes.

        $response = $next($request); // Pasa la solicitud al siguiente middleware o al controlador.
                                    // Aquí es donde tu aplicación genera su respuesta.

        // Una vez que la respuesta ha sido generada por la aplicación,
        // la clase padre de EncryptCookies se encargará de encriptar
        // las cookies que se van a enviar al navegador antes de que la respuesta sea devuelta.

        return $response; // Retorna la respuesta, ahora con las cookies encriptadas (si aplica).
    }
}