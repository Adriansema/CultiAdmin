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
                'password' => Hash::make('agrosena123'), // cámbialo por seguridad
            ]
        );

        $operador = User::firstOrCreate(
            ['email' => 'opera@cultiopera.com'],
            [
                'name' => 'Operador',
                'password' => Hash::make('agrosena321'), // cámbialo por seguridad
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
