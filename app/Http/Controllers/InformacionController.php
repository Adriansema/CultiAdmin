<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Informacion;

class InformacionController extends Controller
{
    public function index() {
        $informaciones = Informacion::all();
        return view('informaciones.index', compact('informaciones'));
    }

    public function create() {
        return view('informaciones.create');
    }

    public function store(Request $request) {
        Informacion::create($request->all());
        return redirect()->route('informaciones.index')->with('success', 'Información creada.');
    }

    public function edit(Informacion $informacion) {
        return view('informaciones.edit', compact('informacion'));
    }

    public function update(Request $request, Informacion $informacion) {
        $informacion->update($request->all());
        return redirect()->route('informaciones.index')->with('success', 'Información actualizada.');
    }

    public function destroy(Informacion $informacion) {
        $informacion->delete();
        return redirect()->route('informaciones.index')->with('success', 'Información eliminada.');
    }
}
