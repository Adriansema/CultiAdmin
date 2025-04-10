<?php

//actualizacion 09/04/2025

namespace App\Http\Controllers\Operador;

use App\Http\Controllers\Controller;
use App\Models\Producto;
use Illuminate\Http\Request;

class OperadorProductoController extends Controller
{
    public function indexPendientes()
    {
        // Obtener los productos con estado 'pendiente'
        $productos = Producto::where('estado', 'pendiente')->get();

        // Devolver la vista con los productos pendientes
        return view('productos.operador.pendientes', compact('productos'));
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
}
