<?php

namespace App\Services;

use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ProductService
{
    private function cleanSearchQuery(string $text): string
    {
        $text = mb_strtolower($text, 'UTF-8');
        $text = str_replace(
            ['á', 'é', 'í', 'ó', 'ú', 'ü', 'ñ', 'Á', 'É', 'Í', 'Ó', 'Ú', 'Ü', 'Ñ'],
            ['a', 'e', 'i', 'o', 'u', 'u', 'n', 'A', 'E', 'I', 'O', 'U', 'U', 'N'],
            $text
        );
        $text = preg_replace('/[^a-z0-9\s]/', '', $text);
        $text = trim(preg_replace('/\s+/', ' ', $text));
        return $text;
    }

    public function obtenerProductosFiltrados(Request $request)
    {
        $perPage = in_array($request->input('per_page'), [5, 10, 25, 50, 100])
            ? $request->input('per_page')
            : 10;

        // --- CAMBIO CLAVE AQUÍ: Inicializar las variables al principio ---
        $searchQuery = $request->input('q', ''); // Inicializar con cadena vacía si no está presente
        $estado = $request->input('estado', ''); // Inicializar con cadena vacía si no está presente
        // --- FIN DEL CAMBIO CLAVE ---

        $productos = Producto::query();

        // La lógica de la búsqueda robusta solo se aplica si hay una consulta
        if (!empty($searchQuery)) { // Usar !empty() es más robusto que solo if($searchQuery)
            $cleanedSearchQuery = $this->cleanSearchQuery($searchQuery);
            
            $productos->where(function ($q) use ($cleanedSearchQuery, $searchQuery) { // Asegúrate de pasar $searchQuery si lo usas para Carbon
                $sqlNormalize = function($column) {
                    if (in_array($column, ['created_at', 'updated_at'])) {
                        return "REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(LOWER(TO_CHAR({$column}, 'YYYY-MM-DD HH24:MI:SS')), 'á', 'a'), 'é', 'e'), 'í', 'i'), 'ó', 'o'), 'ú', 'u'), 'ü', 'u'), 'ñ', 'n'), '.', ''), '-', '')";
                    }
                    return "REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(LOWER({$column}), 'á', 'a'), 'é', 'e'), 'í', 'i'), 'ó', 'o'), 'ú', 'u'), 'ü', 'u'), 'ñ', 'n'), '.', ''), '-', '')";
                };

                $q->orWhereRaw($sqlNormalize('tipo') . ' LIKE ?', ['%' . $cleanedSearchQuery . '%'])
                  ->orWhereRaw($sqlNormalize('observaciones') . ' LIKE ?', ['%' . $cleanedSearchQuery . '%'])
                  ->orWhereRaw($sqlNormalize('estado') . ' LIKE ?', ['%' . $cleanedSearchQuery . '%'])
                  ->orWhereRaw($sqlNormalize('RutaVideo') . ' LIKE ?', ['%' . $cleanedSearchQuery . '%']);
                
                // Intenta buscar por fecha limpia si la cadena de búsqueda parece una fecha
                try {
                    $date = Carbon::parse($searchQuery); // Usa $searchQuery original para parsear la fecha
                    $q->orWhereDate('created_at', $date->toDateString());
                } catch (\Exception $e) {
                    // No hace nada si la fecha no es válida
                }
            });
        }

        // Aplicar filtro por estado (ahora 'estado' siempre estará definido)
        if (!empty($estado) && in_array($estado, ['aprobado', 'pendiente', 'rechazado'])) {
            $productos->where('estado', $estado);
        }

        $productos->orderBy('tipo', 'asc');

        return $productos->paginate($perPage)->withQueryString();
    }
}