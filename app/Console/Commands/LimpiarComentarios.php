<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Comentario;

class LimpiarComentarios extends Command
{
    protected $signature = 'comentarios:limpiar';
    protected $description = 'Eliminar comentarios de más de 2 minutos';

    public function handle()
    {
        Comentario::limpiar();
        $this->info('Comentarios antiguos eliminados.');
    }
}
