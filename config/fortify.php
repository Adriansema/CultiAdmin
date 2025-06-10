<?php

use Laravel\Fortify\Features;

return [

    /*
|--------------------------------------------------------------------------
| Fortify Guard
|--------------------------------------------------------------------------
|
| Aquí puede especificar qué protección de autenticación utilizará Fortify al autenticar usuarios. Este valor debe corresponder a 
| una de sus protecciones que ya esté presente en su archivo de configuración "auth".
|
*/

    'guard' => 'web',

    /*
|--------------------------------------------------------------------------
| Agente de contraseñas de Fortify
|--------------------------------------------------------------------------
|
| Aquí puede especificar qué agente de contraseñas puede usar Fortify cuando un usuario restablece su contraseña. 
| Este valor configurado debe coincidir con uno de los agentes de contraseñas configurados en su archivo de configuración de autenticación.
|
*/

    'passwords' => 'users',

   /*
|--------------------------------------------------------------------------
| Nombre de usuario / Correo electrónico
|--------------------------------------------------------------------------
|
| Este valor define qué atributo del modelo debe considerarse como el campo "nombre de usuario" de su aplicación. Normalmente, 
| este podría ser la dirección de correo electrónico de los usuarios, pero puede cambiar este valor aquí.
|
| De fábrica, Fortify espera que las solicitudes de olvido de contraseña y restablecimiento de contraseña tengan un campo llamado 
| "correo electrónico". Si la aplicación usa otro nombre para el campo, puede definirlo a continuación según sea necesario.
|
*/

    'username' => 'email',

    'email' => 'email',

    /*
|--------------------------------------------------------------------------
| Nombres de usuario en minúsculas
|--------------------------------------------------------------------------
|
| Este valor define si los nombres de usuario deben escribirse en minúsculas antes de guardarlos en la base de datos, 
| ya que algunos campos de cadena del sistema de la base de datos distinguen entre mayúsculas y minúsculas. Puede desactivar 
| esta opción para su aplicación si es necesario.
|
*/

    'lowercase_usernames' => true,

    /*
|--------------------------------------------------------------------------
| Ruta de inicio
|--------------------------------------------------------------------------
|
| Aquí puede configurar la ruta a la que serán redirigidos los usuarios durante la autenticación o el restablecimiento 
| de contraseña cuando las operaciones se realicen correctamente y el usuario esté autenticado. Puede cambiar este valor.
|
*/

    'home' => '/dashboard',

    /*
|--------------------------------------------------------------------------
| Prefijo/Subdominio de Rutas Fortify
|--------------------------------------------------------------------------
|
| Aquí puede especificar el prefijo que Fortify asignará a todas las rutas que registre en la aplicación. 
| Si es necesario, puede cambiar el subdominio bajo el cual estarán disponibles todas las rutas de Fortify.
|
*/

    'prefix' => '',

    'domain' => null,

    /*
|--------------------------------------------------------------------------
| Middleware de Rutas de Fortify
|------------------------------------------------------------------------------------------
|
| Aquí puede especificar qué middleware asignará Fortify a las rutas que registre con la aplicación. Si es necesario, 
| puede cambiar este middleware, pero normalmente se prefiere el valor predeterminado.
|
*/

    'middleware' => ['web'],

    /*
|--------------------------------------------------------------------------
| Limitación de velocidad
|--------------------------------------------------------------------------
|
| De forma predeterminada, Fortify limitará los inicios de sesión a cinco solicitudes por minuto para cada combinación 
| de correo electrónico y dirección IP. Sin embargo, si desea especificar un limitador de velocidad personalizado, puede especificarlo aquí.
|
*/

    'limiters' => [
        'login' => 'login',
        'two-factor' => 'two-factor',
    ],

   /*
|--------------------------------------------------------------------------
| Registrar rutas de vista
|------------------------------------------------------------------------------------------
|
| Aquí puede especificar si las rutas que devuelven vistas deben estar deshabilitadas, ya que
| podría no necesitarlas al crear su propia aplicación. Esto puede ser
| especialmente cierto si está escribiendo una aplicación personalizada de una sola página.
|
*/

    'views' => true,

    /*
    |--------------------------------------------------------------------------
    | Características
    |--------------------------------------------------------------------------
    |
    | Algunas de las características de Fortify son opcionales. Puedes deshabilitarlas
    | eliminándolas de esta matriz. Puedes eliminar solo algunas de estas características o incluso todas si lo necesitas.
    |
    */

    'features' => [
        Features::registration(),
        Features::resetPasswords(),
        Features::emailVerification(),
        Features::updateProfileInformation(),
        Features::updatePasswords(),
        Features::twoFactorAuthentication([
            'confirm' => true,
            'confirmPassword' => true,
            // 'window' => 0,
        ]),
    ],

    'responses' => [
        // ... otras respuestas de Fortify
        'failedlogin' => \App\Http\Responses\Fortify\FailedLoginResponse::class,
        // ...
    ],

];
