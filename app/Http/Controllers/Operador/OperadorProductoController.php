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

    public function showProducto($id)
    {
        $producto = Producto::findOrFail($id);

        if ($producto->estado !== 'pendiente') {
            abort(403, 'Este producto ya fue procesado.');
        }

        return view('operador.productos.show', compact('producto'));
    }

    public function showBoletin($id)
    {
        $boletin = Boletin::findOrFail($id);

        if ($boletin->estado !== 'pendiente') {
            abort(403, 'Este boletín ya fue procesado.');
        }

        return view('operador.boletines.show', compact('boletin'));
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

    }
