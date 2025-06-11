<?php

namespace App\Services;

use App\Models\Boletin;
use Illuminate\Http\Request;

class BoletinService
{
    public function obtenerBoletinFiltrados(Request $request)
    {
        //Define la cantidad de elementos por página, con un valor por defecto y opciones válidas
        $perPage = in_array($request->input('per_page'), [5, 10, 25, 50, 100])
        ? $request->input('per_page')
        : 5; //por defecto 5

        $query = Boletin::query();

        //lógica de filtrado:
        //si el campo 'contenido' está presente en la solucitud, aplica el filtro.
        if ($request->filled('nombre')){
            $query->where('contenido', 'like', '%' . $request->input('nombre') . '%');
        }

        $query->orderBy('contenido', 'asc');

        return $query->paginate($perPage)->withQueryString();
    }
}
