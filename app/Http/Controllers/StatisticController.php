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
                $inicio = $ahora->copy()->subDays(6)->startOfDay();
                $fin = $ahora->copy()->endOfDay();
                $campoAgrupar = "TO_CHAR(created_at, 'YYYY-MM-DD')";
                break;

            case 'mes':
                $inicio = $ahora->copy()->startOfMonth();
                $fin = $ahora->copy()->endOfMonth();
                $campoAgrupar = "FLOOR((EXTRACT(DAY FROM created_at) - 1) / 7) + 1"; // Agrupa por semana del mes
                break;

            case 'año':
                $inicio = $ahora->copy()->startOfYear();
                $fin = $ahora->copy()->endOfYear();
                $campoAgrupar = "TO_CHAR(created_at, 'Month')"; // Nombre del mes
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

        if ($filtro === 'semana') {
            $hoy = Carbon::today();

            // Inicializar los 7 días con 0
            for ($i = 6; $i >= 0; $i--) {
                $fecha = $hoy->copy()->subDays($i);
                $dias->put($fecha->format('Y-m-d'), [
                    'grupo' => ucfirst($fecha->translatedFormat('l')), // Ej: "Martes"
                    'total' => 0,
                ]);
            }

            // Rellenar con datos reales
            foreach ($vistasRaw as $v) {
                try {
                    $fecha = $hoy->copy()->subDays($i)->format('Y-m-d');
                    if ($dias->has($fecha)) {
                        $dias[$fecha]['total'] = $v->total;
                    }
                } catch (\Exception $e) {
                    Log::error('Error al parsear fecha: ' . $e->getMessage());
                }
            }

            $vistas = $dias->values()->map(function ($item) {
                return (object)[
                    'grupo' => $item['grupo'],
                    'total' => $item['total'],
                ];
            });
        } else {
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

        // Estadísticas generales
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
        Log::info('VISITAS RAW:', $vistasRaw->toArray());
        return response()->json([
            'error' => 'Error interno del servidor',
            'detalle' => $e->getMessage()

        ], 500);
    }
}
}
