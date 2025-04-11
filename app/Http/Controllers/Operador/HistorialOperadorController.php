<?php

namespace App\Http\Controllers\Operador;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Producto;
use App\Models\Boletin;

class HistorialOperadorController extends Controller
{
    public function index(Request $request)
    {
        $tipo = $request->input('tipo', 'producto');
        $estado = $request->input('estado');
        $desde = $request->input('desde');
        $hasta = $request->input('hasta');

        $queryProducto = Producto::query();
        $queryBoletin = Boletin::query();

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

        return view('operador.historial.index', [
            'items' => $tipo === 'producto' ? $productos : $boletines,
            'tipo' => $tipo,
            'filtros' => [
                'estado' => $estado,
                'desde' => $desde,
                'hasta' => $hasta,
            ],
        ]);
    }

    public function showProducto(Producto $producto)
    {
        return view('operador.show-producto', compact('producto'));
    }

    public function showBoletin(Boletin $boletin)
    {
        return view('operador.show-boletin', compact('boletin'));
    }

}
