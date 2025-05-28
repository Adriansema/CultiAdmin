<?php

namespace App\Http\Controllers\Operador;

use App\Models\User; // Asegúrate de tener tu modelo de Usuario para enviar correos
use App\Models\Boletin;
use App\Models\Producto;
use Illuminate\Http\Request;
use App\Mail\BoletinEstadoMail;   // Asegúrate de crear estas clases Mailable
use App\Mail\ProductoEstadoMail; // Asegúrate de crear estas clases Mailable
use App\Services\OperadorService;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth; // Por si necesitas el usuario autenticado para permisos

class OperadorProductoController extends Controller
{
   // Método para mostrar productos y boletines pendientes de revisión
    public function pendientes(Request $request, OperadorService $operadorService) // Corregido el paréntesis
    {
        $data = $operadorService->obtenerProductosYBoletinesFiltrados($request); // Renombrado el método

        // Los resultados se obtienen del array retornado por el servicio
        $productos = $data['productos'];
        $boletines = $data['boletines'];

        return view('operador.pendientes', compact('productos', 'boletines'));
    }

    // Si también necesitas una respuesta JSON:
    public function getFilteredProductsAndBoletins(Request $request, OperadorService $operadorService)
    {
        $data = $operadorService->obtenerProductosYBoletinesFiltrados($request);
        // Retornamos ambos en JSON, si es necesario, o solo uno, dependiendo de la necesidad del frontend
        return response()->json([
            'productos' => $data['productos'],
            'boletines' => $data['boletines'],
        ]);
    }

    // Método para mostrar un Producto (Noticia) en detalle para el Operador
    public function showProducto($id)
    {
        $producto = Producto::findOrFail($id);

        // La lógica de abortar 403 si no está pendiente es correcta para la vista del operador
        if ($producto->estado !== 'pendiente') {
            abort(403, 'Este producto ya fue procesado y no requiere acción del operador.');
        }

        return view('operador.productos.show', compact('producto'));
    }

    // Método para mostrar un Boletín en detalle para el Operador
    public function showBoletin($id)
    {
        $boletin = Boletin::findOrFail($id);

        // La lógica de abortar 403 si no está pendiente es correcta para la vista del operador
        if ($boletin->estado !== 'pendiente') {
            abort(403, 'Este boletín ya fue procesado y no requiere acción del operador.');
        }

        return view('operador.boletines.show', compact('boletin'));
    }

    // Método para validar/aprobar un Producto
    public function validar(Request $request, $id)
    {
        // 1. Verificación de permisos con Spatie Permission
        if (!Auth::user()->hasRole('operador')) {
            return back()->with('error', 'No tienes permiso para realizar esta acción.');
            // O, más drástico: abort(403, 'Acción no autorizada.');
        }

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

    // Método para rechazar un Producto
    public function rechazar(Request $request, $id)
    {
        // 1. Verificación de permisos con Spatie Permission
        if (!Auth::user()->hasRole('operador')) {
            return back()->with('error', 'No tienes permiso para realizar esta acción.');
        }

        $request->validate([
            'observaciones' => 'required|string|max:500',
        ]);

        $producto = Producto::findOrFail($id);

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

    // Método para validar/aprobar un Boletín
    public function validarBoletin(Request $request, $id)
    {
        // 1. Verificación de permisos con Spatie Permission
        if (!Auth::user()->hasRole('operador')) {
            return back()->with('error', 'No tienes permiso para realizar esta acción.');
        }

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

    // Método para rechazar un Boletín
    public function rechazarBoletin(Request $request, $id)
    {
        // 1. Verificación de permisos con Spatie Permission
        if (!Auth::user()->hasRole('operador')) {
            return back()->with('error', 'No tienes permiso para realizar esta acción.');
        }

        $request->validate([
            'observaciones' => 'required|string|max:500',
        ]);

        $boletin = Boletin::findOrFail($id);

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
