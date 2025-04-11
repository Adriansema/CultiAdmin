<?php

namespace App\Http\Controllers\Operador;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Producto;
use App\Models\Boletin;

class OperadorProductoController extends Controller
{
    public function pendientes()
    {
        // Verifica si el usuario tiene permisos para ver los productos pendientes
        $productos = Producto::where('estado', 'pendiente')->latest()->paginate(10);
        // Verifica si el usuario tiene permisos para ver los boletines pendientes
        $boletines = Boletin::where('estado', 'pendiente')->latest()->paginate(10);

        return view('operador.pendientes', compact('productos', 'boletines'));
    }

    public function validar($id)
    {
        $producto = Producto::findOrFail($id);
        $producto->update([
            'estado' => 'aprobado',
            'observaciones' => null,
        ]);

        return back()->with('success', 'Producto aprobado.');
    }

    public function rechazar(Request $request, $id)
    {
        $request->validate([
            'observaciones' => 'required|string',
        ]);

        $producto = Producto::findOrFail($id);
        $producto->update([
            'estado' => 'rechazado',
            'observaciones' => $request->observaciones,
        ]);

        return back()->with('error', 'Producto rechazado.');
    }

    public function validarBoletin($id)
    {
        $boletin = Boletin::findOrFail($id);
        $boletin->update([
            'estado' => 'aprobado',
            'observaciones' => null,
        ]);

        return back()->with('success', 'Boletín aprobado.');
    }

    public function rechazarBoletin(Request $request, $id)
    {
        $request->validate([
            'observaciones' => 'required|string',
        ]);

        $boletin = Boletin::findOrFail($id);
        $boletin->update([
            'estado' => 'rechazado',
            'observaciones' => $request->observaciones,
        ]);

        return back()->with('error', 'Boletín rechazado.');
    }

    public function historial(Request $request)
    {
        $estado = $request->input('estado');
        $tipo = $request->input('tipo');
        $fechaInicio = $request->input('fecha_inicio');
        $fechaFin = $request->input('fecha_fin');

        $productos = Producto::query()->whereIn('estado', ['aprobado', 'rechazado']);
        $boletines = Boletin::query()->whereIn('estado', ['aprobado', 'rechazado']);

        if ($estado) {
            $productos->where('estado', $estado);
            $boletines->where('estado', $estado);
        }

        if ($fechaInicio) {
            $productos->whereDate('updated_at', '>=', $fechaInicio);
            $boletines->whereDate('updated_at', '>=', $fechaInicio);
        }

        if ($fechaFin) {
            $productos->whereDate('updated_at', '<=', $fechaFin);
            $boletines->whereDate('updated_at', '<=', $fechaFin);
        }

        // Si el usuario filtra por tipo
        $historialProductos = $tipo === 'boletin' ? collect() : $productos->latest()->paginate(5)->withQueryString();
        $historialBoletines = $tipo === 'producto' ? collect() : $boletines->latest()->paginate(5)->withQueryString();

        return view('operador.historial', compact('historialProductos', 'historialBoletines'));
    }
}
