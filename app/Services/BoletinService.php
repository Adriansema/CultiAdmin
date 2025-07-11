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
            ['a', 'e', 'i', 'o', 'u', 'u', 'u', 'n', 'A', 'E', 'I', 'O', 'U', 'U', 'N'], // Corregido 'ñ' a 'n' en mayúscula
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
        // El per_page se tomará del request si se envía, de lo contrario, se usará 10.
        // Esto es compatible con la paginación de Laravel que se renderiza en Blade.
        $perPage = in_array($request->input('per_page'), [5, 10, 25, 50, 100])
            ? $request->input('per_page')
            : 9;

        $query  = $request->input('q');
        $estado = $request->input('estado');
        $sortBy        = $request->input('sort_by'); // Nuevo: columna para ordenar
        $sortDirection = $request->input('sort_direction'); // Nuevo: dirección de ordenamiento

        $boletines = Boletin::query();

        // Búsqueda robusta por nombre, descripción y fecha de creación
        if ($query) {
            $cleanedQuery = $this->cleanSearchQuery($query);

            $boletines->where(function ($q2) use ($cleanedQuery, $query) {
                $q2->whereRaw("REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(LOWER(nombre), 'á', 'a'), 'é', 'e'), 'í', 'i'), 'ó', 'o'), 'ú', 'u'), 'ü', 'u'), 'ñ', 'n'), '.', ''), '-', '') LIKE ?", ['%' . $cleanedQuery . '%'])
                    ->orWhereRaw("REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(LOWER(descripcion), 'á', 'a'), 'é', 'e'), 'í', 'i'), 'ó', 'o'), 'ú', 'u'), 'ü', 'u'), 'ñ', 'n'), '.', ''), '-', '') LIKE ?", ['%' . $cleanedQuery . '%']);

                try {
                    $date = Carbon::parse($query);
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

        // Ordenamiento genérico por columna y dirección
        $allowedSortColumns = [
            'nombre',
            'descripcion',
            'created_at',
            'estado',
            'precio_mas_alto',
            'precio_mas_bajo',
        ];

        $allowedSortDirections = ['asc', 'desc'];

        if (in_array($sortBy, $allowedSortColumns) && in_array($sortDirection, $allowedSortDirections)) {
            $boletines->orderBy($sortBy, $sortDirection);
        } else {
            // Ordenamiento por defecto si no hay un orden válido o si no se especifica
            $boletines->orderBy('created_at', 'desc');
        }

        // Cargar relaciones necesarias para las vistas de la tabla
        $boletines->with(['user.roles', 'validador', 'rechazador']);

        return $boletines->paginate($perPage)->withQueryString();
    }
}
