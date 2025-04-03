<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class StatisticController extends Controller
{
    public function index()
    {
        $visits = DB::table('visits')
            ->select(DB::raw('COUNT(id) as count, DATE(created_at) as date'))
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();

            // Consultar registros de usuarios por fecha
        $registrations = DB::table('users') // Si tu tabla de usuarios tiene otro nombre, cámbialo aquí
            ->select(DB::raw('COUNT(id) as count, DATE(created_at) as date'))
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();
            
// Retornar datos en formato JSON
        return response()->json([
            'visits' => $visits,
            'registrations' => $registrations
        ]);
    }
}


