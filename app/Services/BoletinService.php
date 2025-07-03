<?php

namespace App\Services;

use App\Models\Boletin;
use Illuminate\Http\Request;
// use Illuminate\Support\Facades\DB; // No necesitas esto si no usas DB::raw() explícitamente fuera de tu sqlNormalize

class BoletinService
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
            ['á', 'é', 'í', 'ó', 'ú', 'ü', 'ñ', 'Á', 'É', 'Í', 'Ó', 'Ú', 'Ü', 'N'],
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
     * Obtiene boletines filtrados con búsqueda robusta por la columna 'contenido'.
     *
     * @param Request $request La solicitud HTTP con los parámetros de filtro.
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function obtenerBoletinFiltrados(Request $request)
    {
        // Define la cantidad de elementos por página, con un valor por defecto y opciones válidas
        $perPage = in_array($request->input('per_page'), [5, 10, 25, 50, 100])
            ? $request->input('per_page')
            : 10; // Por defecto 5

        // Usamos 'q' para la búsqueda general.
        $searchQuery = $request->input('q');
        $estadoFilter = $request->input('estado');

        $boletines = Boletin::query(); // Inicia la consulta del modelo Boletin

        // Lógica de búsqueda robusta general en múltiples columnas
        if ($searchQuery) {
            // Limpiamos y normalizamos el texto de búsqueda ingresado por el usuario
            $cleanedSearchQuery = $this->cleanSearchQuery($searchQuery);

            $boletines->where(function ($q) use ($cleanedSearchQuery) {
                // Función SQL para normalizar texto en la base de datos.
                // Esta función asegura que la búsqueda sea insensible a mayúsculas/minúsculas y acentos.
                $sqlNormalize = function($column, $isDate = false) {
                    // Si es una columna de fecha, la convierte a texto primero usando TO_CHAR (PostgreSQL)
                    if ($isDate) {
                        $column = "TO_CHAR({$column}, 'YYYY-MM-DD HH24:MI:SS')";
                    }
                    // Cadena de REPLACE anidados para eliminar acentos, puntos, guiones y convertir a minúsculas.
                    return "REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(LOWER({$column}), 'á', 'a'), 'é', 'e'), 'í', 'i'), 'ó', 'o'), 'ú', 'u'), 'ü', 'u'), 'ñ', 'n'), '.', ''), '-', '')";
                };

                // APLICAMOS orWhereRaw para buscar en los campos reales de tu DB
                // 'NOMBRE' en la UI (ej. "mora", "cafe") -> Corresponde a 'descripcion' en tu DB
                $q->orWhereRaw($sqlNormalize('descripcion') . ' LIKE ?', ['%' . $cleanedSearchQuery . '%'])
                  // 'DESCRIPCION' en la UI (ej. "kcnjdndnxnxsjsx", "cafe es un cafetero") -> Corresponde a 'observaciones' en tu DB
                  ->orWhereRaw($sqlNormalize('observaciones') . ' LIKE ?', ['%' . $cleanedSearchQuery . '%'])
                  ->orWhereRaw($sqlNormalize('nombre') . ' LIKE ?', ['%' . $cleanedSearchQuery . '%'])
                  // 'FECHA' en la UI (ej. "03 de julio del 2025") -> Corresponde a 'created_at' en tu DB
                  ->orWhereRaw($sqlNormalize('created_at', true) . ' LIKE ?', ['%' . $cleanedSearchQuery . '%']);
            });
        }

        // logica de filtro por estado
        if($estadoFilter && $estadoFilter !== 'todos') {
            $boletines->where('estado', $estadoFilter);
        }

        // Ordena los boletines
        // Ordena por 'created_at' en descendente, que es la columna de fecha que sí existe y es la más lógica para los boletines.
        $boletines->orderBy('created_at', 'desc');

        return $boletines->paginate($perPage)->withQueryString();
    }
}