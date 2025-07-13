<?php

namespace App\Services;

use App\Models\Noticia;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\User;
use Illuminate\Support\Str;

class NoticiaService
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

    public function obtenerNoticiaFiltradas(Request $request)
    {
        $perPage = in_array($request->input('per_page'), [5, 10, 25, 50, 100])
            ? $request->input('per_page')
            : 10;

        $searchQuery   = $request->input('q', '');
        $estadoFilter  = $request->input('estado', ''); // Mantener para el filtro de estado si aún se usa
        $sortBy        = $request->input('sort_by');    // Columna para ordenar
        $sortDirection = $request->input('sort_direction'); // Dirección de ordenamiento

        $noticias = Noticia::query();

        // Cargar la relación 'user' para que la vista pueda acceder a $noticia->user->name
        $noticias->with('user');

        // Bandera para saber si ya hicimos el join con la tabla de usuarios
        $joinedUsersTable = false;

        // Si se va a ordenar por 'creador', hacemos el join y seleccionamos el nombre.
        // No lo hacemos para la búsqueda en este punto, ya que la búsqueda 'orWhereHas' es más flexible.
        if ($sortBy === 'creador') {
            $noticias->leftJoin('users', 'noticias.user_id', '=', 'users.id')
                ->select('noticias.*', 'users.name as creador_name');
            $joinedUsersTable = true;
        } else {
            // Asegura que se seleccionen las columnas de noticias si no hay un join con select
            $noticias->select('noticias.*');
        }

        // Búsqueda robusta general en múltiples columnas: Creador, Autor, Tipo, Titulo, Fecha, Estado
        if (!empty($searchQuery)) {
            $cleanedSearchQuery = $this->cleanSearchQuery($searchQuery);

            $noticias->where(function ($q2) use ($cleanedSearchQuery, $searchQuery, $joinedUsersTable) {
                $sqlNormalize = function ($column) {
                    if (config('database.default') === 'pgsql' && in_array($column, ['created_at', 'updated_at'])) {
                        return "REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(LOWER(TO_CHAR({$column}, 'YYYY-MM-DD HH24:MI:SS')), 'á', 'a'), 'é', 'e'), 'í', 'i'), 'ó', 'o'), 'ú', 'u'), 'ü', 'u'), 'ñ', 'n'), '.', ''), '-', '')";
                    }
                    return "REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(LOWER({$column}), 'á', 'a'), 'é', 'e'), 'í', 'i'), 'ó', 'o'), 'ú', 'u'), 'ü', 'u'), 'ñ', 'n'), '.', ''), '-', '')";
                };

                $q2->orWhereRaw($sqlNormalize('tipo') . ' LIKE ?', ['%' . $cleanedSearchQuery . '%'])
                    ->orWhereRaw($sqlNormalize('titulo') . ' LIKE ?', ['%' . $cleanedSearchQuery . '%'])
                    ->orWhereRaw($sqlNormalize('autor') . ' LIKE ?', ['%' . $cleanedSearchQuery . '%']);

                // Búsqueda por creador (a través de la relación, más flexible que un join directo para la búsqueda)
                $q2->orWhereHas('user', function ($userQuery) use ($cleanedSearchQuery) {
                    $userQuery->whereRaw("REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(LOWER(name), 'á', 'a'), 'é', 'e'), 'í', 'i'), 'ó', 'o'), 'ú', 'u'), 'ü', 'u'), 'ñ', 'n'), '.', ''), '-', '') LIKE ?", ['%' . $cleanedSearchQuery . '%']);
                });

                // Búsqueda por estado (si se desea buscar por texto en el campo de búsqueda general)
                $q2->orWhereRaw($sqlNormalize('estado') . ' LIKE ?', ['%' . $cleanedSearchQuery . '%']);


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
                        'enero' => '01', 'ene' => '01',
                        'febrero' => '02', 'feb' => '02',
                        'marzo' => '03', 'mar' => '03',
                        'abril' => '04', 'abr' => '04',
                        'mayo' => '05', 'may' => '05',
                        'junio' => '06', 'jun' => '06',
                        'julio' => '07', 'jul' => '07',
                        'agosto' => '08', 'ago' => '08',
                        'septiembre' => '09', 'sep' => '09',
                        'octubre' => '10', 'oct' => '10',
                        'noviembre' => '11', 'nov' => '11',
                        'diciembre' => '12', 'dic' => '12',
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
                            } catch (\Exception $e) {
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
                        $q2->orWhereDate('created_at', $date->toDateString());
                    }
                }
                Carbon::setLocale($originalLocale);
                // --- FIN DE LA MODIFICACIÓN DE FECHAS ---
            });
        }

        // Aplicar filtro por estado (si el parámetro 'estado' todavía se usa para filtrar)
        if (!empty($estadoFilter) && in_array($estadoFilter, ['aprobado', 'pendiente', 'rechazado'])) {
            $noticias->where('estado', $estadoFilter);
        }

        // Ordenamiento genérico por columna y dirección
        $allowedSortColumns = [
            'creador',
            'autor',
            'tipo',
            'titulo',
            'created_at',
            'estado',
        ];

        $allowedSortDirections = ['asc', 'desc'];

        if (in_array($sortBy, $allowedSortColumns) && in_array($sortDirection, $allowedSortDirections)) {
            // Si se ordena por 'creador', usamos el alias 'creador_name' del join
            if ($sortBy === 'creador') {
                $noticias->orderBy('creador_name', $sortDirection);
            } else {
                $noticias->orderBy($sortBy, $sortDirection);
            }
        } else {
            $noticias->orderBy('created_at', 'desc');
        }

        return $noticias->paginate($perPage)->withQueryString();
    }

    /**
     * Obtiene noticias filtradas para exportación (sin paginación).
     *
     * @param Request $request La solicitud HTTP con los parámetros de filtro.
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function obtenerNoticiaFiltradasParaExportar(Request $request)
    {
        $searchQuery   = $request->input('q', '');
        $estadoFilter  = $request->input('estado', '');
        $sortBy        = $request->input('sort_by');
        $sortDirection = $request->input('sort_direction');

        $noticias = Noticia::query();

        // Cargar la relación 'user' para el creador
        $noticias->with('user');

        // Búsqueda robusta general en múltiples columnas: Creador, Autor, Tipo, Titulo, Fecha, Estado
        if (!empty($searchQuery)) {
            $cleanedSearchQuery = $this->cleanSearchQuery($searchQuery);

            $noticias->where(function ($q2) use ($cleanedSearchQuery, $searchQuery) {
                $sqlNormalize = function ($column) {
                    if (config('database.default') === 'pgsql' && in_array($column, ['created_at', 'updated_at'])) {
                        return "REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(LOWER(TO_CHAR({$column}, 'YYYY-MM-DD HH24:MI:SS')), 'á', 'a'), 'é', 'e'), 'í', 'i'), 'ó', 'o'), 'ú', 'u'), 'ü', 'u'), 'ñ', 'n'), '.', ''), '-', '')";
                    }
                    return "REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(LOWER({$column}), 'á', 'a'), 'é', 'e'), 'í', 'i'), 'ó', 'o'), 'ú', 'u'), 'ü', 'u'), 'ñ', 'n'), '.', ''), '-', '')";
                };

                $q2->orWhereRaw($sqlNormalize('tipo') . ' LIKE ?', ['%' . $cleanedSearchQuery . '%'])
                    ->orWhereRaw($sqlNormalize('titulo') . ' LIKE ?', ['%' . $cleanedSearchQuery . '%'])
                    ->orWhereRaw($sqlNormalize('autor') . ' LIKE ?', ['%' . $cleanedSearchQuery . '%']);

                // Búsqueda por creador (requiere join o relación cargada)
                $q2->orWhereHas('user', function ($userQuery) use ($cleanedSearchQuery) {
                    $userQuery->whereRaw("REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(LOWER(name), 'á', 'a'), 'é', 'e'), 'í', 'i'), 'ó', 'o'), 'ú', 'u'), 'ü', 'u'), 'ñ', 'n'), '.', ''), '-', '') LIKE ?", ['%' . $cleanedSearchQuery . '%']);
                });

                // Búsqueda por fecha de creación (created_at)
                try {
                    $date = Carbon::parse($searchQuery);
                    $q2->orWhereDate('created_at', $date->toDateString());
                } catch (\Exception $e) {
                    // No hace nada si la fecha no es válida
                }

                // Búsqueda por estado
                $q2->orWhereRaw($sqlNormalize('estado') . ' LIKE ?', ['%' . $cleanedSearchQuery . '%']);
            });
        }

        // Aplicar filtro por estado
        if (!empty($estadoFilter) && in_array($estadoFilter, ['aprobado', 'pendiente', 'rechazado'])) {
            $noticias->where('estado', $estadoFilter);
        }

        // Ordenamiento genérico por columna y dirección
        $allowedSortColumns = [
            'tipo',
            'titulo',
            'autor',
            'creador', // Mapeará a 'users.name' para la ordenación
            'estado',
            'created_at',
        ];

        $allowedSortDirections = ['asc', 'desc'];

        if (in_array($sortBy, $allowedSortColumns) && in_array($sortDirection, $allowedSortDirections)) {
            // Si se ordena por 'creador', necesitamos un join para acceder a 'users.name'
            if ($sortBy === 'creador') {
                // Asegurarse de que el join se haga solo una vez si ya se hizo para la búsqueda
                $noticias->leftJoin('users', 'noticias.user_id', '=', 'users.id')
                    ->orderBy('users.name', $sortDirection)
                    ->select('noticias.*'); // Asegura que solo se seleccionen columnas de noticias
            } else {
                $noticias->orderBy($sortBy, $sortDirection);
            }
        } else {
            // Ordenamiento por defecto si no hay un orden válido o si no se especifica
            $noticias->orderBy('created_at', 'desc');
        }

        // No paginar, obtener todos los resultados
        return $noticias->get();
    }
}
