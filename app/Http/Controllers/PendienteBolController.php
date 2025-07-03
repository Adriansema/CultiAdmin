<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Boletin;
use Illuminate\Http\Request;
use App\Mail\BoletinEstadoMail;

use App\Http\Controllers\Controller;
use App\Services\OperarioAndFuncionarioService; // Asegúrate de que el nombre del servicio sea correcto
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;

class PendienteBolController extends Controller
{
    /**
     * Muestra la lista de boletines pendientes de revisión para el operador.
     */
    public function index(Request $request, OperarioAndFuncionarioService $operarioAndFuncionarioService)
    {
        // Autorizar la acción: 'ver boletines pendiente'
        Gate::authorize('validar boletin');

        // Obtener solo los boletines filtrados utilizando el método específico del servicio
        $boletines = $operarioAndFuncionarioService->obtenerBoletinesFiltrados($request);

        // Retorna la vista con los boletines pendientes
        return view('pendientes.boletines_pendientes', compact('boletines'));
    }

    /**
     * Retorna boletines pendientes de revisión filtrados en formato JSON.
     * Este método se enfoca exclusivamente en los boletines.
     */
    public function getFilteredBoletins(Request $request, OperarioAndFuncionarioService $operarioAndFuncionarioService)
    {
        // Obtener solo los boletines utilizando el método específico del servicio
        $boletines = $operarioAndFuncionarioService->obtenerBoletinesFiltrados($request);

        // Retornar solo los boletines en formato JSON
        return response()->json([
            'boletines' => $boletines,
        ]);
    }

    /**
     * Muestra un Boletín en detalle para el Operador.
     */
    public function show($id)
    {
        // Encuentra el boletín por ID o falla
        $boletin = Boletin::findOrFail($id);

        // Retorna la vista de detalle del boletín
        return view('boletines.show', compact('boletin'));
    }

    /**
     * Valida/Aprueba un Boletín.
     */
    public function validar(Request $request, $id)
    {
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

        return back()->with('status_boletin', 'aprobado');
    }

    /**
     * Rechaza un Boletín.
     */
    public function rechazar(Request $request, $id)
    {
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

        return back()
            ->with('status_boletin', 'rechazado')
            ->with('boletin_id_for_redirect', $boletin->id);
    }
}
