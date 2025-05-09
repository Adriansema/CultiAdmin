<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

use App\Console\Commands\LimpiarComentarios;
use App\Console\Commands\MakeBladeCrud;

class Kernel extends ConsoleKernel
{
    /**
     * Aquí se registran los comandos personalizados.
     */
    protected $commands = [
        LimpiarComentarios::class,
        MakeBladeCrud::class,
    ];

    /**
     * Aquí se define la programación de tareas.
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('comentarios:limpiar')->everyTwoMinutes();
    }

    /**
     * Aquí puedes registrar comandos adicionales si fuera necesario.
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');
    }
}

