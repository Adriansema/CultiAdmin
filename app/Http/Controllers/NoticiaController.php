<?php

namespace App\Http\Controllers;

use App\Models\Noticia; // Importa el modelo Noticia
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth; // Para obtener el ID del usuario autenticado
use Illuminate\Support\Facades\Storage; // Para manejar la carga de imágenes
use Illuminate\Support\Facades\Response;
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
        return redirect()->route('noticias.index')->with('success', 'Noticia creada con éxito.');
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
        // 1. Validar los datos del formulario.
        $request->validate([
            'tipo' => 'required|string|max:255',
            'titulo' => 'nullable|string|max:255',
            'clase' => 'nullable|string|max:255',
            'imagen' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'informacion' => 'nullable|string',
            'numero_pagina' => 'required|integer',
            'autor' => 'nullable|string|max:255',
            // 'estado' puede ser actualizado por un administrador, pero no lo incluimos aquí por simplicidad.
            // Si necesitas que los usuarios normales puedan cambiarlo, añádelo a la validación.
        ]);

        // 2. Lógica para actualizar la imagen.
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
        // 3. Actualizar la noticia.
        $noticia->update([
            'tipo' => $request->tipo,
            'titulo' => $request->titulo,
            'clase' => $request->clase,
            'informacion' => $request->informacion,
            'numero_pagina' => $request->numero_pagina,
            'autor' => $request->autor,
        ]);

        // 4. Redirigir al índice de noticias con un mensaje de éxito.
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
     * Obtiene las noticias más recientes para mostrar en el dashboard.
     *
     * @return \Illuminate\View\View
     */
    public function getDashboardNoticias()
    {
        // Obtener las últimas 5 noticias, ordenadas por fecha de creación descendente.
        // Asegúrate de cargar la relación 'user' si quieres mostrar el autor.
        $noticias = Noticia::with('user')
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
