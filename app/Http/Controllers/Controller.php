<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

abstract class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;
    /*     Es la clase base que usan todos los controladores de tu aplicación. Meterle lógica
como index()con visitas o registros es un error grave , porque se propaga a todos los controladores
que heredan de ahí.
En PHP, cuando una clase hija sobreescribe un método de la clase padre ( index()en este caso), la firma debe coincidir exactamente .
Si en el padre es index()sin parámetros, y en el hijo pones index(Request $request), te lanza un error de incompatibilidad.
 */
}
