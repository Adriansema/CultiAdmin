<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        // Crear roles si no existen
        $adminRole = Role::firstOrCreate(['name' => 'administrador']);
        $operatorRole = Role::firstOrCreate(['name' => 'operador']);

        // Crear usuario administrador si no existe
        $admin = User::firstOrCreate(
            ['email' => 'admin@cultiadmin.com'],
            [
                'name' => 'Administrador Principal',
                'password' => Hash::make('password123'), // Puedes cambiar la clave luego
            ]
        );

        // Asignar rol de administrador
        if (!$admin->hasRole('administrador')) {
            $admin->assignRole($adminRole);
        }
    }
}
