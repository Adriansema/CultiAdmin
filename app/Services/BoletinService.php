<?php

namespace App\Services;

use App\Models\Boletin;
use Illuminate\Http\Request;

class BoletinService
{
    public function obtenerBoletinFiltrados(Request $request)
    {
        $perPage = in_array($request->input('per_page'), [5, 10, 25, 50, 100])
        ? $request->input('per_page')
        : 5;

        $query = Boletin::query();

        $query->orderBy('contenido', 'asc');

        return $query->paginate($perPage);
    }
}
