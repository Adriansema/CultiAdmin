<?php

// actualizacion 09/04/2025

namespace App\Http\Controllers;

use App\Mail\NuevaRevisionPendienteMail; // Para buscar operadores
use App\Models\Boletin;
use App\Models\User;
use App\Services\BoletinService;
use Illuminate\Support\Facades\Response;
use Illuminate\Http\Request; // Importa Mail
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage; // Importa la nueva Mailable

class BoletinController extends Controller
{
    public function index(Request $request, BoletinService $boletinService)
    {
         // Llama al método del servicio para obtener los productos paginados
        $boletines = $boletinService->obtenerBoletinFiltrados($request);
        return view('boletines.index', compact('boletines'));
    }

    // Si también necesitas una respuesta JSON (ej. para una API o Vue/React):
    public function getFilteredBoletin(Request $request, BoletinService $boletinService)
    {
        $productos = $boletinService->obtenerBoletinFiltrados($request);
        return response()->json($productos);
    } // Importar la fachada Response para streamuse App\Services\ProductService;

    public function create()
    {
        return view('boletines.create');
    }

    public function show(Boletin $boletin)
    {
        return view('boletines.show', compact('boletin'));
    }

    public function edit(Boletin $boletin)
    {
        return view('boletines.edit', compact('boletin'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'contenido' => 'required|string',
            'archivo_upload' => 'nullable|file|mimes:pdf|max:5120', // Reglas de validación para el archivo
            // - nullable: El archivo es opcional al crear.
            // - file: Debe ser un archivo válido.
            // - mimes: Tipos de archivo permitidos (ajusta según tus necesidades).
            // - max: Tamaño máximo en kilobytes (5120 KB = 5 MB).
        ]);

        $filePath = null; // Inicializamos la ruta del archivo a null

        // Lógica para manejar la subida del archivo si existe
        if ($request->hasFile('archivo_upload')) {
            // Guardar el archivo en la carpeta 'boletines' dentro de storage/app/public
            $filePath = $request->file('archivo_upload')->store('boletines', 'public');
        }

        $boletin = Boletin::create([
            'user_id' => Auth::id(),
            'estado' => 'pendiente',
            'contenido' => $validated['contenido'],
            'archivo' => $filePath, // Asignamos la ruta del archivo (o null si no se subió)
        ]);

        // *** Lógica para enviar email al operador cuando se crea un boletín ***
        $operadores = User::role('operador')->get(); // Obtiene todos los usuarios con el rol 'operador'
        foreach ($operadores as $operador) {
            Mail::to($operador->email)->send(new NuevaRevisionPendienteMail($boletin, 'Boletín'));
        }

        return redirect()->route('boletines.index')->with('success', 'Boletín creado con éxito y enviado a revisión del operador.');
    }
    
    public function update(Request $request, Boletin $boletin)
    {
        $request->validate([
            'contenido' => 'required|string',
            // 'observaciones' => 'nullable|string', // El usuario puede modificar sus observaciones
            'archivo_upload' => 'nullable|file|mimes:pdf|max:5120', // Reglas para el archivo:
            // - nullable: puede no subir archivo (para mantener el actual)
            // - file: debe ser un archivo
            // - mimes: tipos de archivo permitidos (añade o quita según tus necesidades)
            // - max: tamaño máximo en kilobytes (5120 KB = 5 MB)
        ]);

        // Almacenar el estado original del boletín ANTES de cualquier cambio
        $originalEstado = $boletin->estado;

        // *** Lógica para manejar la subida del archivo ***
        if ($request->hasFile('archivo_upload')) {
            // 1. Eliminar el archivo antiguo si existe
            if ($boletin->archivo && Storage::disk('public')->exists($boletin->archivo)) {
                Storage::disk('public')->delete($boletin->archivo);
            }

            // 2. Guardar el nuevo archivo
            // El método store() guarda el archivo y devuelve la ruta relativa (ej: "boletines/nombre_archivo.pdf")
            // 'boletines' será la subcarpeta dentro de storage/app/public
            $path = $request->file('archivo_upload')->store('boletines', 'public');
            $boletin->archivo = $path; // Guardar esta ruta en la base de datos
        }
        // Si no se sube un nuevo archivo, $boletin->archivo conserva su valor actual
        // ($request->archivo ya no es relevante si usas 'archivo_upload')

        // Actualizar los campos del boletín
        $boletin->contenido = $request->contenido;
        // La línea $boletin->archivo = $request->archivo; DEBE SER ELIMINADA o IGNORADA si usas 'archivo_upload'
        // porque el campo 'archivo' en tu base de datos ahora se llenará desde la lógica de subida de archivos, no desde el input de texto.

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

    public function exportarCSV(Request $request)
    {
        $query = $request->input('q');
        $estado = $request->input('estado');

        $boletines = Boletin::with('user'); // Eager load del usuario asociado

        if ($query) {
            $boletines->where(function ($q2) use ($query) {
                $q2->whereRaw('LOWER(contenido) LIKE ?', ['%'.strtolower($query).'%'])
                    ->orWhereRaw('LOWER(observaciones) LIKE ?', ['%'.strtolower($query).'%']);
            });
        }

        if ($estado) {
            $boletines->where('estado', $estado);
        }

        /* $boletines = $boletines->get(); */

        $nombreArchivo = 'boletines_'.now()->format('Y-m-d_H-i-s').'.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$nombreArchivo\"",
        ];

        $columnas = ['ID', 'Usuario', 'Estado', 'Contenido', 'Observaciones', 'Archivo', 'Creado'];

        $callback = function () use ($boletines, $columnas) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columnas);

            foreach ($boletines as $boletin) {
                fputcsv($file, [
                    $boletin->id,
                    optional($boletin->user)->name ?? 'Sin usuario',
                    $boletin->estado,
                    $boletin->contenido,
                    $boletin->observaciones,
                    $boletin->archivo,
                    $boletin->created_at->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($file);
        };


    }
}

































