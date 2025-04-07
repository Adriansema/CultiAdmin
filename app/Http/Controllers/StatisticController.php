<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StatisticController extends Controller
{
    public function index()
    {
        //  Consultar visitas: agrupar por fecha y contar cuántas visitas hubo cada día
        $visits = DB::table('visits')
            ->select(DB::raw("COUNT(id) as count, created_at::date as date"))
            ->groupBy(DB::raw("created_at::date"))
            ->orderBy(DB::raw("created_at::date"), 'asc')
            ->get();
            
          // Consultar registros de usuarios: agrupar por fecha y contar cuántos usuarios se registraron por día
        $registrations = DB::table('users')
            ->select(DB::raw("COUNT(id) as count, created_at::date as date"))
            ->groupBy(DB::raw("created_at::date"))
            ->orderBy(DB::raw("created_at::date"), 'asc')
            ->get();

            // Devolver los datos como JSON
        return response()->json([
            'visits' => $visits,
            'registrations' => $registrations
        ]);
    }
}


