<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Producto;
use Illuminate\Http\Request;
use App\Mail\ProductoEstadoMail;

use App\Http\Controllers\Controller;
use App\Services\OperarioAndFuncionarioService; // Cambiado a OperarioAndFuncionarioService
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;

class PendienteProController extends Controller
{
    /**
     * Muestra la lista de productos pendientes de revision para el operador.
     */
    public function index(Request $request, OperarioAndFuncionarioService $operarioAndFuncionarioService) // Inyeccion del nuevo servicio
    {
        // Autorizar la accion: 'ver productos pendiente'
        Gate::authorize('validar producto');

        // Obtener solo los productos filtrados utilizando el metodo especifico del servicio
        $productos = $operarioAndFuncionarioService->obtenerProductosFiltrados($request);

        // Retorna la vista con los productos pendientes
        return view('pendientes.productos_pendientes', compact('productos'));
    }

    /**
     * Retorna productos pendientes de revision filtrados en formato JSON.
     * Este metodo se enfoca exclusivamente en los productos.
     */
    public function getFilteredProducts(Request $request, OperarioAndFuncionarioService $operarioAndFuncionarioService)
    {
        // Obtener solo los productos utilizando el metodo especifico del servicio
        $productos = $operarioAndFuncionarioService->obtenerProductosFiltrados($request);

        // Retornar solo los productos en formato JSON
        return response()->json([
            'productos' => $productos,
        ]);
    }

    /**
     * Muestra un Producto en detalle para el Operador.
     */
    public function show($id)
    {
        // Encuentra el producto por ID o falla
        $producto = Producto::findOrFail($id);

        // Retorna la vista de detalle del producto
        return view('productos.show', compact('producto'));
    }

     /**
     * Valida/Aprueba un Producto.
     */
    public function validar(Request $request, $id)
    {
        // Encuentra el producto por ID o falla
        $producto = Producto::findOrFail($id);

        // Actualiza el estado del producto a 'aprobado'
        $producto->update([
            'estado' => 'aprobado',
            'observaciones' => null, // Limpia las observaciones si las hubiera
            'validado_por_user_id' => Auth::id(), // Registra quien lo valido
            'rechazado_por_user_id' => null, // Limpia el ID de rechazador
        ]);

        // Encuentra al creador del producto para enviar el correo
        $creador = User::find($producto->user_id);
        if ($creador && $creador->email) {
            // Envia un correo notificando el cambio de estado
            Mail::to($creador->email)->send(new ProductoEstadoMail($producto));
        }

        return back()->with('status_producto', 'aprobado');
    }

    /**
     * Rechaza un Producto.
     */
    public function rechazar(Request $request, $id)
    {
        // Encuentra el producto por ID o falla
        $producto = Producto::findOrFail($id);

        // Valida que se proporcionen observaciones para el rechazo
        $request->validate([
            'observaciones' => 'required|string|max:500',
        ]);

        // Actualiza el estado del producto a 'rechazado'
        $producto->update([
            'estado' => 'rechazado',
            'observaciones' => $request->observaciones, // Guarda las observaciones del rechazo
            'rechazado_por_user_id' => Auth::id(), // Registra quien lo rechazo
            'validado_por_user_id' => null, // Limpia el ID de validador
        ]);

        // Encuentra al creador del producto para enviar el correo
        $creador = User::find($producto->user_id);
        if ($creador && $creador->email) {
            // Envia un correo notificando el cambio de estado
            Mail::to($creador->email)->send(new ProductoEstadoMail($producto));
        }

        return back()
            ->with('status_producto', 'rechazado')
            ->with('producto_id_for_redirect', $producto->id);
    }
}
