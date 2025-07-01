<?php

namespace App\Http\Controllers;

use App\Mail\NuevaRevisionPendienteMail;
use App\Models\Boletin;
use App\Models\User;
use App\Services\BoletinService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View; // Para la función view()
use Illuminate\Support\Facades\Response; // Para la función response()
use Illuminate\Support\Facades\Redirect; // Para la función redirect()
use Carbon\Carbon; // Para la función now() (si la usas para obtener la fecha y hora actual)
use Illuminate\Support\Str;

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

    /**
     * Almacena un nuevo boletín.
     *
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        Log::info('DEBUG: Método store llamado.');
        Log::info('DEBUG: Request all data: '.json_encode($request->all()));

        $validated = $request->validate([
            'archivo' => 'required|file|mimes:pdf|max:50000',
            'nombre' => 'required|string|max:100',
            'producto' => 'required|string|in:cafe,mora',
            'descripcion' => 'required|string|max:255',
            'precio_mas_alto' => 'nullable|numeric',
            'lugar_precio_mas_alto' => 'nullable|string|max:255',
            'precio_mas_bajo' => 'nullable|numeric',
            'lugar_precio_mas_bajo' => 'nullable|string|max:255',
        ]);

        Log::info('DEBUG: Datos validados: '.json_encode($validated));

        // Guarda el archivo y obtiene la ruta relativa al disco (ej. 'public/boletines/archivo.pdf')
        $filePath = $request->file('archivo')->store('public/boletines');

        // Convierte la ruta relativa a una URL pública para guardar en la DB
       $filePath = $request->file('archivo')->store('boletines', 'public'); // Guarda 'boletines/nombre.pdf');

        $boletin = Boletin::create([
            'user_id' => Auth::id(),
            'estado' => 'pendiente',
            'descripcion' => $validated['descripcion'],
            'nombre' => $validated['nombre'],
            'producto' => $validated['producto'],
            'archivo' => $filePath,
            'precio_mas_alto' => $validated['precio_mas_alto'] ?? null,
            'lugar_precio_mas_alto' => $validated['lugar_precio_mas_alto'] ?? null,
            'precio_mas_bajo' => $validated['precio_mas_bajo'] ?? null,
            'lugar_precio_mas_bajo' => $validated['lugar_precio_mas_bajo'] ?? null,
        ]);

        Log::info('DEBUG: Boletín creado en DB con ID: '.$boletin->id.' y datos: '.json_encode($boletin->toArray()));

        $operadores = User::role('Operario')->get();
        foreach ($operadores as $operador) {
            Mail::to($operador->email)->send(new NuevaRevisionPendienteMail($boletin, 'Boletín'));
        }

        if ($request->expectsJson()) {
            Log::info('DEBUG: Petición AJAX, devolviendo JSON para store.');

            return response()->json([
                'message' => 'Boletín creado exitosamente.',
                'boletin_id' => $boletin->id,
            ], 201);
        }

        Log::info('DEBUG: Petición tradicional, redirigiendo para store.');

        return redirect()->route('boletines.index')->with('success', 'Boletín creado con éxito y enviado a revisión del operador.');
    }

    public function update(Request $request, Boletin $boletin)
    {
        Gate::authorize('editar boletin');

        // *** ACTUALIZACIÓN DE LAS REGLAS DE VALIDACIÓN para incluir nombre y precios ***
        $rules = ([
            'nombre' => 'required|string|max:100', // Agregado para el nombre
            'descripcion' => 'required|string|max:255',
            'archivo_upload' => 'nullable|file|mimes:pdf|max:5000',
            'precio_mas_alto' => 'nullable|numeric', // Agregado
            'lugar_precio_mas_alto' => 'nullable|string|max:255', // Agregado
            'precio_mas_bajo' => 'nullable|numeric', // Agregado
            'lugar_precio_mas_bajo' => 'nullable|string|max:255', // Agregado
        ]);

        $messages = [
            'nombre.required' => 'El nombre del boletín es obligatorio.', // Mensaje para nombre
            'nombre.string' => 'El nombre debe ser texto.',
            'nombre.max' => 'El nombre no debe exceder los 100 caracteres.',

            'descripcion.required' => 'La descripción del boletín es obligatoria.',
            'descripcion.string' => 'La descripción debe ser texto.',
            'descripcion.max' => 'La descripción no debe exceder los 255 caracteres.',

            'archivo_upload.file' => 'El archivo debe ser un archivo válido.',
            'archivo_upload.mimes' => 'El archivo debe ser de tipo PDF.',
            'archivo_upload.max' => 'El archivo no debe pesar más de 5MB.',

            // Mensajes para precios
            'precio_mas_alto.numeric' => 'El precio más alto debe ser un número.',
            'lugar_precio_mas_alto.string' => 'El lugar del precio más alto debe ser texto.',
            'precio_mas_bajo.numeric' => 'El precio más bajo debe ser un número.',
            'lugar_precio_mas_bajo.string' => 'El lugar del precio más bajo debe ser texto.',
        ];

        $validatedData = $request->validate($rules, $messages);

        $originalEstado = $boletin->estado;

        if ($request->hasFile('archivo_upload')) {
            // Elimina el archivo anterior si existe.
            // $boletin->archivo aquí contendrá 'boletines/viejo.pdf'
            if ($boletin->archivo && Storage::disk('public')->exists($boletin->archivo)) {
                Storage::disk('public')->delete($boletin->archivo);
                Log::info('DEBUG: Archivo anterior eliminado: ' . $boletin->archivo);
            }

            // Guarda el nuevo archivo. $path contendrá 'boletines/nuevo.pdf'
            $path = $request->file('archivo_upload')->store('boletines', 'public');
            $boletin->archivo = $path; // ¡CAMBIADO! Asigna a 'archivo' la ruta interna
            Log::info('DEBUG: Nuevo archivo subido y archivo actualizada a: ' . $boletin->archivo);
        }


        // Actualiza los demás campos
        $boletin->nombre = $validatedData['nombre']; // Actualiza el nombre
        $boletin->descripcion = $validatedData['descripcion'];
        $boletin->precio_mas_alto = $validatedData['precio_mas_alto'] ?? null;
        $boletin->lugar_precio_mas_alto = $validatedData['lugar_precio_mas_alto'] ?? null;
        $boletin->precio_mas_bajo = $validatedData['precio_mas_bajo'] ?? null;
        $boletin->lugar_precio_mas_bajo = $validatedData['lugar_precio_mas_bajo'] ?? null;

        $estadoCambiadoAPendiente = false;
        if ($originalEstado === 'aprobado' || $originalEstado === 'rechazado') {
            $boletin->estado = 'pendiente';
            $boletin->observaciones = null;
            $estadoCambiadoAPendiente = true;
        }

        $boletin->save();
        Log::info('DEBUG: Boletín actualizado en DB con ID: ' . $boletin->id . ' y datos: ' . json_encode($boletin->toArray()));


        if ($estadoCambiadoAPendiente) {
            $operadores = User::role('Operario')->get();
            foreach ($operadores as $operador) {
                Mail::to($operador->email)->send(new NuevaRevisionPendienteMail($boletin, 'Boletín'));
            }
        }

        $boletin = $boletin->fresh(); // Recarga el modelo para tener los últimos datos

        $renderedRow = view('boletines.partials.boletin_row', ['boletin' => $boletin])->render();

        if($request->expectsJson()) {
            return response()->json([
                'message' => 'Boletín actualizado con éxito',
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

    /**
     * Obtener el HTML de una fila de boletín específica.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getBoletinRowHtml($id)
    {
        Log::info("DEBUG: getBoletinRowHtml llamado para ID: {$id}");

        try {
            $boletin = Boletin::findOrFail($id);
            Log::info('DEBUG: Boletín encontrado: '.$boletin->nombre);

            $renderedHtml = view('boletines.partials.boletin_row', compact('boletin'))->render();

            Log::info("DEBUG: HTML renderizado para boletín ID {$id}: ".Str::limit($renderedHtml, 500));

            return response($renderedHtml, 200)
                ->header('Content-Type', 'text/html')
                ->header('X-DEBUG-RENDERED', 'true');

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::error("ERROR: Boletín con ID {$id} no encontrado en getBoletinRowHtml. Mensaje: ".$e->getMessage());

            return response('Boletín no encontrado.', 404)
                ->header('Content-Type', 'text/plain');
        } catch (\Throwable $e) {
            Log::error("ERROR: Error inesperado al renderizar fila de boletín ID {$id}: ".$e->getMessage()."\n".$e->getTraceAsString());

            return response('Error interno al generar la fila del boletín.', 500)
                ->header('Content-Type', 'text/plain');
        }
    }

    public function exportarCSV(Request $request)
    {
        $query = $request->input('q');
        $estado = $request->input('estado');

        $boletines = Boletin::with('user');

        if ($query) {
            $boletines->where(function ($q2) use ($query) {
                $q2->whereRaw('LOWER(descripcion) LIKE ?', ['%'.strtolower($query).'%']) // Ajustado para 'descripcion'
                    ->orWhereRaw('LOWER(observaciones) LIKE ?', ['%'.strtolower($query).'%']);
            });
        }

        if ($estado) {
            $boletines->where('estado', $estado);
        }

        $boletinesResultados = $boletines->get();

        $nombreArchivo = 'boletines_'.now()->format('Y-m-d_H-i-s').'.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$nombreArchivo\"",
        ];

        $columnas = ['ID', 'Usuario', 'Estado', 'Nombre', 'Descripción', 'Observaciones', 'Archivo', 'Creado']; // Ajustado para 'Nombre' y 'Descripción'

        $callback = function () use ($boletinesResultados, $columnas) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columnas);

            foreach ($boletinesResultados as $boletin) {
                fputcsv($file, [
                    $boletin->id,
                    optional($boletin->user)->name ?? 'Sin usuario',
                    $boletin->estado,
                    $boletin->nombre, // Usar $boletin->nombre
                    $boletin->descripcion, // Usar $boletin->descripcion
                    $boletin->observaciones,
                    $boletin->ruta_pdf,
                    $boletin->created_at->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
