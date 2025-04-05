<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class OperadorSeeder extends Seeder
{
    public function run(): void
    {
        // Verifica si el rol ya existe (asumimos que ya fue creado por el otro seeder)
        $operadorRole = Role::firstOrCreate(['name' => 'operador']);

        // Crear un usuario operador
        $operador = User::firstOrCreate(
            ['email' => 'operador@cultiadmin.com'],
            [
                'name' => 'Usuario Operador',
                'password' => Hash::make('operador123'), // Puedes cambiar la clave luego
            ]
        );

        // Asignar el rol de operador si aún no lo tiene
        if (!$operador->hasRole('operador')) {
            $operador->assignRole($operadorRole);
        }
    }
}
