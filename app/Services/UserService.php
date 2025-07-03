<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; // Necesitamos importar DB para usar DB::raw()

class UserService
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
     * Obtiene usuarios filtrados con búsqueda robusta por nombre o email,
     * y filtros adicionales por estado y rol.
     *
     * @param Request $request La solicitud HTTP con los parámetros de filtro.
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function obtenerUsuariosFiltrados(Request $request)
    {
        $perPage = in_array($request->input('per_page'), [5, 10, 25, 50, 100])
            ? $request->input('per_page')
            : 10;

        $query      = $request->input('q');        // Búsqueda general
        $estado     = $request->input('estado');   // 'activo' o 'inactivo'
        $rol        = $request->input('rol');      // Nombre del rol

        $usuarios = User::with('roles');

        // Búsqueda robusta por nombre o email
        if ($query) {
            // Limpiamos el texto de búsqueda ingresado por el usuario una vez
            $cleanedQuery = $this->cleanSearchQuery($query);

            $usuarios->where(function ($q2) use ($cleanedQuery) {
                // Para hacer una búsqueda robusta en la base de datos,
                // la mejor forma es normalizar también la columna en la consulta.
                // Sin embargo, esto puede ser menos eficiente si la tabla es muy grande
                // ya que evita el uso de índices.
                // Una solución más optimizada para producción sería tener una columna 'normalizada'
                // en la base de datos y buscar directamente en ella (ej. normalized_name).

                // Búsqueda robusta en la columna 'name'
                // DB::raw() se usa para ejecutar una expresión SQL cruda.
                // Aquí, simulamos la limpieza de la columna 'name' para compararla con el 'cleanedQuery'.
                $q2->whereRaw("REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(LOWER(name), 'á', 'a'), 'é', 'e'), 'í', 'i'), 'ó', 'o'), 'ú', 'u'), 'ü', 'u'), 'ñ', 'n'), '.', ''), '-', '') LIKE ?", ['%' . $cleanedQuery . '%'])
                   // O si prefieres una expresión regular más simple que elmina tildes y caracteres no alfanumericos.
                   // Ten en cuenta que REGEXP es menos universal y puede no ser tan performante como una columna normalizada.
                   // ->orWhereRaw("LOWER(REGEXP_REPLACE(name, '[^a-zA-Z0-9]', '')) LIKE ?", ['%' . $cleanedQuery . '%'])

                   // Búsqueda robusta en la columna 'email'
                   ->orWhereRaw("REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(LOWER(email), 'á', 'a'), 'é', 'e'), 'í', 'i'), 'ó', 'o'), 'ú', 'u'), 'ü', 'u'), 'ñ', 'n'), '.', ''), '-', '') LIKE ?", ['%' . $cleanedQuery . '%']);
            });
        }

        // Filtro por estado (si está presente)
        if ($estado === 'activo' || $estado === 'inactivo') {
            $usuarios->where('estado', $estado);
        }

        // Filtro por rol
        if ($rol) {
            $usuarios->whereHas('roles', function ($q3) use ($rol) {
                $q3->where('name', $rol);
            });
        }

        return $usuarios->paginate($perPage)->withQueryString();
    }
     public function getUsersFilteredQueryForAjax(Request $request) // <-- ¡MÉTODO NUEVO!
    {
        $usersQuery = User::with('roles'); // Inicia la consulta del modelo User, carga roles

        // 1. FILTRO POR ESTADO (Activo o Inactivo)
        $estado = $request->input('estado', 'todos'); // Usamos 'status' como nombre del parámetro y columna
        if ($estado !== 'todos' && !empty($estado)) {
            $usersQuery->where('estado', $estado);
        }

      
        // 3. FILTRO POR ROL (si lo necesitas en el filtro AJAX)
        $rol = $request->input('rol');
        if ($rol) {
            $usersQuery->whereHas('roles', function ($q3) use ($rol) {
                $q3->where('name', $rol);
            });
        }

        // 4. Ordenar los resultados
        $usersQuery->orderBy('created_at', 'desc');

        return $usersQuery; // Retorna el Query Builder sin ejecutar
    }

}


