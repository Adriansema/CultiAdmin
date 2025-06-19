<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Boletin;
use Illuminate\Http\Request;
use App\Mail\BoletinEstadoMail;
use App\Services\OperadorService;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;

class PendienteBolController extends Controller
{
    /**
     * Muestra la lista de boletines pendientes de revisión para el operador.
     */
    public function index(Request $request, OperadorService $operadorService)
    {
        Gate::authorize('ver boletines pendiente');
        $data = $operadorService->obtenerProductosYBoletinesFiltrados($request);
        $boletines = $data['boletines'];
        // No necesitas productos aquí si solo vas a mostrar boletines
        return view('pendientes.boletines_pendientes', compact('boletines'));
    }

    /**
     * Retorna productos y boletines pendientes de revisión filtrados en formato JSON.
     */
    public function getFilteredProductsAndBoletins(Request $request, OperadorService $operadorService)
    {

        $data = $operadorService->obtenerProductosYBoletinesFiltrados($request);
        return response()->json([
            'productos' => $data['productos'],
            'boletines' => $data['boletines'],
        ]);
    }

    /**
     * Muestra un Boletín en detalle para el Operador.
     */
    public function show($id)
    {
        $boletin = Boletin::findOrFail($id);

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
