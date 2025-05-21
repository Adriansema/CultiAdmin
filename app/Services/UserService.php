<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Http\Request;

class UserService
{
    public function obtenerUsuariosFiltrados(Request $request)
    {
        $perPage = in_array($request->input('per_page'), [5, 10, 25, 50, 100])
            ? $request->input('per_page')
            : 5;

        $query      = $request->input('q');        // Búsqueda general
        $estado     = $request->input('estado');   // 'activo' o 'inactivo'
        $rol        = $request->input('rol');      // Nombre del rol

        $usuarios = User::with('roles');

        // Búsqueda por nombre o email
        if ($query) {
            $usuarios->where(function ($q2) use ($query) {
                $q2->whereRaw('LOWER(name) LIKE ?', ['%' . strtolower($query) . '%'])
                    ->orWhereRaw('LOWER(email) LIKE ?', ['%' . strtolower($query) . '%']);
            });
        }

        // Filtro por estado (si está presente)
        if ($estado === 'activo' || $estado === 'inactivo') {
            $usuarios->where('estado', $estado);
        }

        // Filtro por rol
        if ($rol) {
            $usuarios->whereHas('roles', function ($q3) use ($rol) {
                $q3->where('name', $rol);
            });
        }

        return $usuarios->paginate($perPage);
    }
}

