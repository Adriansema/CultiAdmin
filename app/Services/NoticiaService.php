<?php

namespace App\Services;

use App\Models\Noticia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; // Necesitamos importar DB para usar DB::raw()

class NoticiaService
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
     * Obtiene noticias filtradas con búsqueda robusta general.
     *
     * @param Request $request La solicitud HTTP con los parámetros de filtro.
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function obtenerNoticiaFiltradas(Request $request)
    {
        // Define la cantidad de elementos por página, con un valor por defecto y opciones válidas
        $perPage = in_array($request->input('per_page'), [5, 10, 25, 50, 100])
            ? $request->input('per_page')
            : 10; // Por defecto 5

        $query = $request->input('q');        // Búsqueda general

        $noticias = Noticia::query(); // Mejor usar query() en vez de with('') si no hay relaciones

        // Búsqueda robusta general en múltiples columnas
        if ($query) {
            // Limpiamos el texto de búsqueda ingresado por el usuario una vez
            $cleanedQuery = $this->cleanSearchQuery($query);

            $noticias->where(function ($q2) use ($cleanedQuery) {
                // Función SQL para normalizar texto, para usar en múltiples columnas
                $sqlNormalize = function($column) {
                    return "REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(LOWER({$column}), 'á', 'a'), 'é', 'e'), 'í', 'i'), 'ó', 'o'), 'ú', 'u'), 'ü', 'u'), 'ñ', 'n'), '.', ''), '-', '')";
                };

                // Búsqueda robusta en la columna 'tipo'
                $q2->whereRaw($sqlNormalize('tipo') . ' LIKE ?', ['%' . $cleanedQuery . '%'])
                   // Búsqueda robusta en la columna 'titulo'
                   ->orWhereRaw($sqlNormalize('titulo') . ' LIKE ?', ['%' . $cleanedQuery . '%'])
                   // Búsqueda robusta en la columna 'autor'
                   ->orWhereRaw($sqlNormalize('autor') . ' LIKE ?', ['%' . $cleanedQuery . '%'])
                   // Búsqueda robusta en la columna 'clase'
                   ->orWhereRaw($sqlNormalize('clase') . ' LIKE ?', ['%' . $cleanedQuery . '%'])
                   // Búsqueda robusta en la columna 'numero_pagina' (CAST a TEXT)
                   ->orWhereRaw($sqlNormalize('CAST(numero_pagina AS TEXT)') . ' LIKE ?', ['%' . $cleanedQuery . '%']);
            });
        }

        // Retorna las noticias paginadas
        return $noticias->paginate($perPage)->withQueryString(); // Agrega withQueryString()
    }
}
