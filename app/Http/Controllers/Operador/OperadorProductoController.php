<?php

// actualizacion 09/04/2025 (y ahora con ProductPolicy 06/06/2025)

namespace App\Http\Controllers\Operador;

use App\Models\User;
use App\Models\Boletin;
use App\Models\Producto;
use Illuminate\Http\Request;
use App\Mail\BoletinEstadoMail;
use App\Mail\ProductoEstadoMail;
use App\Services\OperadorService; // Asegúrate de que este servicio exista y funcione
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
// No necesitas importar ProductoPolicy y BoletinPolicy aquí, ya que $this->authorize()
// las resuelve automáticamente a través del AuthServiceProvider.

class OperadorProductoController extends Controller
{
    /**
     * Muestra la lista de productos y boletines pendientes de revisión para el operador.
     */
    public function pendientes(Request $request, OperadorService $operadorService)
    {
        /* if (!Auth::user()->hasRole('operador')) {
             abort(403, 'Acción no autorizada. Requiere rol de operador.');
        } */

        $data = $operadorService->obtenerProductosYBoletinesFiltrados($request);

        $productos = $data['productos'];
        $boletines = $data['boletines'];

        return view('operador.pendientes', compact('productos', 'boletines'));
    }

    /**
     * Retorna productos y boletines pendientes de revisión filtrados en formato JSON.
     */
    public function getFilteredProductsAndBoletins(Request $request, OperadorService $operadorService)
    {
        // Mismo control de acceso que 'pendientes'.
        /* if (!Auth::user()->hasRole('operador')) {
            abort(403, 'Acción no autorizada. Requiere rol de operador.');
        } */

        $data = $operadorService->obtenerProductosYBoletinesFiltrados($request);
        return response()->json([
            'productos' => $data['productos'],
            'boletines' => $data['boletines'],
        ]);
    }

    /**
     * Muestra un Producto (Noticia) en detalle para el Operador.
     */
    public function showProducto($id)
    {
        $producto = Producto::findOrFail($id);

        // La siguiente línea (comentada) es ahora redundante debido a la Policy.
        // if ($producto->estado !== 'pendiente') {
        //     abort(403, 'Este producto ya fue procesado y no requiere acción del operador.');
        // }

        return view('operador.productos.show', compact('producto'));
    }

    /**
     * Muestra un Boletín en detalle para el Operador.
     */
    public function showBoletin($id)
    {
        $boletin = Boletin::findOrFail($id);

        // La siguiente línea (comentada) es ahora redundante debido a la Policy.
        // if ($boletin->estado !== 'pendiente') {
        //     abort(403, 'Este boletín ya fue procesado y no requiere acción del operador.');
        // }

        return view('operador.boletines.show', compact('boletin'));
    }

    /**
     * Valida/Aprueba un Producto.
     */
    public function validar(Request $request, $id)
    {
        $producto = Producto::findOrFail($id);

        // La siguiente línea (comentada) es ahora redundante debido a la Policy.
        // if (!Auth::user()->hasRole('operador')) {
        //     return back()->with('error', 'No tienes permiso para realizar esta acción.');
        // }

        $producto->update([
            'estado' => 'aprobado',
            'observaciones' => null,
            'validado_por_user_id' => Auth::id(),
            'rechazado_por_user_id' => null,
        ]);

        $creador = User::find($producto->user_id);
        if ($creador && $creador->email) {
            Mail::to($creador->email)->send(new ProductoEstadoMail($producto));
        }

        return back()->with('status_producto', 'aprobado');
    }

    /**
     * Rechaza un Producto.
     */
    public function rechazar(Request $request, $id)
    {
        $producto = Producto::findOrFail($id);

        // La siguiente línea (comentada) es ahora redundante debido a la Policy.
        // if (!Auth::user()->hasRole('operador')) {
        //     return back()->with('error', 'No tienes permiso para realizar esta acción.');
        // }

        $request->validate([
            'observaciones' => 'required|string|max:500',
        ]);

        $producto->update([
            'estado' => 'rechazado',
            'observaciones' => $request->observaciones,
            'rechazado_por_user_id' => Auth::id(),
            'validado_por_user_id' => null,
        ]);

        $creador = User::find($producto->user_id);
        if ($creador && $creador->email) {
            Mail::to($creador->email)->send(new ProductoEstadoMail($producto));
        }

        return back()
            ->with('status_producto', 'rechazado')
            ->with('producto_id_for_redirect', $producto->id);
    }

    /**
     * Valida/Aprueba un Boletín.
     */
    public function validarBoletin(Request $request, $id)
    {
        $boletin = Boletin::findOrFail($id);

        // La siguiente línea (comentada) es ahora redundante debido a la Policy.
        // if (!Auth::user()->hasRole('operador')) {
        //     return back()->with('error', 'No tienes permiso para realizar esta acción.');
        // }

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
    public function rechazarBoletin(Request $request, $id)
    {
        $boletin = Boletin::findOrFail($id);

        // La siguiente línea (comentada) es ahora redundante debido a la Policy.
        // if (!Auth::user()->hasRole('operador')) {
        //     return back()->with('error', 'No tienes permiso para realizar esta acción.');
        // }

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
