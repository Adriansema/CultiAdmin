<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Boletin;
use Illuminate\Http\Request;
use App\Mail\BoletinEstadoMail;

use App\Http\Controllers\Controller;
use App\Services\OperarioAndFuncionarioService; // Asegurate de que el nombre del servicio sea correcto
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;

class PendienteBolController extends Controller
{
    /**
     * Muestra la lista de boletines pendientes de revision para el operador.
     */
    public function index(Request $request, OperarioAndFuncionarioService $operarioAndFuncionarioService)
    {
        // Autorizar la accion: 'ver boletines pendiente'
        Gate::authorize('validar boletin');

        // Obtener solo los boletines filtrados utilizando el metodo especifico del servicio
        $boletines = $operarioAndFuncionarioService->obtenerBoletinesFiltrados($request);

        // Retorna la vista con los boletines pendientes
        return view('pendientes.boletines_pendientes', compact('boletines'));
    }

    /**
     * Retorna boletines pendientes de revision filtrados en formato JSON.
     * Este metodo se enfoca exclusivamente en los boletines.
     */
    public function getFilteredBoletins(Request $request, OperarioAndFuncionarioService $operarioAndFuncionarioService)
    {
        // Obtener solo los boletines utilizando el metodo especifico del servicio
        $boletines = $operarioAndFuncionarioService->obtenerBoletinesFiltrados($request);

        // Retornar solo los boletines en formato JSON
        return response()->json([
            'boletines' => $boletines,
        ]);
    }

    /**
     * Muestra un Boletin en detalle para el Operador.
     */
    public function show($id)
    {
        // Encuentra el boletin por ID o falla
        $boletin = Boletin::findOrFail($id);

        // Retorna la vista de detalle del boletin
        return view('boletines.show', compact('boletin'));
    }

    /**
     * Valida/Aprueba un Boletin.
     */
    public function validar(Request $request, $id)
    {
        // ¡Añade esto al principio del método!
        $boletin = Boletin::findOrFail($id);

        $boletin->update([
            'estado' => 'aprobado',
            'observaciones' => null,
            'validado_por_user_id' => Auth::id(),
            'rechazado_por_user_id' => null,
        ]);

        $creador = User::find($boletin->user_id);
        if ($creador && $creador->email) {
            Mail::to($creador->email)->send(new BoletinEstadoMail($boletin));
        }

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Boletín aprobado con éxito.'], 200);
        }
        return back()->with('status_boletin', 'aprobado');
    }

    /**
     * Rechaza un Boletin.
     */
    public function rechazar(Request $request, $id)
    {
        // ¡Añade esto al principio del método!
        $boletin = Boletin::findOrFail($id);

        $request->validate([
            'observaciones' => 'required|string|max:500',
        ]);

        $boletin->update([
            'estado' => 'rechazado',
            'observaciones' => $request->observaciones,
            'rechazado_por_user_id' => Auth::id(),
            'validado_por_user_id' => null,
        ]);

        $creador = User::find($boletin->user_id);
        if ($creador && $creador->email) {
            Mail::to($creador->email)->send(new BoletinEstadoMail($boletin));
        }

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Boletín rechazado con éxito.'], 200);
        }
        return back()
            ->with('status_boletin', 'rechazado')
            ->with('boletin_id_for_redirect', $boletin->id);
    }
}
