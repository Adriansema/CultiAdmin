<?php

namespace App\Actions\Fortify;
trait PasswordValidationRules
{
    /**
     * Get the validation rules for passwords.
     *
     * @return array
     */
    protected function passwordRules(): array
    {
        return [
            'required',
            'string',
            'min:8', // Establece la longitud mínima directamente
            // Aquí tu regex para la complejidad, que incluirá mayúsculas, minúsculas, números y caracteres especiales
            'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*(_|[^\\w])).+$/',
            'confirmed', // Sigue siendo útil para formularios de registro/cambio
        ];
    }
}
