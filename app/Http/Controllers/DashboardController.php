<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        return view('dashboard');
    }

    public function getData($range)
    {
        $startDate = now();
        switch ($range) {
            case 'hoy':
                $startDate = now()->startOfDay();
                break;
            case 'semana':
                $startDate = now()->startOfWeek();
                break;
            case 'mes':
                $startDate = now()->startOfMonth();
                break;
            case 'año':
                $startDate = now()->startOfYear();
                break;
        }

        $visits = DB::table('visits')
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->where('created_at', '>=', $startDate)
            ->groupBy('date')
            ->get();

        $registrations = DB::table('users')
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->where('created_at', '>=', $startDate)
            ->groupBy('date')
            ->get();

        $connectedUsers = DB::table('users') // Simulación
            ->where('last_seen', '>=', now()->subMinutes(15))
            ->count();

        return response()->json([
            'visits' => $visits,
            'registrations' => $registrations,
            'total_users' => DB::table('users')->count(),
            'registered' => DB::table('users')->where('created_at', '>=', $startDate)->count(),
            'active' => rand(3000, 4000), // Simulación
            'connected' => $connectedUsers,
        ]);
    }
}
