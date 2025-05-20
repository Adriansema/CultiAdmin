<?php

//actualizacion 09/04/2025

namespace App\Http\Controllers;

use App\Models\Boletin;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Smalot\PdfParser\Parser;


class BoletinController extends Controller
{

    public function index()
    {
        $role = Role::select('name')->get();
        $boletines = Boletin::latest()->get();
        return view('boletines.index', compact('boletines'));
    }

    public function create()
    {
        return view('boletines.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'asunto' => 'required|string|max:255',
            'contenido' => 'required|string',
        ]);

        Boletin::create($validated);

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
        $validated = $request->validate([
            'asunto' => 'required|string|max:255',
            'contenido' => 'required|string',
        ]);

        $boletin->update($validated);

        return redirect()->route('boletines.index')->with('success', 'Boletín actualizado correctamente.');
    }

    public function destroy(Boletin $boletin)
    {
        $boletin->delete();

        return redirect()->route('boletines.index')->with('success', 'Boletín eliminado.');
    }

    public function cafe()
    {
        return view('boletines.cafe');
    }

    public function mora()
    {
        return view('boletines.mora');
    }

public function importarPdf(Request $request)
{
    // Guardar el archivo en storage/app/public/boletines
    $archivo = $request->file('archivo');
    $rutaArchivo = $archivo->store('boletines', 'public');

    // Crear boletín con contenido, asunto y ruta del archivo
    Boletin::create([
        'asunto' => $request->asunto ?? 'Sin asunto',  // <-- aquí el valor por defecto
        'contenido' => $request->contenido,
        'archivo' => $rutaArchivo,
    ]);

    return redirect()->route('boletines.index')->with('success', 'Boletín importado correctamente');
}
}