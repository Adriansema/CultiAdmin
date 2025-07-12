<?php

namespace App\Http\Controllers;

use App\Models\Noticia; // Importa el modelo Noticia
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth; // Para obtener el ID del usuario autenticado
use Illuminate\Support\Facades\Storage; // Para manejar la carga de imagenes
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Log; // Importar la clase Log para registrar errores
use Illuminate\Database\QueryException; //
use Symfony\Component\HttpFoundation\StreamedResponse; // Necesario para la descarga de archivos grandes
use App\Services\NoticiaService;
use Carbon\Carbon;
use GuzzleHttp\Psr7\Message;
use League\Csv\Writer; // Asegúrate de tener esta librería instalada (composer require league/csv)
use SplTempFileObject; // Necesario para League/Csv

class NoticiaController extends Controller
{
    public function index(Request $request, NoticiaService $noticiaService)
    {
        // Autorizacion para ver la lista de noticias
        Gate::authorize('crear noticia'); // Asegurate de que este permiso es el adecuado

        // Llama al servicio para obtener las noticias filtradas/paginadas
        $noticias = $noticiaService->obtenerNoticiaFiltradas($request);

        // Retorna la vista con los datos de las noticias
        return view('noticias.index', compact('noticias'));
    }
    /**
     * Show the form for creating a new resource.
     * Muestra el formulario para crear una nueva noticia.
     */
    public function create()
    {
        return view('noticias.create');
    }

    /**
     * Store a newly created resource in storage.
     * Guarda una nueva noticia en la base de datos.
     */
    public function store(Request $request)
    {
        // 1. Validar los datos del formulario.
        $rules = [
            'tipo' => 'required|string|in:cafe,mora',
            'titulo' => 'required|string|max:255',
            'clase' => 'required|string|max:255',
            'imagen' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'informacion' => 'required|string',
            'autor' => 'required|string|max:255',
        ];

        $messages = [
            'tipo.required' => 'El campo tipo es obligatorio.',
            'tipo.string' => 'El campo tipo debe ser texto.',
            'tipo.max' => 'El campo tipo no debe exceder los :max caracteres.',

            'titulo.required' => 'El campo título es obligatorio.',
            'titulo.string' => 'El campo título debe ser texto.',
            'titulo.max' => 'El campo título no debe exceder los :max caracteres.',

            'clase.required' => 'El campo clase es obligatorio.',
            'clase.string' => 'El campo clase debe ser texto.',
            'clase.max' => 'El campo clase no debe exceder los :max caracteres.',

            'imagen.required' => 'La imagen es obligatoria.',
            'imagen.image' => 'El archivo debe ser una imagen.',
            'imagen.mimes' => 'La imagen debe ser un archivo de tipo: :values.',
            'imagen.max' => 'La imagen no debe ser mayor de :max kilobytes.',

            'informacion.required' => 'El campo información es obligatorio.',
            'informacion.string' => 'El campo información debe ser texto.',

            'autor.required' => 'El campo autor es obligatorio.',
            'autor.string' => 'El campo autor debe ser texto.',
            'autor.max' => 'El campo autor no debe exceder los :max caracteres.',
        ];

        // 3. Aplicar las reglas de validacion.
        $request->validate($rules, $messages);

        try {
            // 2. Logica para guardar la imagen (si se ha subido).
            $imagenPath = null;
            if ($request->hasFile('imagen')) {
                $file = $request->file('imagen');

                // Genera un nombre de archivo unico con la extension original del cliente
                $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();

                // Guarda la imagen en storage/app/public/noticias con el nombre generado
                $imagenPath = $file->storeAs('noticias', $filename, 'public');
            }

            // 3. Crear la nueva noticia.
            // Si Noticia::create() falla, lanzara una excepcion que sera capturada por el bloque catch.
            Noticia::create([
                'user_id' => Auth::id(),
                'tipo' => $request->tipo,
                'titulo' => $request->titulo,
                'clase' => $request->clase,
                'imagen' => $imagenPath, // Guarda la ruta de la imagen
                'informacion' => $request->informacion,
                'autor' => $request->autor,
                'leida' => false, // Nueva columna, por defecto false!
            ]);

            // Si la creacion es exitosa, el codigo continua aqui.
            return redirect()->route('noticias.index')->with('success_message', '!Noticia creada con exito!');
        } catch (QueryException $e) {
            // Captura errores especificos de la base de datos
            Log::error('Error de base de datos al crear noticia: ' . $e->getMessage());
            return redirect()->back()->with('error_message', 'Ocurrio un error de base de datos al crear la noticia. Por favor, intentalo de nuevo.');
        } catch (\Exception $e) {
            // Captura cualquier otra excepcion inesperada
            Log::error('Error inesperado al crear noticia: ' . $e->getMessage());
            return redirect()->back()->with('error_message', 'Ocurrio un error inesperado al crear la noticia. Por favor, intentalo de nuevo.');
        }
    }

    /**
     * Display the specified resource.
     * Muestra los detalles de una noticia especifica.
     */
    public function show(Noticia $noticia)
    {
        // Carga la relacion 'user' para mostrar quien la creo.
        $noticia->load('user');
        return view('noticias.show', compact('noticia'));
    }

    /**
     * Show the form for editing the specified resource.
     * Muestra el formulario para editar una noticia existente.
     */
    public function edit(Noticia $noticia)
    {
        Gate::authorize('editar noticia');
        return view('noticias.edit', compact('noticia'));
    }

    /**
     * Update the specified resource in storage.
     * Actualiza una noticia existente en la base de datos.
     */
    public function update(Request $request, Noticia $noticia)
    {
        Gate::authorize('editar noticia');

        try {
            // Guarda el estado original de la noticia antes de cualquier actualizacion
            $originalEstado = $noticia->estado;

            // Valida los datos de la solicitud
            $rules = [
                'tipo' => 'required|string|max:255',
                'titulo' => 'required|string|max:255',
                'clase' => 'required|string|max:255',
                'imagen' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'informacion' => 'required|string',
                'autor' => 'required|string|max:255',
            ];

            // 3. Aplicar las reglas de validacion.
            $request->validate($rules);

            // 1. Logica para actualizar la imagen.
            if ($request->hasFile('imagen')) {
                // Eliminar imagen anterior si existe
                if ($noticia->imagen && Storage::disk('public')->exists($noticia->imagen)) {
                    Storage::disk('public')->delete($noticia->imagen);
                }

                $file = $request->file('imagen');
                $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $imagenPath = $file->storeAs('noticias', $filename, 'public');

                $noticia->imagen = $imagenPath; // Asigna la nueva ruta
            }

            // 2. Logica para cambiar el estado a 'pendiente' si la noticia fue editada
            //    y su estado anterior era 'aprobado' o 'rechazado'.
            //    Esto asegura que una noticia editada vuelva al flujo de revision.
            if ($originalEstado === 'aprobado' || $originalEstado === 'rechazado') {
                $noticia->estado = 'pendiente';
                $noticia->observaciones = null; // Limpia las observaciones anteriores si las hubiera
                // Si tienes un campo especifico para observaciones del operador, tambien limpialo:
                // $noticia->observaciones_operador = null;
            }

            // 3. Actualizar las propiedades de la noticia con los datos del request.
            //    No necesitas llamar a $noticia->save() y luego $noticia->update().
            //    Puedes asignar las propiedades y luego llamar a save() una vez.
            $noticia->tipo = $request->tipo;
            $noticia->titulo = $request->titulo;
            $noticia->clase = $request->clase;
            $noticia->informacion = $request->informacion;
            $noticia->autor = $request->autor;

            // Guarda todos los cambios en la base de datos en una sola operacion
            $noticia->save();

            // Si la actualizacion es exitosa, el codigo continua aqui.
            return redirect()->route('noticias.index')->with('success_message', '!Noticia actualizada con exito!');
        } catch (QueryException $e) {
            // Captura errores especificos de la base de datos
            Log::error('Error de base de datos al actualizar noticia (ID: ' . $noticia->id . '): ' . $e->getMessage());
            return redirect()->back()->with('error_message', 'Ocurrio un error de base de datos al actualizar la noticia. Por favor, intentalo de nuevo.');
        } catch (\Exception $e) {
            // Captura cualquier otra excepcion inesperada
            Log::error('Error inesperado al actualizar noticia (ID: ' . $noticia->id . '): ' . $e->getMessage());
            return redirect()->back()->with('error_message', 'Ocurrio un error inesperado al actualizar la noticia. Por favor, intentalo de nuevo.');
        }
    }

    /**
     * Remove the specified resource from storage.
     * Elimina una noticia de la base de datos.
     */
    public function destroy(Noticia $noticia)
    {
        Gate::authorize('eliminar noticia');
        // 1. Eliminar la imagen asociada si existe.
        if ($noticia->imagen && Storage::disk('public')->exists($noticia->imagen)) {
            Storage::disk('public')->delete($noticia->imagen);
        }

        // 2. Eliminar la noticia.
        $noticia->delete();

        // 3. Redirigir al indice de noticias con un mensaje de exito.
        return redirect()->route('noticias.index')->with('success', 'Noticia eliminada con exito.');
    }

    /**
     * Exporta las noticias a un archivo CSV, aplicando los filtros y ordenamiento actuales.
     *
     * @param Request $request
     * @param NoticiaService $noticiaService // <-- CAMBIO CLAVE: Inyectar el servicio aquí
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function exportarCsv(Request $request, NoticiaService $noticiaService)
    {
        // Obtiene los mismos parámetros de filtro y ordenación que la tabla
        $query = $request->input('q');
        $estado = $request->input('estado');
        $sortBy = $request->input('sort_by');
        $sortDirection = $request->input('sort_direction');

        // Llama al nuevo método del servicio para obtener las noticias sin paginación
        $noticiasResultados = $noticiaService->obtenerNoticiaFiltradasParaExportar($request);

        $nombreArchivo = 'noticias_' . now()->format('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"$nombreArchivo\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        // Columnas que se incluirán en el CSV.
        // Asegúrate de que estos nombres coincidan con los nombres de las columnas en tu tabla 'noticias'.
        // Ajustado para incluir 'creador' y eliminar 'clase', 'numero_pagina'
        $columnas = [
            'id',
            'tipo',
            'titulo',
            'autor',
            'creador', // Para el nombre del usuario creador
            'estado',
            'informacion',
            'imagen_url', // Para la URL pública de la imagen
            'leida',
            'created_at',
            'updated_at',
        ];

        $callback = function () use ($noticiasResultados, $columnas) {
            $file = fopen('php://output', 'w'); // Abre el flujo de salida para escribir el CSV
            // Añadir la marca BOM para asegurar que UTF-8 se muestre correctamente en Excel
            fputs($file, $bom = (chr(0xEF) . chr(0xBB) . chr(0xBF)));
            fputcsv($file, $columnas);

            foreach ($noticiasResultados as $noticia) {
                $row = [];
                foreach ($columnas as $column) {
                    $value = null;
                    switch ($column) {
                        case 'creador':
                            $value = optional($noticia->user)->name ?? 'N/A'; // Accede al nombre del usuario
                            break;
                        case 'imagen_url':
                            $value = $noticia->imagen ? asset('storage/' . $noticia->imagen) : ''; // URL pública de la imagen
                            break;
                        case 'informacion':
                            $value = strip_tags($noticia->informacion ?? ''); // Elimina HTML y decodifica entidades
                            $value = html_entity_decode($value, ENT_QUOTES | ENT_HTML5, 'UTF-8');
                            $value = str_replace(["\r", "\n"], " ", $value); // Reemplaza saltos de línea
                            break;
                        default:
                            $value = $noticia->$column;
                            // Formatear fechas si es necesario
                            if (in_array($column, ['created_at', 'updated_at']) && $value instanceof Carbon) {
                                $value = $value->format('Y-m-d H:i:s');
                            }
                            break;
                    }
                    $row[] = $value;
                }
                fputcsv($file, $row); // Escribe la fila de datos
            }

            fclose($file); // Cierra el flujo de salida
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Obtiene las noticias mas recientes para mostrar en el dashboard.
     *
     * @return \Illuminate\View\View
     */
    public function getDashboardNoticias()
    {
        // Obtener las ultimas 5 noticias, ordenadas por fecha de creacion descendente.
        // Asegurate de cargar la relacion 'user' si quieres mostrar el autor.
        $noticias = Noticia::with('user')
            ->where('leida', false)
            ->latest() // Ordena por created_at de forma descendente
            ->limit(5) // Limita a las ultimas 5 noticias
            ->get();

        // Retorna la vista parcial con las noticias.
        return view('partials.notification-noticia', compact('noticias'));
    }

    /**
     * Marca una noticia como leida.
     *
     * @param  \App\Models\Noticia  $noticia
     * @return \Illuminate\Http\JsonResponse
     */
    public function markAsRead(Noticia $noticia)
    {
        $noticia->leida = true;
        $noticia->save();

        return response()->json(['message' => 'Noticia marcada como leida.']);
    }
}
