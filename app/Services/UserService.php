<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Spatie\Permission\Models\Role; // Asegúrate de importar el modelo Role si lo usas directamente aquí

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

        $searchQuery   = $request->input('q', '');
        $estadoFilter  = $request->input('estado', ''); // Ahora usado solo para filtrar, no para ordenar
        $rolFilter     = $request->input('rol', '');    // Ahora usado solo para filtrar, no para ordenar
        $sortBy        = $request->input('sort_by');    // Nuevo: columna para ordenar
        $sortDirection = $request->input('sort_direction'); // Nuevo: dirección de ordenamiento

        $cleanedSearchQuery = $this->cleanSearchQuery($searchQuery);

        $usuarios = User::query();

        // Condicionalmente unimos las tablas de roles si se va a ordenar por rol o filtrar por rol
        $joinedRolesTable = false;
        if (($sortBy === 'roles.name') || !empty($rolFilter)) {
            $usuarios->leftJoin('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
                ->leftJoin('roles', 'model_has_roles.role_id', '=', 'roles.id')
                ->select('users.*', 'roles.name as role_name'); // Seleccionar role_name para usarlo en el orderBy
            $joinedRolesTable = true;
        }

        // Siempre cargar la relación 'roles' para mostrar en la vista, incluso si no se une para ordenar
        $usuarios->with('roles');

        // Búsqueda robusta en múltiples columnas
        if (!empty($searchQuery)) {
            $usuarios->where(function ($q) use ($cleanedSearchQuery, $searchQuery, $joinedRolesTable) {
                $sqlNormalize = function ($column) {
                    // Adaptación para PostgreSQL, si usas MySQL, 'TO_CHAR' no es necesario para fechas
                    if (config('database.default') === 'pgsql' && in_array($column, ['created_at', 'updated_at'])) {
                        return "REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(LOWER(TO_CHAR({$column}, 'YYYY-MM-DD HH24:MI:SS')), 'á', 'a'), 'é', 'e'), 'í', 'i'), 'ó', 'o'), 'ú', 'u'), 'ü', 'u'), 'ñ', 'n'), '.', ''), '-', '')";
                    }
                    return "REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(LOWER({$column}), 'á', 'a'), 'é', 'e'), 'í', 'i'), 'ó', 'o'), 'ú', 'u'), 'ü', 'u'), 'ñ', 'n'), '.', ''), '-', '')";
                };

                $q->orWhereRaw($sqlNormalize('name') . ' LIKE ?', ['%' . $cleanedSearchQuery . '%'])
                    ->orWhereRaw($sqlNormalize('email') . ' LIKE ?', ['%' . $cleanedSearchQuery . '%'])
                    ->orWhereRaw($sqlNormalize('lastname') . ' LIKE ?', ['%' . $cleanedSearchQuery . '%']);

                // Búsqueda por rol si la tabla de roles está unida
                if ($joinedRolesTable) {
                    $q->orWhereRaw($sqlNormalize('roles.name') . ' LIKE ?', ['%' . $cleanedSearchQuery . '%']);
                }

                try {
                    $date = Carbon::parse($searchQuery);
                    $q->orWhereDate('created_at', $date->toDateString());
                } catch (\Exception $e) {
                    // No hace nada si la fecha no es válida
                }
            });
        }

        // Aplicar filtro por estado (si el parámetro 'estado' todavía se usa para filtrar)
        // Nota: Si el filtro de estado ahora es solo por ordenamiento, puedes quitar este bloque.
        // Lo mantengo por si necesitas la funcionalidad de filtro además de la ordenación.
        if (!empty($estadoFilter) && in_array($estadoFilter, ['activo', 'inactivo'])) {
            $usuarios->where('estado', $estadoFilter);
        }

        // Aplicar filtro por rol
        if (!empty($rolFilter)) {
            if ($joinedRolesTable) {
                $usuarios->where('roles.name', $rolFilter);
            } else {
                $usuarios->whereHas('roles', function ($query) use ($rolFilter) {
                    $query->where('name', $rolFilter);
                });
            }
        }


        // Ordenamiento genérico por columna y dirección
        $allowedSortColumns = [
            'name',
            'email',
            'estado',
            'created_at',
            'roles.name', // Ahora 'roles_name' es una columna válida después del join
        ];

        $allowedSortDirections = ['asc', 'desc'];

        if (in_array($sortBy, $allowedSortColumns) && in_array($sortDirection, $allowedSortDirections)) {
            // Si se ordena por rol, usamos el alias 'role_name' si se hizo el select
            if ($sortBy === 'roles.name') {
                $usuarios->orderBy('role_name', $sortDirection);
            } else {
                $usuarios->orderBy($sortBy, $sortDirection);
            }
        } else {
            // Ordenamiento por defecto si no hay un orden válido o si no se especifica
            $usuarios->orderBy('name', 'asc');
        }

        return $usuarios->paginate($perPage)->withQueryString();
    }

    /**
     * Obtiene usuarios filtrados para exportación (sin paginación).
     * Reutiliza la lógica de filtrado y ordenamiento.
     *
     * @param Request $request La solicitud HTTP con los parámetros de filtro.
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function obtenerUsuariosFiltradosParaExportar(Request $request)
    {
        $searchQuery   = $request->input('q', '');
        $estadoFilter  = $request->input('estado', '');
        $rolFilter     = $request->input('rol', '');
        $sortBy        = $request->input('sort_by');
        $sortDirection = $request->input('sort_direction');

        $cleanedSearchQuery = $this->cleanSearchQuery($searchQuery);

        $usuarios = User::query();

        // Condicionalmente unimos las tablas de roles si se va a ordenar por rol o filtrar por rol
        $joinedRolesTable = false;
        if (($sortBy === 'roles.name') || !empty($rolFilter)) {
            $usuarios->leftJoin('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
                ->leftJoin('roles', 'model_has_roles.role_id', '=', 'roles.id')
                ->select('users.*', 'roles.name as role_name');
            $joinedRolesTable = true;
        } else {
            $usuarios->select('users.*'); // Asegura que solo se seleccionen columnas de users
        }

        // Siempre cargar la relación 'roles' para mostrar en el CSV
        $usuarios->with('roles');

        // Búsqueda robusta en múltiples columnas
        if (!empty($searchQuery)) {
            $usuarios->where(function ($q) use ($cleanedSearchQuery, $searchQuery, $joinedRolesTable) {
                $sqlNormalize = function ($column) {
                    if (config('database.default') === 'pgsql' && in_array($column, ['created_at', 'updated_at'])) {
                        return "REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(LOWER(TO_CHAR({$column}, 'YYYY-MM-DD HH24:MI:SS')), 'á', 'a'), 'é', 'e'), 'í', 'i'), 'ó', 'o'), 'ú', 'u'), 'ü', 'u'), 'ñ', 'n'), '.', ''), '-', '')";
                    }
                    return "REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(LOWER({$column}), 'á', 'a'), 'é', 'e'), 'í', 'i'), 'ó', 'o'), 'ú', 'u'), 'ü', 'u'), 'ñ', 'n'), '.', ''), '-', '')";
                };

                $q->orWhereRaw($sqlNormalize('name') . ' LIKE ?', ['%' . $cleanedSearchQuery . '%'])
                    ->orWhereRaw($sqlNormalize('email') . ' LIKE ?', ['%' . $cleanedSearchQuery . '%'])
                    ->orWhereRaw($sqlNormalize('lastname') . ' LIKE ?', ['%' . $cleanedSearchQuery . '%']);

                // Búsqueda por rol si la tabla de roles está unida
                if ($joinedRolesTable) {
                    $q->orWhereRaw($sqlNormalize('roles.name') . ' LIKE ?', ['%' . $cleanedSearchQuery . '%']);
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
        if (!empty($estadoFilter) && in_array($estadoFilter, ['activo', 'inactivo'])) {
            $usuarios->where('estado', $estadoFilter);
        }

        // Aplicar filtro por rol
        if (!empty($rolFilter)) {
            if ($joinedRolesTable) {
                $usuarios->where('roles.name', $rolFilter);
            } else {
                $usuarios->whereHas('roles', function ($query) use ($rolFilter) {
                    $query->where('name', $rolFilter);
                });
            }
        }

        // Ordenamiento genérico por columna y dirección
        $allowedSortColumns = [
            'name',
            'email',
            'estado',
            'created_at',
            'roles.name', // Se usará el alias 'role_name' después del join
        ];

        $allowedSortDirections = ['asc', 'desc'];

        if (in_array($sortBy, $allowedSortColumns) && in_array($sortDirection, $allowedSortDirections)) {
            if ($sortBy === 'roles.name') {
                $usuarios->orderBy('role_name', $sortDirection);
            } else {
                $usuarios->orderBy($sortBy, $sortDirection);
            }
        } else {
            $usuarios->orderBy('name', 'asc');
        }

        return $usuarios->get(); // <-- No paginar, obtener todos los resultados
    }
}
