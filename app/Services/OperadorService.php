<?php

namespace App\Services;

use App\Models\Producto;
use App\Models\Boletin;
use Illuminate\Http\Request;

// Renombrado de ProductService a OperadorService para mayor claridad
class OperadorService
{
    public function obtenerProductosYBoletinesFiltrados(Request $request)
    {
        $perPage = in_array($request->input('per_page'), [5, 10, 25, 50, 100])
            ? $request->input('per_page')
            : 10; // Un valor por defecto más común, ajusta si lo necesitas

        // 1. Consulta para Productos
        $productosQuery = Producto::query();

        // Filtrado de productos (ej. por estado 'pendiente')
        $productosQuery->where('estado', 'pendiente');

        // Puedes añadir aquí filtros específicos para productos
        if ($request->filled('search_producto')) {
            $productosQuery->where('nombre', 'like', '%' . $request->input('search_producto') . '%')
                           ->orWhere('tipo', 'like', '%' . $request->input('search_producto') . '%');
        }

        // Ordena los productos
        $productosQuery->orderBy('created_at', 'desc'); // O por 'nombre', 'tipo', etc.

        // Retorna los productos paginados
        $productos = $productosQuery->paginate($perPage, ['*'], 'productos_page'); // 'productos_page' para diferenciar paginadores

        // 2. Consulta para Boletines
        $boletinesQuery = Boletin::query();

        // Filtrado de boletines (ej. por estado 'pendiente')
        $boletinesQuery->where('estado', 'pendiente');

        // Puedes añadir aquí filtros específicos para boletines
        if ($request->filled('search_boletin')) {
            $boletinesQuery->where('titulo', 'like', '%' . $request->input('search_boletin') . '%')
                           ->orWhere('contenido', 'like', '%' . $request->input('search_boletin') . '%');
        }

        // Ordena los boletines
        $boletinesQuery->orderBy('created_at', 'desc'); // O por 'titulo', etc.

        // Retorna los boletines paginados
        $boletines = $boletinesQuery->paginate($perPage, ['*'], 'boletines_page'); // 'boletines_page' para diferenciar paginadores

        // Retornar ambos resultados en un array o un objeto
        return [
            'productos' => $productos,
            'boletines' => $boletines,
        ];
    }
}