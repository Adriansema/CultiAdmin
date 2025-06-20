<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\Storage; // Importa el facade Storage

class ExportarCsvController extends Controller
{
    public function generarCsv()
    {
        $faker = Faker::create('es_ES');
        $usuarios = [];

        // Tipos de documento comunes en Colombia (ejemplo)
        $documentTypes = ['CC', 'TI', 'CE', 'PAS'];

        // -----------------------------------------------------------------------------------------

        // 6 Administradores
        for ($i = 0; $i < 6; $i++) {
            $usuarios[] = [
                'name'          => $faker->name,
                'email'         => $faker->unique()->safeEmail,
                'type_document' => $faker->randomElement($documentTypes),
                'document'      => $faker->unique()->numerify('##########'), // 10 dígitos numéricos
                'rol'           => 'Administrador',
            ];
        }

        // 5 Operarios
        for ($i = 0; $i < 5; $i++) {
            $usuarios[] = [
                'name'          => $faker->name,
                'email'         => $faker->unique()->safeEmail,
                'type_document' => $faker->randomElement($documentTypes),
                'document'      => $faker->unique()->numerify('##########'),
                'rol'           => 'Operario',
            ];
        }

        // 5 Funcionarios
        for ($i = 0; $i < 5; $i++) {
            $usuarios[] = [
                'name'          => $faker->name,
                'email'         => $faker->unique()->safeEmail,
                'type_document' => $faker->randomElement($documentTypes),
                'document'      => $faker->unique()->numerify('##########'),
                'rol'           => 'Funcionario',
            ];
        }

        // Mezclar la lista para que los roles no estén agrupados
        shuffle($usuarios);

        // Definir los encabezados del CSV
        $headers = ['name', 'email', 'type_document', 'document', 'rol'];

        // Guardar CSV en el disco 'public'
        $filename = 'usuarios_generados_' . now()->format('Ymd_His') . '_' . Str::random(5) . '.csv';
        $path = 'public/' . $filename; // Ruta relativa al disco 'public'

        // Abre el archivo en modo escritura
        $file = fopen(Storage::path($path), 'w');

        // Escribe los encabezados
        fputcsv($file, $headers);

        // Escribe los datos de cada usuario
        foreach ($usuarios as $usuario) {
            // Asegúrate de que el orden de los datos coincida con los encabezados
            fputcsv($file, [
                $usuario['name'],
                $usuario['email'],
                $usuario['type_document'],
                $usuario['document'],
                $usuario['rol'],
            ]);
        }

        fclose($file);

        // Descargar el archivo
        return response()->download(Storage::path($path))->deleteFileAfterSend(true);
    }
}