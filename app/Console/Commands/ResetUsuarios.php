<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class ResetUsuarios extends Command
{
    protected $signature = 'reset:usuarios';
    protected $description = 'Elimina todos los usuarios y reinicia el ID autoincremental';

    public function handle()
    {
        $this->info('Eliminando todos los usuarios...');

        // Desactiva restricciones de clave foránea si existen dependencias
        DB::statement('SET session_replication_role = replica');

        // Vacía la tabla users
        User::truncate();

        // Reinicia la secuencia del ID
        DB::statement("ALTER SEQUENCE users_id_seq RESTART WITH 1");

        // Reactiva restricciones
        DB::statement('SET session_replication_role = DEFAULT');

        $this->info('Todos los usuarios fueron eliminados y el ID fue reiniciado a 1.');
    }
}