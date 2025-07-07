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
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View; // Para la función view()
use Illuminate\Support\Facades\Response; // Para la función response()
use Illuminate\Support\Facades\Redirect; // Para la función redirect()
use Illuminate\Database\QueryException; // Importar para manejar errores de base de datos
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

    // ELIMINADO: getFilteredBoletin ya no es necesario con el enfoque de recarga de página.
    // ELIMINADO: getBoletinRowHtml ya no es necesario.
    // ELIMINADO: getRowAndModalsHtml ya no es necesario.

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
        // $boletin ya contendrá los datos más recientes de la base de datos
        // Si hay lógica de carga de relaciones, asegúrate de que se ejecute aquí.
        return view('boletines.partials.modal-edit', compact('boletin'));
    }

    /**
     * Almacena un nuevo boletín.
     *
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        Log::info('DEBUG: Método store llamado.');
        Log::info('DEBUG: Request all data: ' . json_encode($request->all()));

        // 1. Validar los datos del formulario.
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

        Log::info('DEBUG: Datos validados: ' . json_encode($validated));

        try {
            // Guarda el archivo y obtiene la ruta relativa al disco (ej. 'boletines/archivo.pdf')
            $filePath = $request->file('archivo')->store('boletines', 'public');

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

            Log::info('DEBUG: Boletín creado en DB con ID: ' . $boletin->id . ' y datos: ' . json_encode($boletin->toArray()));

            // Envío de correo a operadores
            $operadores = User::role('Operario')->get();
            foreach ($operadores as $operador) {
                Mail::to($operador->email)->send(new NuevaRevisionPendienteMail($boletin, 'Boletín'));
            }

            if ($request->expectsJson()) {
                Log::info('DEBUG: Petición AJAX, devolviendo JSON para store (esperando recarga de JS).');
                return response()->json([
                    'message' => 'Boletín creado exitosamente.',
                    'boletin_id' => $boletin->id,
                ], 201);
            }

            Log::info('DEBUG: Petición tradicional, redirigiendo para store.');
            return redirect()->route('boletines.index')->with('success_message', 'Boletín creado con éxito y enviado a revisión del operador.');
        } catch (QueryException $e) {
            Log::error('Error de base de datos al crear boletín: ' . $e->getMessage());
            // Si el archivo se subió antes de la falla de la DB, intentar eliminarlo
            if (isset($filePath) && Storage::disk('public')->exists($filePath)) {
                Storage::disk('public')->delete($filePath);
            }
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Ocurrió un error de base de datos al crear el boletín. Por favor, inténtalo de nuevo.'], 500);
            }
            return redirect()->back()->with('error_message', 'Ocurrió un error de base de datos al crear el boletín. Por favor, inténtalo de nuevo.');
        } catch (\Exception $e) {
            Log::error('Error inesperado al crear boletín: ' . $e->getMessage());
            // Si el archivo se subió antes de la falla, intentar eliminarlo
            if (isset($filePath) && Storage::disk('public')->exists($filePath)) {
                Storage::disk('public')->delete($filePath);
            }
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Ocurrió un error inesperado al crear el boletín. Por favor, inténtalo de nuevo.'], 500);
            }
            return redirect()->back()->with('error_message', 'Ocurrió un error inesperado al crear el boletín. Por favor, inténtalo de nuevo.');
        }
    }

    /**
     * Update the specified resource in storage.
     * Actualiza un boletín existente en la base de datos.
     */
    public function update(Request $request, Boletin $boletin)
    {
        Gate::authorize('editar boletin'); // La autorización se realiza antes del try-catch

        try {
            $rules = ([
                'nombre' => 'required|string|max:100',
                'descripcion' => 'required|string|max:255',
                'archivo_upload' => 'nullable|file|mimes:pdf|max:5000',
                'precio_mas_alto' => 'nullable|numeric',
                'lugar_precio_mas_alto' => 'nullable|string|max:255',
                'precio_mas_bajo' => 'nullable|numeric',
                'lugar_precio_mas_bajo' => 'nullable|string|max:255',
            ]);

            $messages = [
                'nombre.required' => 'El nombre del boletín es obligatorio.',
                'nombre.string' => 'El nombre debe ser texto.',
                'nombre.max' => 'El nombre no debe exceder los 100 caracteres.',
                'descripcion.required' => 'La descripción del boletín es obligatoria.',
                'descripcion.string' => 'La descripción debe ser texto.',
                'descripcion.max' => 'La descripción no debe exceder los 255 caracteres.',
                'archivo_upload.file' => 'El archivo debe ser un archivo válido.',
                'archivo_upload.mimes' => 'El archivo debe ser de tipo PDF.',
                'archivo_upload.max' => 'El archivo no debe pesar más de 5MB.',
                'precio_mas_alto.numeric' => 'El precio más alto debe ser un número.',
                'lugar_precio_mas_alto.string' => 'El lugar del precio más alto debe ser texto.',
                'precio_mas_bajo.numeric' => 'El precio más bajo debe ser un número.',
                'lugar_precio_mas_bajo.string' => 'El lugar del precio más bajo debe ser texto.',
            ];

            $validatedData = $request->validate($rules, $messages);

            $originalEstado = $boletin->estado;
            $oldFilePath = $boletin->archivo; // Guarda la ruta del archivo anterior

            if ($request->hasFile('archivo_upload')) {
                // Guarda el nuevo archivo.
                $newFilePath = $request->file('archivo_upload')->store('boletines', 'public');
                $boletin->archivo = $newFilePath; // Asigna la nueva ruta
                Log::info('DEBUG: Nuevo archivo subido y archivo actualizada a: ' . $boletin->archivo);
            }

            // Actualiza los demás campos
            $boletin->nombre = $validatedData['nombre'];
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

            // Guarda todos los cambios en la base de datos en una sola operación
            $boletin->save();
            Log::info('DEBUG: Boletín actualizado en DB con ID: ' . $boletin->id . ' y datos: ' . json_encode($boletin->toArray()));

            // Elimina el archivo anterior SOLO si el nuevo archivo se guardó con éxito
            if ($request->hasFile('archivo_upload') && $oldFilePath && Storage::disk('public')->exists($oldFilePath)) {
                Storage::disk('public')->delete($oldFilePath);
                Log::info('DEBUG: Archivo anterior eliminado: ' . $oldFilePath);
            }

            if ($estadoCambiadoAPendiente) {
                $operadores = User::role('Operario')->get();
                foreach ($operadores as $operador) {
                    Mail::to($operador->email)->send(new NuevaRevisionPendienteMail($boletin, 'Boletín'));
                }
            }

            // Recarga el modelo para tener los últimos datos, necesario si vas a redirigir
            $boletin = $boletin->fresh();

            // Renderizar la fila HTML para la actualización, aunque con recarga no es estrictamente necesario,
            // si la dejas, puede ser útil si decides volver al AJAX para updates específicos.
            // Por ahora, con la recarga total de la página tras la creación, no es un problema.
            $renderedRow = view('boletines.partials.boletin_row', ['boletin' => $boletin])->render();

            if ($request->expectsJson()) {
                // Para updates, aún puedes devolver la fila actualizada si lo necesitas para otras funcionalidades,
                // pero la página se recargará completamente después de la creación/filtrado.
                return response()->json([
                    'message' => 'Boletín actualizado con éxito',
                    'boletin' => $boletin,
                    'html_row' => $renderedRow,
                ]);
            }

            return redirect()->route('boletines.index')->with('success_message', 'Boletín actualizado y enviado a revisión del operador.');
        } catch (QueryException $e) {
            Log::error('Error de base de datos al actualizar boletín (ID: ' . $boletin->id . '): ' . $e->getMessage());
            // Si se subió un nuevo archivo y la DB falló, intentar eliminar el nuevo archivo
            if (isset($newFilePath) && Storage::disk('public')->exists($newFilePath)) {
                Storage::disk('public')->delete($newFilePath);
            }
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Ocurrió un error de base de datos al actualizar el boletín. Por favor, inténtalo de nuevo.'], 500);
            }
            return redirect()->back()->with('error_message', 'Ocurrió un error de base de datos al actualizar el boletín. Por favor, inténtalo de nuevo.');
        } catch (\Exception $e) {
            Log::error('Error inesperado al actualizar boletín (ID: ' . $boletin->id . '): ' . $e->getMessage());
            // Si se subió un nuevo archivo y la operación falló, intentar eliminar el nuevo archivo
            if (isset($newFilePath) && Storage::disk('public')->exists($newFilePath)) {
                Storage::disk('public')->delete($newFilePath);
            }
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Ocurrió un error inesperado al actualizar el boletín. Por favor, inténtalo de nuevo.'], 500);
            }
            return redirect()->back()->with('error_message', 'Ocurrió un error inesperado al actualizar el boletín. Por favor, inténtalo de nuevo.');
        }
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
     * Obtiene los boletines más recientes para mostrar en el dashboard.
     *
     * @return \Illuminate\View\View
     */
    public function getDashboardBoletines()
    {
        // Obtener los últimos 10 boletines, ordenados por fecha de creación descendente.
        // Asumimos que solo queremos boletines 'aprobados' para el dashboard.
        $boletines = Boletin::latest() // Ordena por created_at de forma descendente
            ->limit(10) // Limita a los últimos 10 boletines
            ->get();

        // Retorna la vista parcial con los boletines.
        return view('partials.notification-boletin', compact('boletines'));
    }

    /**
     * Descarga un archivo de boletín.
     *
     * @param  \App\Models\Boletin  $boletin
     * @return \Illuminate\Http\Response
     */
    public function downloadBoletin(Boletin $boletin)
    {
        // Asegúrate de que el archivo existe en el disco 'public'
        if ($boletin->archivo && Storage::disk('public')->exists($boletin->archivo)) {
            // Usa el nombre original del archivo para la descarga, si es posible,
            // o un nombre generado a partir del nombre del boletín.
            $fileName = Str::slug($boletin->nombre) . '.pdf';
            return Storage::disk('public')->download($boletin->archivo, $fileName);
        } else {
            abort(404, 'Archivo de boletín no encontrado.');
        }
    }

    public function exportarCSV(Request $request)
    {
        $query = $request->input('q');
        $estado = $request->input('estado');
        $precio = $request->input('precio'); // <-- NUEVO: Obtener el parámetro de filtro por precio

        $boletines = Boletin::with('user');

        if ($query) {
            $boletines->where(function ($q2) use ($query) {
                $q2->whereRaw('LOWER(nombre) LIKE ?', ['%' . strtolower($query) . '%']) // Añadir nombre a la búsqueda si lo quieres
                    ->orWhereRaw('LOWER(descripcion) LIKE ?', ['%' . strtolower($query) . '%'])
                    ->orWhereRaw('LOWER(observaciones) LIKE ?', ['%' . strtolower($query) . '%'])
                    ->orWhereRaw('LOWER(lugar_precio_mas_alto) LIKE ?', ['%' . strtolower($query) . '%']) // Incluir lugares de precio en la búsqueda
                    ->orWhereRaw('LOWER(lugar_precio_mas_bajo) LIKE ?', ['%' . strtolower($query) . '%']);
            });
        }

        if ($estado) {
            $boletines->where('estado', $estado);
        }

        // <-- NUEVO: Lógica para el filtro por precio
        if ($precio) {
            if ($precio === 'precio_alto_desc') {
                $boletines->orderByDesc('precio_mas_alto');
            } elseif ($precio === 'precio_bajo_asc') {
                $boletines->orderBy('precio_mas_bajo');
            }
            // Puedes añadir más condiciones si tienes otros valores para 'precio'
        }

        $boletinesResultados = $boletines->get();

        $nombreArchivo = 'boletines_' . now()->format('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$nombreArchivo\"",
        ];

        // <-- MODIFICADO: Añadir nuevas columnas
        $columnas = [
            'ID',
            'Usuario',
            'Estado',
            'Nombre',
            'Descripción',
            'Observaciones',
            'Archivo',
            'Precio Más Alto',
            'Lugar Precio Más Alto',
            'Precio Más Bajo',
            'Lugar Precio Más Bajo',
            'Creado'
        ];

        $callback = function () use ($boletinesResultados, $columnas) {
            $file = fopen('php://output', 'w');
            // Añadir la marca BOM para asegurar que UTF-8 se muestre correctamente en Excel
            fputs($file, $bom = (chr(0xEF) . chr(0xBB) . chr(0xBF)));
            fputcsv($file, $columnas);

            foreach ($boletinesResultados as $boletin) {
                fputcsv($file, [
                    $boletin->id,
                    optional($boletin->user)->name ?? 'Sin usuario',
                    $boletin->estado,
                    $boletin->nombre,
                    $boletin->descripcion,
                    $boletin->observaciones,
                    $boletin->archivo,
                    $boletin->precio_mas_alto_formatted,
                    $boletin->lugar_precio_mas_alto,
                    $boletin->precio_mas_bajo_formatted,
                    $boletin->lugar_precio_mas_bajo,
                    $boletin->created_at->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}