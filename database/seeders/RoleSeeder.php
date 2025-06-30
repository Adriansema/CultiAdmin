<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Importante: Reiniciar la caché de permisos de Spatie.
        // Esto asegura que cualquier cambio en roles o permisos se refleje inmediatamente.
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // --- 1. Definir TODOS los Permisos del sistema ---
        // Se definen todos los permisos posibles que existiran en tu aplicación.
        // Esto es crucial porque todos los permisos deben existir antes de asignarlos a roles.
        $allSystemPermissions = [

            // Permisos para el Módulo de Gestión de Usuarios
            'crear usuario',
            'editar usuario',

            // Permisos para Cultivos (Validar/Rechazar)
            'validar producto',

            // Permisos para Noticias (Validar/Rechazar)
            'validar noticia',

            // Permisos para Boletines (Validar/Rechazar)
            'validar boletin',

            // Permisos para el Módulo de Cultivos
            'crear producto',
            'editar producto',
            'eliminar producto',

            // Permisos para el Módulo de Noticias 
            'crear noticia',
            'editar noticia',
            'eliminar noticia',

            // Permisos para el Módulo de Boletines
            'crear boletin',
            'editar boletin',
            'eliminar boletin',
        ];

        // --- 2. Crear todos los Permisos en la base de datos ---
        // firstOrCreate crea el permiso si no existe. array_unique asegura que no haya duplicados.
        foreach (array_unique($allSystemPermissions) as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // --- 3. Crear Roles y Asignar Permisos por Defecto ---

        // Rol: SuperAdmin - Asignar TODOS los permisos creados
        // Este rol es el controlador principal del sistema.
        $superAdminRole = Role::firstOrCreate(['name' => 'SuperAdmin', 'guard_name' => 'web']);
        $superAdminRole->givePermissionTo(Permission::all()); // Le damos todos los permisos existentes en el sistema

        // Rol: Administrador - Con permisos básicos por defecto según tu especificación
        $adminRole = Role::firstOrCreate(['name' => 'Administrador', 'guard_name' => 'web']);
        $adminRole->givePermissionTo([
            'crear producto',
            'crear usuario',
        ]);

        // Rol: Operario - Con permisos básicos por defecto según tu especificación
        $operarioRole = Role::firstOrCreate(['name' => 'Operario', 'guard_name' => 'web']);
        $operarioRole->givePermissionTo([
            'crear noticia',
        ]);

        // Rol: Funcionario - Con permisos básicos por defecto según tu especificación
        $funcionarioRole = Role::firstOrCreate(['name' => 'Funcionario', 'guard_name' => 'web']);
        $funcionarioRole->givePermissionTo([
            'crear boletin',
        ]);

        // --- 4. Crear o Actualizar Usuario SuperAdmin y Asignar Rol ---
        // Este usuario será el punto de entrada inicial para gestionar el sistema.
        $superAdminUser = User::firstOrCreate(
            ['email' => 'super@admin.com'], // Condición de búsqueda: Email único
            [ // Datos a crear si no se encuentra
                'name'              => 'SuperAdmin', // Nombre descriptivo
                'lastname'          => 'SuperAdministrador',
                'phone'             => '3156489578',
                'type_document'     => 'CC',
                'document'          => '1000000000',
                'password'          => Hash::make('SuperAdmin_2025!'), // Contraseña segura
                'email_verified_at' => now(), // Marca el correo como verificado
            ]
        );
        // Asignar el rol 'SuperAdmin' a este usuario.
        $superAdminUser->syncRoles([$superAdminRole]);

        $this->command->info('Roles y Permisos iniciales sembrados exitosamente.');
        $this->command->info('Usuario SuperAdmin (super@admin.com) creado/actualizado.');
    }
}
