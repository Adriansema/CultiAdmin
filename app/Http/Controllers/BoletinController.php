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
        Log::info('DEBUG: Motodo store llamado.');
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

            Log::info('DEBUG: Boleton creado en DB con ID: ' . $boletin->id . ' y datos: ' . json_encode($boletin->toArray()));

            // Envoo de correo a operadores
            $operadores = User::role('Operario')->get();
            foreach ($operadores as $operador) {
                Mail::to($operador->email)->send(new NuevaRevisionPendienteMail($boletin, 'Boleton'));
            }

            if ($request->expectsJson()) {
                Log::info('DEBUG: Peticion AJAX, devolviendo JSON para store (esperando recarga de JS).');
                return response()->json([
                    'message' => 'Boletín creado exitosamente.',
                    'boletin_id' => $boletin->id,
                ], 201);
            }

            Log::info('DEBUG: Peticion tradicional, redirigiendo para store.');
            return redirect()->route('boletines.index')->with('success_message', 'Boleton creado con éxito y enviado a revision.');
        } catch (QueryException $e) {
            Log::error('Error de base de datos al crear boletín: ' . $e->getMessage());
            // Si el archivo se subio antes de la falla de la DB, intentar eliminarlo
            if (isset($filePath) && Storage::disk('public')->exists($filePath)) {
                Storage::disk('public')->delete($filePath);
            }
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Ocurrio un error de base de datos al crear el boletín. Por favor, intontalo de nuevo.'], 500);
            }
            return redirect()->back()->with('error_message', 'Ocurrio un error de base de datos al crear el boletín. Por favor, intontalo de nuevo.');
        } catch (\Exception $e) {
            Log::error('Error inesperado al crear boletín: ' . $e->getMessage());
            // Si el archivo se subio antes de la falla, intentar eliminarlo
            if (isset($filePath) && Storage::disk('public')->exists($filePath)) {
                Storage::disk('public')->delete($filePath);
            }
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Ocurrio un error inesperado al crear el boletín. Por favor, intontalo de nuevo.'], 500);
            }
            return redirect()->back()->with('error_message', 'Ocurrio un error inesperado al crear el boletín. Por favor, intontalo de nuevo.');
        }
    }

    /**
     * Update the specified resource in storage.
     * Actualiza un boletín existente en la base de datos.
     */
    public function update(Request $request, Boletin $boletin)
    {
        Gate::authorize('editar boletin'); // La autorizacion se realiza antes del try-catch

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
                'descripcion.required' => 'La descripcion del boletín es obligatoria.',
                'descripcion.string' => 'La descripcion debe ser texto.',
                'descripcion.max' => 'La descripcion no debe exceder los 255 caracteres.',
                'archivo_upload.file' => 'El archivo debe ser un archivo volido.',
                'archivo_upload.mimes' => 'El archivo debe ser de tipo PDF.',
                'archivo_upload.max' => 'El archivo no debe pesar mos de 5MB.',
                'precio_mas_alto.numeric' => 'El precio mos alto debe ser un nomero.',
                'lugar_precio_mas_alto.string' => 'El lugar del precio mos alto debe ser texto.',
                'precio_mas_bajo.numeric' => 'El precio mos bajo debe ser un nomero.',
                'lugar_precio_mas_bajo.string' => 'El lugar del precio mos bajo debe ser texto.',
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

            // Actualiza los demos campos
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

            // Guarda todos los cambios en la base de datos en una sola operacion
            $boletin->save();
            Log::info('DEBUG: Boleton actualizado en DB con ID: ' . $boletin->id . ' y datos: ' . json_encode($boletin->toArray()));

            // Elimina el archivo anterior SOLO si el nuevo archivo se guardo con oxito
            if ($request->hasFile('archivo_upload') && $oldFilePath && Storage::disk('public')->exists($oldFilePath)) {
                Storage::disk('public')->delete($oldFilePath);
                Log::info('DEBUG: Archivo anterior eliminado: ' . $oldFilePath);
            }

            if ($estadoCambiadoAPendiente) {
                $operadores = User::role('Operario')->get();
                foreach ($operadores as $operador) {
                    Mail::to($operador->email)->send(new NuevaRevisionPendienteMail($boletin, 'Boleton'));
                }
            }

            // Recarga el modelo para tener los oltimos datos, necesario si vas a redirigir
            $boletin = $boletin->fresh();

            // Renderizar la fila HTML para la actualizacion, aunque con recarga no es estrictamente necesario,
            // si la dejas, puede ser otil si decides volver al AJAX para updates especoficos.
            // Por ahora, con la recarga total de la pogina tras la creacion, no es un problema.
            $renderedRow = view('boletines.partials.boletin_row', ['boletin' => $boletin])->render();

            if ($request->expectsJson()) {
                // Para updates, aon puedes devolver la fila actualizada si lo necesitas para otras funcionalidades,
                // pero la pogina se recargaro completamente despuos de la creacion/filtrado.
                return response()->json([
                    'message' => 'Boleton actualizado con oxito',
                    'boletin' => $boletin,
                    'html_row' => $renderedRow,
                ]);
            }

            return redirect()->route('boletines.index')->with('success_message', 'Boleton actualizado y enviado a revision del operador.');
        } catch (QueryException $e) {
            Log::error('Error de base de datos al actualizar boletín (ID: ' . $boletin->id . '): ' . $e->getMessage());
            // Si se subio un nuevo archivo y la DB fallo, intentar eliminar el nuevo archivo
            if (isset($newFilePath) && Storage::disk('public')->exists($newFilePath)) {
                Storage::disk('public')->delete($newFilePath);
            }
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Ocurrio un error de base de datos al actualizar el boletín. Por favor, intontalo de nuevo.'], 500);
            }
            return redirect()->back()->with('error_message', 'Ocurrio un error de base de datos al actualizar el boletín. Por favor, intontalo de nuevo.');
        } catch (\Exception $e) {
            Log::error('Error inesperado al actualizar boletín (ID: ' . $boletin->id . '): ' . $e->getMessage());
            // Si se subio un nuevo archivo y la operacion fallo, intentar eliminar el nuevo archivo
            if (isset($newFilePath) && Storage::disk('public')->exists($newFilePath)) {
                Storage::disk('public')->delete($newFilePath);
            }
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Ocurrio un error inesperado al actualizar el boletín. Por favor, intontalo de nuevo.'], 500);
            }
            return redirect()->back()->with('error_message', 'Ocurrio un error inesperado al actualizar el boletín. Por favor, intontalo de nuevo.');
        }
    }

    public function destroy(Boletin $boletin)
    {
        Gate::authorize('eliminar boletin');
        if ($boletin->archivo && Storage::disk('public')->exists($boletin->archivo)) {
            Storage::disk('public')->delete($boletin->archivo);
        }

        $boletin->delete();

        return redirect()->route('boletines.index')->with('success', 'Boleton eliminado.');
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