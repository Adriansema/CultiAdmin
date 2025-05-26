<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class MakeBladeCrud extends Command
{
    /**
     * El nombre y la firma del comando de la consola.
     *
     * @var string
     */
    protected $signature = 'make:blade-crud {name}';

    /**
     * La descripción del comando de la consola.
     *
     * @var string
     */
    protected $description = 'Genera las vistas Blade necesarias para un CRUD de un recurso';

    /**
     * Ejecutar el comando de la consola.
     *
     * @return void
     */
    public function handle()
    {
        $name = $this->argument('name');
        $path = resource_path("views/{$name}");

        if (!File::exists($path)) {
            File::makeDirectory($path, 0755, true);
            $this->info("Carpeta 'resources/views/{$name}' creada.");
        }

        $views = [
            'index' => 'Listado de ' . ucfirst($name),
            'create' => 'Crear ' . rtrim(ucfirst($name), 's'),
            'edit' => 'Editar ' . rtrim(ucfirst($name), 's'),
            'show' => 'Detalle de ' . rtrim(ucfirst($name), 's'),
            '_form' => 'Formulario de ' . rtrim(ucfirst($name), 's'),
        ];

        foreach ($views as $file => $title) {
            $filePath = "{$path}/{$file}.blade.php";

            if (!File::exists($filePath)) {
                $content = $file === '_form'
                    ? <<<BLADE
        <!-- Formulario para {$title} -->
        <form method="POST" action="#">
            @csrf
            <!-- Campos aquí -->
        </form>
        BLADE
                            : <<<BLADE
        @extends('layouts.app')

        @section('title', '{$title}')

        @section('content')
            <div class="container mx-auto mt-4">
                <h1 class="mb-4 text-2xl font-bold">{$title}</h1>

                <!-- Contenido de la vista '{$file}' -->
            </div>
        @endsection
        BLADE;

                        File::put($filePath, $content);
                        $this->info("Archivo '{$file}.blade.php' creado en '{$path}'.");
                    }
                }

                $this->info("CRUD completo para '{$name}' generado con contenido base.");
            }
        }
