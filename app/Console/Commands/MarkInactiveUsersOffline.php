<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User; // <--- ¡Asegúrate de que este es el namespace correcto para tu modelo User!
use Carbon\Carbon;

class MarkInactiveUsersOffline extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:mark-offline'; // Un nombre único y descriptivo para tu comando

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Marks users as offline if they have been inactive for a certain period.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // Define el umbral de inactividad (ej. 15 minutos).
        // Si la columna 'last_login_at' de un usuario es más antigua que este umbral,
        // se considerará inactivo y se le marcará como offline.
        $inactiveThreshold = Carbon::now()->subMinutes(15);

        // Realiza la actualización en la base de datos:
        // Busca usuarios que actualmente están marcados como online (is_online = true)
        // Y cuya última actividad ('last_login_at') fue hace más de 15 minutos.
        // Luego, los actualiza a is_online = false.
        $count = User::where('is_online', true)
                     ->where('last_login_at', '<', $inactiveThreshold)
                     ->update(['is_online' => false]);

        $this->info("Se marcaron {$count} usuarios como offline debido a inactividad.");

        return 0; // 0 significa que el comando se ejecutó con éxito
    }
}
