<?php

// app/Http/Controllers/BoletinController.php

namespace App\Http\Controllers;

use App\Models\Boletin;
use Illuminate\Http\Request;

class BoletinController extends Controller
{
    public function index()
    {
        $boletines = Boletin::latest()->get();
        return view('boletines.index', compact('boletines'));
    }

    public function create()
    {
        return view('boletines.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'asunto' => 'required|string|max:255',
            'contenido' => 'required|string',
        ]);

        Boletin::create($request->all());

        return redirect()->route('boletines.index')->with('success', 'Boletín creado correctamente.');
    }

    public function show(Boletin $boletin)
    {
        return view('boletines.show', compact('boletin'));
    }

    public function edit(Boletin $boletin)
    {
        return view('boletines.edit', compact('boletin'));
    }

    public function update(Request $request, Boletin $boletin)
    {
        $request->validate([
            'asunto' => 'required|string|max:255',
            'contenido' => 'required|string',
        ]);

        $boletin->update($request->all());

        return redirect()->route('boletines.index')->with('success', 'Boletín actualizado correctamente.');
    }

    public function destroy(Boletin $boletin)
    {
        $boletin->delete();

        return redirect()->route('boletines.index')->with('success', 'Boletín eliminado.');
    }
}
