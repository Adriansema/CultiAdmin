<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class UserService
{
    private function cleanSearchQuery(string $text): string
    {
        $text = mb_strtolower($text, 'UTF-8');
        $text = str_replace(
            ['á', 'é', 'í', 'ó', 'ú', 'ü', 'ñ', 'Á', 'É', 'Í', 'Ó', 'Ú', 'Ü', 'Ñ'],
            ['a', 'e', 'i', 'o', 'u', 'u', 'n', 'A', 'E', 'I', 'O', 'U', 'U', 'N'],
            $text
        );
        $text = preg_replace('/[^a-z0-9\s]/', '', $text);
        $text = trim(preg_replace('/\s+/', ' ', $text));
        return $text;
    }

    public function obtenerUsuariosFiltrados(Request $request)
    {
        $perPage = in_array($request->input('per_page'), [5, 10, 25, 50, 100])
            ? $request->input('per_page')
            : 10;

        // Inicializar todas las variables al principio
        $searchQuery = $request->input('q', '');
        $estadoFilter = $request->input('estado', '');
        $rolFilter = $request->input('rol', '');

        // Asegurarse de que $cleanedSearchQuery siempre esté definida
        $cleanedSearchQuery = $this->cleanSearchQuery($searchQuery); // Mover fuera del 'if' para que siempre exista

        $usuarios = User::query()->with('roles');

        // Búsqueda robusta en múltiples columnas (solo se aplica si hay algo en searchQuery)
        if (!empty($searchQuery)) { // La condición 'if' ahora solo controla la aplicación del filtro, no la definición de la variable
            $usuarios->where(function ($q) use ($cleanedSearchQuery, $searchQuery) { // Pasa $cleanedSearchQuery aquí
                $sqlNormalize = function($column) {
                    if (in_array($column, ['created_at', 'updated_at'])) {
                        return "REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(LOWER(TO_CHAR({$column}, 'YYYY-MM-DD HH24:MI:SS')), 'á', 'a'), 'é', 'e'), 'í', 'i'), 'ó', 'o'), 'ú', 'u'), 'ü', 'u'), 'ñ', 'n'), '.', ''), '-', '')";
                    }
                    return "REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(LOWER({$column}), 'á', 'a'), 'é', 'e'), 'í', 'i'), 'ó', 'o'), 'ú', 'u'), 'ü', 'u'), 'ñ', 'n'), '.', ''), '-', '')";
                };

                $q->orWhereRaw($sqlNormalize('name') . ' LIKE ?', ['%' . $cleanedSearchQuery . '%'])
                  ->orWhereRaw($sqlNormalize('email') . ' LIKE ?', ['%' . $cleanedSearchQuery . '%'])
                  ->orWhereRaw($sqlNormalize('apellido') . ' LIKE ?', ['%' . $cleanedSearchQuery . '%']);
                
                try {
                    $date = Carbon::parse($searchQuery);
                    $q->orWhereDate('created_at', $date->toDateString());
                } catch (\Exception $e) {
                    // No hace nada si la fecha no es válida
                }
            });
        }

        // Aplicar filtro por estado
        if (!empty($estadoFilter) && in_array($estadoFilter, ['activo', 'inactivo'])) {
            $usuarios->where('estado', $estadoFilter);
        }

        // Aplicar filtro por rol
        if (!empty($rolFilter)) {
            $usuarios->whereHas('roles', function ($query) use ($rolFilter) {
                $query->where('name', $rolFilter);
            });
        }

        $usuarios->orderBy('name', 'asc');

        return $usuarios->paginate($perPage)->withQueryString();
    }
}