<?php

namespace App\Actions\Fortify;
trait PasswordValidationRules
{
    /**
     * Get the validation rules for passwords.
     *
     * @param bool $forLogin Indica si las reglas son para el formulario de login.
     * @return array
     */
    protected function passwordRules(bool $forLogin = false) // Anadimos el parametro $forLogin
    {
        $rules = [
            'required',
            'string',
            'min:8', // Longitud minima
            'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*(_|[^\\w])).+$/', // Tu regex
        ];

        // Si NO es para el login, o si no se especifico $forLogin (comportamiento por defecto para registro/cambio)
        if (!$forLogin) {
            $rules[] = 'confirmed'; // Solo anadimos 'confirmed' si no es para el login
        }

        return $rules;
    }
}
