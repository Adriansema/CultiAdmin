<?php

//actualizacion 09/04/2025

namespace App\Http\Controllers;

use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductoController extends Controller
{
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
            'detalles' => 'required|array',
            'tipo' => 'required|string',
            'imagen' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'observaciones' => 'nullable|string',
        ]);

        $imagen = null;
        if ($request->hasFile('imagen')) {
            $imagen = $request->file('imagen')->store('productos', 'public');
        }

        Producto::create([
            'user_id' => Auth::id(),
            'detalles_json' => json_encode($request->detalles, JSON_UNESCAPED_UNICODE),
            'estado' => 'pendiente',
            'observaciones' => $request->observaciones,
            'imagen' => $imagen,
            'tipo' => $request->tipo,
        ]);

        return redirect()->route('productos.index')->with('success', 'Producto creado con éxito.');
    }


    public function show(Producto $producto)
    {
        return view('productos.show', compact('producto'));
    }

    public function edit(Producto $producto)
    {
        return view('productos.edit', compact('producto'));
    }

    public function update(Request $request, Producto $producto)
    {
        $request->validate([
            'detalles' => 'required|array',
            'tipo' => 'required|string',
            'imagen' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'observaciones' => 'nullable|string',
        ]);

        // Actualizar imagen si viene una nueva
        if ($request->hasFile('imagen')) {
            // Opcional: eliminar imagen anterior si querés
            // Storage::disk('public')->delete($producto->imagen);

            $imagen = $request->file('imagen')->store('productos', 'public');
            $producto->imagen = $imagen;
        }

        // Actualizar los demás campos
        $producto->tipo = $request->tipo;
        $producto->detalles_json = json_encode($request->detalles, JSON_UNESCAPED_UNICODE);
        $producto->observaciones = $request->observaciones;

        $producto->save();

        return redirect()->route('productos.index')->with('success', 'Producto actualizado con éxito.');
    }


    public function destroy(Producto $producto)
    {
        $producto->delete();

        return redirect()->route('productos.index')->with('success', 'Producto eliminado.');
    }

    public function cafe()
    {
        // puedes pasar datos a la vista si necesitas
        return view('productos.cafe');
    }

    public function mora()
    {
        return view('productos.mora');
    }

}
