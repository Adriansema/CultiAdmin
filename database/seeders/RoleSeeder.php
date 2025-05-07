<?php

//actualizacion 08/04/2025

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // --- Permisos del Administrador ---
        $adminPermissions = [
            'crear datos de información',
            'editar datos de información',
            'eliminar datos de información',
            'crear boletines',
            'editar boletines',
            'eliminar boletines',
            'enviar a validación',
            'gestionar usuarios',
            'definir flujos',
            'iniciar proceso',
        ];

        // --- Permisos del Operador ---
        $operadorPermissions = [
            'revisar datos',
            'ver datos pendientes',
            'validar datos',
            'devolver para corrección',
            'supervisar calidad',
            'ver historial de cambios',
        ];

        // Crear o actualizar permisos
        $allPermissions = array_merge($adminPermissions, $operadorPermissions);
        foreach ($allPermissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Crear roles
        $adminRole = Role::firstOrCreate(['name' => 'administrador']);
        $operadorRole = Role::firstOrCreate(['name' => 'operador']);

        // Asignar permisos a cada rol
        $adminRole->syncPermissions($adminPermissions);
        $operadorRole->syncPermissions($operadorPermissions);
    }
}
