<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use Faker\Factory as Faker;
use App\Models\User; // Importa el modelo User para la Policy

class ExportarCsvController extends Controller
{
    public function generarCsv()
    {
        // Autorización: Solo usuarios con el permiso 'generar_usuarios_prueba' pueden ejecutar esta acción.
        // Usamos User::class porque esta acción está relacionada con la generación de usuarios.
        $this->authorize('generateCsv', User::class);

        $faker = Faker::create('es_ES');

        $usuarios = [];

        // 30 administradores
        for ($i = 0; $i < 1; $i++) {
            $usuarios[] = [
                'name' => $faker->name,
                'email' => $faker->unique()->safeEmail,
                'password' => 'password123',
                'rol' => 'administrador',
            ];
        }

        // 70 operadores
        for ($i = 0; $i < 1; $i++) {
            $usuarios[] = [
                'name' => $faker->name,
                'email' => $faker->unique()->safeEmail,
                'password' => 'password123',
                'rol' => 'operador',
            ];
        }

        // Mezclar la lista
        shuffle($usuarios);

        // Guardar CSV
        $filename = 'usuarios_generados_' . Str::random(5) . '.csv';
        $path = storage_path("app/public/{$filename}");

        $file = fopen($path, 'w');
        fputcsv($file, ['name', 'email', 'password', 'rol']);

        foreach ($usuarios as $usuario) {
            fputcsv($file, $usuario);
        }

        fclose($file);

        // Descargar
        return response()->download($path)->deleteFileAfterSend();
    }
}