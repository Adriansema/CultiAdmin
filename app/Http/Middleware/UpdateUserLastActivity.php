<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log; // <-- Asegúrate de que esta línea esté

class UpdateUserLastActivity
{
    public function handle(Request $request, Closure $next)
    {
         
        if (Auth::check()) {
            $user = Auth::user();
            $now = Carbon::now();

            // Log: Antes de intentar guardar
            Log::info('Middleware UpdateUserLastActivity: Intentando actualizar last_login_at para usuario ID: ' . $user->id . ' a ' . $now->toDateTimeString());

            $user->last_login_at = $now;
            $saved = $user->save(); // <-- Usando save() directamente

            // Log: Después de intentar guardar
            if ($saved) {
                Log::info('Middleware UpdateUserLastActivity: last_login_at guardado EXITOSAMENTE para usuario ID: ' . $user->id);
            } else {
                Log::error('Middleware UpdateUserLastActivity: FALLO al guardar last_login_at para usuario ID: ' . $user->id);
            }
        }

        return $next($request);
    }
}
