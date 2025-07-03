<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Boletin;
use App\Models\Noticia;

use Illuminate\Support\Facades\Storage; // Importa Storage
class DashboardController extends Controller
{
    public function index()
    {
        // Obtener los últimos 10 boletines para el dashboard display
        $boletines = Boletin::latest()
            ->limit(10)
            ->get();

        // Obtener el total de noticias NO LEÍDAS
        $totalUnreadNoticiasCount = Noticia::where('leida', false)->count();

        // Obtener las últimas 10 noticias para el dashboard
        // Usamos 'with' para cargar la relación 'user' y 'latest' para ordenar por fecha de creación descendente.
        $noticias = Noticia::with('user')
            ->where('leida', false) // ¡Filtra por noticias no leídas!
            ->latest() // Ordena por created_at de forma descendente
            ->limit(10) // Limita a las últimas 10 noticias
            ->get();

        // Pasa los boletines, las noticias limitadas y el total de noticias no leídas a la vista
        return view('dashboard', compact('boletines', 'noticias', 'totalUnreadNoticiasCount'));
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
