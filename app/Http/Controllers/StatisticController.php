<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class StatisticController extends Controller
{
    /* public function getStatistics(Request $request)
    {
        $filtro = $request->query('filtro', 'hoy');
        $ahora = Carbon::now();

        switch ($filtro) {
            case 'hoy':
                $inicio = $ahora->copy()->startOfDay();
                $fin = $ahora->copy()->endOfDay();
                $formato = "HH24:00"; // cada hora
                break;
            case 'semana':
                $inicio = $ahora->copy()->startOfWeek();
                $fin = $ahora->copy()->endOfWeek();
                $formato = "Day"; // día de la semana
                break;
            case 'mes':
                $inicio = $ahora->copy()->startOfMonth();
                $fin = $ahora->copy()->endOfMonth();
                $formato = "DD"; // día del mes
                break;
            case 'año':
                $inicio = $ahora->copy()->startOfYear();
                $fin = $ahora->copy()->endOfYear();
                $formato = "Month"; // nombre del mes
                break;
            default:
                $inicio = $ahora->copy()->startOfDay();
                $fin = $ahora->copy()->endOfDay();
                $formato = "HH24:00";
                break;
        }

        // Datos de la gráfica de visitas
        $vistas = DB::table('visits')
            ->select(DB::raw("TO_CHAR(created_at, '$formato') AS grupo"), DB::raw("COUNT(*) AS total"))
            ->whereBetween('created_at', [$inicio, $fin])
            ->groupBy(DB::raw("TO_CHAR(created_at, '$formato')"))
            ->orderBy(DB::raw("MIN(created_at)"))
            ->get();

        // Métricas
        $usuarios = DB::table('users')->count();

        $registrados = DB::table('users')
            ->whereBetween('created_at', [$inicio, $fin])
            ->count();

        $activos = DB::table('users')
            ->whereBetween('last_login_at', [$inicio, $fin])
            ->count();

        $conectados = DB::table('users')
            ->where('is_online', true) // ajusta según tu campo real
            ->count();

        return response()->json([
            'vistas' => $vistas,
            'usuarios' => $usuarios,
            'registrados' => $registrados,
            'activos' => $activos,
            'conectados' => $conectados,
        ]);
    } */

    public function getStatistics(Request $request)
    {
        try {
            // Aquí obtienes las estadísticas de la base de datos
            // Por ejemplo, si estás utilizando un modelo llamado Statistic:
            $filtro = $request->query('filtro', 'default'); // Opción de filtro por defecto

            // Suponiendo que `Statistic` es el modelo para las estadísticas:
            $statistics = Statistic::where('filtro', $filtro)->get();

            // Si usas JackChart para visualización, tal vez necesites estructurar los datos de manera adecuada
            $data = $statistics->map(function ($stat) {
                return [
                    'mes' => $stat->mes,
                    'vistas' => $stat->vistas,
                ];
            });

            return response()->json($data);
        } catch (\Exception $e) {
            \Log::error('Error al obtener las estadísticas: ' . $e->getMessage());
            return response()->json(['error' => 'Error al obtener las estadísticas'], 500);
        }
    }
}
