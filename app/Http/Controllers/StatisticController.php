<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Statistic;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Support\Facades\App;

Carbon::setLocale(App::getLocale());

class StatisticController extends Controller
{
    public function getStatistics(Request $request)
    {
        try {
            setlocale(LC_TIME, 'es_ES.UTF-8');
            Carbon::setLocale('es');

            $filtro = $request->query('filtro', 'ultimos3dias');
            $ahora = Carbon::now();
            $inicio = $fin = null;
            $campoAgrupar = null;
            $formato = null;

            $dias = collect(); // Solo se usa en semana

            switch ($filtro) {
                case 'ultimos3dias':
                    $inicio = $ahora->copy()->subDays(2)->startOfDay();
                    $fin = $ahora->copy()->endOfDay();
                    $formato = 'YYYY-MM-DD';
                    $campoAgrupar = "TO_CHAR(created_at, '$formato')";
                    break;

                case 'semana':
                    $inicio = $ahora->copy()->startOfWeek(Carbon::MONDAY)->startOfDay();
                    $fin = $ahora->copy()->endOfWeek(Carbon::SUNDAY)->endOfDay();
                    $campoAgrupar = "DATE(created_at)";
                    break;

                case 'mes':
                    $inicio = $ahora->copy()->startOfMonth();
                    $fin = $ahora->copy()->endOfMonth();
                    $campoAgrupar = "FLOOR((EXTRACT(DAY FROM created_at) - 1) / 7) + 1";
                    break;

                case 'año':
                    $inicio = $ahora->copy()->startOfYear();
                    $fin = $ahora->copy()->endOfYear();
                    $campoAgrupar = "TO_CHAR(created_at, 'Month')";
                    break;

                default:
                    $inicio = $ahora->copy()->startOfDay();
                    $fin = $ahora->copy()->endOfDay();
                    $formato = 'HH24:00';
                    $campoAgrupar = "TO_CHAR(created_at, '$formato')";
                    break;
            }



            $vistasRaw = DB::table('visits')
                ->select(DB::raw("$campoAgrupar AS grupo"), DB::raw("COUNT(*) AS total"))
                ->whereBetween('created_at', [$inicio, $fin])
                ->groupBy(DB::raw($campoAgrupar))
                ->orderBy(DB::raw("MIN(created_at)"))
                ->get();


            // Log de depuración
            Log::info('VISITAS RAW:', $vistasRaw->toArray());
            if ($filtro === 'semana') {
                $dias = collect();
                $hoy = Carbon::today();
                $inicio = $hoy->copy()->subDays(6)->startOfDay(); // 7 días hacia atrás incluyendo hoy
                $fin = $hoy->copy()->endOfDay();

                // Crear días vacíos (7 días atrás incluyendo hoy)
                for ($i = 6; $i >= 0; $i--) {
                    $fecha = $hoy->copy()->subDays($i);
                    $dias->put($fecha->format('Y-m-d'), [
                        'grupo' => ucfirst($fecha->translatedFormat('l')),
                        'total' => 0,
                    ]);
                }

                // Log para verificar días
                Log::info("FILTRO PERSONALIZADO - Semana (últimos 7 días):");
                Log::info("DÍAS DE REFERENCIA:", $dias->keys()->toArray());

                // Traer datos reales de la base de datos
                $vistasRaw = DB::table('visits')
                    ->select(DB::raw("DATE(created_at) AS fecha"), DB::raw("COUNT(*) AS total"))
                    ->whereBetween('created_at', [$inicio, $fin])
                    ->groupBy(DB::raw("DATE(created_at)"))
                    ->pluck('total', 'fecha');

                // Rellenar los días con los valores reales si existen
                $dias = $dias->mapWithKeys(function ($data, $fecha) use ($vistasRaw) {
                    if (isset($vistasRaw[$fecha])) {
                        $data['total'] = $vistasRaw[$fecha];
                    }
                    return [$fecha => $data];
                });

                $vistas = $dias->values()->map(function ($item) {
                    return (object)[
                        'grupo' => $item['grupo'],
                        'total' => $item['total'],
                    ];
                });
            }

          else {
                $vistas = $vistasRaw->map(function ($vista) use ($filtro) {
                    try {
                        if ($filtro === 'mes') {
                            $vista->grupo = 'Semana ' . $vista->grupo;
                        } elseif ($filtro === 'año') {
                            $vista->grupo = ucfirst(trim($vista->grupo));
                        } elseif ($filtro === 'ultimos3dias') {
                            $vista->grupo = Carbon::parse($vista->grupo)->translatedFormat('l');
                        }
                    } catch (\Exception $e) {
                        // Si falla, deja el grupo como está
                    }
                    return $vista;
                });
            }

            // Métricas generales
            $usuarios = DB::table('users')->count();
            $registrados = DB::table('users')->whereBetween('created_at', [$inicio, $fin])->count();
            $activos = DB::table('users')->whereBetween('last_login_at', [$inicio, $fin])->count();
            $conectados = DB::table('users')->where('is_online', true)->count();

            return response()->json([
                'vistas' => $vistas,
                'usuarios' => $usuarios,
                'registrados' => $registrados,
                'activos' => $activos,
                'conectados' => $conectados,
            ]);
        } catch (\Throwable $e) {
            Log::error('Error al obtener estadísticas: ' . $e->getMessage());
            return response()->json([
                'error' => 'Error interno del servidor',
                'detalle' => $e->getMessage()
            ], 500);


        }
    }
}
