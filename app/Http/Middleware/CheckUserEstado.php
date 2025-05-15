<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\IntentoAcceso;
use Illuminate\Support\Facades\Auth;

class CheckUserEstado
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        if ($user && $user->estado !== 'activo') {
            IntentoAcceso::create([
                'user_id' => $user->id,
                'email' => $user->email,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            Auth::guard('web')->logout();

            return redirect()->route('login')->with('inactivo', true);
        } 

        return $next($request);
    }
} 

