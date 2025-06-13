<?php

//actualizacion 06/06/2025


namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder

{

    public function run(): void

    {

        // --- Permisos del Administrador ---

        $adminPermissions = [

            //Permisos para el Módulo de cultivos
            'crear productos',
            'ver productos',
            'editar productos',
            'eliminar productos',
            'importar productos',
            'exportar productos',
            'enviar a validación',


            //Permisos para el Módulo de boletines
            'crear boletines',
            'ver boletines',
            'editar boletines',
            'eliminar boletines',
            'importar boletines',
            'exportar boletines',
            'enviar a validación',


            //Permisos para el Módulo de gestion de usuarios
            'ver lista de usuarios',
            'crear usuarios',
            'editar usuarios',
            'activar usuarios',
            'desactivar usuarios',
            'importar usuarios',
            'exportar usuarios',
            'generar usuarios masivos',
            'editar roles y permisos de usuario',

            // Permisos para el Módulo de Accesibilidad
            'administrar accesibilidad',

            // Permisos para el Módulo de Centro de Ayuda
            'ver centro de ayuda',
            'ver formulario de contacto',
            'buscar preguntas frecuentes',
            'enviar mensaje en el boton contactenos',

            // Permisos para el Módulo de Statistic
            'ver estadisticas',
        ];


        // --- Permisos del Operador ---

        $operadorPermissions = [

            //cultivos
            'ver productos pendientes',
            'validar productos',
            'rechazar productos',
            'devolver para corrección',

            //boletines
            'ver boletines pendientes',
            'validar boletines',
            'rechazar boletines',
            'devolver para corrección',

            // Permisos para el Módulo de Accesibilidad
            'administrar accesibilidad',

            // Permisos para el Módulo de Centro de Ayuda
            'ver centro de ayuda',
            'ver formulario de contacto',
            'buscar preguntas frecuentes',
            'enviar mensaje en el boton contactenos',

        ];

        // Crear o actualizar permisos

        $allPermissions = array_unique(array_merge($adminPermissions,              $operadorPermissions)); // Evitar duplicados

        foreach ($allPermissions as $permission) {

            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']); // Añadir guard_name

        }

        // Crear roles

        $adminRole = Role::firstOrCreate(['name' => 'administrador', 'guard_name' => 'web']); // Añadir guard_name

        $operadorRole = Role::firstOrCreate(['name' => 'operador', 'guard_name' => 'web']); // Añadir guard_name



        // Asignar permisos a cada rol

        $adminRole->syncPermissions($adminPermissions);

        $operadorRole->syncPermissions($operadorPermissions);

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
