<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\Storage; // Importa el facade Storage
use Illuminate\Http\Request; // Importa Request para obtener el tipo de CSV

class ExportarCsvController extends Controller
{
    /**
     * Genera un archivo CSV con diferentes tipos de datos para pruebas.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\StreamedResponse|\Illuminate\Http\JsonResponse
     */
    public function generarCsv(Request $request)
    {
        $type = $request->query('type', 'correctos'); // Obtiene el tipo de CSV a generar, por defecto 'correctos'
        $faker = Faker::create('es_ES');
        $usuarios = [];

        // Tipos de documento comunes en Colombia (ejemplo)
        $documentTypes = ['CC', 'TI', 'CE', 'PAS', 'NIT']; // Añadido PAS, NIT para variedad

        // Delegar la generación de usuarios según el tipo solicitado
        switch ($type) {
            case 'vacio':
                // No se añaden usuarios, se generará un CSV con solo encabezados.
                $filename = 'usuarios_vacio_' . now()->format('Ymd_His') . '_' . Str::random(5) . '.csv';
                break;
            case 'duplicados':
                $usuarios = $this->generarUsuariosDuplicados($faker, $documentTypes);
                $filename = 'usuarios_duplicados_' . now()->format('Ymd_His') . '_' . Str::random(5) . '.csv';
                break;
            case 'invalidos':
                $usuarios = $this->generarUsuariosInvalidos($faker, $documentTypes);
                $filename = 'usuarios_invalidos_' . now()->format('Ymd_His') . '_' . Str::random(5) . '.csv';
                break;
            case 'campos_faltantes':
                $usuarios = $this->generarUsuariosCamposFaltantes($faker, $documentTypes);
                $filename = 'usuarios_campos_faltantes_' . now()->format('Ymd_His') . '_' . Str::random(5) . '.csv';
                break;
            case 'correctos':
            default:
                $usuarios = $this->generarUsuariosCorrectos($faker, $documentTypes);
                $filename = 'usuarios_correctos_' . now()->format('Ymd_His') . '_' . Str::random(5) . '.csv';
                break;
        }

        // Definir los encabezados del CSV
        $headers = ['name', 'lastname', 'email', 'phone', 'type_document', 'document', 'role'];

        $path = 'public/' . $filename;
        $file = fopen(Storage::path($path), 'w');

        // Escribe los encabezados
        fputcsv($file, $headers);

        // Escribe los datos de cada usuario
        foreach ($usuarios as $usuario) {
            // Asegúrate de que el orden de los datos coincida exactamente con los encabezados
            // y que los campos existan (si faltan, fputcsv pondrá un campo vacío)
            fputcsv($file, [
                $usuario['name'] ?? '',
                $usuario['lastname'] ?? '',
                $usuario['email'] ?? '',
                $usuario['phone'] ?? '',
                $usuario['type_document'] ?? '',
                $usuario['document'] ?? '',
                $usuario['role'] ?? '',
            ]);
        }

        fclose($file);

        return response()->download(Storage::path($path))->deleteFileAfterSend(true);
    }

    /**
     * Genera un array de usuarios con datos correctos.
     * @param \Faker\Generator $faker
     * @param array $documentTypes
     * @return array
     */
    private function generarUsuariosCorrectos($faker, $documentTypes)
    {
        $usuarios = [];

        // 6 Administradores
        for ($i = 0; $i < 6; $i++) {
            $usuarios[] = [
                'name'          => $faker->firstName,
                'lastname'      => $faker->lastName,
                'email'         => $faker->unique()->safeEmail,
                'phone'         => $faker->unique()->phoneNumber,
                'type_document' => $faker->randomElement($documentTypes),
                'document'      => $faker->unique()->numerify('##########'),
                'role'          => 'Administrador',
            ];
        }

        // 5 Operarios
        for ($i = 0; $i < 5; $i++) {
            $usuarios[] = [
                'name'          => $faker->firstName,
                'lastname'      => $faker->lastName,
                'email'         => $faker->unique()->safeEmail,
                'phone'         => $faker->unique()->phoneNumber,
                'type_document' => $faker->randomElement($documentTypes),
                'document'      => $faker->unique()->numerify('##########'),
                'role'          => 'Operario',
            ];
        }

        // 5 Funcionarios
        for ($i = 0; $i < 5; $i++) {
            $usuarios[] = [
                'name'          => $faker->firstName,
                'lastname'      => $faker->lastName,
                'email'         => $faker->unique()->safeEmail,
                'phone'         => $faker->unique()->phoneNumber,
                'type_document' => $faker->randomElement($documentTypes),
                'document'      => $faker->unique()->numerify('##########'),
                'role'          => 'Funcionario',
            ];
        }

        shuffle($usuarios);
        return $usuarios;
    }

    /**
     * Genera un array de usuarios con algunos duplicados (email o documento).
     * @param \Faker\Generator $faker
     * @param array $documentTypes
     * @return array
     */
    private function generarUsuariosDuplicados($faker, $documentTypes)
    {
        $usuarios = $this->generarUsuariosCorrectos($faker, $documentTypes); // Genera una base de usuarios correctos
        
        // Añadir 3 usuarios duplicados
        for ($i = 0; $i < 3; $i++) {
            // Duplicar un email existente del primer usuario
            if (isset($usuarios[0])) {
                $duplicateUserEmail = $usuarios[0]['email'];
                $usuarios[] = [
                    'name'          => $faker->firstName,
                    'lastname'      => $faker->lastName,
                    'email'         => $duplicateUserEmail, // Duplicado
                    'phone'         => $faker->unique()->phoneNumber,
                    'type_document' => $faker->randomElement($documentTypes),
                    'document'      => $faker->unique()->numerify('##########'),
                    'role'          => 'Operario',
                ];
            }

            // Duplicar un documento existente del segundo usuario
            if (isset($usuarios[1])) {
                $duplicateUserDocument = $usuarios[1]['document'];
                $usuarios[] = [
                    'name'          => $faker->firstName,
                    'lastname'      => $faker->lastName,
                    'email'         => $faker->unique()->safeEmail,
                    'phone'         => $faker->unique()->phoneNumber,
                    'type_document' => $faker->randomElement($documentTypes),
                    'document'      => $duplicateUserDocument, // Duplicado
                    'role'          => 'Funcionario',
                ];
            }
        }

        shuffle($usuarios); // Mezclar para que los duplicados no estén al principio
        return $usuarios;
    }

    /**
     * Genera un array de usuarios con algunos datos inválidos (ej. email mal formado, documento con letras).
     * @param \Faker\Generator $faker
     * @param array $documentTypes
     * @return array
     */
    private function generarUsuariosInvalidos($faker, $documentTypes)
    {
        $usuarios = $this->generarUsuariosCorrectos($faker, $documentTypes); // Genera una base de usuarios correctos

        // Modificar algunos para que sean inválidos
        if (isset($usuarios[0])) {
            $usuarios[0]['email'] = 'email_invalido.com'; // Email sin @
        }
        if (isset($usuarios[1])) {
            $usuarios[1]['document'] = 'ABC123XYZ'; // Documento no numérico
        }
        if (isset($usuarios[2])) {
            $usuarios[2]['role'] = 'RolNoExiste'; // Rol que no existe
        }
        if (isset($usuarios[3])) {
            $usuarios[3]['email'] = $faker->unique()->safeEmail . ' @'; // Espacio en el email
        }
        
        return $usuarios;
    }

    /**
     * Genera un array de usuarios con algunos campos obligatorios faltantes.
     * @param \Faker\Generator $faker
     * @param array $documentTypes
     * @return array
     */
    private function generarUsuariosCamposFaltantes($faker, $documentTypes)
    {
        $usuarios = $this->generarUsuariosCorrectos($faker, $documentTypes); // Genera una base de usuarios correctos

        // Eliminar algunos campos obligatorios
        if (isset($usuarios[0])) {
            $usuarios[0]['name'] = ''; // Nombre vacío
        }
        if (isset($usuarios[1])) {
            $usuarios[1]['email'] = ''; // Email vacío
        }
        if (isset($usuarios[2])) {
            unset($usuarios[2]['document']); // Documento faltante (se espera '')
        }
        if (isset($usuarios[3])) {
            $usuarios[3]['role'] = ''; // Rol vacío
        }
        if (isset($usuarios[4])) {
            $usuarios[4]['type_document'] = null; // Tipo de documento nulo
        }

        return $usuarios;
    }
}