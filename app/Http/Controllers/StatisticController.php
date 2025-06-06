<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

// Asegura que la localización de Carbon sea global para el contexto del controlador
Carbon::setLocale(App::getLocale());

class StatisticController extends Controller
{
    /**
     * Muestra la página del dashboard con los filtros y la gráfica.
     * Este método se encarga de cargar la vista inicial y los años disponibles.
     */
    public function showDashboardPage(Request $request)
    {
        $currentYear = Carbon::now()->year;
        $availableYears = [];

        // Obtener el año más antiguo de registros en la tabla 'visits' (o la que uses para visitas)
        // Asegúrate que esta tabla exista y tenga la columna 'created_at'.
        $firstRecordYear = DB::table('visits')->min(DB::raw('EXTRACT(YEAR FROM created_at)'));

        // El año de inicio para el selector: mínimo 2020 o el año del primer registro si es anterior
        $startYear = max(2020, $firstRecordYear ?? $currentYear);

        // Generar años desde el "startYear" hasta el año actual + 1
        for ($year = $startYear; $year <= ($currentYear + 1); $year++) {
            $availableYears[] = (string)$year;
        }

        // Asegurarse de que el año actual y el siguiente estén siempre disponibles
        if (!in_array((string)$currentYear, $availableYears)) {
            $availableYears[] = (string)$currentYear;
        }
        if (!in_array((string)($currentYear + 1), $availableYears)) {
            $availableYears[] = (string)($currentYear + 1);
        }
        sort($availableYears); // Ordenar para que estén en orden ascendente

        $selectedYear = $request->input('year', $currentYear); // Por defecto, el año actual
        $selectedFilter = $request->input('filter', 'ultimos3dias'); // Por defecto, el filtro a 'ultimos3dias'

        // Pasa $boletines aunque sea una colección vacía si no la usas en la vista para evitar errores
        $boletines = collect();

        return view('dashboard', compact('availableYears', 'selectedYear', 'selectedFilter', 'boletines'));
    }

    /**
     * Este método obtiene los datos para la gráfica y las métricas,
     * basado en los filtros enviados desde el frontend.
     */
    public function getStatistics(Request $request)
    {
        try {
            // Asegura la localización para Carbon y funciones de fecha en PHP
            setlocale(LC_TIME, 'es_ES.UTF-8');
            Carbon::setLocale('es');

            $filtro = $request->query('filtro', 'ultimos3dias'); // Filtro principal
            $selectedYear = (int)$request->query('year', Carbon::now()->year); // Año seleccionado del Flatpickr
            // Sub-filtro para el 'año': month, week, day, hour (por defecto 'month' si no se especifica)
            $chartSubFilter = $request->query('chartFilter', 'month');

            $vistas = collect(); // Colección para los datos de la gráfica (grupo y total)
            $inicio = null;
            $fin = null;

            // Fecha actual en el contexto del año seleccionado para filtros como 'ultimos3dias', 'semana', 'mes'
            // Esto permite que si seleccionas 2024, "últimos 3 días" se refiera a los últimos 3 días de 2024.
            $currentDateForSelectedYear = Carbon::create(
                $selectedYear,
                Carbon::now()->month,
                Carbon::now()->day,
                Carbon::now()->hour,
                Carbon::now()->minute,
                Carbon::now()->second
            );

            // Consulta base para las visitas (¡Asegúrate que 'visits' sea tu tabla real!)
            $baseQuery = DB::table('visits');

            switch ($filtro) {
                case 'ultimos3dias':
                    // Rango: los últimos 3 días (incluyendo hoy) en el contexto del año seleccionado
                    $fin = $currentDateForSelectedYear->copy()->endOfDay();
                    $inicio = $fin->copy()->subDays(2)->startOfDay(); // 3 días: hoy, ayer, anteayer

                    $dbDateFormatter = (env('DB_CONNECTION') === 'pgsql') ? "TO_CHAR(created_at, 'YYYY-MM-DD')" : "DATE_FORMAT(created_at, '%Y-%m-%d')";

                    $tempVistas = [];
                    for ($i = 2; $i >= 0; $i--) {
                        $date = $fin->copy()->subDays($i);
                        $formattedDateKey = $date->toDateString(); // Clave 'YYYY-MM-DD' para el array asociativo
                        $tempVistas[$formattedDateKey] = (object)['grupo' => $date->translatedFormat('l'), 'total' => 0]; // Ej: "viernes"
                    }

                    $results = $baseQuery->whereBetween('created_at', [$inicio, $fin])
                                         ->select(DB::raw("$dbDateFormatter AS date_key"), DB::raw('COUNT(*) AS total'))
                                         ->groupBy(DB::raw($dbDateFormatter))
                                         ->get();

                    foreach ($results as $item) {
                        if (isset($tempVistas[$item->date_key])) {
                            $tempVistas[$item->date_key]->total = $item->total;
                        }
                    }
                    $vistas = collect(array_values($tempVistas)); // Convertir a array indexado numéricamente
                    break;

                case 'semana':
                    // Rango: 7 días terminando en la baseDateForFiltering
                    $fin = $currentDateForSelectedYear->copy()->endOfDay();
                    $inicio = $fin->copy()->subDays(6)->startOfDay(); // 7 días: hoy y 6 anteriores

                    $dbDateFormatter = (env('DB_CONNECTION') === 'pgsql') ? "TO_CHAR(created_at, 'YYYY-MM-DD')" : "DATE_FORMAT(created_at, '%Y-%m-%d')";

                    $tempVistas = [];
                    for ($i = 6; $i >= 0; $i--) {
                        $date = $fin->copy()->subDays($i);
                        $formattedDateKey = $date->toDateString(); // Clave 'YYYY-MM-DD'
                        $tempVistas[$formattedDateKey] = (object)['grupo' => $date->translatedFormat('D d M'), 'total' => 0]; // Ej: "Vie 06 Jun"
                    }

                    $results = $baseQuery->whereBetween('created_at', [$inicio, $fin])
                                         ->select(DB::raw("$dbDateFormatter AS date_key"), DB::raw('COUNT(*) AS total'))
                                         ->groupBy(DB::raw($dbDateFormatter))
                                         ->get();

                    foreach ($results as $item) {
                        if (isset($tempVistas[$item->date_key])) {
                             $tempVistas[$item->date_key]->total = $item->total;
                        }
                    }
                    $vistas = collect(array_values($tempVistas));
                    break;

                case 'mes':
                    // Rango: El mes completo de la baseDateForFiltering
                    $inicio = $currentDateForSelectedYear->copy()->startOfMonth();
                    $fin = $currentDateForSelectedYear->copy()->endOfMonth();

                    // Agrupar por semana del mes.
                    // ATENCIÓN: WEEKOFYEAR en MySQL puede ser inconsistente sin un modo específico.
                    // Para MySQL, se recomienda 'WEEK(created_at, 3)' para ISO-8601 (lunes como primer día de la semana).
                    // Pero para agrupar semanas del mes, la lógica que tenías era compleja.
                    // Es más simple agrupar por día y luego el frontend puede interpretarlo si lo necesitas semanal.
                    $dbWeekFormatter = (env('DB_CONNECTION') === 'pgsql') ? 'FLOOR((EXTRACT(DAY FROM created_at) - 1) / 7) + 1' : 'CEIL(DAY(created_at) / 7)';

                    $results = $baseQuery->whereBetween('created_at', [$inicio, $fin])
                                         ->select(DB::raw("$dbWeekFormatter AS grupo"), DB::raw('COUNT(*) AS total'))
                                         ->groupBy(DB::raw($dbWeekFormatter))
                                         ->orderBy('grupo') // Asegura el orden por semana del mes
                                         ->get();

                    // Rellenar las semanas faltantes
                    $weeksInMonth = (int)ceil($currentDateForSelectedYear->daysInMonth / 7);
                    $tempVistas = [];
                    for ($i = 1; $i <= $weeksInMonth; $i++) {
                        $tempVistas[$i] = (object)['grupo' => 'Semana ' . $i, 'total' => 0];
                    }

                    foreach ($results as $item) {
                        if (isset($tempVistas[$item->grupo])) { // $item->grupo será el número de semana
                            $tempVistas[$item->grupo]->total = $item->total;
                        }
                    }
                    $vistas = collect(array_values($tempVistas));
                    break;

                case 'año':
                    // Lógica para el filtro 'año', usando el sub-filtro $chartSubFilter
                    $inicio = Carbon::create($selectedYear, 1, 1)->startOfYear();
                    $fin = Carbon::create($selectedYear, 12, 31)->endOfYear();

                    $currentYear = Carbon::now()->year;
                    $currentMonth = Carbon::now()->month;
                    $currentWeekOfYear = Carbon::now()->weekOfYear;
                    $currentDayOfYear = Carbon::now()->dayOfYear;
                    $currentHour = Carbon::now()->hour;

                    switch ($chartSubFilter) {
                        case 'month':
                            $dbMonthFormatter = (env('DB_CONNECTION') === 'pgsql') ? "EXTRACT(MONTH FROM created_at)" : "MONTH(created_at)";

                            $results = $baseQuery->whereYear('created_at', $selectedYear)
                                                 ->select(DB::raw("$dbMonthFormatter AS month_num"), DB::raw('COUNT(*) AS total'))
                                                 ->groupBy(DB::raw($dbMonthFormatter))
                                                 ->orderBy('month_num')
                                                 ->get();

                            $tempVistas = [];
                            $monthsToIterate = 12; // Por defecto 12 meses
                            if ($selectedYear === $currentYear) {
                                $monthsToIterate = $currentMonth; // Hasta el mes actual
                            } elseif ($selectedYear > $currentYear) {
                                $monthsToIterate = 0; // Si es un año futuro, no hay datos aún
                            }

                            for ($i = 1; $i <= $monthsToIterate; $i++) {
                                $monthName = Carbon::create(null, $i, 1)->translatedFormat('F'); // Nombre completo del mes
                                $tempVistas[$i] = (object)['grupo' => ucfirst($monthName), 'total' => 0];
                            }

                            foreach ($results as $item) {
                                if (isset($tempVistas[$item->month_num])) {
                                    $tempVistas[$item->month_num]->total = $item->total;
                                }
                            }
                            $vistas = collect(array_values($tempVistas));
                            break;

                        case 'week':
                            $dbWeekFormatter = (env('DB_CONNECTION') === 'pgsql') ? "EXTRACT(WEEK FROM created_at)" : "WEEK(created_at, 3)"; // WEEK(date, 3) for ISO-8601 weeks

                            $results = $baseQuery->whereYear('created_at', $selectedYear)
                                                 ->select(DB::raw("$dbWeekFormatter AS week_num"), DB::raw('COUNT(*) AS total'))
                                                 ->groupBy(DB::raw($dbWeekFormatter))
                                                 ->orderBy('week_num')
                                                 ->get();

                            $tempVistas = [];
                            $weeksInSelectedYear = (new Carbon("{$selectedYear}-12-31"))->weekOfYear; // Total de semanas en el año seleccionado
                            $weeksToIterate = $weeksInSelectedYear; // Por defecto todas las semanas
                            if ($selectedYear === $currentYear) {
                                $weeksToIterate = $currentWeekOfYear; // Hasta la semana actual
                            } elseif ($selectedYear > $currentYear) {
                                $weeksToIterate = 0; // Si es un año futuro, no hay datos aún
                            }

                            for ($i = 1; $i <= $weeksToIterate; $i++) {
                                $tempVistas[$i] = (object)['grupo' => 'Semana ' . $i, 'total' => 0];
                            }

                            foreach ($results as $item) {
                                if (isset($tempVistas[$item->week_num])) {
                                    $tempVistas[$item->week_num]->total = $item->total;
                                }
                            }
                            $vistas = collect(array_values($tempVistas));
                            break;

                        case 'day':
                            $dbDayFormatter = (env('DB_CONNECTION') === 'pgsql') ? "TO_CHAR(created_at, 'YYYY-MM-DD')" : "DATE_FORMAT(created_at, '%Y-%m-%d')";

                            $results = $baseQuery->whereYear('created_at', $selectedYear)
                                                 ->select(DB::raw("$dbDayFormatter AS date_key"), DB::raw('COUNT(*) AS total'))
                                                 ->groupBy(DB::raw($dbDayFormatter))
                                                 ->orderBy('date_key')
                                                 ->get();

                            $tempVistas = [];
                            $daysInSelectedYear = Carbon::create($selectedYear, 1, 1)->daysInYear;
                            $daysToIterate = $daysInSelectedYear; // Por defecto todos los días
                            if ($selectedYear === $currentYear) {
                                $daysToIterate = $currentDayOfYear; // Hasta el día actual del año
                            } elseif ($selectedYear > $currentYear) {
                                $daysToIterate = 0; // Si es un año futuro, no hay datos aún
                            }

                            for ($i = 0; $i < $daysToIterate; $i++) {
                                $date = Carbon::create($selectedYear, 1, 1)->addDays($i);
                                $formattedDateKey = $date->toDateString();
                                $tempVistas[$formattedDateKey] = (object)['grupo' => $date->translatedFormat('d M'), 'total' => 0]; // Ej: "01 Ene", "06 Jun"
                            }

                            foreach ($results as $item) {
                                if (isset($tempVistas[$item->date_key])) {
                                    $tempVistas[$item->date_key]->total = $item->total;
                                }
                            }
                            $vistas = collect(array_values($tempVistas));
                            break;

                        case 'hour':
                            // Para 'hour', se asume que se quiere ver las horas del *día actual* del *año seleccionado*
                            $targetDateForHours = Carbon::create($selectedYear, Carbon::now()->month, Carbon::now()->day);

                            // Si el año seleccionado es futuro, o si es el año actual pero el día objetivo es futuro, no hay horas.
                            $hoursToIterate = 24; // Por defecto todas las horas
                            if ($selectedYear > $currentYear || ($selectedYear === $currentYear && $targetDateForHours->gt(Carbon::now()))) {
                                $hoursToIterate = 0;
                            } elseif ($targetDateForHours->isToday()) {
                                $hoursToIterate = $currentHour + 1; // Hasta la hora actual de hoy
                            }

                            $dbHourFormatter = (env('DB_CONNECTION') === 'pgsql') ? "EXTRACT(HOUR FROM created_at)" : "HOUR(created_at)";

                            $results = $baseQuery->whereYear('created_at', $selectedYear)
                                                 ->whereDate('created_at', $targetDateForHours->toDateString())
                                                 ->select(DB::raw("$dbHourFormatter AS hour_num"), DB::raw('COUNT(*) AS total'))
                                                 ->groupBy(DB::raw($dbHourFormatter))
                                                 ->orderBy('hour_num')
                                                 ->get();

                            $tempVistas = [];
                            for ($i = 0; $i < $hoursToIterate; $i++) {
                                $tempVistas[$i] = (object)['grupo' => sprintf('%02d:00', $i), 'total' => 0];
                            }

                            foreach ($results as $item) {
                                if (isset($tempVistas[$item->hour_num])) {
                                    $tempVistas[$item->hour_num]->total = $item->total;
                                }
                            }
                            $vistas = collect(array_values($tempVistas));
                            break;
                    }
                    break;

                default: // Fallback si no se reconoce el filtro
                    // Se usa la misma lógica que 'ultimos3dias' como un default seguro
                    $fin = $currentDateForSelectedYear->copy()->endOfDay();
                    $inicio = $fin->copy()->subDays(2)->startOfDay();
                    $dbDateFormatter = (env('DB_CONNECTION') === 'pgsql') ? "TO_CHAR(created_at, 'YYYY-MM-DD')" : "DATE_FORMAT(created_at, '%Y-%m-%d')";

                    $tempVistas = [];
                    for ($i = 2; $i >= 0; $i--) {
                        $date = $fin->copy()->subDays($i);
                        $formattedDateKey = $date->toDateString();
                        $tempVistas[$formattedDateKey] = (object)['grupo' => $date->translatedFormat('l'), 'total' => 0];
                    }

                    $results = $baseQuery->whereBetween('created_at', [$inicio, $fin])
                                         ->select(DB::raw("$dbDateFormatter AS date_key"), DB::raw('COUNT(*) AS total'))
                                         ->groupBy(DB::raw("$dbDateFormatter"))
                                         ->get();

                    foreach ($results as $item) {
                        if (isset($tempVistas[$item->date_key])) {
                            $tempVistas[$item->date_key]->total = $item->total;
                        }
                    }
                    $vistas = collect(array_values($tempVistas));
                    break;
            }

            // Métricas generales: Estas son las métricas que aparecen en las tarjetas
            // Asegúrate de que las tablas y columnas sean correctas (ej. 'users', 'last_login_at', 'is_online')
            // Se mantienen filtradas por el rango $inicio y $fin, si quieres que sean globales, quita los whereBetween
            $usuarios = DB::table('users')->count(); // Total de usuarios en la BD
            $registrados = DB::table('users')->whereBetween('created_at', [$inicio, $fin])->count();
            $activos = DB::table('users')->whereBetween('last_login_at', [$inicio, $fin])->count();
            // Asumiendo que 'conectados' se refiere a usuarios actualmente online
            $conectados = DB::table('users')->where('is_online', true)->count(); // Requiere una columna 'is_online'

            Log::info('Datos de estadísticas enviados:', [
                'filtro' => $filtro,
                'selectedYear' => $selectedYear,
                'chartSubFilter' => $chartSubFilter,
                'inicio' => $inicio ? $inicio->toDateTimeString() : 'N/A',
                'fin' => $fin ? $fin->toDateTimeString() : 'N/A',
                'vistas_count' => $vistas->count(),
                'usuarios' => $usuarios,
                'registrados' => $registrados,
                'activos' => $activos,
                'conectados' => $conectados,
                // 'vistas_data' => $vistas->toArray() // Descomenta si necesitas ver los datos crudos en el log
            ]);

            return response()->json([
                'vistas' => $vistas->values()->toArray(), // Asegúrate de que sea un array de objetos
                'usuarios' => $usuarios,
                'registrados' => $registrados,
                'activos' => $activos,
                'conectados' => $conectados,
                'selectedYear' => $selectedYear,
                'selectedFilter' => $filtro,
                'chartSubFilter' => $chartSubFilter,
            ]);

        } catch (\Throwable $e) {
            Log::error('Error al obtener estadísticas: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json([
                'error' => 'Error interno del servidor al obtener estadísticas',
                'detalle' => $e->getMessage(),
                'trace' => env('APP_DEBUG') ? $e->getTraceAsString() : null,
            ], 500);
        }
    }
}
