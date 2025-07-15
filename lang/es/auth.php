<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Lineas de Lenguaje para Autenticacion
    |--------------------------------------------------------------------------
    |
    | Las siguientes lineas de lenguaje se utilizan durante la autenticacion para
    | varios mensajes que necesitamos mostrar al usuario. Eres libre de modificar
    | estas lineas de lenguaje de acuerdo a los requisitos de tu aplicacion.
    |
    */

    'failed' => 'Estas credenciales no coinciden con nuestros registros.', // Lo restauramos al valor por defecto para detectarlo.
    'throttle' => 'Demasiados intentos de inicio de sesión. Por favor, inténtelo de nuevo en :seconds segundos.',
    'password' => 'La contraseña proporcionada es incorrecta.', // Este no se usa por defecto para fallos de auth generales.
    'email_not_found' => 'El correo electrónico no coincide.', // para cuando el email no existe
    'password_mismatch' => 'La contraseña no coincide.', // para cuando el email existe pero la password es errónea

];
