<?php

// app/Http/Controllers/PqrsController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pqrs;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail; // ¡Importa la fachada Mail!
use App\Mail\PqrsConfirmationMail; // ¡Importa tu nueva clase Mailable!

class PqrsController extends Controller
{
    public function create()
    {
        return view('pqrs.create');
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'email' => ['required', 'string', 'email', 'max:255'],
            'nombre' => ['nullable', 'string', 'max:255'],
            'telefono' => ['nullable', 'string', 'max:50'],
            'asunto' => ['required', 'string', 'max:255'],
            'mensaje' => ['required', 'string'],
            'tipo' => ['required', 'in:pregunta,queja,reclamo,sugerencia'],
        ]);

        $pqrs = Pqrs::create([
            'user_id' => Auth::id(), // Si el usuario está autenticado, su ID; de lo contrario, null
            'email' => $validatedData['email'],
            'nombre' => $validatedData['nombre'],
            'telefono' => $validatedData['telefono'],
            'asunto' => $validatedData['asunto'],
            'mensaje' => $validatedData['mensaje'],
            'tipo' => $validatedData['tipo'],
            'estado' => 'pendiente',
        ]);

        // **USO DE $pqrs: ENVIAR EL CORREO DE CONFIRMACIÓN**
        try {
            Mail::to($pqrs->email)->send(new PqrsConfirmationMail($pqrs));
            // Opcional: Si quieres enviar una copia al equipo de soporte
            // Mail::to('soporteayuda2025@gmail.com')->send(new PqrsConfirmationMail($pqrs));
        } catch (\Exception $e) {
            // Es buena práctica capturar excepciones para no romper la aplicación si el envío falla
            // Puedes loggear el error o notificar a un administrador
            \Log::error("Error al enviar correo de confirmación de PQR (ID: {$pqrs->id}): " . $e->getMessage());
        }

        return redirect()->back()->with('success', 'Tu PQR ha sido enviado exitosamente. Revisamos tu solicitud y te enviaremos una confirmación a tu correo.');
    }
}