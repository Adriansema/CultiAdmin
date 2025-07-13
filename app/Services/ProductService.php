<?php

namespace App\Services;

use App\Models\Producto;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\User;
use Illuminate\Support\Str;

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

        // Si se va a ordenar por 'creador', hacemos el join y seleccionamos el nombre.
        if ($sortBy === 'creador') {
            $productos->leftJoin('users', 'productos.user_id', '=', 'users.id')
                ->select('productos.*', 'users.name as creador_name');
            $joinedUsersTable = true;
        } else {
            // Asegura que se seleccionen las columnas de productos si no hay un join con select
            $productos->select('productos.*');
        }

        // Búsqueda robusta general en múltiples columnas
        if (!empty($searchQuery)) {
            $cleanedSearchQuery = $this->cleanSearchQuery($searchQuery);

            $productos->where(function ($q) use ($cleanedSearchQuery, $searchQuery, $joinedUsersTable) {
                $sqlNormalize = function ($column) {
                    if (config('database.default') === 'pgsql' && in_array($column, ['created_at', 'updated_at'])) {
                        return "REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(LOWER(TO_CHAR({$column}, 'YYYY-MM-DD HH24:MI:SS')), 'á', 'a'), 'é', 'e'), 'í', 'i'), 'ó', 'o'), 'ú', 'u'), 'ü', 'u'), 'ñ', 'n'), '.', ''), '-', '')";
                    }
                    return "REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(LOWER({$column}), 'á', 'a'), 'é', 'e'), 'í', 'i'), 'ó', 'o'), 'ú', 'u'), 'ü', 'u'), 'ñ', 'n'), '.', ''), '-', '')";
                };

                // Búsqueda robusta en las columnas de Producto
                $q->orWhereRaw($sqlNormalize('tipo') . ' LIKE ?', ['%' . $cleanedSearchQuery . '%']);

                // Búsqueda por estado
                $q->orWhereRaw($sqlNormalize('estado') . ' LIKE ?', ['%' . $cleanedSearchQuery . '%']);

                // Búsqueda por creador (a través de la relación, más flexible que un join directo para la búsqueda)
                $q->orWhereHas('user', function ($userQuery) use ($cleanedSearchQuery) {
                    $userQuery->whereRaw("REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(LOWER(name), 'á', 'a'), 'é', 'e'), 'í', 'i'), 'ó', 'o'), 'ú', 'u'), 'ü', 'u'), 'ñ', 'n'), '.', ''), '-', '') LIKE ?", ['%' . $cleanedSearchQuery . '%']);
                });

                // --- INICIO DE LA MODIFICACIÓN DE FECHAS (COPIADO DE BOLETINES) ---
                $date = null; // Variable para almacenar la fecha parseada
                $originalQueryCleanedForDate = trim(mb_strtolower($searchQuery, 'UTF-8')); // Usa $searchQuery aquí

                $originalLocale = Carbon::getLocale();
                Carbon::setLocale('es');

                try {
                    if (preg_match('/^\d{4}[\/\-]\d{2}[\/\-]\d{2}$/', $originalQueryCleanedForDate)) {
                        $date = Carbon::createFromFormat('Y-m-d', str_replace('/', '-', $originalQueryCleanedForDate));
                    } elseif (preg_match('/^\d{2}[\/\-]\d{2}[\/\-]\d{4}$/', $originalQueryCleanedForDate)) {
                        $date = Carbon::createFromFormat('d-m-Y', str_replace('/', '-', $originalQueryCleanedForDate));
                    } elseif (preg_match('/^\d{2}[\/\-]\d{2}[\/\-]\d{4}$/', $originalQueryCleanedForDate)) {
                        $date = Carbon::createFromFormat('m-d-Y', str_replace('/', '-', $originalQueryCleanedForDate));
                    }
                } catch (\Exception $e) {
                    // No hace nada, se intentará el siguiente método
                }

                if (!$date) {
                    $normalizedMonthQuery = $originalQueryCleanedForDate;

                    $monthMap = [
                        'enero' => '01',
                        'ene' => '01',
                        'febrero' => '02',
                        'feb' => '02',
                        'marzo' => '03',
                        'mar' => '03',
                        'abril' => '04',
                        'abr' => '04',
                        'mayo' => '05',
                        'mayo' => '05',
                        'junio' => '06',
                        'jun' => '06',
                        'julio' => '07',
                        'jul' => '07',
                        'agosto' => '08',
                        'ago' => '08',
                        'septiembre' => '09',
                        'sep' => '09',
                        'octubre' => '10',
                        'oct' => '10',
                        'noviembre' => '11',
                        'nov' => '11',
                        'diciembre' => '12',
                        'dic' => '12',
                    ];

                    foreach ($monthMap as $monthName => $monthNum) {
                        $normalizedMonthQuery = str_replace($monthName, $monthNum, $normalizedMonthQuery);
                    }

                    $normalizedMonthQuery = str_replace([' de ', ' del ', ' del año '], ' ', $normalizedMonthQuery);
                    $normalizedMonthQuery = trim(preg_replace('/\s+/', ' ', $normalizedMonthQuery));

                    try {
                        $date = Carbon::createFromFormat('d m Y', $normalizedMonthQuery);
                    } catch (\Exception $e) {
                        if (!$date) {
                            try {
                                $normalizedMonthQuery = str_ireplace(['a.m.', 'p.m.', 'a m', 'p m'], ['am', 'pm', 'am', 'pm'], $normalizedMonthQuery);
                                $date = Carbon::createFromFormat('d m Y H:i', $normalizedMonthQuery);
                            } catch (\Exception | \InvalidArgumentException $e) { // Agrega InvalidArgumentException
                                try {
                                    $date = Carbon::createFromFormat('d m Y h:i a', $normalizedMonthQuery);
                                } catch (\Exception $e) {
                                    // Fallback final si nada de lo anterior funcionó para formatos con mes
                                }
                            }
                        }
                    }
                }

                if ($date) {
                    if ($date->year > 1900 && $date->year < 2100) {
                        $q->orWhereDate('created_at', $date->toDateString()); // q en lugar de q2
                    }
                }
                Carbon::setLocale($originalLocale);
                // --- FIN DE LA NUEVA MODIFICACIÓN PARA FECHAS ---
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
                $sqlNormalize = function ($column) {
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
