<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Driver de Hash por Defecto
    |--------------------------------------------------------------------------
    |
    | Esta opcion controla el driver de hash por defecto que se utilizara para
    | hashear contrasenas en tu aplicacion. Por defecto, se usa el algoritmo bcrypt;
    | sin embargo, eres libre de modificar esta opcion si lo deseas.
    |
    | Soportados: "bcrypt", "argon", "argon2id"
    |
    */

    'driver' => env('HASH_DRIVER', 'bcrypt'), // <-- "!"ESTA ES LA PARTE MaS IMPORTANTE! Define el algoritmo de hashing.

    /*
    |--------------------------------------------------------------------------
    | Opciones de Bcrypt
    |--------------------------------------------------------------------------
    |
    | Aqui puedes especificar las opciones de configuracion que deben usarse cuando
    | las contrasenas se hashean con el algoritmo Bcrypt. Esto te permitira
    | controlar la cantidad de tiempo que toma hashear la contrasena dada.
    |
    */

    'bcrypt' => [
        'rounds' => env('BCRYPT_ROUNDS', 12), // Numero de "rondas" para Bcrypt, que afectan la lentitud/seguridad.
        'verify' => env('HASH_VERIFY', true), // Si se deben verificar las contrasenas al hashear (raramente se cambia).
        'limit' => env('BCRYPT_LIMIT', null), // Limite de caracteres para la contrasena (nulo significa sin limite).
    ],

    /*
    |--------------------------------------------------------------------------
    | Opciones de Argon
    |--------------------------------------------------------------------------
    |
    | Aqui puedes especificar las opciones de configuracion que deben usarse cuando
    | las contrasenas se hashean con el algoritmo Argon. Esto te permitira
    | controlar la cantidad de tiempo que toma hashear la contrasena dada.
    |
    */

    'argon' => [
        'memory' => env('ARGON_MEMORY', 65536), // Cantidad de memoria a usar (en Kibibytes).
        'threads' => env('ARGON_THREADS', 1), // Numero de hilos de CPU a usar.
        'time' => env('ARGON_TIME', 4), // Numero de iteraciones o tiempo de procesamiento.
        'verify' => env('HASH_VERIFY', true), // Si se deben verificar las contrasenas al hashear.
    ],

    /*
    |--------------------------------------------------------------------------
    | Rehashear al Iniciar Sesion
    |--------------------------------------------------------------------------
    |
    | Configurar esta opcion en `true` le indicara a Laravel que vuelva a hashear
    | automaticamente la contrasena del usuario durante el inicio de sesion si el factor de trabajo
    | configurado para el algoritmo ha cambiado, permitiendo actualizaciones graduales de los hashes.
    |
    */

    'rehash_on_login' => true, // Si Laravel debe actualizar el hash de la contrasena de un usuario durante el login si el algoritmo/rondas cambian.

];