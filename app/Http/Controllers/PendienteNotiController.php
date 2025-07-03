<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Noticia; // Cambiado de Boletin a Noticia
use Illuminate\Http\Request;
use App\Mail\NoticiaEstadoMail; // Asumiendo una clase de correo similar para Noticia
use App\Http\Controllers\Controller;
use App\Services\OperarioAndFuncionarioService;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;

class PendienteNotiController extends Controller
{
    /**
     * Muestra la lista de noticias pendientes de revisión para el operador.
     */
    public function index(Request $request, OperarioAndFuncionarioService $operadorService)
    {
        Gate::authorize('validar noticia');

        $data = $operadorService->obtenerNoticiasFiltradas($request);
        $noticias = $data['noticias'];

        // Retorna la vista con las noticias pendientes
        return view('pendientes.noticias_pendientes', compact('noticias'));
    }

    /**
     * Retorna noticias pendientes de revisión filtradas en formato JSON.
     */
    public function getFilteredNews(Request $request, OperarioAndFuncionarioService $operadorService)
    {
        // Asumiendo que OperadorService tiene un método similar para noticias
        $data = $operadorService->obtenerNoticiasFiltradas($request);

        return response()->json([
            // Si también manejas productos relacionados con noticias, inclúyelos aquí.
            // Para este ejemplo, solo se devuelven noticias.
            'noticias' => $data['noticias'],
        ]);
    }

    /**
     * Muestra una Noticia en detalle para el Operador.
     */
    public function show($id)
    {
        // Encuentra la noticia por ID o falla
        $noticia = Noticia::findOrFail($id);

        // Retorna la vista de detalle de la noticia
        return view('noticias.show', compact('noticia'));
    }

    /**
     * Valida/Aprueba una Noticia.
     */
    public function validar(Request $request, $id)
    {
        // Encuentra la noticia por ID o falla
        $noticia = Noticia::findOrFail($id);

        // Actualiza el estado de la noticia a 'aprobado'
        $noticia->update([
            'estado' => 'aprobado',
            'observaciones' => null, // Limpia las observaciones si las hubiera
            'validado_por_user_id' => Auth::id(), // Registra quién la validó
            'rechazado_por_user_id' => null, // Limpia el ID de rechazador
        ]);

        // Encuentra al creador de la noticia para enviar el correo
        $creador = User::find($noticia->user_id);
        if ($creador && $creador->email) {
            // Envía un correo notificando el cambio de estado
            Mail::to($creador->email)->send(new NoticiaEstadoMail($noticia));
        }

        // Redirige de vuelta con un mensaje de estado
        return back()->with('status_noticia', 'aprobado');
    }

    /**
     * Rechaza una Noticia.
     */
    public function rechazar(Request $request, $id)
    {
        // Encuentra la noticia por ID o falla
        $noticia = Noticia::findOrFail($id);

        // Valida que se proporcionen observaciones para el rechazo
        $request->validate([
            'observaciones' => 'required|string|max:500',
        ]);

        // Actualiza el estado de la noticia a 'rechazado'
        $noticia->update([
            'estado' => 'rechazado',
            'observaciones' => $request->observaciones, // Guarda las observaciones del rechazo
            'rechazado_por_user_id' => Auth::id(), // Registra quién la rechazó
            'validado_por_user_id' => null, // Limpia el ID de validador
        ]);

        // Encuentra al creador de la noticia para enviar el correo
        $creador = User::find($noticia->user_id);
        if ($creador && $creador->email) {
            // Envía un correo notificando el cambio de estado
            Mail::to($creador->email)->send(new NoticiaEstadoMail($noticia));
        }

        // Redirige de vuelta con un mensaje de estado y el ID de la noticia para posible redirección
        return back()
            ->with('status_noticia', 'rechazado')
            ->with('noticia_id_for_redirect', $noticia->id);
    }
}
