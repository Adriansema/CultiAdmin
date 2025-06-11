<?php

// database/seeders/UserSeeder.php - Ajustes para consistencia con Spatie (06/06/2025)

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role; // Asegúrate de importar Role de Spatie
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // -------------------------------------------------------------------
        // Asegurarse de que los roles existan Y obtenerlos con su guard_name
        // -------------------------------------------------------------------
        // Es CRUCIAL que el 'guard_name' aquí coincida con el que usas en RoleSeeder.php
        $adminRole = Role::firstOrCreate(['name' => 'administrador', 'guard_name' => 'web']);
        $operadorRole = Role::firstOrCreate(['name' => 'operador', 'guard_name' => 'web']);

        // -------------------------------------------------------------------
        // 1. Crear o actualizar al usuario Administrador
        // -------------------------------------------------------------------
        $admin = User::firstOrCreate(
            ['email' => 'admin@cultiadmin.com'], // Condición de búsqueda
            [ // Datos a crear si no se encuentra
                'name' => 'Administrador',
                'password' => Hash::make('CultiAdmin_2025!'), // Contraseña segura
                // Puedes añadir otros campos como 'email_verified_at' si los necesitas
                'email_verified_at' => now(), // Opcional: marca el correo como verificado
            ]
        );
        // Asignar el rol al usuario
        // `syncRoles` es útil para asegurar que solo tenga los roles especificados
        $admin->syncRoles([$adminRole]);
        echo "Usuario Administrador creado/actualizado y rol asignado.\n";

        // -------------------------------------------------------------------
        // 2. Crear o actualizar al usuario Operador
        // -------------------------------------------------------------------
        $operador = User::firstOrCreate(
            ['email' => 'opera@cultiopera.com'],
            [
                'name' => 'Operador',
                'password' => Hash::make('CultiOpera_2025!'),
                'email_verified_at' => now(), // Opcional
            ]
        );
        // Asignar el rol al usuario
        $operador->syncRoles([$operadorRole]);
        echo "Usuario Operador creado/actualizado y rol asignado.\n";
    }
}