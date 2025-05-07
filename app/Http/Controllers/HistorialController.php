<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Producto;
use App\Models\Boletin;
use Illuminate\Support\Facades\Auth;

class HistorialController extends Controller
{
    public function index(Request $request)
    {
        $userId = Auth::id();
        $tipo = $request->input('tipo', 'producto');
        $estado = $request->input('estado');
        $desde = $request->input('desde');
        $hasta = $request->input('hasta');

        $queryProducto = Producto::where('user_id', $userId);
        $queryBoletin = Boletin::where('user_id', $userId);

        if ($estado) {
            $queryProducto->where('estado', $estado);
            $queryBoletin->where('estado', $estado);
        }

        if ($desde) {
            $queryProducto->whereDate('created_at', '>=', $desde);
            $queryBoletin->whereDate('created_at', '>=', $desde);
        }

        if ($hasta) {
            $queryProducto->whereDate('created_at', '<=', $hasta);
            $queryBoletin->whereDate('created_at', '<=', $hasta);
        }

        $productos = $queryProducto->latest()->paginate(5)->withQueryString();
        $boletines = $queryBoletin->latest()->paginate(5)->withQueryString();

        return view('historial.index', [
            'items' => $tipo === 'producto' ? $productos : $boletines,
            'tipo' => $tipo,
            'filtros' => [
                'estado' => $estado,
                'desde' => $desde,
                'hasta' => $hasta,
            ],
        ]);


    }
}
