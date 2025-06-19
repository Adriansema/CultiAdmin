<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Producto;
use Illuminate\Http\Request;
use App\Mail\ProductoEstadoMail;
use App\Services\OperadorService;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;

class PendienteProController extends Controller
{
    /**
     * Muestra la lista de productos y boletines pendientes de revisión para el operador.
     */
    public function index(Request $request, OperadorService $operadorService)
    {
        Gate::authorize('ver productos pendiente');
        $data = $operadorService->obtenerProductosYBoletinesFiltrados($request);
        $productos = $data['productos'];
        // No necesitas boletines aquí si solo vas a mostrar productos
        return view('pendientes.productos_pendientes', compact('productos'));
    }

     /**
     * Muestra un Producto (Noticia) en detalle para el Operador.
     */
    public function show($id)
    {
        $producto = Producto::findOrFail($id);

        return view('productos.show', compact('producto'));
    }

     /**
     * Valida/Aprueba un Producto.
     */
    public function validar(Request $request, $id)
    {
        $producto = Producto::findOrFail($id);

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
}