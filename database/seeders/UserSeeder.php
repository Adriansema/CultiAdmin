<?php

//actualizacion 007/04/2025

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Crear al usuario administrador
        $admin = User::firstOrCreate(
            ['email' => 'admin@cultiadmin.com'],
            [
                'name' => 'Administrador',
                // Usa una contraseña que CLARAMENTE cumpla la regex:
                'password' => Hash::make('CultiAdmin_2025!'),
            ]
        );

        $operador = User::firstOrCreate(
            ['email' => 'opera@cultiopera.com'],
            [
                'name' => 'Operador',
                // Otra contraseña que CLARAMENTE cumpla la regex:
                'password' => Hash::make('CultiOpera_2025!'),
            ]
        );
        
        // Crear roles si no existen
        $adminRole = Role::firstOrCreate(['name' => 'administrador']);
        $operadorRole = Role::firstOrCreate(['name' => 'operador']);

        // Asignar roles
        $admin->assignRole($adminRole);
        $operador->assignRole($operadorRole);
    }
}
