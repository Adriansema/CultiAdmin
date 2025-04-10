<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class StatisticController extends Controller
{
    public function getStatistics(Request $request)
{
    $filtro = $request->query('filtro', 'hoy');
    $ahora = now();

    switch ($filtro) {
        case 'hoy':
            $desde = $ahora->copy()->startOfDay();
            $hasta = $ahora->copy()->endOfDay();
            $formato = 'HH24'; // hora en 24h
            break;
        case 'semana':
            $desde = $ahora->copy()->startOfWeek();
            $hasta = $ahora->copy()->endOfWeek();
            $formato = 'YYYY-MM-DD'; // día completo
            break;
            case 'mes':
                $desde = $ahora->copy()->startOfMonth();
                $hasta = $ahora->copy()->endOfMonth();
                $formato = 'WW'; // semana del año
                break;
            case 'año':
                $desde = $ahora->copy()->startOfYear();
                $hasta = $ahora->copy()->endOfYear();
                $formato = 'Month'; // nombre del mes
                break;
            default:
                $desde = $ahora->copy()->startOfDay();
                $hasta = $ahora->copy()->endOfDay();
                $formato = 'HH24';
        }
    
        $vistas = DB::table('visits')
            ->selectRaw("to_char(created_at, '{$formato}') as grupo, count(*) as total, date_part('month', created_at) as numero_mes")
            ->whereBetween('created_at', [$desde, $hasta])
            ->groupBy(DB::raw("to_char(created_at, '{$formato}')"), DB::raw("date_part('month', created_at)"))
            ->orderByRaw("date_part('month', created_at)")
            ->get();
    
        if ($filtro === 'año') {
            $meses = collect([
                'January', 'February', 'March', 'April', 'May', 'June',
                'July', 'August', 'September', 'October', 'November', 'December'
            ]);
    
            $vistas = $meses->map(function ($mes) use ($vistas) {
                $dato = $vistas->firstWhere('grupo', trim($mes));
                return [
                    'grupo' => $mes,
                    'total' => $dato->total ?? 0,
                ];
            });
        }
    
        return response()->json([
            'vistas' => $vistas,
            'usuarios' => DB::table('users')->count(),
            'registrados' => DB::table('users')->whereBetween('created_at', [$desde, $hasta])->count(),
            'activos' => DB::table('users')->whereNotNull('last_login_at')->where('last_login_at', '>=', $desde)->count(),
            'conectados' => 10,
        ]);
    }
};