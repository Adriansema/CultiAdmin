<?php

namespace App\Http\Controllers;

use App\Models\ProductoAgricola;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProductoAgricolaController extends Controller
{
    public function index()
    {
        $productos = ProductoAgricola::all();
        return view('productos_agricolas.index', compact('productos'));
    }

    public function create()
    {
        return view('productos_agricolas.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'tipo' => 'required|string',
            'suelo' => 'required|string',
            'caracteristicas' => 'nullable|string',
            'imagen' => 'nullable|image|max:2048',
        ]);

        $imagenPath = $request->file('imagen')?->store('productos', 'public');

        ProductoAgricola::create([
            'nombre' => $request->nombre,
            'tipo' => $request->tipo,
            'suelo' => $request->suelo,
            'caracteristicas' => $request->caracteristicas,
            'imagen' => $imagenPath,
            'user_id' => Auth::id(),
        ]);

        return redirect()->route('productos-agricolas.index')->with('success', 'Producto creado correctamente');
    }

    public function show(ProductoAgricola $productoAgricola)
    {
        return view('productos_agricolas.show', compact('productoAgricola'));
    }

    public function edit(ProductoAgricola $productoAgricola)
    {
        return view('productos_agricolas.edit', compact('productoAgricola'));
    }

    public function update(Request $request, ProductoAgricola $productoAgricola)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'tipo' => 'required|string',
            'suelo' => 'required|string',
            'caracteristicas' => 'nullable|string',
            'imagen' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('imagen')) {
            Storage::disk('public')->delete($productoAgricola->imagen);
            $productoAgricola->imagen = $request->file('imagen')->store('productos', 'public');
        }

        $productoAgricola->update([
            'nombre' => $request->nombre,
            'tipo' => $request->tipo,
            'suelo' => $request->suelo,
            'caracteristicas' => $request->caracteristicas,
        ]);

        return redirect()->route('productos-agricolas.index')->with('success', 'Producto actualizado');
    }

    public function destroy(ProductoAgricola $productoAgricola)
    {
        if ($productoAgricola->imagen) {
            Storage::disk('public')->delete($productoAgricola->imagen);
        }

        $productoAgricola->delete();

        return redirect()->route('productos-agricolas.index')->with('success', 'Producto eliminado');
    }

    public function validar($id)
    {
        $producto = ProductoAgricola::findOrFail($id);
        $producto->update(['estado' => 'validado', 'observaciones' => null]);

        return redirect()->back()->with('success', 'Producto validado correctamente.');
    }

    public function rechazar(Request $request, $id)
    {
        $request->validate([
            'observaciones' => 'required|string|min:5',
        ]);

        $producto = ProductoAgricola::findOrFail($id);
        $producto->update([
            'estado' => 'rechazado',
            'observaciones' => $request->observaciones,
        ]);

        return redirect()->back()->with('error', 'Producto rechazado con observaciones.');
    }

    public function pendientes()
    {
        // Solo trae productos con estado 'pendiente'
        $productos = ProductoAgricola::where('estado', 'pendiente')->get();

        return view('productos-agricolas.pendientes', compact('productos'));
    }
}
