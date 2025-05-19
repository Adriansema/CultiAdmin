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
    $request->validate([
        'archivo' => 'required|file|mimes:pdf|max:5120', // Máx. 5MB
    ]);

    $archivo = $request->file('archivo');

    $parser = new Parser();
    $pdf = $parser->parseFile($archivo->getRealPath());
    $texto = $pdf->getText();

    // Separar boletines por '===' como separador
    $bloques = preg_split('/===+/', $texto);

    foreach ($bloques as $bloque) {
        $lineas = array_filter(array_map('trim', explode("\n", $bloque)));

        $asunto = '';
        $contenido = '';

        foreach ($lineas as $linea) {
            if (stripos($linea, 'asunto:') === 0) {
                $asunto = trim(substr($linea, 7));
            } elseif (stripos($linea, 'contenido:') === 0) {
                $contenido = trim(substr($linea, 10));
            } else {
                $contenido .= ' ' . $linea;
            }
        }

        if ($asunto && $contenido) {
            Boletin::create([
                'asunto' => $asunto,
                'contenido' => $contenido,
            ]);
        }
    }

      return back()->with('success', 'Boletines importados correctamente desde el PDF.');
   }

        public function formImportar()
           {
            return view('boletines.importar-pdf');
           }


}
