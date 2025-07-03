<?php

namespace App\Http\Controllers;

use App\Models\Noticia; // Importa el modelo Noticia
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth; // Para obtener el ID del usuario autenticado
use Illuminate\Support\Facades\Storage; // Para manejar la carga de imágenes
use Illuminate\Support\Facades\Response;
use Symfony\Component\HttpFoundation\StreamedResponse; // Necesario para la descarga de archivos grandes
use App\Services\NoticiaService;

class NoticiaController extends Controller
{
    /**
     * Display a listing of the resource.
     * Muestra una lista de todas las noticias.
     */
    public function index(Request $request, NoticiaService $noticiaService)
    {
        Gate::authorize('crear noticia');
        $noticias = $noticiaService->obtenerNoticiaFiltradas($request);
        // Carga todas las noticias, incluyendo la relación con User para mostrar quién la creó.
        /* $noticias = Noticia::with('user')->get(); */
        return view('noticias.index', compact('noticias'));
    }

    public function getFilteredNoticy(Request $request, NoticiaService $noticiaService)
    {
        $noticias = $noticiaService->obtenerNoticiaFiltradas($request);
        return response()->json($noticias);
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
        $request->validate([
            'tipo' => 'required|string|max:255',
            'titulo' => 'nullable|string|max:255',
            'clase' => 'nullable|string|max:255',
            'imagen' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Validación para imagen
            'informacion' => 'nullable|string',
            'numero_pagina' => 'required|integer',
            'autor' => 'nullable|string|max:255',
        ]);

        // 2. Lógica para guardar la imagen (si se ha subido).
        $imagenPath = null;
        if ($request->hasFile('imagen')) {
            $file = $request->file('imagen');

            // Genera un nombre de archivo único con la extensión original del cliente
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();

            // Guarda la imagen en storage/app/public/noticias con el nombre generado
            $imagenPath = $file->storeAs('noticias', $filename, 'public');
        }
        // 3. Crear la nueva noticia.
        Noticia::create([
            'user_id' => Auth::id(),
            'tipo' => $request->tipo,
            'titulo' => $request->titulo,
            'clase' => $request->clase,
            'imagen' => $imagenPath, // Guarda la ruta de la imagen
            'informacion' => $request->informacion,
            'numero_pagina' => $request->numero_pagina,
            'autor' => $request->autor,
            'leida' => false, // ¡Nueva columna, por defecto false!
        ]);

        // 4. Redirigir al índice de noticias con un mensaje de éxito.
        return redirect()->route('noticias.index')->with('modal_success_message', '¡Noticia creada con éxito!');
    }

    /**
     * Display the specified resource.
     * Muestra los detalles de una noticia específica.
     */
    public function show(Noticia $noticia)
    {
        // Carga la relación 'user' para mostrar quién la creó.
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

        //  Guarda el estado original de la noticia antes de cualquier actualización
        $originalEstado = $noticia->estado;

        //  Valida los datos de la solicitud
        $request->validate([
            'tipo' => 'required|string|max:255',
            'titulo' => 'nullable|string|max:255',
            'clase' => 'nullable|string|max:255',
            'imagen' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'informacion' => 'nullable|string',
            'numero_pagina' => 'required|integer',
            'autor' => 'nullable|string|max:255',
        ]);

        // Lógica para actualizar la imagen.
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

        // Lógica para cambiar el estado a 'pendiente' si la noticia fue editada
        // y su estado anterior era 'aprobado' o 'rechazado'.
        // Esto asegura que una noticia editada vuelva al flujo de revisión.
        if ($originalEstado === 'aprobado' || $originalEstado === 'rechazado') {
            $noticia->estado = 'pendiente';
            $noticia->observaciones = null; // Limpia las observaciones anteriores si las hubiera
            // Si tienes un campo específico para observaciones del operador, también límpialo:
            // $noticia->observaciones_operador = null;
        }

        // Guarda los cambios en la base de datos
        $noticia->save();

        // Actualizar la noticia
        $noticia->update([
            'tipo' => $request->tipo,
            'titulo' => $request->titulo,
            'clase' => $request->clase,
            'informacion' => $request->informacion,
            'numero_pagina' => $request->numero_pagina,
            'autor' => $request->autor,
        ]);

        // Redirigir al índice de noticias con un mensaje de éxito
        return redirect()->route('noticias.index')->with('success', 'Noticia actualizada con éxito.');
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

        // 3. Redirigir al índice de noticias con un mensaje de éxito.
        return redirect()->route('noticias.index')->with('success', 'Noticia eliminada con éxito.');
    }

    /**
     * Exporta todas las noticias a un archivo CSV.
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function exportarCsv()
    {
        // Define los encabezados del CSV
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="noticias_exportadas_' . now()->format('Ymd_His') . '.csv"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        // Columnas que se incluirán en el CSV.
        // Asegúrate de que estos nombres coincidan con los nombres de las columnas en tu tabla 'noticias'.
        $columns = [
            'id',
            'user_id',
            'tipo',
            'titulo',
            'clase',
            'imagen', // Ruta de la imagen
            'informacion',
            'numero_pagina',
            'autor',
            'leida',
            'created_at',
            'updated_at',
        ];

        // Crea una respuesta de tipo StreamedResponse para manejar archivos grandes de manera eficiente
        $callback = function() use ($columns) {
            $file = fopen('php://output', 'w'); // Abre el flujo de salida para escribir el CSV

            // Escribe los encabezados del CSV
            fputcsv($file, $columns);

            // Obtiene todas las noticias. Para conjuntos de datos muy grandes, considera paginación
            // o chunking para evitar problemas de memoria.
            // Ejemplo con chunking (recomendado para muchos registros):
            Noticia::chunk(2000, function ($noticias) use ($file, $columns) {
                foreach ($noticias as $noticia) {
                    $row = [];
                    foreach ($columns as $column) {
                        // Accede al atributo del modelo.
                        // Para 'imagen', podrías querer la URL completa en lugar de solo la ruta de almacenamiento.
                        // Si 'informacion' puede contener saltos de línea, fputcsv los manejará correctamente.
                        $value = $noticia->$column;

                        // Si la columna es 'imagen' y quieres la URL pública, puedes hacer esto:
                        if ($column === 'imagen' && $value) {
                            $value = asset('storage/' . $value); // Asume que 'noticias' está en el disco 'public'
                        }

                        // Si la columna es 'informacion' y necesitas limpiar caracteres especiales o HTML:
                        if ($column === 'informacion' && $value) {
                            $value = strip_tags($value); // Elimina etiquetas HTML
                            $value = html_entity_decode($value, ENT_QUOTES | ENT_HTML5, 'UTF-8'); // Decodifica entidades HTML
                            $value = str_replace(["\r", "\n"], " ", $value); // Reemplaza saltos de línea por espacios
                        }

                        $row[] = $value;
                    }
                    fputcsv($file, $row); // Escribe la fila de datos
                }
            });

            fclose($file); // Cierra el flujo de salida
        };

        return new StreamedResponse($callback, 200, $headers);
    }

    /**
     * Obtiene las noticias más recientes para mostrar en el dashboard.
     *
     * @return \Illuminate\View\View
     */
    public function getDashboardNoticias()
    {
        // Obtener las últimas 5 noticias, ordenadas por fecha de creación descendente.
        // Asegúrate de cargar la relación 'user' si quieres mostrar el autor.
        $noticias = Noticia::with('user')
            ->where('leida', false)
            ->latest() // Ordena por created_at de forma descendente
            ->limit(5) // Limita a las últimas 5 noticias
            ->get();

        // Retorna la vista parcial con las noticias.
        return view('partials.notificacion-noticia', compact('noticias'));
    }

    /**
     * Marca una noticia como leída.
     *
     * @param  \App\Models\Noticia  $noticia
     * @return \Illuminate\Http\JsonResponse
     */
    public function markAsRead(Noticia $noticia)
    {
        $noticia->leida = true;
        $noticia->save();

        return response()->json(['message' => 'Noticia marcada como leída.']);
    }
}
