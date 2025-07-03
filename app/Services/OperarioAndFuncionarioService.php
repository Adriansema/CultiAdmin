<?php

namespace App\Services;

use App\Models\Producto;
use App\Models\Boletin;
use App\Models\Noticia; // Importar el modelo Noticia
use Illuminate\Http\Request;

class OperarioAndFuncionarioService
{
    /**
     * Obtiene productos, boletines y noticias filtrados, incluyendo un filtro por estado.
     * Este método ahora orquesta las llamadas a los métodos individuales.
     */
    public function obtenerProductosBoletinesNoticiasFiltrados(Request $request)
    {
        // Llama a los métodos separados para obtener productos y boletines
        $productos = $this->obtenerProductosFiltrados($request);
        $boletines = $this->obtenerBoletinesFiltrados($request);
        $noticias = $this->obtenerNoticiasFiltradas($request); // Obtener las noticias directamente

        // Retornar todos los resultados
        return [
            'productos' => $productos,
            'boletines' => $boletines,
            'noticias' => $noticias, // Directamente la colección paginada
        ];
    }

    /**
     * Obtiene productos filtrados por varios criterios, incluyendo un filtro por estado.
     */
    public function obtenerProductosFiltrados(Request $request)
    {
        // Define la cantidad de elementos por página, con un valor por defecto
        $perPage = in_array($request->input('per_page'), [5, 10, 25, 50, 100])
            ? $request->input('per_page')
            : 10;

        // Obtiene el estado de la solicitud, por defecto 'pendiente'
        $estado = $request->input('estado', 'pendiente');

        // Inicia la consulta para Productos
        $productosQuery = Producto::query();

        // Aplicar filtro por estado para productos
        $productosQuery->where('estado', $estado);

        // Filtrado específico para productos
        if ($request->filled('search_producto')) {
            $searchTerm = '%' . $request->input('search_producto') . '%';
            $productosQuery->where(function ($query) use ($searchTerm) {
                $query->where('nombre', 'like', $searchTerm)
                      ->orWhere('tipo', 'like', $searchTerm);
            });
        }

        // Ordena los productos
        $productosQuery->orderBy('created_at', 'desc');

        // Retorna los productos paginados
        return $productosQuery->paginate($perPage, ['*'], 'productos_page');
    }

    /**
     * Obtiene boletines filtrados por varios criterios, incluyendo un filtro por estado.
     */
    public function obtenerBoletinesFiltrados(Request $request)
    {
        // Define la cantidad de elementos por página, con un valor por defecto
        $perPage = in_array($request->input('per_page'), [5, 10, 25, 50, 100])
            ? $request->input('per_page')
            : 10;

        // Obtiene el estado de la solicitud, por defecto 'pendiente'
        $estado = $request->input('estado', 'pendiente');

        // Inicia la consulta para Boletines
        $boletinesQuery = Boletin::query();

        // Aplicar filtro por estado para boletines
        $boletinesQuery->where('estado', $estado);

        // Filtrado específico para boletines
        if ($request->filled('search_boletin')) {
            $searchTerm = '%' . $request->input('search_boletin') . '%';
            $boletinesQuery->where(function ($query) use ($searchTerm) {
                $query->where('titulo', 'like', $searchTerm)
                      ->orWhere('contenido', 'like', $searchTerm);
            });
        }

        // Ordena los boletines
        $boletinesQuery->orderBy('created_at', 'desc');

        // Retorna los boletines paginados
        return $boletinesQuery->paginate($perPage, ['*'], 'boletines_page');
    }

    /**
     * Obtiene noticias filtradas por varios criterios, incluyendo un filtro por estado.
     */
    public function obtenerNoticiasFiltradas(Request $request)
    {
        // Define la cantidad de elementos por página, con un valor por defecto
        $perPage = in_array($request->input('per_page'), [5, 10, 25, 50, 100])
            ? $request->input('per_page')
            : 10;

        // Obtiene el estado de la solicitud, por defecto 'pendiente'
        $estado = $request->input('estado', 'pendiente');

        // Inicia la consulta para Noticia
        $noticiasQuery = Noticia::query();

        // Aplicar filtro por estado para noticias
        $noticiasQuery->where('estado', $estado);

        // Filtrado general de noticias por 'search_noticia'
        if ($request->filled('search_noticia')) {
            $searchTerm = '%' . $request->input('search_noticia') . '%';
            $noticiasQuery->where(function ($query) use ($searchTerm) {
                $query->where('titulo', 'like', $searchTerm)
                      ->orWhere('tipo', 'like', $searchTerm)
                      ->orWhere('clase', 'like', $searchTerm);
            });
        }

        // Filtrado por 'auto' o 'creador' (asumiendo que es el nombre del usuario creador)
        if ($request->filled('search_creador')) {
            $creadorTerm = '%' . $request->input('search_creador') . '%';
            $noticiasQuery->whereHas('user', function ($query) use ($creadorTerm) {
                $query->where('name', 'like', $creadorTerm); // Asumiendo que el nombre del usuario está en el campo 'name'
            });
        }

        // Ordena las noticias
        $noticiasQuery->orderBy('created_at', 'desc');

        // Retorna las noticias paginadas directamente
        return $noticiasQuery->paginate($perPage, ['*'], 'noticias_page');
    }
}
