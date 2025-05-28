<?php

namespace App\Services;

use App\Models\Producto; // Asumo que tu modelo se llama Producto
use Illuminate\Http\Request;

class ProductService
{
    public function obtenerProductosFiltrados(Request $request)
    {
        // Define la cantidad de elementos por página, con un valor por defecto y opciones válidas
        $perPage = in_array($request->input('per_page'), [5, 10, 25, 50, 100])
            ? $request->input('per_page')
            : 5; // Por defecto 5, puedes ajustarlo

        $query = Producto::query();

        // Puedes añadir aquí lógica de filtrado similar a la de tus usuarios
        // Por ejemplo:
        // if ($request->filled('nombre')) {
        //     $query->where('nombre', 'like', '%' . $request->input('nombre') . '%');
        // }
        // if ($request->filled('categoria_id')) {
        //     $query->where('categoria_id', $request->input('categoria_id'));
        // }

        // Ordena los productos (por ejemplo, por nombre)
        $query->orderBy('tipo', 'asc');

        // Retorna los productos paginados
        return $query->paginate($perPage);
    }
}