<?php

namespace App\Services;

use App\Models\Noticia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class NoticiaService
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

    public function obtenerNoticiaFiltradas(Request $request)
    {
        $perPage = in_array($request->input('per_page'), [5, 10, 25, 50, 100])
            ? $request->input('per_page')
            : 10;

        // Inicializar variables al principio de la función para asegurar su definición
        $searchQuery = $request->input('q', '');
        $estadoFilter = $request->input('estado', '');

        $noticias = Noticia::query();

        // Búsqueda robusta general en múltiples columnas
        if (!empty($searchQuery)) {
            // Asegúrate de que $cleanedSearchQuery se define aquí, dentro del if donde se usa
            $cleanedSearchQuery = $this->cleanSearchQuery($searchQuery);

            $noticias->where(function ($q2) use ($cleanedSearchQuery, $searchQuery) { // Pasar $cleanedSearchQuery a la clausura
                $sqlNormalize = function($column) {
                    if (in_array($column, ['created_at', 'updated_at'])) {
                        return "REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(LOWER(TO_CHAR({$column}, 'YYYY-MM-DD HH24:MI:SS')), 'á', 'a'), 'é', 'e'), 'í', 'i'), 'ó', 'o'), 'ú', 'u'), 'ü', 'u'), 'ñ', 'n'), '.', ''), '-', '')";
                    }
                    return "REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(LOWER({$column}), 'á', 'a'), 'é', 'e'), 'í', 'i'), 'ó', 'o'), 'ú', 'u'), 'ü', 'u'), 'ñ', 'n'), '.', ''), '-', '')";
                };

                // Búsqueda robusta en las columnas de Noticia
                $q2->orWhereRaw($sqlNormalize('tipo') . ' LIKE ?', ['%' . $cleanedSearchQuery . '%'])
                   ->orWhereRaw($sqlNormalize('titulo') . ' LIKE ?', ['%' . $cleanedSearchQuery . '%'])
                   ->orWhereRaw($sqlNormalize('autor') . ' LIKE ?', ['%' . $cleanedSearchQuery . '%'])
                   ->orWhereRaw($sqlNormalize('clase') . ' LIKE ?', ['%' . $cleanedSearchQuery . '%'])
                   ->orWhereRaw($sqlNormalize('CAST(numero_pagina AS TEXT)') . ' LIKE ?', ['%' . $cleanedSearchQuery . '%']);
                
                try {
                    $date = Carbon::parse($searchQuery);
                    $q2->orWhereDate('created_at', $date->toDateString());
                } catch (\Exception $e) {
                    // No hace nada si la fecha no es válida
                }
            });
        }

        // Aplicar filtro por estado
        if (!empty($estadoFilter) && in_array($estadoFilter, ['aprobado', 'pendiente', 'rechazado'])) {
            $noticias->where('estado', $estadoFilter);
        }

        $noticias->orderBy('tipo', 'asc');

        return $noticias->paginate($perPage)->withQueryString();
    }
}