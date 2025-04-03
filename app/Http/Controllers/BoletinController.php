<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Boletin;

class BoletinController extends Controller
{
    public function index() {
        $boletines = Boletin::all();
        return view('boletines.index', compact('boletines'));
    }

    public function create() {
        return view('boletines.create');
    }

    public function store(Request $request) {
        Boletin::create($request->all());
        return redirect()->route('boletines.index')->with('success', 'Boletín creado.');
    }

    public function edit(Boletin $boletin) {
        return view('boletines.edit', compact('boletin'));
    }

    public function update(Request $request, Boletin $boletin) {
        $boletin->update($request->all());
        return redirect()->route('boletines.index')->with('success', 'Boletín actualizado.');
    }

    public function destroy(Boletin $boletin) {
        $boletin->delete();
        return redirect()->route('boletines.index')->with('success', 'Boletín eliminado.');
    }
}
