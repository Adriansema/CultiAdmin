<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Boletin;
use Illuminate\Http\Request;
use App\Services\BoletinService;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use App\Mail\NuevaRevisionPendienteMail;
class BoletinController extends Controller
{
    public function index(Request $request, BoletinService $boletinService)
    {
        Gate::authorize('crear boletin');
        $boletines = $boletinService->obtenerBoletinFiltrados($request);
        return view('boletines.index', compact('boletines'));
    }

    public function getFilteredBoletin(Request $request, BoletinService $boletinService)
    {
        $boletines = $boletinService->obtenerBoletinFiltrados($request);
        return response()->json($boletines);
    }

    public function create()
    {
        return view('boletines.create');
    }

    public function show(Boletin $boletin)
    {
        return view('boletines.show', compact('boletin'));
    }

    public function edit(Boletin $boletin)
    {
        Gate::authorize('editar boletin');
        return view('boletines.edit', compact('boletin'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'contenido' => 'required|string',
            'archivo_upload' => 'nullable|file|mimes:pdf|max:5120',
        ]);

        $filePath = null;

        if ($request->hasFile('archivo_upload')) {
            $filePath = $request->file('archivo_upload')->store('boletines', 'public');
        }

        $boletin = Boletin::create([
            'user_id' => Auth::id(),
            'estado' => 'pendiente',
            'contenido' => $validated['contenido'],
            'archivo' => $filePath,
        ]);

        $operadores = User::role('Operario')->get();
        foreach ($operadores as $operador) {
            Mail::to($operador->email)->send(new NuevaRevisionPendienteMail($boletin, 'Boletín'));
        }

        return redirect()->route('boletines.index')->with('success', 'Boletín creado con éxito y enviado a revisión del operador.');
    }

    public function update(Request $request, Boletin $boletin)
    {
        Gate::authorize('editar boletin');
        $rules = ([
            'contenido' => 'required|string|max:100',
            'archivo_upload' => 'nullable|file|mimes:pdf|max:5000',
        ]);

        $messages = [
            'contenido.required' => 'El contenido del boletin es obligatorio.',
            'contenido.string' => 'El contenido debe der texto.',
            'contenido.max' => 'El contenido no debe exceder lo 100 caracteres.',
            'archivo_upload.file' => 'El archivo debe ser un archivo válido.',
            'archivo_upload.mimes' => 'El archivo debe ser de tipo PDF.',
            'archivo_upload.max' => 'El archivo no debe pesar más de 5MB.',
        ];

        $validatedData = $request->validate($rules, $messages);

        $originalEstado = $boletin->estado;

        if ($request->hasFile('archivo_upload')) {
            if ($boletin->archivo && Storage::disk('public')->exists($boletin->archivo)) {
                Storage::disk('public')->delete($boletin->archivo);
            }
            $path = $request->file('archivo_upload')->store('boletines', 'public');
            $boletin->archivo = $path;
        }

        $boletin->contenido = $validatedData['contenido'];

        $estadoCambiadoAPendiente = false;
        if ($originalEstado === 'aprobado' || $originalEstado === 'rechazado') {
            $boletin->estado = 'pendiente';
            $boletin->observaciones = null;
            $estadoCambiadoAPendiente = true;
        }

        $boletin->save();

        if ($estadoCambiadoAPendiente) {
            $operadores = User::role('Operario')->get();
            foreach ($operadores as $operador) {
                Mail::to($operador->email)->send(new NuevaRevisionPendienteMail($boletin, 'Boletín'));
            }
        }

        $boletin = $boletin->fresh();

        $renderedRow = view('boletines.partials.boletin_row', ['boletin' => $boletin])->render();

        if($request->expectsJson()) {
            return response()->json([
                'message' => 'Boletin actualizado con éxito',
                'boletin' => $boletin,
                'html_row' => $renderedRow,
            ]);
        }

        return redirect()->route('boletines.index')->with('success', 'Boletín actualizado y enviado a revisión del operador.');
    }

    public function destroy(Boletin $boletin)
    {
        Gate::authorize('eliminar boletin');
        if ($boletin->archivo && Storage::disk('public')->exists($boletin->archivo)) {
            Storage::disk('public')->delete($boletin->archivo);
        }

        $boletin->delete();

        return redirect()->route('boletines.index')->with('success', 'Boletín eliminado.');
    }

    public function importarPdf(Request $request)
    {
        $request->validate([
            'archivo' => 'required|file|mimes:pdf|max:10240',
            'contenido' => 'nullable|string',
        ]);

        $rutaArchivo = $request->file('archivo')->store('boletines', 'public');

        $boletin = Boletin::create([
            'user_id' => Auth::id(),
            'archivo' => $rutaArchivo,
            'contenido' => $request->contenido,
            'estado' => 'pendiente',
        ]);

        $operadores = User::role('Operario')->get();
        foreach ($operadores as $operador) {
            Mail::to($operador->email)->send(new NuevaRevisionPendienteMail($boletin, 'Boletín'));
        }

        return redirect()->route('boletines.index')->with('success', 'Boletín importado correctamente y pendiente de revisión.');
    }

    public function exportarCSV(Request $request)
    {
        $query = $request->input('q');
        $estado = $request->input('estado');

        $boletines = Boletin::with('user');

        if ($query) {
            $boletines->where(function ($q2) use ($query) {
                $q2->whereRaw('LOWER(contenido) LIKE ?', ['%' . strtolower($query) . '%'])
                    ->orWhereRaw('LOWER(observaciones) LIKE ?', ['%' . strtolower($query) . '%']);
            });
        }

        if ($estado) {
            $boletines->where('estado', $estado);
        }

        $boletinesResultados = $boletines->get();

        $nombreArchivo = 'boletines_' . now()->format('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$nombreArchivo\"",
        ];

        $columnas = ['ID', 'Usuario', 'Estado', 'Contenido', 'Observaciones', 'Archivo', 'Creado'];

        $callback = function () use ($boletinesResultados, $columnas) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columnas);

            foreach ($boletinesResultados as $boletin) {
                fputcsv($file, [
                    $boletin->id,
                    optional($boletin->user)->name ?? 'Sin usuario',
                    $boletin->estado,
                    $boletin->contenido,
                    $boletin->observaciones,
                    $boletin->archivo,
                    $boletin->created_at->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}