<?php

namespace App\Services;

use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\User; // Asegúrate de importar el modelo User

class ProductService
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

    /**
     * Obtiene productos filtrados y paginados para la tabla principal.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function obtenerProductosFiltrados(Request $request)
    {
        $perPage = in_array($request->input('per_page'), [5, 10, 25, 50, 100])
            ? $request->input('per_page')
            : 10;

        $searchQuery   = $request->input('q', '');
        $estadoFilter  = $request->input('estado', '');
        $sortBy        = $request->input('sort_by');
        $sortDirection = $request->input('sort_direction');

        $productos = Producto::query();

        // Cargar las relaciones necesarias para la vista de la tabla
        $productos->with('user');

        // Bandera para saber si ya hicimos el join con la tabla de usuarios para ordenar/buscar
        $joinedUsersTable = false;

        // Si se va a ordenar por 'creador' o buscar por 'creador', hacemos el join
        // Asumimos que 'creador' en el frontend se mapea a 'users.name' en la DB
        if ($sortBy === 'creador' || (
            !empty($searchQuery) &&
            (str_contains($this->cleanSearchQuery($searchQuery), 'creador')) // Heurística simple para búsqueda
        )) {
            $productos->leftJoin('users', 'productos.user_id', '=', 'users.id')
                     ->select('productos.*', 'users.name as creador_name'); // Seleccionamos el nombre del usuario como 'creador_name'
            $joinedUsersTable = true;
        } else {
            // Si no hay join por 'creador' y no hay select previo, seleccionar todas las columnas de productos
            // para evitar errores si se llama a select() múltiples veces sin agregar columnas.
            // Si ya hay un select() en otro lugar, esto podría ser redundante o causar problemas.
            // Lo ideal es que el select() inicial sea más explícito o que el join siempre se haga si 'creador' es una columna mostrada.
            // Para simplificar, si no hay join, no se añade un select específico aquí.
        }

        // Búsqueda robusta general en múltiples columnas
        if (!empty($searchQuery)) {
            $cleanedSearchQuery = $this->cleanSearchQuery($searchQuery);
            
            $productos->where(function ($q) use ($cleanedSearchQuery, $searchQuery, $joinedUsersTable) {
                $sqlNormalize = function($column) {
                    if (config('database.default') === 'pgsql' && in_array($column, ['created_at', 'updated_at'])) {
                        return "REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(LOWER(TO_CHAR({$column}, 'YYYY-MM-DD HH24:MI:SS')), 'á', 'a'), 'é', 'e'), 'í', 'i'), 'ó', 'o'), 'ú', 'u'), 'ü', 'u'), 'ñ', 'n'), '.', ''), '-', '')";
                    }
                    return "REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(LOWER({$column}), 'á', 'a'), 'é', 'e'), 'í', 'i'), 'ó', 'o'), 'ú', 'u'), 'ü', 'u'), 'ñ', 'n'), '.', ''), '-', '')";
                };

                // Búsqueda robusta en las columnas de Producto
                $q->orWhereRaw($sqlNormalize('tipo') . ' LIKE ?', ['%' . $cleanedSearchQuery . '%'])
                  ->orWhereRaw($sqlNormalize('observaciones') . ' LIKE ?', ['%' . $cleanedSearchQuery . '%'])
                  ->orWhereRaw($sqlNormalize('RutaVideo') . ' LIKE ?', ['%' . $cleanedSearchQuery . '%']);
                
                // Búsqueda por estado
                $q->orWhereRaw($sqlNormalize('estado') . ' LIKE ?', ['%' . $cleanedSearchQuery . '%']);

                // Búsqueda por creador (si la tabla de usuarios está unida o se puede acceder vía relación)
                // Usamos whereHas para buscar en la relación si no se hizo un join explícito para la ordenación.
                // Si ya se hizo un leftJoin con select('users.name as creador_name'), entonces podemos usar 'creador_name'.
                if ($joinedUsersTable) {
                    $q->orWhereRaw($sqlNormalize('users.name') . ' LIKE ?', ['%' . $cleanedSearchQuery . '%']);
                } else {
                    $q->orWhereHas('user', function ($userQuery) use ($cleanedSearchQuery) {
                        $userQuery->whereRaw("REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(LOWER(name), 'á', 'a'), 'é', 'e'), 'í', 'i'), 'ó', 'o'), 'ú', 'u'), 'ü', 'u'), 'ñ', 'n'), '.', ''), '-', '') LIKE ?", ['%' . $cleanedSearchQuery . '%']);
                    });
                }

                // Intenta buscar por fecha limpia si la cadena de búsqueda parece una fecha
                try {
                    $date = Carbon::parse($searchQuery);
                    $q->orWhereDate('created_at', $date->toDateString());
                } catch (\Exception $e) {
                    // No hace nada si la fecha no es válida
                }
            });
        }

        // Aplicar filtro por estado
        if (!empty($estadoFilter) && in_array($estadoFilter, ['aprobado', 'pendiente', 'rechazado'])) {
            $productos->where('estado', $estadoFilter);
        }

        // Ordenamiento genérico por columna y dirección
        $allowedSortColumns = [
            'creador', // Mapeará a 'creador_name' o 'users.name'
            'tipo',
            'created_at',
            'estado',
        ];

        $allowedSortDirections = ['asc', 'desc'];

        if (in_array($sortBy, $allowedSortColumns) && in_array($sortDirection, $allowedSortDirections)) {
            // Si se ordena por 'creador', usamos el alias 'creador_name' del join
            if ($sortBy === 'creador') {
                // Asegurarse de que el join se haga solo una vez si ya se hizo para la búsqueda
                if (!$joinedUsersTable) { // Solo si no se unió antes
                    $productos->leftJoin('users', 'productos.user_id', '=', 'users.id')
                             ->select('productos.*', 'users.name as creador_name');
                }
                $productos->orderBy('creador_name', $sortDirection);
            } else {
                $productos->orderBy($sortBy, $sortDirection);
            }
        } else {
            // Ordenamiento por defecto si no hay un orden válido o si no se especifica
            $productos->orderBy('created_at', 'desc');
        }

        return $productos->paginate($perPage)->withQueryString();
    }

    /**
     * Obtiene productos filtrados para exportación (sin paginación).
     * Reutiliza la lógica de filtrado y ordenamiento.
     *
     * @param Request $request
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function obtenerProductosFiltradosParaExportar(Request $request)
    {
        $searchQuery   = $request->input('q', '');
        $estadoFilter  = $request->input('estado', '');
        $sortBy        = $request->input('sort_by');
        $sortDirection = $request->input('sort_direction');

        $productos = Producto::query();

        // Cargar todas las relaciones de detalle y el usuario para el CSV
        $productos->with(['user', 'cafe', 'mora', 'videos']);

        // Bandera para saber si ya hicimos el join con la tabla de usuarios para ordenar/buscar
        $joinedUsersTable = false;

        // Si se va a ordenar por 'creador' o buscar por 'creador', hacemos el join
        if ($sortBy === 'creador' || (
            !empty($searchQuery) &&
            (str_contains($this->cleanSearchQuery($searchQuery), 'creador'))
        )) {
            $productos->leftJoin('users', 'productos.user_id', '=', 'users.id')
                     ->select('productos.*', 'users.name as creador_name');
            $joinedUsersTable = true;
        } else {
            $productos->select('productos.*'); // Asegura que solo se seleccionen columnas de productos si no hay join
        }

        // Búsqueda robusta general en múltiples columnas
        if (!empty($searchQuery)) {
            $cleanedSearchQuery = $this->cleanSearchQuery($searchQuery);
            
            $productos->where(function ($q) use ($cleanedSearchQuery, $searchQuery, $joinedUsersTable) {
                $sqlNormalize = function($column) {
                    if (config('database.default') === 'pgsql' && in_array($column, ['created_at', 'updated_at'])) {
                        return "REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(LOWER(TO_CHAR({$column}, 'YYYY-MM-DD HH24:MI:SS')), 'á', 'a'), 'é', 'e'), 'í', 'i'), 'ó', 'o'), 'ú', 'u'), 'ü', 'u'), 'ñ', 'n'), '.', ''), '-', '')";
                    }
                    return "REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(LOWER({$column}), 'á', 'a'), 'é', 'e'), 'í', 'i'), 'ó', 'o'), 'ú', 'u'), 'ü', 'u'), 'ñ', 'n'), '.', ''), '-', '')";
                };

                $q->orWhereRaw($sqlNormalize('tipo') . ' LIKE ?', ['%' . $cleanedSearchQuery . '%'])
                  ->orWhereRaw($sqlNormalize('observaciones') . ' LIKE ?', ['%' . $cleanedSearchQuery . '%'])
                  ->orWhereRaw($sqlNormalize('RutaVideo') . ' LIKE ?', ['%' . $cleanedSearchQuery . '%']);
                
                $q->orWhereRaw($sqlNormalize('estado') . ' LIKE ?', ['%' . $cleanedSearchQuery . '%']);

                if ($joinedUsersTable) {
                    $q->orWhereRaw($sqlNormalize('users.name') . ' LIKE ?', ['%' . $cleanedSearchQuery . '%']);
                } else {
                    $q->orWhereHas('user', function ($userQuery) use ($cleanedSearchQuery) {
                        $userQuery->whereRaw("REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(LOWER(name), 'á', 'a'), 'é', 'e'), 'í', 'i'), 'ó', 'o'), 'ú', 'u'), 'ü', 'u'), 'ñ', 'n'), '.', ''), '-', '') LIKE ?", ['%' . $cleanedSearchQuery . '%']);
                    });
                }

                try {
                    $date = Carbon::parse($searchQuery);
                    $q->orWhereDate('created_at', $date->toDateString());
                } catch (\Exception $e) {
                    // No hace nada si la fecha no es válida
                }
            });
        }

        // Aplicar filtro por estado
        if (!empty($estadoFilter) && in_array($estadoFilter, ['aprobado', 'pendiente', 'rechazado'])) {
            $productos->where('estado', $estadoFilter);
        }

        // Ordenamiento genérico por columna y dirección
        $allowedSortColumns = [
            'creador', // Mapeará a 'creador_name' o 'users.name'
            'tipo',
            'created_at',
            'estado',
        ];

        $allowedSortDirections = ['asc', 'desc'];

        if (in_array($sortBy, $allowedSortColumns) && in_array($sortDirection, $allowedSortDirections)) {
            if ($sortBy === 'creador') {
                if (!$joinedUsersTable) { // Solo si no se unió antes
                    $productos->leftJoin('users', 'productos.user_id', '=', 'users.id')
                             ->select('productos.*', 'users.name as creador_name');
                }
                $productos->orderBy('creador_name', $sortDirection);
            } else {
                $productos->orderBy($sortBy, $sortDirection);
            }
        } else {
            $productos->orderBy('created_at', 'desc');
        }

        return $productos->get(); // <-- No paginar, obtener todos los resultados
    }
}
