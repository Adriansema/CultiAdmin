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
use Illuminate\Support\Str; // Importar la clase Str para Str::limit
use Illuminate\Support\Facades\Log; // Importar la clase Log para depuración

class BoletinController extends Controller
{
    public function index(Request $request, BoletinService $boletinService)
    {
        Gate::authorize('crear boletin'); // Asumiendo que el índice requiere este permiso
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

    /**
     * Almacena un nuevo boletín. Este método manejará tanto las subidas AJAX del modal
     * como las subidas de formularios tradicionales.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        Log::info('DEBUG: Método store llamado.');

        // Validaciones para los campos enviados desde el modal Alpine.js
        $validated = $request->validate([
            'archivo' => 'required|file|mimes:pdf|max:50000', // max 50MB (coincide con JS)
            'nombre_boletin' => 'required|string|max:100', // Campo del modal
            'producto' => 'required|string|in:cafe,mora', // Campo del modal
            'contenido' => 'required|string|max:500', // Campo del modal
        ]);

        $filePath = $request->file('archivo')->store('public/boletines'); // Guardar archivo

        $boletin = Boletin::create([
            'user_id' => Auth::id(),
            'estado' => 'pendiente',
            'contenido' => $validated['contenido'],
            'nombre_boletin' => $validated['nombre_boletin'], // Guardar nombre_boletin
            'producto' => $validated['producto'],             // Guardar producto
            'ruta_pdf' => Storage::url($filePath), // Guarda la URL pública
        ]);

        // Lógica de envío de correo (mantener tu lógica existente)
        $operadores = User::role('Operario')->get();
        foreach ($operadores as $operador) {
            Mail::to($operador->email)->send(new NuevaRevisionPendienteMail($boletin, 'Boletín'));
        }

        // Si la petición viene de AJAX (del modal), devuelve JSON
        if ($request->expectsJson()) {
            Log::info('DEBUG: Petición AJAX, devolviendo JSON para store.');
            return response()->json([
                'message' => 'Boletín creado exitosamente.',
                'boletin_id' => $boletin->id, // Devuelve el ID para la actualización dinámica
            ], 201); // Código 201 Created
        }

        // Si es una petición tradicional (no AJAX), redirige
        Log::info('DEBUG: Petición tradicional, redirigiendo para store.');
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

    // *** NUEVO MÉTODO CRÍTICO para obtener el HTML de una fila específica para AJAX ***
    /**
     * Obtener el HTML de una fila de boletín específica.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getBoletinRowHtml($id)
    {
        Log::info("DEBUG: getBoletinRowHtml llamado para ID: {$id}");

        $boletin = Boletin::findOrFail($id);
        Log::info("DEBUG: Boletín encontrado: " . $boletin->nombre_boletin);

        // ¡CRÍTICO! Usar ->render() para devolver SOLO el HTML del partial
        // Asegúrate de que 'boletines.partials.boletin_row' es la ruta CORRECTA a tu partial Blade
        return view('boletines.partials.boletin_row', compact('boletin'))->render();
    }

    // NO HAY NECESIDAD DE importarPdf aquí, su lógica se ha movido/fusionado con store.
    // Si tenías dos métodos importarPdf, esto lo soluciona.

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
                    $boletin->ruta_pdf, // Asegúrate de que este es el campo correcto para la URL del PDF
                    $boletin->created_at->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
