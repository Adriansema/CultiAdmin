<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User; // Asegúrate de que tu modelo de usuario sea correcto
use Illuminate\Support\Facades\Hash; // Para hashear la contraseña

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Datos del usuario a crear
        $userData = [
            'name'              => 'Usuario',
            'lastname'          => 'sin rol',
            'phone'             => '3156489578',
            'type_document'     => 'CC',
            'document'          => '101010101',
            'password'          => Hash::make('Josmart_2025!'), // Contraseña hasheada con Bcrypt
        ];

        // Condición de búsqueda (email único)
        $searchCondition = ['email' => 'user@sinrol.com'];

        // Busca el usuario por el email. Si no existe, lo crea.
        // updateOrCreate intentará encontrar un registro que coincida con la primera matriz
        // de argumentos. Si lo encuentra, actualizará el registro con los valores de la segunda
        // matriz. Si no lo encuentra, creará un nuevo registro combinando ambas matrices.
        $user = User::updateOrCreate(
            $searchCondition,
            $userData
        );

        // Mensaje en consola para confirmar la acción
        if ($user->wasRecentlyCreated) {
            $this->command->info('Usuario SuperAdmin creado exitosamente: ' . $user->email);
        } else {
            $this->command->info('Usuario SuperAdmin ya existe: ' . $user->email . ' (datos actualizados si hubo cambios).');
        }

        // NOTA IMPORTANTE: No se asigna ningún rol aquí, cumpliendo el requisito de "sin rol".
        // Si en el futuro necesitas asignar roles, lo harías con $user->assignRole('nombre-del-rol');
    }
}