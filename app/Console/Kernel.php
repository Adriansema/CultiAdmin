<?php
namespace App\Console;
use App\Console\Commands\LimpiarComentarios;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Artisan;

class Kernel extends ConsoleKernel
{

    protected function schedule(Schedule $schedule)
    {
        $schedule->command('comentarios:limpiar')->everyTwoMinutes();
    }

}