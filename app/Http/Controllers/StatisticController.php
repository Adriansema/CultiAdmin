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

        // Obtener el año más antiguo de registros en la tabla 'users' (para la lista de años del dropdown)
        // Usaremos 'users.created_at' como base para el año de inicio.
        $firstRecordYear = DB::table('users')->min(DB::raw('EXTRACT(YEAR FROM created_at)'));

        // El año de inicio para el selector: mínimo 2025 o el año del primer registro si es anterior
        $startYear = max(2025, $firstRecordYear ?? $currentYear);

        // Generar años para el dropdown en el frontend
        for ($year = $currentYear; $year >= $startYear; $year--) {
            $availableYears[] = (string)$year;
        }
        sort($availableYears); // Ordenar para que estén en orden ascendente (el JS los ordenará al revés para el dropdown)

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
            $selectedYear = (int)$request->query('year', Carbon::now()->year); // Año seleccionado del dropdown
            $chartSubFilter = $request->query('subFilter', 'month'); // Sub-filtro para 'año'

            $vistas = collect(); // Colección para los datos de la gráfica (grupo y total)
            $inicio = null;
            $fin = null;

            // La tabla base para la gráfica será SIEMPRE 'users' para contar registros
            $baseChartQuery = DB::table('users'); // <-- ¡CAMBIO CLAVE: SIEMPRE CONSULTAMOS USERS!

            // La fecha de referencia para los cálculos de rango (hoy, semana, mes)
            // Si el filtro es 'año', nos basamos en el 1 de enero de ese año. Si no, en la fecha actual.
            $baseDateForFiltering = ($filtro === 'año') ? Carbon::create($selectedYear, 1, 1) : Carbon::now();

            switch ($filtro) {
                case 'ultimos3dias':
                    // Rango: los últimos 3 días (incluyendo hoy)
                    $fin = $baseDateForFiltering->copy()->endOfDay();
                    $inicio = $fin->copy()->subDays(2)->startOfDay();

                    $dbDateFormatter = (env('DB_CONNECTION') === 'pgsql') ? "TO_CHAR(created_at, 'YYYY-MM-DD')" : "DATE_FORMAT(created_at, '%Y-%m-%d')";

                    $tempVistas = [];
                    for ($i = 2; $i >= 0; $i--) {
                        $date = $fin->copy()->subDays($i);
                        $formattedDateKey = $date->toDateString(); // Clave 'YYYY-MM-DD'
                        $tempVistas[$formattedDateKey] = (object)['grupo' => $date->translatedFormat('l'), 'total' => 0]; // Ej: "viernes"
                    }

                    $results = $baseChartQuery->whereBetween('created_at', [$inicio, $fin]) // <-- Consulta users.created_at
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

                case 'semana':
                    // Rango: 7 días terminando en la baseDateForFiltering
                    $fin = $baseDateForFiltering->copy()->endOfDay();
                    $inicio = $fin->copy()->subDays(6)->startOfDay();

                    $dbDateFormatter = (env('DB_CONNECTION') === 'pgsql') ? "TO_CHAR(created_at, 'YYYY-MM-DD')" : "DATE_FORMAT(created_at, '%Y-%m-%d')";

                    $tempVistas = [];
                    for ($i = 6; $i >= 0; $i--) {
                        $date = $fin->copy()->subDays($i);
                        $formattedDateKey = $date->toDateString(); // Clave 'YYYY-MM-DD'
                        $tempVistas[$formattedDateKey] = (object)['grupo' => $date->translatedFormat('D d M'), 'total' => 0]; // Ej: "Vie 06 Jun"
                    }

                    $results = $baseChartQuery->whereBetween('created_at', [$inicio, $fin]) // <-- Consulta users.created_at
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
                    $inicio = $baseDateForFiltering->copy()->startOfMonth();
                    $fin = $baseDateForFiltering->copy()->endOfMonth();

                    $dbWeekFormatter = (env('DB_CONNECTION') === 'pgsql') ? 'FLOOR((EXTRACT(DAY FROM created_at) - 1) / 7) + 1' : 'CEIL(DAY(created_at) / 7)';

                    $results = $baseChartQuery->whereBetween('created_at', [$inicio, $fin]) // <-- Consulta users.created_at
                                               ->select(DB::raw("$dbWeekFormatter AS grupo"), DB::raw('COUNT(*) AS total'))
                                               ->groupBy(DB::raw($dbWeekFormatter))
                                               ->orderBy('grupo')
                                               ->get();

                    // Rellenar las semanas faltantes
                    $weeksInMonth = (int)ceil($baseDateForFiltering->daysInMonth / 7);
                    $tempVistas = [];
                    for ($i = 1; $i <= $weeksInMonth; $i++) {
                        $tempVistas[$i] = (object)['grupo' => 'Semana ' . $i, 'total' => 0];
                    }

                    foreach ($results as $item) {
                        if (isset($tempVistas[$item->grupo])) {
                            $tempVistas[$item->grupo]->total = $item->total;
                        }
                    }
                    $vistas = collect(array_values($tempVistas));
                    break;

                case 'año':
                    // Lógica para el filtro 'año', usando el sub-filtro $chartSubFilter
                    $inicio = Carbon::create($selectedYear, 1, 1)->startOfYear();
                    $fin = Carbon::create($selectedYear, 12, 31)->endOfYear();

                    $currentMonth = Carbon::now()->month;
                    $currentWeekOfYear = Carbon::now()->weekOfYear;
                    $currentDayOfYear = Carbon::now()->dayOfYear;
                    $currentHour = Carbon::now()->hour;

                    switch ($chartSubFilter) {
                        case 'month':
                            // Consulta la tabla 'users' para los registros por mes del año seleccionado
                            $dbMonthFormatter = (env('DB_CONNECTION') === 'pgsql') ? "EXTRACT(MONTH FROM created_at)" : "MONTH(created_at)";

                            $results = $baseChartQuery // <-- Consulta users.created_at
                                        ->whereYear('created_at', $selectedYear)
                                        ->select(DB::raw("$dbMonthFormatter AS month_num"), DB::raw('COUNT(*) AS total'))
                                        ->groupBy(DB::raw($dbMonthFormatter))
                                        ->orderBy('month_num')
                                        ->get();

                            $tempVistas = [];
                            $monthsToIterate = 12;
                            if ($selectedYear === Carbon::now()->year) {
                                $monthsToIterate = $currentMonth;
                            } elseif ($selectedYear > Carbon::now()->year) {
                                $monthsToIterate = 0;
                            }

                            for ($i = 1; $i <= $monthsToIterate; $i++) {
                                $monthName = Carbon::create(null, $i, 1)->translatedFormat('F');
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
                            $dbWeekFormatter = (env('DB_CONNECTION') === 'pgsql') ? "EXTRACT(WEEK FROM created_at)" : "WEEK(created_at, 3)";

                            $results = $baseChartQuery // <-- Consulta users.created_at
                                         ->whereYear('created_at', $selectedYear)
                                         ->select(DB::raw("$dbWeekFormatter AS week_num"), DB::raw('COUNT(*) AS total'))
                                         ->groupBy(DB::raw($dbWeekFormatter))
                                         ->orderBy('week_num')
                                         ->get();

                            $tempVistas = [];
                            $weeksInSelectedYear = (new Carbon("{$selectedYear}-12-31"))->weekOfYear;
                            $weeksToIterate = $weeksInSelectedYear;
                            if ($selectedYear === Carbon::now()->year) {
                                $weeksToIterate = $currentWeekOfYear;
                            } elseif ($selectedYear > Carbon::now()->year) {
                                $weeksToIterate = 0;
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

                            $results = $baseChartQuery // <-- Consulta users.created_at
                                         ->whereYear('created_at', $selectedYear)
                                         ->select(DB::raw("$dbDayFormatter AS date_key"), DB::raw('COUNT(*) AS total'))
                                         ->groupBy(DB::raw($dbDayFormatter))
                                         ->orderBy('date_key')
                                         ->get();

                            $tempVistas = [];
                            $daysInSelectedYear = Carbon::create($selectedYear, 1, 1)->daysInYear;
                            $daysToIterate = $daysInSelectedYear;
                            if ($selectedYear === Carbon::now()->year) {
                                $daysToIterate = $currentDayOfYear;
                            } elseif ($selectedYear > Carbon::now()->year) {
                                $daysToIterate = 0;
                            }

                            for ($i = 0; $i < $daysToIterate; $i++) {
                                $date = Carbon::create($selectedYear, 1, 1)->addDays($i);
                                $formattedDateKey = $date->toDateString();
                                $tempVistas[$formattedDateKey] = (object)['grupo' => $date->translatedFormat('d M'), 'total' => 0];
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

                            $hoursToIterate = 24;
                            if ($selectedYear > Carbon::now()->year || ($selectedYear === Carbon::now()->year && $targetDateForHours->gt(Carbon::now()))) {
                                $hoursToIterate = 0;
                            } elseif ($targetDateForHours->isToday()) {
                                $hoursToIterate = $currentHour + 1;
                            }

                            $dbHourFormatter = (env('DB_CONNECTION') === 'pgsql') ? "EXTRACT(HOUR FROM created_at)" : "HOUR(created_at)";

                            $results = $baseChartQuery // <-- Consulta users.created_at
                                         ->whereYear('created_at', $selectedYear)
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

                case 'rangoPersonalizado':
                    $startDate = $request->query('startDate');
                    $endDate = $request->query('endDate');

                    if ($startDate && $endDate) {
                        $inicio = Carbon::parse($startDate)->startOfDay();
                        $fin = Carbon::parse($endDate)->endOfDay();
                        $dbDateFormatter = (env('DB_CONNECTION') === 'pgsql') ? "TO_CHAR(created_at, 'YYYY-MM-DD')" : "DATE_FORMAT(created_at, '%Y-%m-%d')";

                        $results = $baseChartQuery->whereBetween('created_at', [$inicio, $fin]) // <-- Consulta users.created_at
                                                   ->select(DB::raw("$dbDateFormatter AS date_key"), DB::raw('COUNT(*) AS total'))
                                                   ->groupBy(DB::raw($dbDateFormatter))
                                                   ->orderBy('date_key')
                                                   ->get();

                        // Rellenar días faltantes en el rango
                        $interval = $inicio->diffInDays($fin) + 1;
                        $tempVistas = [];
                        for ($i = 0; $i < $interval; $i++) {
                            $date = $inicio->copy()->addDays($i);
                            $formattedDateKey = $date->toDateString();
                            $tempVistas[$formattedDateKey] = (object)['grupo' => $date->translatedFormat('D d M'), 'total' => 0];
                        }
                        foreach ($results as $item) {
                            if (isset($tempVistas[$item->date_key])) {
                                $tempVistas[$item->date_key]->total = $item->total;
                            }
                        }
                        $vistas = collect(array_values($tempVistas));
                    }
                    break;
                case 'todo':
                    // Rango: Desde el primer registro de usuario hasta el día actual.
                    $inicio = $baseChartQuery->min('created_at') ? Carbon::parse($baseChartQuery->min('created_at'))->startOfDay() : Carbon::now()->startOfDay();
                    $fin = Carbon::now()->endOfDay();

                    $dbYearFormatter = (env('DB_CONNECTION') === 'pgsql') ? "EXTRACT(YEAR FROM created_at)" : "YEAR(created_at)";

                    $results = $baseChartQuery->whereBetween('created_at', [$inicio, $fin]) // <-- Consulta users.created_at
                                               ->select(DB::raw("$dbYearFormatter AS year_num"), DB::raw('COUNT(*) AS total'))
                                               ->groupBy(DB::raw($dbYearFormatter))
                                               ->orderBy('year_num')
                                               ->get();

                    $tempVistas = [];
                    $minYear = $inicio->year;
                    $maxYear = $fin->year;
                    for ($year = $minYear; $year <= $maxYear; $year++) {
                        $tempVistas[$year] = (object)['grupo' => (string)$year, 'total' => 0];
                    }

                    foreach ($results as $item) {
                        if (isset($tempVistas[$item->year_num])) {
                            $tempVistas[$item->year_num]->total = $item->total;
                        }
                    }
                    $vistas = collect(array_values($tempVistas));
                    break;


                default: // Fallback si no se reconoce el filtro
                    // Se usa la misma lógica que 'ultimos3dias' como un default seguro
                    $fin = $baseDateForFiltering->copy()->endOfDay();
                    $inicio = $fin->copy()->subDays(2)->startOfDay();
                    $dbDateFormatter = (env('DB_CONNECTION') === 'pgsql') ? "TO_CHAR(created_at, 'YYYY-MM-DD')" : "DATE_FORMAT(created_at, '%Y-%m-%d')";

                    $tempVistas = [];
                    for ($i = 2; $i >= 0; $i--) {
                        $date = $fin->copy()->subDays($i);
                        $formattedDateKey = $date->toDateString();
                        $tempVistas[$formattedDateKey] = (object)['grupo' => $date->translatedFormat('l'), 'total' => 0];
                    }

                    $results = $baseChartQuery->whereBetween('created_at', [$inicio, $fin]) // <-- Consulta users.created_at
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
            // Estas métricas NO se filtran por $inicio y $fin, son totales si las quieres globales.
            $usuarios = DB::table('users')->count(); // Total de usuarios en la BD (global)
            $registrados = DB::table('users')->count(); // Total de usuarios registrados (global)

            // Para activos y conectados, si quieres que se basen en el filtro de tiempo de la gráfica:
            // $activos = DB::table('users')->whereBetween('last_login_at', [$inicio, $fin])->count();
            // $conectados = DB::table('users')->where('is_online', true)->whereBetween('last_login_at', [$inicio, $fin])->count();
            // Si quieres que 'activos' y 'conectados' sean GLOBALES (no dependientes del filtro de fecha):
            $activos = DB::table('users')->where('last_login_at', '>=', Carbon::now()->subMinutes(30))->count(); // Ejemplo: activos en los últimos 30 minutos
            $conectados = DB::table('users')->where('is_online', true)->count(); // Usuarios con 'is_online' en true

            Log::info('Datos de estadísticas enviados:', [
                'filtro' => $filtro,
                'selectedYear' => $selectedYear,
                'chartSubFilter' => $chartSubFilter,
                'inicio' => $inicio ? $inicio->toDateTimeString() : 'N/A',
                'fin' => $fin ? $fin->toDateTimeString() : 'N/A',
                'vistas_count' => $vistas->count(), // Ahora es 'registros_count'
                'usuarios' => $usuarios,
                'registrados' => $registrados, // Este siempre será el total de users si no lo filtras
                'activos' => $activos,
                'conectados' => $conectados,
                // 'vistas_data' => $vistas->toArray() // Descomenta si necesitas ver los datos crudos en el log
            ]);

            return response()->json([
                'vistas' => $vistas->values()->toArray(), // Ahora representa 'registros'
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
