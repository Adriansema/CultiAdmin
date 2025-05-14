<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Faker\Factory as Faker;

class ExportarCsvController extends Controller
{
    public function generarCsv()
    {
        $faker = Faker::create('es_ES');

        $usuarios = [];

        // 30 administradores
        for ($i = 0; $i < 10; $i++) {
            $usuarios[] = [
                'name' => $faker->name,
                'email' => $faker->unique()->safeEmail,
                'password' => 'password123',
                'rol' => 'administrador',
            ];
        }

        // 70 operadores
        for ($i = 0; $i < 7; $i++) {
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
