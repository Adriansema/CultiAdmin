<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

use App\Console\Commands\LimpiarComentarios;
use App\Console\Commands\MakeBladeCrud;
use App\Console\Commands\ResetUsuarios;

class Kernel extends ConsoleKernel
{
    /**
     * Aqui se registran los comandos personalizados.
     */
    protected $commands = [
        LimpiarComentarios::class,
        MakeBladeCrud::class,
        ResetUsuarios::class,
    ];

    /**
     * Aqui se define la programacion de tareas.
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('comentarios:limpiar')->everyTwoMinutes();
    }

    /**
     * Aqui puedes registrar comandos adicionales si fuera necesario.
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');
    }
}

