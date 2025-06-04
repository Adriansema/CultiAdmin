<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Driver de Hash por Defecto
    |--------------------------------------------------------------------------
    |
    | Esta opción controla el driver de hash por defecto que se utilizará para
    | hashear contraseñas en tu aplicación. Por defecto, se usa el algoritmo bcrypt;
    | sin embargo, eres libre de modificar esta opción si lo deseas.
    |
    | Soportados: "bcrypt", "argon", "argon2id"
    |
    */

    'driver' => env('HASH_DRIVER', 'bcrypt'), // <-- ¡ESTA ES LA PARTE MÁS IMPORTANTE! Define el algoritmo de hashing.

    /*
    |--------------------------------------------------------------------------
    | Opciones de Bcrypt
    |--------------------------------------------------------------------------
    |
    | Aquí puedes especificar las opciones de configuración que deben usarse cuando
    | las contraseñas se hashean con el algoritmo Bcrypt. Esto te permitirá
    | controlar la cantidad de tiempo que toma hashear la contraseña dada.
    |
    */

    'bcrypt' => [
        'rounds' => env('BCRYPT_ROUNDS', 12), // Número de "rondas" para Bcrypt, que afectan la lentitud/seguridad.
        'verify' => env('HASH_VERIFY', true), // Si se deben verificar las contraseñas al hashear (raramente se cambia).
        'limit' => env('BCRYPT_LIMIT', null), // Límite de caracteres para la contraseña (nulo significa sin límite).
    ],

    /*
    |--------------------------------------------------------------------------
    | Opciones de Argon
    |--------------------------------------------------------------------------
    |
    | Aquí puedes especificar las opciones de configuración que deben usarse cuando
    | las contraseñas se hashean con el algoritmo Argon. Esto te permitirá
    | controlar la cantidad de tiempo que toma hashear la contraseña dada.
    |
    */

    'argon' => [
        'memory' => env('ARGON_MEMORY', 65536), // Cantidad de memoria a usar (en Kibibytes).
        'threads' => env('ARGON_THREADS', 1), // Número de hilos de CPU a usar.
        'time' => env('ARGON_TIME', 4), // Número de iteraciones o tiempo de procesamiento.
        'verify' => env('HASH_VERIFY', true), // Si se deben verificar las contraseñas al hashear.
    ],

    /*
    |--------------------------------------------------------------------------
    | Rehashear al Iniciar Sesión
    |--------------------------------------------------------------------------
    |
    | Configurar esta opción en `true` le indicará a Laravel que vuelva a hashear
    | automáticamente la contraseña del usuario durante el inicio de sesión si el factor de trabajo
    | configurado para el algoritmo ha cambiado, permitiendo actualizaciones graduales de los hashes.
    |
    */

    'rehash_on_login' => true, // Si Laravel debe actualizar el hash de la contraseña de un usuario durante el login si el algoritmo/rondas cambian.

];