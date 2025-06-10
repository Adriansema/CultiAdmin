<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Líneas de Lenguaje para Autenticación
    |--------------------------------------------------------------------------
    |
    | Las siguientes líneas de lenguaje se utilizan durante la autenticación para
    | varios mensajes que necesitamos mostrar al usuario. Eres libre de modificar
    | estas líneas de lenguaje de acuerdo a los requisitos de tu aplicación.
    |
    */

    'failed' => 'el correo o la contraseña no coincide.',
    'password' => 'La contraseña proporcionada es incorrecta.',
    'throttle' => 'Demasiados intentos de inicio de sesión. Por favor, inténtalo de nuevo en :seconds segundos.',

];

/**
 * GET|HEAD  forgot-password .. password.request › Laravel\Fortify › PasswordResetLinkController@create
 * POST      forgot-password .. password.email › Laravel\Fortify › PasswordResetLinkController@store
 */