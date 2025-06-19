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

        // Obtener el año más antiguo de registros en la tabla 'users' para la lista de años del dropdown.
        $firstRecordYear = DB::table('users')->min(DB::raw('EXTRACT(YEAR FROM created_at)'));

        $startYear = max(2025, $firstRecordYear ?? $currentYear);

        for ($year = $currentYear; $year >= $startYear; $year--) {
            $availableYears[] = (string)$year;
        }
        sort($availableYears);

        $selectedYear = $request->input('year', $currentYear);
        $selectedFilter = $request->input('filter', 'ultimos3dias');

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
            setlocale(LC_TIME, 'es_ES.UTF-8');
            Carbon::setLocale('es');

            $filtro = $request->query('filter', 'ultimos3dias');
            $selectedYear = (int)$request->query('year', Carbon::now()->year);
            $chartSubFilter = $request->query('subFilter', 'month');

            $vistas = collect();
            $inicio = null;
            $fin = null;

            // --- Lógica CLAVE: Determinar la tabla base para la gráfica dinámicamente ---
            $baseChartQuery = null;
            $isUsersChart = ($filtro === 'año' && $chartSubFilter === 'month');

            if ($isUsersChart) {
                $baseChartQuery = DB::table('users');
                Log::info('DEBUG (Backend): Gráfica consultando la tabla "users" (Registros).');
            } else {
                $baseChartQuery = DB::table('visits');
                Log::info('DEBUG (Backend): Gráfica consultando la tabla "visits" (Visitas).');
            }

            $baseDateForFiltering = ($filtro === 'año') ? Carbon::create($selectedYear, 1, 1) : Carbon::now();
            $dbDateFormatter = (env('DB_CONNECTION') === 'pgsql') ? "TO_CHAR(created_at, 'YYYY-MM-DD')" : "DATE_FORMAT(created_at, '%Y-%m-%d')";
            $dbWeekFormatter = (env('DB_CONNECTION') === 'pgsql') ? 'FLOOR((EXTRACT(DAY FROM created_at) - 1) / 7) + 1' : 'CEIL(DAY(created_at) / 7)';
            $dbMonthFormatter = (env('DB_CONNECTION') === 'pgsql') ? "EXTRACT(MONTH FROM created_at)" : "MONTH(created_at)";
            $dbDayOfYearFormatter = (env('DB_CONNECTION') === 'pgsql') ? "EXTRACT(DOY FROM created_at)" : "DAYOFYEAR(created_at)";
            $dbHourFormatter = (env('DB_CONNECTION') === 'pgsql') ? "EXTRACT(HOUR FROM created_at)" : "HOUR(created_at)";


            switch ($filtro) {
                case 'ultimos3dias':
                    $fin = $baseDateForFiltering->copy()->endOfDay();
                    $inicio = $fin->copy()->subDays(2)->startOfDay();
                    Log::info("DEBUG (Backend): Filtro 'ultimos3dias'. Rango: {$inicio->toDateTimeString()} a {$fin->toDateTimeString()}");

                    $tempVistas = [];
                    for ($i = 2; $i >= 0; $i--) {
                        $date = $fin->copy()->subDays($i);
                        $formattedDateKey = $date->toDateString();
                        $groupLabel = ucfirst($date->translatedFormat('l'));
                        $tempVistas[$formattedDateKey] = (object)['grupo' => $groupLabel, 'total' => 0];
                        Log::debug("DEBUG (Backend): Ultimos3dias - Fecha: {$formattedDateKey}, Label: {$groupLabel}");
                    }

                    $results = $baseChartQuery->whereBetween('created_at', [$inicio, $fin])
                                               ->select(DB::raw("$dbDateFormatter AS date_key"), DB::raw('COUNT(*) AS total'))
                                               ->groupBy(DB::raw($dbDateFormatter))
                                               ->get();

                    foreach ($results as $item) {
                        if (isset($tempVistas[$item->date_key])) {
                            $tempVistas[$item->date_key]->total = $item->total;
                        }
                    }
                    $vistas = collect(array_values($tempVistas));
                    Log::info('DEBUG (Backend): Datos generados para ultimos3dias:', ['vistas' => $vistas->toArray()]);
                    break;

                case 'semana':
                    $fin = $baseDateForFiltering->copy()->endOfDay();
                    $inicio = $fin->copy()->subDays(6)->startOfDay(); // Los últimos 7 días terminando hoy
                    Log::info("DEBUG (Backend): Filtro 'semana'. Rango: {$inicio->toDateTimeString()} a {$fin->toDateTimeString()}");

                    $tempVistas = [];
                    for ($i = 6; $i >= 0; $i--) {
                        $date = $fin->copy()->subDays($i);
                        $formattedDateKey = $date->toDateString();
                        $groupLabel = $date->translatedFormat('D d M.'); // Añadido el punto final para consistencia
                        $tempVistas[$formattedDateKey] = (object)['grupo' => ucfirst(str_replace('.', '', $groupLabel)), 'total' => 0];
                        Log::debug("DEBUG (Backend): Semana - Fecha: {$formattedDateKey}, Label PHP: {$date->translatedFormat('D d M')}, Label para Grupo: {$tempVistas[$formattedDateKey]->grupo}");
                    }

                    $results = $baseChartQuery->whereBetween('created_at', [$inicio, $fin])
                                               ->select(DB::raw("$dbDateFormatter AS date_key"), DB::raw('COUNT(*) AS total'))
                                               ->groupBy(DB::raw($dbDateFormatter))
                                               ->get();

                    foreach ($results as $item) {
                        if (isset($tempVistas[$item->date_key])) {
                             $tempVistas[$item->date_key]->total = $item->total;
                        }
                    }
                    $vistas = collect(array_values($tempVistas));
                    Log::info('DEBUG (Backend): Datos generados para semana:', ['vistas' => $vistas->toArray()]);
                    break;

                case 'mes':
                    $inicio = $baseDateForFiltering->copy()->startOfMonth();
                    $fin = $baseDateForFiltering->copy()->endOfMonth();
                    Log::info("DEBUG (Backend): Filtro 'mes'. Rango: {$inicio->toDateTimeString()} a {$fin->toDateTimeString()}");

                    $results = $baseChartQuery->whereBetween('created_at', [$inicio, $fin])
                                               ->select(DB::raw("$dbWeekFormatter AS grupo"), DB::raw('COUNT(*) AS total'))
                                               ->groupBy(DB::raw($dbWeekFormatter))
                                               ->orderBy('grupo')
                                               ->get();

                    // Rellenar solo hasta la semana actual del mes si el año es el actual
                    $currentWeekOfMonth = (int)ceil(Carbon::now()->day / 7);
                    $weeksToIterate = (Carbon::now()->month === $baseDateForFiltering->month && Carbon::now()->year === $baseDateForFiltering->year)
                                        ? $currentWeekOfMonth
                                        : (int)ceil($baseDateForFiltering->daysInMonth / 7); // Todas las semanas si es un mes pasado/futuro completo

                    $tempVistas = [];
                    for ($i = 1; $i <= $weeksToIterate; $i++) {
                        $tempVistas[$i] = (object)['grupo' => 'Semana ' . $i, 'total' => 0];
                        Log::debug("DEBUG (Backend): Mes - Rellenando Semana: {$i}");
                    }

                    foreach ($results as $item) {
                        if (isset($tempVistas[$item->grupo])) {
                            $tempVistas[$item->grupo]->total = $item->total;
                            Log::debug("DEBUG (Backend): Mes - Dato encontrado para Semana {$item->grupo}: {$item->total}");
                        }
                    }
                    $vistas = collect(array_values($tempVistas));
                    Log::info('DEBUG (Backend): Datos generados para mes:', ['vistas' => $vistas->toArray()]);
                    break;

                case 'año':
                    $inicio = Carbon::create($selectedYear, 1, 1)->startOfYear();
                    $fin = Carbon::create($selectedYear, 12, 31)->endOfYear();
                    Log::info("DEBUG (Backend): Filtro 'año'. Año: {$selectedYear}. Subfiltro: {$chartSubFilter}. Rango: {$inicio->toDateTimeString()} a {$fin->toDateTimeString()}");

                    $currentMonth = Carbon::now()->month;
                    $currentWeekOfYear = Carbon::now()->weekOfYear;
                    $currentDayOfYear = Carbon::now()->dayOfYear;
                    $currentHour = Carbon::now()->hour;

                    switch ($chartSubFilter) {
                        case 'month':
                            $results = $baseChartQuery
                                        ->whereYear('created_at', $selectedYear)
                                        ->select(DB::raw("$dbMonthFormatter AS month_num"), DB::raw('COUNT(*) AS total'))
                                        ->groupBy(DB::raw($dbMonthFormatter))
                                        ->orderBy('month_num')
                                        ->get();

                            $tempVistas = [];
                            $monthsToIterate = 12;
                            if ($selectedYear === Carbon::now()->year) {
                                $monthsToIterate = $currentMonth; // Solo hasta el mes actual
                            } elseif ($selectedYear > Carbon::now()->year) {
                                $monthsToIterate = 0; // Si es un año futuro, no hay datos aún
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
                            ksort($tempVistas);
                            $vistas = collect(array_values($tempVistas));
                            Log::info('DEBUG (Backend): Datos generados para año (mes):', ['vistas' => $vistas->toArray()]);
                            break;

                        case 'week':
                            $dbWeekFormatter = (env('DB_CONNECTION') === 'pgsql') ? "EXTRACT(WEEK FROM created_at)" : "WEEK(created_at, 3)";

                            $results = $baseChartQuery
                                         ->whereYear('created_at', $selectedYear)
                                         ->select(DB::raw("$dbWeekFormatter AS week_num"), DB::raw('COUNT(*) AS total'))
                                         ->groupBy(DB::raw($dbWeekFormatter))
                                         ->orderBy('week_num')
                                         ->get();

                            $tempVistas = [];
                            $weeksInSelectedYear = (new Carbon("{$selectedYear}-12-31"))->weekOfYear;
                            $weeksToIterate = $weeksInSelectedYear;
                            if ($selectedYear === Carbon::now()->year) {
                                $weeksToIterate = $currentWeekOfYear; // Solo hasta la semana actual del año
                            } elseif ($selectedYear > Carbon::now()->year) {
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
                            Log::info('DEBUG (Backend): Datos generados para año (semana):', ['vistas' => $vistas->toArray()]);
                            break;

                        case 'day':
                            $results = $baseChartQuery
                                         ->whereYear('created_at', $selectedYear)
                                         ->select(DB::raw("$dbDayOfYearFormatter AS day_num"), DB::raw('COUNT(*) AS total'))
                                         ->groupBy(DB::raw($dbDayOfYearFormatter))
                                         ->orderBy('day_num')
                                         ->get();

                            $tempVistas = [];
                            $daysInSelectedYear = Carbon::create($selectedYear, 1, 1)->daysInYear;
                            $daysToIterate = $daysInSelectedYear;
                            if ($selectedYear === Carbon::now()->year) {
                                $daysToIterate = $currentDayOfYear; // Solo hasta el día actual del año
                            } elseif ($selectedYear > Carbon::now()->year) {
                                $daysToIterate = 0; // Si es un año futuro, no hay datos aún
                            }

                            for ($i = 1; $i <= $daysToIterate; $i++) {
                                $date = Carbon::create($selectedYear, 1, 1)->addDays($i - 1);
                                $formattedDayKey = $i;
                                $groupLabel = $date->translatedFormat('d M');
                                $tempVistas[$formattedDayKey] = (object)['grupo' => $groupLabel, 'total' => 0];
                            }

                            foreach ($results as $item) {
                                if (isset($tempVistas[$item->day_num])) {
                                    $tempVistas[$item->day_num]->total = $item->total;
                                }
                            }
                            ksort($tempVistas);
                            $vistas = collect(array_values($tempVistas));
                            Log::info('DEBUG (Backend): Datos generados para año (día):', ['vistas' => $vistas->toArray()]);
                            break;

                        case 'hour':
                            // Para 'hour', siempre se muestra el día actual del año seleccionado, hasta la hora actual
                            $targetDateForHours = Carbon::create($selectedYear, Carbon::now()->month, Carbon::now()->day);

                            $hoursToIterate = 24;
                            if ($selectedYear > Carbon::now()->year || ($selectedYear === Carbon::now()->year && $targetDateForHours->gt(Carbon::now()))) {
                                $hoursToIterate = 0;
                            } elseif ($targetDateForHours->isToday()) {
                                $hoursToIterate = $currentHour + 1; // Incluir la hora actual
                            }

                            $results = $baseChartQuery
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
                            Log::info('DEBUG (Backend): Datos generados para año (hora):', ['vistas' => $vistas->toArray()]);
                            break;
                    }
                    break;

                case 'rangoPersonalizado':
                    $startDate = $request->query('startDate');
                    $endDate = $request->query('endDate');
                    Log::info("DEBUG (Backend): Filtro 'rangoPersonalizado'. Rango: {$startDate} a {$endDate}");

                    if ($startDate && $endDate) {
                        $inicio = Carbon::parse($startDate)->startOfDay();
                        $fin = Carbon::parse($endDate)->endOfDay();

                        $results = $baseChartQuery->whereBetween('created_at', [$inicio, $fin])
                                                   ->select(DB::raw("$dbDateFormatter AS date_key"), DB::raw('COUNT(*) AS total'))
                                                   ->groupBy(DB::raw($dbDateFormatter))
                                                   ->orderBy('date_key')
                                                   ->get();

                        $interval = $inicio->diffInDays($fin) + 1;
                        $tempVistas = [];
                        for ($i = 0; $i < $interval; $i++) {
                            $date = $inicio->copy()->addDays($i);
                            $formattedDateKey = $date->toDateString();
                            $groupLabel = $date->translatedFormat('D d M.');
                            $tempVistas[$formattedDateKey] = (object)['grupo' => ucfirst(str_replace('.', '', $groupLabel)), 'total' => 0];
                            Log::debug("DEBUG (Backend): RangoPersonalizado - Fecha: {$formattedDateKey}, Label PHP: {$date->translatedFormat('D d M')}, Label para Grupo: {$tempVistas[$formattedDateKey]->grupo}");
                        }
                        foreach ($results as $item) {
                            if (isset($tempVistas[$item->date_key])) {
                                $tempVistas[$item->date_key]->total = $item->total;
                            }
                        }
                        $vistas = collect(array_values($tempVistas));
                        Log::info('DEBUG (Backend): Datos generados para rangoPersonalizado:', ['vistas' => $vistas->toArray()]);
                    }
                    break;
                case 'todo':
                    $minCreatedAt = $baseChartQuery->min('created_at');
                    $inicio = $minCreatedAt ? Carbon::parse($minCreatedAt)->startOfDay() : Carbon::now()->startOfDay();
                    $fin = Carbon::now()->endOfDay();
                    Log::info("DEBUG (Backend): Filtro 'todo'. Rango: {$inicio->toDateTimeString()} a {$fin->toDateTimeString()}");

                    $dbYearFormatter = (env('DB_CONNECTION') === 'pgsql') ? "EXTRACT(YEAR FROM created_at)" : "YEAR(created_at)";

                    $results = $baseChartQuery->whereBetween('created_at', [$inicio, $fin])
                                               ->select(DB::raw("$dbYearFormatter AS year_num"), DB::raw('COUNT(*) AS total'))
                                               ->groupBy(DB::raw($dbYearFormatter))
                                               ->orderBy('year_num')
                                               ->get();

                    $tempVistas = [];
                    $minYear = $inicio->year;
                    $maxYear = Carbon::now()->year; // Solo hasta el año actual
                    for ($year = $minYear; $year <= $maxYear; $year++) {
                        $tempVistas[$year] = (object)['grupo' => (string)$year, 'total' => 0];
                    }

                    foreach ($results as $item) {
                        if (isset($tempVistas[$item->year_num])) {
                            $tempVistas[$item->year_num]->total = $item->total;
                        }
                    }
                    $vistas = collect(array_values($tempVistas));
                    Log::info('DEBUG (Backend): Datos generados para todo:', ['vistas' => $vistas->toArray()]);
                    break;


                default:
                    $fin = $baseDateForFiltering->copy()->endOfDay();
                    $inicio = $fin->copy()->subDays(2)->startOfDay();
                    $tempVistas = [];
                    for ($i = 2; $i >= 0; $i--) {
                        $date = $fin->copy()->subDays($i);
                        $formattedDateKey = $date->toDateString();
                        $groupLabel = ucfirst($date->translatedFormat('l'));
                        $tempVistas[$formattedDateKey] = (object)['grupo' => $groupLabel, 'total' => 0];
                    }

                    $results = $baseChartQuery->whereBetween('created_at', [$inicio, $fin])
                                               ->select(DB::raw("$dbDateFormatter AS date_key"), DB::raw('COUNT(*) AS total'))
                                               ->groupBy(DB::raw("$dbDateFormatter"))
                                               ->get();

                    foreach ($results as $item) {
                        if (isset($tempVistas[$item->date_key])) {
                            $tempVistas[$item->date_key]->total = $item->total;
                        }
                    }
                    $vistas = collect(array_values($tempVistas));
                    Log::info('DEBUG (Backend): Datos generados para DEFAULT:', ['vistas' => $vistas->toArray()]);
                    break;
            }

            $usuarios = DB::table('users')->count();
            $activos = DB::table('users')->where('last_login_at', '>=', Carbon::now()->subMinutes(30))->count();
            $conectados = DB::table('users')->where('is_online', true)->count();

            Log::info('Datos de estadísticas enviados FINAL:', [
                'filtro' => $filtro,
                'selectedYear' => $selectedYear,
                'chartSubFilter' => $chartSubFilter,
                'inicio' => $inicio ? $inicio->toDateTimeString() : 'N/A',
                'fin' => $fin ? $fin->toDateTimeString() : 'N/A',
                'vistas_count' => $vistas->count(),
                'usuarios' => $usuarios,
                'activos' => $activos,
                'conectados' => $conectados,
                'vistas_data' => $vistas->toArray()
            ]);

            return response()->json([
                'vistas' => $vistas->values()->toArray(),
                'usuarios' => $usuarios,
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
