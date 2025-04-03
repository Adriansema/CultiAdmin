<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TrackVisits
{
    public function handle(Request $request, Closure $next)
    {
        // Guardar la visita en la tabla 'vissit'
        DB::table('visits')->insert([
            'page' => $request->path(), // Página visitada
            'ip' => $request->ip(),     // Dirección IP del visitante
            'created_at' => now()       // Fecha y hora de la visita
        ]);

        return $next($request);
    }
}
