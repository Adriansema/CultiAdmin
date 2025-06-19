<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use Faker\Factory as Faker;
class ExportarCsvController extends Controller
{
    public function generarCsv()
    {

        $faker = Faker::create('es_ES');

        $usuarios = [];

        // 6 Administradores
        for ($i = 0; $i < 6; $i++) {
            $usuarios[] = [
                'name' => $faker->name,
                'email' => $faker->unique()->safeEmail,
                'password' => 'password123',
                'rol' => 'Administrador',
            ];
        }

        // 5 Operarios
        for ($i = 0; $i < 5; $i++) {
            $usuarios[] = [
                'name' => $faker->name,
                'email' => $faker->unique()->safeEmail,
                'password' => 'password123',
                'rol' => 'Operario',
            ];
        }

        // 5 Funcionario
        for ($i = 0; $i < 5; $i++) {
            $usuarios[] = [
                'name' => $faker->name,
                'email' => $faker->unique()->safeEmail,
                'password' => 'password123',
                'rol' => 'Funcionario',
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
