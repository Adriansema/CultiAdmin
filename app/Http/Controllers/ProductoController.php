<?php

//actualizacion 09/04/2025

namespace App\Http\Controllers;

use App\Models\Producto;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductoController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        $productos = Producto::all();
        return view('productos.index', compact('productos'));
    }

    public function create()
    {
        return view('productos.create');
    }

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
            'user_id' => Auth::id(),
            'estado' => 'pendiente',
        ]);

        return redirect()->route('admin.productos.index')->with('success', 'Producto creado.');
    }

    public function show(Producto $producto)
    {
        $this->authorize('view', $producto);
        return view('productos.show', compact('producto'));
    }

    public function edit(Producto $producto)
    {
        $this->authorize('update', $producto);
        return view('productos.edit', compact('producto'));
    }

    public function update(Request $request, Producto $producto)
    {
        $this->authorize('update', $producto);

        $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'required|string',
        ]);

        $producto->update($request->only('nombre', 'descripcion'));

        return redirect()->route('admin.productos.index')->with('success', 'Producto actualizado.');
    }

    public function destroy(Producto $producto)
    {
        $this->authorize('delete', $producto);
        $producto->delete();

        return redirect()->route('admin.productos.index')->with('success', 'Producto eliminado.');
    }
}
