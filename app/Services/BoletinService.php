<?php

namespace App\Services;

use App\Models\Boletin; // Asegúrate de que el modelo Boletin esté importado
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon; // Necesario para trabajar con fechas

class BoletinService
{
    /**
     * Limpia el texto de búsqueda para una comparación robusta.
     * Convierte a minúsculas, elimina tildes y caracteres especiales.
     *
     * @param string $text El texto a limpiar.
     * @return string El texto limpio.
     */
    private function cleanSearchQuery(string $text): string
    {
        $text = mb_strtolower($text, 'UTF-8');
        $text = str_replace(
            ['á', 'é', 'í', 'ó', 'ú', 'ü', 'ñ', 'Á', 'É', 'Í', 'Ó', 'Ú', 'Ü', 'Ñ'],
            ['a', 'e', 'i', 'o', 'u', 'u', 'u', 'n', 'A', 'E', 'I', 'O', 'U', 'U', 'N'], // Corregido 'ñ' a 'n' en mayúscula
            $text
        );
        $text = preg_replace('/[^a-z0-9\s]/', '', $text);
        $text = trim(preg_replace('/\s+/', ' ', $text));
        return $text;
    }

    /**
     * Obtiene boletines filtrados con búsqueda robusta por nombre, descripción y fecha de creación,
     * y filtros adicionales por estado y ordenamiento por precios.
     *
     * @param Request $request La solicitud HTTP con los parámetros de filtro.
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function obtenerBoletinFiltrados(Request $request)
    {
        // El per_page se tomará del request si se envía, de lo contrario, se usará 10.
        // Esto es compatible con la paginación de Laravel que se renderiza en Blade.
        $perPage = in_array($request->input('per_page'), [5, 10, 25, 50, 100])
            ? $request->input('per_page')
            : 10;

        $query  = $request->input('q');
        $estado = $request->input('estado');
        $sortBy        = $request->input('sort_by'); // Nuevo: columna para ordenar
        $sortDirection = $request->input('sort_direction'); // Nuevo: dirección de ordenamiento

        $boletines = Boletin::query();

        // Cargar la relación 'user' para el creador
        $boletines->with('user');

        // Búsqueda robusta por nombre, descripción y fecha de creación
        if ($query) {
            $cleanedQuery = $this->cleanSearchQuery($query);

            $boletines->where(function ($q2) use ($cleanedQuery, $query) {
                $sqlNormalize = function ($column) {
                    if (config('database.default') === 'pgsql' && in_array($column, ['created_at', 'updated_at'])) {
                        return "REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(LOWER(TO_CHAR({$column}, 'YYYY-MM-DD HH24:MI:SS')), 'á', 'a'), 'é', 'e'), 'í', 'i'), 'ó', 'o'), 'ú', 'u'), 'ü', 'u'), 'ñ', 'n'), '.', ''), '-', '')";
                    }
                    return "REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(LOWER({$column}), 'á', 'a'), 'é', 'e'), 'í', 'i'), 'ó', 'o'), 'ú', 'u'), 'ü', 'u'), 'ñ', 'n'), '.', ''), '-', '')";
                };

                $q2->whereRaw($sqlNormalize('nombre') . ' LIKE ?', ['%' . $cleanedQuery . '%'])
                    ->orWhereRaw($sqlNormalize('descripcion') . ' LIKE ?', ['%' . $cleanedQuery . '%']);

                // --- INICIO DE LA MODIFICACIÓN REFINADA ---
                $date = null; // Variable para almacenar la fecha parseada
                $originalQuery = trim(mb_strtolower($query, 'UTF-8')); // Convertir a minúsculas para consistencia

                $originalLocale = Carbon::getLocale();
                // Establecer explícitamente el locale a español para el parseo
                Carbon::setLocale('es');

                // 1. Intentar parsear formatos numéricos comunes (YYYY-MM-DD y DD/MM/YYYY)
                // Usamos createFromFormat porque es estricto y nos da control
                try {
                    // Intento para YYYY-MM-DD o YYYY/MM/DD (que ya te funciona)
                    if (preg_match('/^\d{4}[\/\-]\d{2}[\/\-]\d{2}$/', $originalQuery)) {
                        $date = Carbon::createFromFormat('Y-m-d', str_replace('/', '-', $originalQuery));
                    }
                    // Intento para DD/MM/YYYY o DD-MM-YYYY (el que no te funciona)
                    elseif (preg_match('/^\d{2}[\/\-]\d{2}[\/\-]\d{4}$/', $originalQuery)) {
                        $date = Carbon::createFromFormat('d-m-Y', str_replace('/', '-', $originalQuery));
                    }
                    // Intento para MM/DD/YYYY (el que te funciona, para asegurar)
                    elseif (preg_match('/^\d{2}[\/\-]\d{2}[\/\-]\d{4}$/', $originalQuery)) {
                        // Carbon es flexible, pero si necesitamos un orden específico
                        // este es un fallback para el orden MM/DD/YYYY
                        $date = Carbon::createFromFormat('m-d-Y', str_replace('/', '-', $originalQuery));
                    }
                } catch (\Exception $e) {
                    // No hace nada, se intentará el siguiente método
                }

                // 2. Si no se encontró, intentar parsear formatos con nombres de mes en español
                if (!$date) {
                    $normalizedMonthQuery = $originalQuery;

                    // Mapeo de nombres de meses en español (completos y abreviados) a números
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
                        'may' => '05',
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
                        // Reemplazar el nombre del mes por su número
                        $normalizedMonthQuery = str_replace($monthName, $monthNum, $normalizedMonthQuery);
                    }

                    // Limpiar "de", "del" y "del año" para simplificar la cadena para Carbon
                    $normalizedMonthQuery = str_replace([' de ', ' del ', ' del año '], ' ', $normalizedMonthQuery);
                    // Eliminar múltiples espacios y trim
                    $normalizedMonthQuery = trim(preg_replace('/\s+/', ' ', $normalizedMonthQuery));

                    // Intentar parsear el formato "Día MesNum Año" (ej. 12 07 2025)
                    try {
                        // Carbon tiene problemas con DD MM YYYY cuando el mes es número sin guiones/barras
                        // Forzamos un formato más estricto
                        $date = Carbon::createFromFormat('d m Y', $normalizedMonthQuery);
                    } catch (\Exception $e) {
                        // No hace nada, se intentará el siguiente
                    }

                    // Intentar parsear si el usuario incluyó la hora (ej. 12 07 2025 02:06 pm)
                    if (!$date) {
                        try {
                            $normalizedMonthQuery = str_ireplace(['a.m.', 'p.m.', 'a m', 'p m'], ['am', 'pm', 'am', 'pm'], $normalizedMonthQuery);
                            $date = Carbon::createFromFormat('d m Y H:i', $normalizedMonthQuery); // Para 24 horas
                        } catch (\Exception $e) {
                            try {
                                $date = Carbon::createFromFormat('d m Y h:i a', $normalizedMonthQuery); // Para 12 horas AM/PM
                            } catch (\Exception $e) {
                                // No hace nada
                            }
                        }
                    }
                }

                // Aplicar el filtro si se encontró una fecha válida
                if ($date) {
                    // Verificar que la fecha sea razonable (ej. no del año 0 o 1)
                    // Ajusta este rango según tus datos, ej. si tus boletines no son de antes del 2000
                    if ($date->year > 1900 && $date->year < 2100) {
                        $q2->orWhereDate('created_at', $date->toDateString());
                    }
                }
                // Restaurar el locale original de Carbon
                Carbon::setLocale($originalLocale);
                // --- FIN DE LA NUEVA MODIFICACIÓN PARA FECHAS ---
            });
        }

        // Filtro por estado
        if (in_array($estado, ['aprobado', 'pendiente', 'rechazado'])) {
            $boletines->where('estado', $estado);
        }

        // Ordenamiento genérico por columna y dirección
        $allowedSortColumns = [
            'nombre',
            'descripcion',
            'created_at',
            'estado',
            'precio_mas_alto',
            'precio_mas_bajo',
        ];

        $allowedSortDirections = ['asc', 'desc'];

        if (in_array($sortBy, $allowedSortColumns) && in_array($sortDirection, $allowedSortDirections)) {
            $boletines->orderBy($sortBy, $sortDirection);
        } else {
            // Ordenamiento por defecto si no hay un orden válido o si no se especifica
            $boletines->orderBy('created_at', 'desc');
        }

        // Cargar relaciones necesarias para las vistas de la tabla
        $boletines->with(['user.roles', 'validador', 'rechazador']);

        return $boletines->paginate($perPage)->withQueryString();
    }

    /**
     * Obtiene boletines filtrados para exportación (sin paginación).
     *
     * @param Request $request La solicitud HTTP con los parámetros de filtro.
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function obtenerBoletinFiltradosParaExportar(Request $request)
    {
        $query         = $request->input('q');
        $estado        = $request->input('estado');
        $sortBy        = $request->input('sort_by');
        $sortDirection = $request->input('sort_direction');

        $boletines = Boletin::query();

        // Cargar la relación 'user' para el creador
        $boletines->with('user');

        // Búsqueda robusta por nombre, descripción y fecha de creación
        if ($query) {
            $cleanedQuery = $this->cleanSearchQuery($query);

            $boletines->where(function ($q2) use ($cleanedQuery, $query) {
                $sqlNormalize = function ($column) {
                    if (config('database.default') === 'pgsql' && in_array($column, ['created_at', 'updated_at'])) {
                        return "REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(LOWER(TO_CHAR({$column}, 'YYYY-MM-DD HH24:MI:SS')), 'á', 'a'), 'é', 'e'), 'í', 'i'), 'ó', 'o'), 'ú', 'u'), 'ü', 'u'), 'ñ', 'n'), '.', ''), '-', '')";
                    }
                    return "REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(LOWER({$column}), 'á', 'a'), 'é', 'e'), 'í', 'i'), 'ó', 'o'), 'ú', 'u'), 'ü', 'u'), 'ñ', 'n'), '.', ''), '-', '')";
                };

                $q2->whereRaw($sqlNormalize('nombre') . ' LIKE ?', ['%' . $cleanedQuery . '%'])
                    ->orWhereRaw($sqlNormalize('descripcion') . ' LIKE ?', ['%' . $cleanedQuery . '%']);

                try {
                    $date = Carbon::parse($query);
                    $q2->orWhereDate('created_at', $date->toDateString());
                } catch (\Exception $e) {
                    // No hace nada si la fecha no es válida
                }
            });
        }

        // Filtro por estado
        if (in_array($estado, ['aprobado', 'pendiente', 'rechazado'])) {
            $boletines->where('estado', $estado);
        }

        // Ordenamiento genérico por columna y dirección
        $allowedSortColumns = [
            'nombre',
            'descripcion',
            'created_at',
            'estado',
            'precio_mas_alto',
            'precio_mas_bajo',
        ];

        $allowedSortDirections = ['asc', 'desc'];

        if (in_array($sortBy, $allowedSortColumns) && in_array($sortDirection, $allowedSortDirections)) {
            $boletines->orderBy($sortBy, $sortDirection);
        } else {
            $boletines->orderBy('created_at', 'desc');
        }

        // No paginar, obtener todos los resultados
        return $boletines->get();
    }
}
