<?php

//actualizacion 06/06/2025


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

        $allPermissions = array_unique(array_merge($adminPermissions,      		$operadorPermissions)); // Evitar duplicados

        foreach ($allPermissions as $permission) {

            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']); // Añadir guard_name

        }

        // Crear roles

        $adminRole = Role::firstOrCreate(['name' => 'administrador', 'guard_name' => 'web']); // Añadir guard_name

        $operadorRole = Role::firstOrCreate(['name' => 'operador', 'guard_name' => 'web']); // Añadir guard_name



        // Asignar permisos a cada rol

        $adminRole->syncPermissions($adminPermissions);

        $operadorRole->syncPermissions($operadorPermissions);

    }

}
