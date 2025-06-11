<?php

namespace App\Services;

use App\Models\Producto;
use Illuminate\Http\Request;

class ProductService
{
    public function obtenerProductosFiltrados(Request $request)
    {
        // Define la cantidad de elementos por página, con un valor por defecto y opciones válidas
        $perPage = in_array($request->input('per_page'), [5, 10, 25, 50, 100])
            ? $request->input('per_page')
            : 5; // Por defecto 5

        $query = Producto::query();

        // Lógica de filtrado:
        // Si el campo 'tipo' está presente en la solicitud, aplica el filtro.
        if ($request->filled('nombre')) {
            $query->where('tipo', 'like', '%' . $request->input('nombre') . '%');
        }

        // Ordena los productos
        $query->orderBy('tipo', 'asc');

        // Retorna los productos paginados
        return $query->paginate($perPage)->withQueryString(); // Agrega withQueryString()
    }
}