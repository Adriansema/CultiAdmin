<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use Illuminate\Http\Request;


class ProductoController extends Controller
{
    // Mostrar todos los productos del usuario autenticado
    public function index()
    {
        $productos = Producto::where('user_id', auth()->id())->get();
        return view('productos.index', compact('productos'));
    }

    // Formulario de creación
    public function create()
    {
        return view('productos.create');
    }

    // Guardar nuevo producto
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'required|string',
            'imagen' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $imagen = null;
        if ($request->hasFile('imagen')) {
            $imagen = $request->file('imagen')->store('productos', 'public');
        }

        Producto::create([
            'nombre' => $request->nombre,
            'descripcion' => $request->descripcion,
            'imagen' => $imagen,
            'user_id' => auth()->id(),
            'estado' => 'pendiente'
        ]);

        return redirect()->route('productos.index')->with('success', 'Producto creado.');
    }

    // Ver detalles
    public function show(Producto $producto)
    {
        return view('productos.show', compact('producto'));
    }

    // Formulario de edición
    public function edit(Producto $producto)
    {
        return view('productos.edit', compact('producto'));
    }

    // Actualizar producto
    public function update(Request $request, Producto $producto)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'required|string',
        ]);

        $producto->update($request->only('nombre', 'descripcion'));

        return redirect()->route('productos.index')->with('success', 'Producto actualizado.');
    }

    // Eliminar producto
    public function destroy(Producto $producto)
    {
        $producto->delete();
        return redirect()->route('productos.index')->with('success', 'Producto eliminado.');
    }

    // Vista para operador: productos pendientes
    public function pendientes()
    {
        $productos = Producto::where('estado', 'pendiente')->get();
        return view('productos.pendientes', compact('productos'));
    }

    // Validar producto
    public function validar($id)
    {
        $producto = Producto::findOrFail($id);
        $producto->update([
            'estado' => 'aprobado',
            'observaciones' => null,
        ]);

        return back()->with('success', 'Producto aprobado.');
    }

    // Rechazar producto
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
