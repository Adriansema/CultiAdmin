<?php

namespace App\Http\Controllers;

use App\Models\User;
use League\Csv\Writer;
use SplTempFileObject;
use App\Models\Boletin;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Services\BoletinService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\QueryException;
use App\Mail\NuevaRevisionPendienteMail;

class BoletinController extends Controller
{
    public function index(Request $request, BoletinService $boletinService)
    {
        Gate::authorize('crear boletin');
        $boletines = $boletinService->obtenerBoletinFiltrados($request);

        return view('boletines.index', compact('boletines'));
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
        // $boletin ya contendro los datos mos recientes de la base de datos
        // Si hay logica de carga de relaciones, asegorate de que se ejecute aquo.
        return view('boletines.partials.modal-edit', compact('boletin'));
    }

    /**
     * Almacena un nuevo boletín.
     *
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
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

            // Envío de correo a operadores
            $operadores = User::role('Operario')->get();
            foreach ($operadores as $operador) {
                Mail::to($operador->email)->send(new NuevaRevisionPendienteMail($boletin, 'Boletín'));
            }

            // Consolidar la lógica de respuesta
            if ($request->expectsJson()) {
                Log::info('DEBUG: Petición AJAX, devolviendo JSON para store.');
                return response()->json([
                    'message' => 'Boletín creado con éxito y enviado a revisión.', // Mensaje unificado
                    'boletin_id' => $boletin->id,
                ], 201); // Usar 201 Created para una nueva creación exitosa
            }

            Log::info('DEBUG: Petición tradicional, redirigiendo para store.');
            return redirect()->route('boletines.index')->with('success_message', 'Boletín creado con éxito y enviado a revisión.');
        } catch (QueryException $e) {
            Log::error('Error de base de datos al crear boletín: ' . $e->getMessage());
            if (isset($filePath) && Storage::disk('public')->exists($filePath)) {
                Storage::disk('public')->delete($filePath);
            }
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Ocurrió un error de base de datos al crear el boletín. Por favor, inténtalo de nuevo.'], 500);
            }
            return redirect()->back()->with('error_message', 'Ocurrió un error de base de datos al crear el boletín. Por favor, inténtalo de nuevo.');
        } catch (\Exception $e) {
            Log::error('Error inesperado al crear boletín: ' . $e->getMessage());
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
                'nombre' => 'string|max:255',
                'descripcion' => 'string|max:255',
                'archivo_upload' => 'nullable|file|mimes:pdf|max:5120',
                'precio_mas_alto' => 'nullable|numeric',
                'lugar_precio_mas_alto' => 'nullable|string|max:255',
                'precio_mas_bajo' => 'nullable|numeric',
                'lugar_precio_mas_bajo' => 'nullable|string|max:255',
            ]);

            $messages = [
                'nombre.required' => 'El nombre del boletín es obligatorio.',
                'nombre.string' => 'El nombre debe ser texto.',
                'nombre.max' => 'El nombre no debe exceder los 255 caracteres.',
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

            Log::info('DEBUG: Datos recibidos antes de la validación: ' . json_encode($request->all()));
           /*  dd($request->all()); */ // Vuelve a poner el dd aquí

            $validator = Validator::make($request->all(), $rules, $messages);

            if ($validator->fails()) {
                // Si la petición espera JSON, devuelve los errores de validación con un código 422
                if ($request->expectsJson()) {
                    return response()->json([
                        'message' => 'Error de validación',
                        'errors' => $validator->errors()
                    ], 422); // Código 422 para errores de validación
                }
                // Si no es JSON, redirige con los errores
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $validatedData = $validator->validated(); // Obtiene los datos validados

            $originalEstado = $boletin->estado;
            $oldFilePath = $boletin->archivo;

            $newFilePath = null; // Inicializar a null

            if ($request->hasFile('archivo_upload')) {
                $newFilePath = $request->file('archivo_upload')->store('boletines', 'public');
                $boletin->archivo = $newFilePath;
                Log::info('DEBUG: Nuevo archivo subido y archivo actualizada a: ' . $boletin->archivo);
            } elseif ($request->input('remove_archivo') == 1 && $oldFilePath) {
                // Lógica para eliminar el archivo si se marca la opción y existe uno
                Storage::disk('public')->delete($oldFilePath);
                $boletin->archivo = null;
                Log::info('DEBUG: Archivo anterior marcado para eliminar y eliminado: ' . $oldFilePath);
            }
            // Importante: Si no se sube un nuevo archivo y no se marca para eliminar,
            // el $boletin->archivo debe mantener su valor actual (oldFilePath).
            // No lo asignes a null a menos que explícitamente se elimine.
            // La línea $boletin->archivo = $newFilePath; ya se encarga de actualizarlo si hay un nuevo archivo.
            // Si no hay nuevo archivo y no se elimina, el valor de $boletin->archivo persiste.


            // Actualiza los demás campos usando $validatedData
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

            $boletin->save();
            Log::info('DEBUG: Boletín actualizado en DB con ID: ' . $boletin->id . ' y datos: ' . json_encode($boletin->toArray()));

            // Elimina el archivo anterior SOLO si el nuevo archivo se guardó con éxito
            // y no se eliminó explícitamente antes.
            // Esta lógica es un poco redundante si ya se manejó en el 'elseif' de remove_archivo
            // y si $newFilePath se asigna solo si hay un nuevo archivo.
            // Si $request->hasFile('archivo_upload') es true, significa que se subió un nuevo archivo
            // y el $oldFilePath debe ser eliminado.
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

            $boletin = $boletin->fresh();

            $renderedRow = view('boletines.partials.boletin_row', ['boletin' => $boletin])->render();

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Boletín actualizado con éxito',
                    'boletin' => $boletin,
                    'html_row' => $renderedRow,
                ]);
            }

            return redirect()->route('boletines.index')->with('success_message', 'Boletín actualizado y enviado a revisión del operador.');
        } catch (QueryException $e) {
            Log::error('Error de base de datos al actualizar boletín (ID: ' . $boletin->id . '): ' . $e->getMessage());
            if (isset($newFilePath) && Storage::disk('public')->exists($newFilePath)) {
                Storage::disk('public')->delete($newFilePath);
            }
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Ocurrió un error de base de datos al actualizar el boletín. Por favor, inténtalo de nuevo.'], 500);
            }
            return redirect()->back()->with('error_message', 'Ocurrió un error de base de datos al actualizar el boletín. Por favor, inténtalo de nuevo.');
        } catch (\Exception $e) {
            Log::error('Error inesperado al actualizar boletín (ID: ' . $boletin->id . '): ' . $e->getMessage());
            if (isset($newFilePath) && Storage::disk('public')->exists($newFilePath)) {
                Storage::disk('public')->delete($newFilePath);
            }
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Ocurrió un error inesperado al actualizar el boletín. Por favor, inténtalo de nuevo.'], 500);
            }
            return redirect()->back()->with('error_message', 'Ocurrió un error inesperado al actualizar el boletín. Por favor, inténtalo de nuevo.');
        }
    }

    public function destroy(Boletin $boletin, Request $request)
    {
        Gate::authorize('eliminar boletin');
        try {
            if ($boletin->archivo && Storage::disk('public')->exists($boletin->archivo)) {
                Storage::disk('public')->delete($boletin->archivo);
            }
            $boletin->delete(); // <-- El boletín se elimina aquí

            // ESTE ES EL BLOQUE CLAVE:
            if ($request->expectsJson()) { // <--- ¿Esta condición es TRUE?
                return response()->json(['message' => 'Boletín eliminado con éxito.'], 200); // <-- ¿Se alcanza esta línea?
            }
            // SI NO SE ALCANZA LA LÍNEA ANTERIOR, SE EJECUTA ESTA O LA REDIRECCIÓN DE ERROR
            return redirect()->route('boletines.index')->with('success', 'Boleton eliminado.');
        } catch (\Exception $e) {
            Log::error('Error al eliminar boletín (ID: ' . $boletin->id . '): ' . $e->getMessage());
            if (isset($filePath) && Storage::disk('public')->exists($filePath)) {
                Storage::disk('public')->delete($filePath);
            }
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Ocurrió un error al eliminar el boletín. Por favor, inténtalo de nuevo.'], 500);
            }
            return redirect()->back()->with('error_message', 'Ocurrió un error al eliminar el boletín.');
        }
    }

    /**
     * Obtiene los boletines mos recientes para mostrar en el dashboard.
     *
     * @return \Illuminate\View\View
     */
    public function getDashboardBoletines()
    {
        // Obtener los oltimos 10 boletines, ordenados por fecha de creacion descendente.
        // Asumimos que solo queremos boletines 'aprobados' para el dashboard.
        $boletines = Boletin::latest() // Ordena por created_at de forma descendente
            ->limit(10) // Limita a los oltimos 10 boletines
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
        // Asegorate de que el archivo existe en el disco 'public'
        if ($boletin->archivo && Storage::disk('public')->exists($boletin->archivo)) {
            // Usa el nombre original del archivo para la descarga, si es posible,
            // o un nombre generado a partir del nombre del boletín.
            $fileName = Str::slug($boletin->nombre) . '.pdf';
            return Storage::disk('public')->download($boletin->archivo, $fileName);
        } else {
            abort(404, 'Archivo de boletín no encontrado.');
        }
    }

    /**
     * Exporta los boletines a un archivo CSV, aplicando los filtros y ordenamiento actuales.
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function exportarCSV(Request  $request, BoletinService $boletinService)
    {
        // Obtiene los mismos parámetros de filtro y ordenación que la tabla
        // Ahora se usan 'sort_by' y 'sort_direction' en lugar de 'precio'
        $query = $request->input('q');
        $estado = $request->input('estado');
        $sortBy = $request->input('sort_by');
        $sortDirection = $request->input('sort_direction');

        // Llama al nuevo método del servicio para obtener los boletines sin paginación
        $boletinesResultados = $boletinService->obtenerBoletinFiltradosParaExportar($request);

        $nombreArchivo = 'boletines_' . now()->format('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8', // Asegura UTF-8
            'Content-Disposition' => "attachment; filename=\"$nombreArchivo\"",
        ];

        $columnas = [
            'ID',
            'Usuario',
            'Estado',
            'Nombre',
            'Descripcion',
            'Observaciones',
            'Archivo',
            'Precio Mas Alto',
            'Lugar Precio Mas Alto',
            'Precio Mas Bajo',
            'Lugar Precio Mas Bajo',
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
                    optional($boletin->user)->name ?? 'Sin usuario', // Accede al nombre del usuario si la relación existe
                    $boletin->estado,
                    $boletin->nombre,
                    $boletin->descripcion,
                    $boletin->observaciones,
                    $boletin->archivo,
                    $boletin->precio_mas_alto, // Usa el valor numérico, no el formateado para CSV
                    $boletin->lugar_precio_mas_alto,
                    $boletin->precio_mas_bajo, // Usa el valor numérico, no el formateado para CSV
                    $boletin->lugar_precio_mas_bajo,
                    $boletin->created_at->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
