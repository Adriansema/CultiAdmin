<?php

namespace App\Services;

use App\Models\Boletin; // Asegúrate de que el modelo Boletin esté importado
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon; // Necesario para trabajar con fechas

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

    /**
     * Obtiene boletines filtrados con búsqueda robusta por nombre, descripción y fecha de creación,
     * y filtros adicionales por estado y ordenamiento por precios.
     *
     * @param Request $request La solicitud HTTP con los parámetros de filtro.
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function obtenerBoletinFiltrados(Request $request)
    {
        $perPage = in_array($request->input('per_page'), [5, 10, 25, 50, 100])
            ? $request->input('per_page')
            : 10; // Tu JS está enviando '5', ajusta este valor si necesitas más por defecto

        $query  = $request->input('q'); // Esta es la variable original, no se usa dentro del cierre donde da el error
        $estado = $request->input('estado');
        $precio = $request->input('precio');

        $boletines = Boletin::query();

        // Búsqueda robusta por nombre, descripción y fecha de creación
        if ($query) {
            // Aquí se define $cleanedQuery
            $cleanedQuery = $this->cleanSearchQuery($query);

            $boletines->where(function ($q2) use ($cleanedQuery, $query) { // Asegúrate de pasar $query si la vas a usar para Carbon
                // Búsqueda en 'nombre'
                $q2->whereRaw("REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(LOWER(nombre), 'á', 'a'), 'é', 'e'), 'í', 'i'), 'ó', 'o'), 'ú', 'u'), 'ü', 'u'), 'ñ', 'n'), '.', ''), '-', '') LIKE ?", ['%' . $cleanedQuery . '%'])
                   // Búsqueda en 'descripcion'
                   ->orWhereRaw("REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(LOWER(descripcion), 'á', 'a'), 'é', 'e'), 'í', 'i'), 'ó', 'o'), 'ú', 'u'), 'ü', 'u'), 'ñ', 'n'), '.', ''), '-', '') LIKE ?", ['%' . $cleanedQuery . '%']);

                // Búsqueda por fecha de creación (created_at)
                // Es aquí donde podrías estar intentando usar $query sin pasarla en el 'use'
                try {
                    // Si usas $query aquí, DEBE estar en el 'use' del closure
                    $date = Carbon::parse($query); // <-- Línea 65 es probablemente esta
                    $q2->orWhereDate('created_at', $date->toDateString());
                } catch (\Exception $e) {
                    // Si no es una fecha válida, no se aplica este filtro de fecha
                }
            });
        }

        // Filtro por estado
        if (in_array($estado, ['aprobado', 'pendiente', 'rechazado'])) {
            $boletines->where('estado', $estado);
        }

        // Ordenamiento por precios
        if ($precio) {
            switch ($precio) {
                case 'precio_alto_desc':
                    $boletines->orderBy('precio_mas_alto', 'desc');
                    break;
                case 'precio_alto_asc':
                    $boletines->orderBy('precio_mas_alto', 'asc');
                    break;
                case 'precio_bajo_desc':
                    $boletines->orderBy('precio_mas_bajo', 'desc');
                    break;
                case 'precio_bajo_asc':
                    $boletines->orderBy('precio_mas_bajo', 'asc');
                    break;
                // Puedes añadir un default si quieres un ordenamiento por defecto cuando se selecciona algo inválido
            }
        }

        // Ordenamiento por defecto si no hay un orden de precios específico
        // Esto evita que los resultados salgan en un orden aleatorio si no se aplica un filtro de precio.
        if (!$precio) {
            $boletines->orderBy('created_at', 'desc');
        }


        return $boletines->paginate($perPage)->withQueryString();
    }
}