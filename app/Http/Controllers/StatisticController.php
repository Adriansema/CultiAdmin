<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
// use GuzzleHttp\Promise\Create; // Esta línea no es necesaria y la voy a quitar para limpiar.
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

Carbon::setLocale(App::getLocale());

class StatisticController extends Controller
{
    /**
     * Muestra la página del dashboard con los filtros y la gráfica.
     * Este método se encarga de cargar la vista inicial y los años disponibles.
     */

    public function showDashboardPage(Request $request)
    {
        // 1. Determinar los años disponibles para el selector
        $currentYear = Carbon::now()->year;
        $availableYears = [];

        // Obtener el año más antiguo de registros en la tabla 'boletins'
        // Esto asume que tienes datos en 'boletins'. Si no, puedes dejarlo comentado
        // y usar un año de inicio fijo como 2020.
        $firstBoletinYear = DB::table('boletins')->min(DB::raw('EXTRACT(YEAR FROM created_at)'));

        // Si no hay boletines en la base de datos, o quieres empezar siempre desde un año fijo (ej. 2020)
        // Usa el mayor entre un año fijo y el año del primer boletín (si existe)
        $startYear = max(2020, $firstBoletinYear ?? $currentYear); // Empieza en 2020 o el primer año de boletín

        // Generar años desde el "startYear" hasta el año actual + 1
        // Esto asegura que 2025 y 2026 (si estamos en 2025) aparezcan.
        for ($year = $startYear; $year <= ($currentYear + 1); $year++) {
            $availableYears[] = (string)$year;
        }

        // Si quieres incluir el año actual aunque no haya datos antiguos, y quizás el próximo
        // Puedes asegurar que $availableYears no esté vacío si startYear es futuro (lo cual es raro)
        if (!in_array((string)$currentYear, $availableYears)) {
            $availableYears[] = (string)$currentYear;
        }
        if (!in_array((string)($currentYear + 1), $availableYears)) {
            $availableYears[] = (string)($currentYear + 1);
        }
        // Ordenar para que estén en orden ascendente
        sort($availableYears);


        $selectedYear = $request->input('year', $currentYear); // Por defecto, el año actual
        $selectedFilter = $request->input('filter', 'year'); // Por defecto, el filtro por año

        // Define $boletines aunque sea un array vacío si no los usas para no dar error en la vista
        $boletines = collect(); // Esto creará una colección vacía para pasar a la vista


        return view('dashboard', compact('availableYears', 'selectedYear', 'selectedFilter', 'boletines'));
    }

      public function getChartData(Request $request)
    {
        $year = (int)$request->input('year', Carbon::now()->year); // Año seleccionado del frontend
        $filter = $request->input('filter', 'year');

        $labels = [];
        $data = [];

        // Para el filtro 'year' (que agrupa por meses)
        if ($filter === 'year') {
            $currentYear = Carbon::now()->year;
            $currentMonth = Carbon::now()->month;

            // Si el año seleccionado es el año actual o un año futuro,
            // solo muestra los meses que ya han transcurrido (hasta el mes actual).
            // Si es un año pasado, muestra los 12 meses.
            $monthsToShow = ($year <= $currentYear) ? ($year === $currentYear ? $currentMonth : 12) : 0;
            // ^^^ Lógica mejorada: Si es el año actual, hasta el mes actual; si es pasado, 12 meses; si es futuro, 0 inicialmente.

            // Consulta de datos
            $results = DB::table('boletins')
                ->select(DB::raw('EXTRACT(MONTH FROM created_at) as month'), DB::raw('COUNT(*) as count'))
                ->whereYear('created_at', $year)
                ->groupBy('month')
                ->orderBy('month')
                ->get();

            // Rellenar con 0s y generar etiquetas hasta el mes adecuado
            for ($i = 1; $i <= $monthsToShow; $i++) {
                $monthName = Carbon::create(null, $i, 1)->monthName;
                $labels[] = ucfirst($monthName);
                $found = $results->firstWhere('month', $i);
                $data[] = $found ? $found->count : 0;
            }

        } // ... (resto de filtros como 'month', 'week', 'day' si los implementas) ...

        return response()->json(['labels' => $labels, 'data' => $data]);
    }

    // ... (Tu método getChartData va aquí) ...
 public function getStatistics(Request $request)
    {
        try {
            // Configuración regional para fechas (asegúrate de que el sistema tenga los locales instalados)
            setlocale(LC_TIME, 'es_ES.UTF-8');
            Carbon::setLocale('es');

            $filtro = $request->query('filtro', 'ultimos3dias'); // Filtro principal: ultimos3dias, semana, mes, año
            $selectedYear = (int)$request->query('year', Carbon::now()->year); // Año seleccionado del Flatpickr
            $chartSubFilter = $request->query('chartFilter', 'month'); // Sub-filtro para el 'año': month, week, day, hour

            $vistas = collect(); // Para almacenar los datos de la gráfica (grupo y total)
            $inicio = null;
            $fin = null;
            $campoAgrupar = null;

            // La fecha base para el rango ahora siempre usa el año seleccionado, pero el mes/día/hora actuales
            // si no se especifica lo contrario (para filtros como 'ultimos3dias', 'semana', 'mes').
            $baseDateForFiltering = Carbon::create(
                $selectedYear,
                Carbon::now()->month,
                Carbon::now()->day,
                Carbon::now()->hour,
                Carbon::now()->minute,
                Carbon::now()->second
            );

            // Consulta base para las visitas (se inicializa aquí para aplicar filtros)
            $baseQuery = DB::table('visits'); // Asegúrate que 'visits' es la tabla correcta

            switch ($filtro) {
                case 'ultimos3dias':
                    $inicio = $baseDateForFiltering->copy()->subDays(2)->startOfDay();
                    $fin = $baseDateForFiltering->copy()->endOfDay();
                    // PostgreSQL: TO_CHAR(created_at, 'YYYY-MM-DD')
                    // MySQL: DATE_FORMAT(created_at, '%Y-%m-%d')
                    $campoAgrupar = (env('DB_CONNECTION') === 'pgsql') ? "TO_CHAR(created_at, 'YYYY-MM-DD')" : "DATE_FORMAT(created_at, '%Y-%m-%d')";

                    // Llenar datos con 0s para los 3 días
                    $tempVistas = [];
                    for ($i = 2; $i >= 0; $i--) {
                        $date = $baseDateForFiltering->copy()->subDays($i);
                        $formattedDate = $date->toDateString(); // YYYY-MM-DD
                        $tempVistas[$formattedDate] = (object)['grupo' => $date->translatedFormat('l'), 'total' => 0]; // Formato de día de la semana
                    }

                    $results = $baseQuery->whereBetween('created_at', [$inicio, $fin])
                                         ->select(DB::raw("$campoAgrupar AS grupo"), DB::raw('COUNT(*) AS total'))
                                         ->groupBy(DB::raw($campoAgrupar))
                                         ->get();

                    foreach ($results as $item) {
                        $tempVistas[$item->grupo]->total = $item->total;
                    }
                    $vistas = collect(array_values($tempVistas));
                    break;

                case 'semana':
                    // Obtener los últimos 7 días terminando en la baseDateForFiltering
                    $inicio = $baseDateForFiltering->copy()->subDays(6)->startOfDay();
                    $fin = $baseDateForFiltering->copy()->endOfDay();

                    $tempVistas = [];
                    for ($i = 6; $i >= 0; $i--) {
                        $date = $baseDateForFiltering->copy()->subDays($i);
                        $formattedDate = $date->toDateString(); // YYYY-MM-DD
                        $tempVistas[$formattedDate] = (object)['grupo' => $date->translatedFormat('D d M'), 'total' => 0]; // Ej: Lun 03 Jun
                    }

                    $results = $baseQuery->whereBetween('created_at', [$inicio, $fin])
                                         ->select(DB::raw((env('DB_CONNECTION') === 'pgsql' ? 'DATE(created_at)' : 'DATE(created_at)') . ' AS grupo'), DB::raw('COUNT(*) AS total')) // Agrupar por fecha completa
                                         ->groupBy(DB::raw((env('DB_CONNECTION') === 'pgsql' ? 'DATE(created_at)' : 'DATE(created_at)')))
                                         ->get();

                    foreach ($results as $item) {
                        if (isset($tempVistas[$item->grupo])) {
                             $tempVistas[$item->grupo]->total = $item->total;
                        }
                    }
                    $vistas = collect(array_values($tempVistas));
                    break;

                case 'mes':
                    $inicio = $baseDateForFiltering->copy()->startOfMonth();
                    $fin = $baseDateForFiltering->copy()->endOfMonth();
                    // Agrupa por semana del mes. Adaptar para MySQL/PostgreSQL si es necesario.
                    $campoAgrupar = (env('DB_CONNECTION') === 'pgsql') ? 'FLOOR((EXTRACT(DAY FROM created_at) - 1) / 7) + 1' : 'WEEKOFYEAR(created_at) - WEEKOFYEAR(DATE_FORMAT(created_at, \'%Y-%m-01\')) + 1';

                    $results = $baseQuery->whereBetween('created_at', [$inicio, $fin])
                                         ->select(DB::raw("$campoAgrupar AS grupo"), DB::raw('COUNT(*) AS total'))
                                         ->groupBy(DB::raw($campoAgrupar))
                                         ->orderBy('grupo') // Asegura el orden por semana del mes
                                         ->get();

                    // Rellenar las semanas faltantes
                    $weeksInMonth = (int)ceil($baseDateForFiltering->daysInMonth / 7);
                    $tempVistas = [];
                    for ($i = 1; $i <= $weeksInMonth; $i++) {
                        $tempVistas[$i] = (object)['grupo' => 'Semana ' . $i, 'total' => 0];
                    }

                    foreach ($results as $item) {
                        $tempVistas[$item->grupo]->total = $item->total;
                    }
                    $vistas = collect(array_values($tempVistas));
                    break;

                case 'año':
                    // Lógica para el filtro 'año', usando el sub-filtro $chartSubFilter
                    $inicio = Carbon::create($selectedYear, 1, 1)->startOfYear();
                    $fin = Carbon::create($selectedYear, 12, 31)->endOfYear();

                    $currentYear = Carbon::now()->year;
                    $currentMonth = Carbon::now()->month;
                    $currentWeek = Carbon::now()->weekOfYear;
                    $currentDayOfYear = Carbon::now()->dayOfYear;
                    $currentHour = Carbon::now()->hour;

                    switch ($chartSubFilter) {
                        case 'month':
                            // Mostrar meses hasta el actual si es el año actual, o los 12 meses si es pasado/futuro
                            $monthsToIterate = ($selectedYear === $currentYear) ? $currentMonth : 12;
                            if ($selectedYear > $currentYear) { // Si es un año futuro, no muestra meses pasados del futuro
                                $monthsToIterate = 0; // O si quieres, puedes mostrar 0, o dejarlo vacío
                            }


                            $campoAgrupar = (env('DB_CONNECTION') === 'pgsql') ? "EXTRACT(MONTH FROM created_at)" : "MONTH(created_at)";

                            $results = $baseQuery->whereYear('created_at', $selectedYear)
                                                 ->select(DB::raw("$campoAgrupar AS grupo"), DB::raw('COUNT(*) AS total'))
                                                 ->groupBy(DB::raw($campoAgrupar))
                                                 ->orderBy('grupo')
                                                 ->get();

                            $tempVistas = [];
                            for ($i = 1; $i <= $monthsToIterate; $i++) {
                                $monthName = Carbon::create(null, $i, 1)->monthName;
                                $tempVistas[$i] = (object)['grupo' => ucfirst($monthName), 'total' => 0];
                            }

                            foreach ($results as $item) {
                                if (isset($tempVistas[$item->grupo])) {
                                    $tempVistas[$item->grupo]->total = $item->total;
                                }
                            }
                            $vistas = collect(array_values($tempVistas));
                            break;

                        case 'week':
                            // Agrupar por semanas dentro del año
                            $weeksInYear = (new Carbon("{$selectedYear}-12-31"))->weekOfYear; // Total de semanas en el año
                            $weeksToIterate = ($selectedYear === $currentYear) ? $currentWeek : $weeksInYear;
                            if ($selectedYear > $currentYear) {
                                $weeksToIterate = 0;
                            }

                            $campoAgrupar = (env('DB_CONNECTION') === 'pgsql') ? "EXTRACT(WEEK FROM created_at)" : "WEEK(created_at, 3)"; // 3 para semana ISO (lunes-domingo)

                            $results = $baseQuery->whereYear('created_at', $selectedYear)
                                                 ->select(DB::raw("$campoAgrupar AS grupo"), DB::raw('COUNT(*) AS total'))
                                                 ->groupBy(DB::raw($campoAgrupar))
                                                 ->orderBy('grupo')
                                                 ->get();

                            $tempVistas = [];
                            for ($i = 1; $i <= $weeksToIterate; $i++) {
                                $tempVistas[$i] = (object)['grupo' => 'Semana ' . $i, 'total' => 0];
                            }

                            foreach ($results as $item) {
                                if (isset($tempVistas[$item->grupo])) {
                                    $tempVistas[$item->grupo]->total = $item->total;
                                }
                            }
                            $vistas = collect(array_values($tempVistas));
                            break;

                        case 'day':
                            // Agrupar por días del año
                            $daysInYear = Carbon::create($selectedYear, 1, 1)->daysInYear;
                            $daysToIterate = ($selectedYear === $currentYear) ? $currentDayOfYear : $daysInYear;
                            if ($selectedYear > $currentYear) {
                                $daysToIterate = 0;
                            }

                            $campoAgrupar = (env('DB_CONNECTION') === 'pgsql') ? "TO_CHAR(created_at, 'YYYY-MM-DD')" : "DATE_FORMAT(created_at, '%Y-%m-%d')";

                            $results = $baseQuery->whereYear('created_at', $selectedYear)
                                                 ->select(DB::raw("$campoAgrupar AS grupo"), DB::raw('COUNT(*) AS total'))
                                                 ->groupBy(DB::raw($campoAgrupar))
                                                 ->orderBy('grupo')
                                                 ->get();

                            $tempVistas = [];
                            for ($i = 0; $i < $daysToIterate; $i++) {
                                $date = Carbon::create($selectedYear, 1, 1)->addDays($i);
                                $formattedDate = $date->toDateString();
                                $tempVistas[$formattedDate] = (object)['grupo' => $date->translatedFormat('d M'), 'total' => 0];
                            }

                            foreach ($results as $item) {
                                if (isset($tempVistas[$item->grupo])) { // Match by 'YYYY-MM-DD'
                                    $tempVistas[$item->grupo]->total = $item->total;
                                }
                            }
                             $vistas = collect(array_values($tempVistas));
                            break;

                        case 'hour':
                            // Agrupar por horas del día (para el día actual del año seleccionado)
                            // Se asume que quieres las horas del día actual, pero dentro del año seleccionado
                            $targetDateForHours = Carbon::create($selectedYear, Carbon::now()->month, Carbon::now()->day);
                            if ($selectedYear > $currentYear || ($selectedYear === $currentYear && $targetDateForHours->gt(Carbon::now())) ) {
                                $hoursToIterate = 0; // Si es futuro, no hay horas
                            } else {
                                $hoursToIterate = ($targetDateForHours->isToday()) ? $currentHour + 1 : 24;
                            }

                            $campoAgrupar = (env('DB_CONNECTION') === 'pgsql') ? "EXTRACT(HOUR FROM created_at)" : "HOUR(created_at)";

                            $results = $baseQuery->whereYear('created_at', $selectedYear)
                                                 ->whereDate('created_at', $targetDateForHours->toDateString())
                                                 ->select(DB::raw("$campoAgrupar AS grupo"), DB::raw('COUNT(*) AS total'))
                                                 ->groupBy(DB::raw($campoAgrupar))
                                                 ->orderBy('grupo')
                                                 ->get();

                            $tempVistas = [];
                            for ($i = 0; $i < $hoursToIterate; $i++) {
                                $tempVistas[$i] = (object)['grupo' => sprintf('%02d:00', $i), 'total' => 0];
                            }

                            foreach ($results as $item) {
                                if (isset($tempVistas[$item->grupo])) {
                                    $tempVistas[$item->grupo]->total = $item->total;
                                }
                            }
                            $vistas = collect(array_values($tempVistas));
                            break;
                    }
                    break;

                default: // Si el filtro no es 'año', 'ultimos3dias', 'semana', 'mes', por defecto a 'ultimos3dias' o puedes definir otro
                    // Esto es un fallback. Tu JS envía 'ultimos3dias' por defecto.
                    // Podrías redirigir a un caso de filtro conocido si lo deseas.
                    $inicio = $baseDateForFiltering->copy()->subDays(2)->startOfDay();
                    $fin = $baseDateForFiltering->copy()->endOfDay();
                    $campoAgrupar = (env('DB_CONNECTION') === 'pgsql') ? "TO_CHAR(created_at, 'YYYY-MM-DD')" : "DATE_FORMAT(created_at, '%Y-%m-%d')";

                    $tempVistas = [];
                    for ($i = 2; $i >= 0; $i--) {
                        $date = $baseDateForFiltering->copy()->subDays($i);
                        $formattedDate = $date->toDateString(); // YYYY-MM-DD
                        $tempVistas[$formattedDate] = (object)['grupo' => $date->translatedFormat('l'), 'total' => 0]; // Formato de día de la semana
                    }

                    $results = $baseQuery->whereBetween('created_at', [$inicio, $fin])
                                         ->select(DB::raw("$campoAgrupar AS grupo"), DB::raw('COUNT(*) AS total'))
                                         ->groupBy(DB::raw($campoAgrupar))
                                         ->get();

                    foreach ($results as $item) {
                        $tempVistas[$item->grupo]->total = $item->total;
                    }
                    $vistas = collect(array_values($tempVistas));
                    break;
            }

            // Métricas generales: DEBEN consultar en función de $inicio y $fin
            // Asegúrate de que tus tablas y columnas sean correctas (ej. 'users', 'last_login_at', 'is_online')
            $usuarios = DB::table('users')->count(); // Total de usuarios, independiente del filtro de fecha
            $registrados = DB::table('users')->whereBetween('created_at', [$inicio, $fin])->count();
            $activos = DB::table('users')->whereBetween('last_login_at', [$inicio, $fin])->count();
            // Para 'conectados', asumimos que es en tiempo real y no depende del filtro de fecha
            $conectados = DB::table('users')->where('is_online', true)->count();

            // Log de depuración para ver qué datos se envían al frontend
            Log::info('Datos de estadísticas enviados:', [
                'filtro' => $filtro,
                'selectedYear' => $selectedYear,
                'chartSubFilter' => $chartSubFilter,
                'vistas_count' => $vistas->count(),
                'usuarios' => $usuarios,
                'registrados' => $registrados,
                'activos' => $activos,
                'conectados' => $conectados,
                // 'vistas_data' => $vistas->toArray() // Descomenta si necesitas ver los datos crudos en el log
            ]);

            return response()->json([
                'vistas' => $vistas->values()->toArray(), // Asegúrate de que sea un array indexado numéricamente
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
                'trace' => env('APP_DEBUG') ? $e->getTraceAsString() : null, // Mostrar trace solo en modo debug
            ], 500);
        }
    }
}



