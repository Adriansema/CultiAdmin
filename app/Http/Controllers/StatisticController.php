<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Statistic;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;



class StatisticController extends Controller
{public function getStatistics(Request $request)
    {
        try {
            $filtro = $request->query('filtro', 'hoy');
            $ahora = Carbon::now();

            switch ($filtro) {
                case 'hoy':
                    $inicio = $ahora->copy()->startOfDay();
                    $fin = $ahora->copy()->endOfDay();
                    $formato = "HH24:00";
                    break;
                case 'semana':
                    $inicio = $ahora->copy()->startOfWeek();
                    $fin = $ahora->copy()->endOfWeek();
                    $formato = "Day";
                    break;
                case 'mes':
                    $inicio = $ahora->copy()->startOfMonth();
                    $fin = $ahora->copy()->endOfMonth();
                    $formato = "DD";
                    break;
                case 'año':
                    $inicio = $ahora->copy()->startOfYear();
                    $fin = $ahora->copy()->endOfYear();
                    $formato = "Month";
                    break;
                default:
                    $inicio = $ahora->copy()->startOfDay();
                    $fin = $ahora->copy()->endOfDay();
                    $formato = "HH24:00";
                    break;
            }

            $vistas = DB::table('visits')
                ->select(DB::raw("TO_CHAR(created_at, '$formato') AS grupo"), DB::raw("COUNT(*) AS total"))
                ->whereBetween('created_at', [$inicio, $fin])
                ->groupBy(DB::raw("TO_CHAR(created_at, '$formato')"))
                ->orderBy(DB::raw("MIN(created_at)"))
                ->get();

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
            return response()->json(['error' => 'Error interno del servidor', 'detalle' => $e->getMessage()], 500);
        }
    }
}

