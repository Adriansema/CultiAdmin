<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = Role::create(['name' => 'administrador']);
        $operador = Role::create(['name' => 'operador']);

        Permission::create(['name' => 'crear cultivos']);
        Permission::create(['name' => 'editar cultivos']);
        Permission::create(['name' => 'eliminar cultivos']);
        Permission::create(['name' => 'enviar cultivos a validación']);
        Permission::create(['name' => 'validar cultivos']);
        Permission::create(['name' => 'ver cultivos pendientes']);
        Permission::create(['name' => 'ver historial de validaciones']);

        $admin->givePermissionTo(['crear cultivos', 'editar cultivos', 'eliminar cultivos', 'enviar cultivos a validación']);
        $operador->givePermissionTo(['validar cultivos', 'ver cultivos pendientes', 'ver historial de validaciones']);
    }
}
