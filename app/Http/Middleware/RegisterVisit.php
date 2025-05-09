<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Visit;

class RegisterVisit
{
    public function handle(Request $request, Closure $next)
    {
        if ($request->isMethod('GET') && !$request->is('admin/*')) { // Evita registrar visitas del admin
            Visit::create([
                'page' => $request->path(),
                'ip' => $request->ip(),
                'created_at' => now(), // Solo si no tienes timestamps activados
            ]);
        }

        return $next($request);
    }

    
}
