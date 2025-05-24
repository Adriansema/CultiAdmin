<?php

// actualizacion 09/04/2025

namespace App\Http\Controllers;

use App\Models\User; // Para buscar operadores
use App\Models\Boletin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail; // Importa Mail
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Mail\NuevaRevisionPendienteMail; // Importa la nueva Mailable


class BoletinController extends Controller
{
    public function index()
    {
        $boletines = Boletin::latest()->get();

        return view('boletines.index', compact('boletines'));
    }

    public function create()
    {
        return view('boletines.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'archivo' => 'nullable|string', //por el momento esta null, ya que en futuro se reutilizar
            'contenido' => 'required|string',
        ]);

        $boletin = Boletin::create([ // Captura la instancia del boletín creado
            'user_id' => Auth::id(),
            'estado' => 'pendiente',
            'contenido' => $validated['contenido'],
            'archivo' => $validated['archivo'],
        ]);

        // *** Lógica para enviar email al operador cuando se crea un boletín ***
        $operadores = User::role('operador')->get(); // Obtiene todos los usuarios con el rol 'operador'
        foreach ($operadores as $operador) {
            Mail::to($operador->email)->send(new NuevaRevisionPendienteMail($boletin, 'Boletín'));
        }

        return redirect()->route('boletines.index')->with('success', 'Boletín creado con éxito y enviado a revisión del operador.');
    }

    public function show(Boletin $boletin)
    {
        return view('boletines.show', compact('boletin'));
    }

    public function edit(Boletin $boletin)
    {
        return view('boletines.edit', compact('boletin'));
    }

    public function update(Request $request, Boletin $boletin)
    {
        $request->validate([
            'archivo' => 'nullable|string', // Puede ser nulo o string dependiendo del flujo
            'contenido' => 'required|string',
            // 'observaciones' => 'nullable|string', // El usuario puede modificar sus observaciones
        ]);

        // Almacenar el estado original del boletín ANTES de cualquier cambio
        $originalEstado = $boletin->estado;

        // Actualizar los campos del boletín
        $boletin->contenido = $request->contenido;
        $boletin->archivo = $request->archivo; // Asigna si es relevante para el flujo actual

        // *** Lógica para cambiar el estado a 'pendiente' si el boletín fue editado
        // *** y su estado original era 'aprobado' o 'rechazado'.
        // Esto asegura que cada edición por parte del creador requiera una nueva validación del operador.
        $estadoCambiadoAPendiente = false;
        if ($originalEstado === 'aprobado' || $originalEstado === 'rechazado') {
             $boletin->estado = 'pendiente';
             // Opcional: limpiar la observación del operador al volver a pendiente.
             // Asumiendo que 'observaciones' es la columna donde el operador pone la observación.
             $boletin->observaciones = null; // Limpiar observación del operador
             $estadoCambiadoAPendiente = true;
        }
        // No se permite al usuario cambiar el estado directamente desde esta vista.

        $boletin->save();

        // *** Lógica para enviar email al operador cuando un boletín editado vuelve a pendiente ***
        if ($estadoCambiadoAPendiente) {
            $operadores = User::role('operador')->get(); // Obtiene todos los usuarios con el rol 'operador'
            foreach ($operadores as $operador) {
                Mail::to($operador->email)->send(new NuevaRevisionPendienteMail($boletin, 'Boletín'));
            }
        }

        // Mensaje de éxito más descriptivo para el usuario
        return redirect()->route('boletines.index')->with('success', 'Boletín actualizado y enviado a revisión del operador.');
    }

    public function destroy(Boletin $boletin)
    {
        // Opcional: Eliminar archivo asociado si existe
        if ($boletin->archivo && Storage::disk('public')->exists($boletin->archivo)) {
            Storage::disk('public')->delete($boletin->archivo);
        }

        $boletin->delete();

        return redirect()->route('boletines.index')->with('success', 'Boletín eliminado.');
    }

    public function importarPdf(Request $request)
    {
        $request->validate([
            'archivo' => 'required|file|mimes:pdf|max:10240', // Validar que sea un PDF y tamaño
            'contenido' => 'nullable|string', // Contenido podría ser una descripción del PDF
        ]);

        // Guardar el archivo en storage/app/public/boletines
        $rutaArchivo = $request->file('archivo')->store('boletines', 'public');

        // Crear boletín con contenido y ruta del archivo
        $boletin = Boletin::create([ // Captura la instancia del boletín creado
            'user_id' => Auth::id(),
            'archivo' => $rutaArchivo,
            'contenido' => $request->contenido,
            'estado' => 'pendiente',
        ]);

        // *** Lógica para enviar email al operador cuando se importa un PDF (que también es pendiente) ***
        $operadores = User::role('operador')->get();
        foreach ($operadores as $operador) {
            Mail::to($operador->email)->send(new NuevaRevisionPendienteMail($boletin, 'Boletín'));
        }
        
        return redirect()->route('boletines.index')->with('success', 'Boletín importado correctamente y pendiente de revisión.');
    }
}
