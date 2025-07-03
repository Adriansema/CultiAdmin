<?php

namespace App\Services;

use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; // Necesitamos importar DB para usar DB::raw()

class ProductService
{
    /**
     * Limpia el texto de búsqueda para una comparación robusta.
     * Convierte a minúsculas, elimina tildes y caracteres especiales.
     *
     * @param string $text El texto a limpiar.
     * @return string El texto limpio.
     */
    private function cleanSearchQuery(string $text): string
    {
        // Convertir a minúsculas
        $text = mb_strtolower($text, 'UTF-8');

        // Reemplazar caracteres con tildes por sus equivalentes sin tilde
        $text = str_replace(
            ['á', 'é', 'í', 'ó', 'ú', 'ü', 'ñ', 'Á', 'É', 'Í', 'Ó', 'Ú', 'Ü', 'Ñ'],
            ['a', 'e', 'i', 'o', 'u', 'u', 'n', 'A', 'E', 'I', 'O', 'U', 'U', 'N'],
            $text
        );

        // Remover caracteres no alfanuméricos (excepto espacios)
        // Esto elimina puntos, comas, guiones, etc., dejando solo letras, números y espacios
        $text = preg_replace('/[^a-z0-9\s]/', '', $text);

        // Remover espacios múltiples y eliminar espacios al inicio/final
        $text = trim(preg_replace('/\s+/', ' ', $text));

        return $text;
    }

    /**
     * Obtiene productos filtrados con búsqueda robusta por la columna 'tipo'.
     *
     * @param Request $request La solicitud HTTP con los parámetros de filtro.
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function obtenerProductosFiltrados(Request $request)
    {
        // Define la cantidad de elementos por página, con un valor por defecto y opciones válidas
        $perPage = in_array($request->input('per_page'), [5, 10, 25, 50, 100])
            ? $request->input('per_page')
            : 5; // Por defecto 5

        // Usamos 'q' para la búsqueda general.
        $searchQuery = $request->input('q');

        $productos = Producto::query();

        // Búsqueda robusta solo en la columna 'tipo'
        if ($searchQuery) {
            // Limpiamos el texto de búsqueda ingresado por el usuario una vez
            $cleanedSearchQuery = $this->cleanSearchQuery($searchQuery);
            /* dd($searchQuery, $cleanedSearchQuery); */

            $productos->where(function ($q) use ($cleanedSearchQuery) {
                // Función SQL para normalizar texto.
                // Asumimos que 'tipo' es TEXT/VARCHAR.
                $sqlNormalize = function($column) {
                    $fechaCampos = ['created_at', 'updated_at'];
                    // Si es fecha, conviértelo a texto
                    if (in_array($column, $fechaCampos)) {
                        $column = "TO_CHAR({$column}, 'YYYY-MM-DD HH24:MI:SS')";
                    }

                    return "REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(LOWER({$column}), 'á', 'a'), 'é', 'e'), 'í', 'i'), 'ó', 'o'), 'ú', 'u'), 'ü', 'u'), 'ñ', 'n'), '.', ''), '-', '')";
                };

                // Búsqueda robusta únicamente en la columna 'tipo'
                $q->orWhereRaw($sqlNormalize('tipo') . ' LIKE ?', ['%' . $cleanedSearchQuery . '%'])
                ->orWhereRaw($sqlNormalize('observaciones') . ' LIKE ?', ['%' . $cleanedSearchQuery . '%'])
                ->orWhereRaw($sqlNormalize('estado') . ' LIKE ?', ['%' . $cleanedSearchQuery . '%'])
                ->orWhereRaw($sqlNormalize('RutaVideo') . ' LIKE ?', ['%' . $cleanedSearchQuery . '%'])
                ->orWhereRaw($sqlNormalize('created_at') . ' LIKE ?', ['%' . $cleanedSearchQuery . '%']);
            });
            /* dd($productos->toSql(), $productos->getBindings()); */
        }

        // Ordena los productos
        $productos->orderBy('tipo', 'asc'); // Mantiene tu ordenamiento original

        // Retorna los productos paginados
        return $productos->paginate($perPage)->withQueryString();
    }
}
