<?php

namespace App\Http\Controllers\Operador;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Producto;
use App\Models\Boletin;
use App\Models\User; // Asegúrate de tener tu modelo de Usuario para enviar correos
use Illuminate\Support\Facades\Mail;
use App\Mail\ProductoEstadoMail; // Asegúrate de crear estas clases Mailable
use App\Mail\BoletinEstadoMail;   // Asegúrate de crear estas clases Mailable
use Illuminate\Support\Facades\Auth; // Por si necesitas el usuario autenticado para permisos

class OperadorProductoController extends Controller
{
    // Método para mostrar productos y boletines pendientes de revisión
    public function pendientes()
    {
        // Puedes agregar lógica de permisos aquí, por ejemplo:
        // $this->authorize('viewAny', Producto::class); // Si usas Policies

        $productos = Producto::where('estado', 'pendiente')->latest()->paginate(10);
        $boletines = Boletin::where('estado', 'pendiente')->latest()->paginate(10);

        return view('operador.pendientes', compact('productos', 'boletines'));
    }

    // Método para validar/aprobar un Producto (Noticia)
    public function validar(Request $request, $id) // Renombrado a validarProducto para claridad en el contexto
    {
        $producto = Producto::findOrFail($id);

        $producto->update([
            'estado' => 'aprobado',
            'observaciones' => null, // Limpiar observaciones anteriores al aprobar
        ]);

        // Enviar notificación por correo
        // Asumiendo que 'user_id' en Producto es el ID del creador de la noticia
        $creador = User::find($producto->user_id);
        if ($creador && $creador->email) {
            Mail::to($creador->email)->send(new ProductoEstadoMail($producto));
        }

        // Retornar con mensaje para SweetAlert2 y sin redirección de ID, ya que no hay botón "Ir" aquí
        return back()->with('status_producto', 'aprobado');
    }

    // Método para rechazar un Producto (Noticia)
    public function rechazar(Request $request, $id) // Renombrado a rechazarProducto para claridad en el contexto
    {
        $request->validate([
            'observaciones' => 'required|string|max:500', // Asegúrate de que las observaciones sean obligatorias y tengan un límite
        ]);

        $producto = Producto::findOrFail($id);

        $producto->update([
            'estado' => 'rechazado',
            'observaciones' => $request->observaciones,
        ]);

        // Enviar notificación por correo
        // Asumiendo que 'user_id' en Producto es el ID del creador de la noticia
        $creador = User::find($producto->user_id);
        if ($creador && $creador->email) {
            Mail::to($creador->email)->send(new ProductoEstadoMail($producto));
        }

        // Retornar con mensaje para SweetAlert2, y el ID del producto para el botón "Ir"
        return back()
            ->with('status_producto', 'rechazado')
            ->with('producto_id_for_redirect', $producto->id);
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

    // Método para validar/aprobar un Boletín
    public function validarBoletin(Request $request, $id)
    {
        $boletin = Boletin::findOrFail($id);

        $boletin->update([
            'estado' => 'aprobado',
            'observaciones' => null, // Limpiar observaciones anteriores al aprobar
        ]);

        // Enviar notificación por correo
        // Asumiendo que 'user_id' en Boletin es el ID del creador del boletín
        $creador = User::find($boletin->user_id);
        if ($creador && $creador->email) {
            Mail::to($creador->email)->send(new BoletinEstadoMail($boletin));
        }

        // Retornar con mensaje para SweetAlert2
        return back()->with('status_boletin', 'aprobado');
    }

    // Método para rechazar un Boletín
    public function rechazarBoletin(Request $request, $id)
    {
        $request->validate([
            'observaciones' => 'required|string|max:500', // Asegúrate de que las observaciones sean obligatorias y tengan un límite
        ]);

        $boletin = Boletin::findOrFail($id);

        $boletin->update([
            'estado' => 'rechazado',
            'observaciones' => $request->observaciones,
        ]);

        // Enviar notificación por correo
        // Asumiendo que 'user_id' en Boletin es el ID del creador del boletín
        $creador = User::find($boletin->user_id);
        if ($creador && $creador->email) {
            Mail::to($creador->email)->send(new BoletinEstadoMail($boletin));
        }

        // Retornar con mensaje para SweetAlert2, y el ID del boletín para el botón "Ir"
        return back()
            ->with('status_boletin', 'rechazado')
            ->with('boletin_id_for_redirect', $boletin->id);
    }
}