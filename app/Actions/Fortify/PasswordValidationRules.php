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
    protected function passwordRules(bool $forLogin = false) // Añadimos el parámetro $forLogin
    {
        $rules = [
            'required',
            'string',
            'min:8', // Longitud mínima
            'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*(_|[^\\w])).+$/', // Tu regex
        ];

        // Si NO es para el login, o si no se especificó $forLogin (comportamiento por defecto para registro/cambio)
        if (!$forLogin) {
            $rules[] = 'confirmed'; // Solo añadimos 'confirmed' si no es para el login
        }

        return $rules;
    }
}
