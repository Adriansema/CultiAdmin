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
        $boletines = Boletin::orderBy('created_at', 'desc')->take(5)->get();

        // Obtener las últimas 10 noticias para el dashboard
        // Usamos 'with' para cargar la relación 'user' y 'latest' para ordenar por fecha de creación descendente.
        $noticias = Noticia::with('user')
            ->where('leida', false) // ¡Filtra por noticias no leídas!
            ->latest() // Ordena por created_at de forma descendente
            ->limit(10) // Limita a las últimas 10 noticias
            ->get();

        // Pasa tanto los boletines como las noticias a la vista
        return view('dashboard', compact('boletines', 'noticias'));
    }

    public function download($id)
    {
        $boletin = Boletin::findOrFail($id);

        // Suponiendo que tienes un campo 'archivo' con la ruta del archivo
        $filePath = $boletin->archivo;

        if (Storage::exists($filePath)) {
            return Storage::download($filePath);
        } else {
            abort(404, 'Archivo no encontrado');
        }
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
