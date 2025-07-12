<?php

namespace App\Http\Controllers;

use Carbon\Carbon; // Necesario para formatear fechas
use App\Models\User;
use App\Models\Cafe;
use App\Models\Mora;
use App\Models\Video;
use League\Csv\Writer; // Asegúrate de tener esta librería instalada (composer require league/csv)
use SplTempFileObject; // Necesario para League/Csv
use App\Models\Producto;
use Illuminate\Http\Request;
use App\Services\ProductService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;
use Illuminate\Database\QueryException;
use App\Mail\NuevaRevisionPendienteMail;

class ProductoController extends Controller
{
    public function index(Request $request, ProductService $productService)
    {
        Gate::authorize('crear producto');
        $productos = $productService->obtenerProductosFiltrados($request);
        return view('productos.index', compact('productos'));
    }

    // Si tambien necesitas una respuesta JSON (ej. para una API o Vue/React):
    public function getFilteredProducts(Request $request, ProductService $productService)
    {
        $productos = $productService->obtenerProductosFiltrados($request);
        return response()->json($productos);
    }

    public function create()
    {
        return view('productos.create');
    }

    /**
     * Guarda un nuevo producto (cafe o mora o video) en la base de datos.
     * Incluye validacion de datos y manejo de carga de imagenes.
     */
    public function store(Request $request)
    {
        // 1. Definir las reglas de validacion base para el producto.
        $rules = [
            'tipo' => 'required|string|in:cafe,mora,videos', // El tipo principal de producto
            'observaciones' => 'nullable|string',
        ];

        // 2. Anadir reglas de validacion condicionalmente segun el tipo de producto.
        $tipoProductoPrincipal = $request->input('tipo');

        if ($tipoProductoPrincipal === 'cafe') {
            $rules['imagen'] = 'required|image|mimes:jpeg,png,jpg|max:2048';
            $rules['cafe_data.numero_pagina'] = 'required|integer';
            $rules['cafe_data.clase'] = 'required|string|max:100';
            $rules['cafe_data.informacion'] = 'required|string';
            $rules['rutavideo'] = 'required|url|max:255'; // Se aplica solo si el tipo es cafe

        } elseif ($tipoProductoPrincipal === 'mora') {
            $rules['imagen'] = 'required|image|mimes:jpeg,png,jpg|max:2048';
            $rules['mora_data.numero_pagina'] = 'required|integer';
            $rules['mora_data.clase'] = 'required|string|max:100';
            $rules['mora_data.informacion'] = 'required|string';
            $rules['rutavideo'] = 'required|url|max:255'; // Se aplica solo si el tipo es mora

        } elseif ($tipoProductoPrincipal === 'videos') {
            $rules['videos_data.tipo'] = 'required|string|in:educativos,recomendados,insumos_y_abonos,cuidados_generales,preparacion_terreno_siembra,sugerencias_generales,metodos_recoleccion,cuidados_cosecha,buenas_practicas_agricolas';
            $subtipoSeleccionado = $request->input('videos_data.tipo');

            if ($subtipoSeleccionado) {
                $rules["videos_data.{$subtipoSeleccionado}.autor"] = 'required|string|max:255';
                $rules["videos_data.{$subtipoSeleccionado}.titulo"] = 'required|string|max:255';
                $rules["videos_data.{$subtipoSeleccionado}.descripcion"] = 'required|string';
                $rules["videos_data.{$subtipoSeleccionado}.rutaVideo"] = 'required|url|max:255';
            }
        }

        $messages = [
            // Mensajes para 'cafe'
            'imagen.required' => 'La imagen es obligatoria.',
            'imagen.image' => 'El archivo debe ser una imagen.',
            'imagen.mimes' => 'La imagen debe ser de tipo JPEG, PNG o JPG.',
            'imagen.max' => 'La imagen no debe exceder los 2MB.',
            'cafe_data.numero_pagina.required' => 'El numero de pagina es obligatorio.',
            'cafe_data.numero_pagina.integer' => 'El numero de pagina debe ser un numero entero.',
            'cafe_data.clase.required' => 'La clase es obligatoria.',
            'cafe_data.clase.string' => 'La clase debe ser texto.',
            'cafe_data.clase.max' => 'La clase no debe exceder los 100 caracteres.',
            'cafe_data.informacion.required' => 'La informacion es obligatoria.',
            'cafe_data.informacion.string' => 'La informacion debe ser texto.',
            'rutavideo.required' => 'La URL del video es obligatoria.',
            'rutavideo.url' => 'La URL del video debe ser una URL valida.',
            'rutavideo.max' => 'La URL del video no debe exceder los 255 caracteres.',

            // Mensajes para 'mora'
            'mora_data.numero_pagina.required' => 'El numero de pagina es obligatorio.',
            'mora_data.numero_pagina.integer' => 'El numero de pagina debe ser un numero entero.',
            'mora_data.clase.required' => 'La clase es obligatoria.',
            'mora_data.clase.string' => 'La clase debe ser texto.',
            'mora_data.clase.max' => 'La clase no debe exceder los 100 caracteres.',
            'mora_data.informacion.required' => 'La informacion es obligatoria.',
            'mora_data.informacion.string' => 'La informacion debe ser texto.',

            // Mensajes para 'videos'
            'videos_data.tipo.required' => 'El tipo de video (primarios, recomendados o categorias) es obligatorio.',
            'videos_data.tipo.string' => 'El tipo de video debe ser texto.',
            'videos_data.tipo.in' => 'El tipo de video seleccionado no es valido. Debe ser "primarios", "recomendados", "Insumos y Abonos", "Cuidados Generales", "Preparacion del terreno y siembra", "Sugerencias generales", "Metodos de recoleccion", "Cuidados de la cosecha", "Buenas Practicas Agricolas".',

            // Mensajes dinamicos para 'videos' segun el subtipo seleccionado
            'videos_data.*.autor.required' => 'El autor es obligatorio.',
            'videos_data.*.autor.string' => 'El autor debe ser texto.',
            'videos_data.*.autor.max' => 'El autor no debe exceder los 255 caracteres.',
            'videos_data.*.titulo.required' => 'El titulo es obligatorio.',
            'videos_data.*.titulo.string' => 'El titulo debe ser texto.',
            'videos_data.*.titulo.max' => 'El titulo no debe exceder los 255 caracteres.',
            'videos_data.*.descripcion.required' => 'La descripcion es obligatoria.',
            'videos_data.*.descripcion.string' => 'La descripcion debe ser texto.',
            'videos_data.*.rutaVideo.required' => 'La URL del video es obligatoria.',
            'videos_data.*.rutaVideo.url' => 'La URL del video debe ser una URL valida.',
            'videos_data.*.rutaVideo.max' => 'La URL del video no debe exceder los 255 caracteres.',
        ];

        // 3. Aplicar las reglas de validacion.
        $request->validate($rules, $messages);

        // Inicializar variables para posible limpieza en caso de error
        $imagen = null;
        $producto = null; // Se inicializa para asegurar que exista en el catch

        try {
            // 4. Logica para guardar la imagen (si se ha subido).
            if ($request->hasFile('imagen')) {
                $imagen = $request->file('imagen')->store('productos', 'public');
            }

            // Determinar la RutaVideo para la tabla 'productos'
            $productorutavideo = null;
            if ($tipoProductoPrincipal === 'cafe' || $tipoProductoPrincipal === 'mora') {
                $productorutavideo = $request->rutavideo;
            }

            // 5. Crear el registro principal en la tabla 'productos'.
            $producto = Producto::create([
                'user_id' => Auth::id(),
                'estado' => 'pendiente',
                'observaciones' => $request->observaciones,
                'imagen' => $imagen,
                'tipo' => $tipoProductoPrincipal,
                'rutavideo' => $productorutavideo,
            ]);

            // 6. Guardar los datos especificos del producto en sus tablas correspondientes.
            if ($tipoProductoPrincipal === 'cafe') {
                $cafeData = $request->input('cafe_data', []);
                Cafe::create([
                    'producto_id' => $producto->id,
                    'numero_pagina' => $cafeData['numero_pagina'],
                    'clase' => $cafeData['clase'] ?? null,
                    'informacion' => $cafeData['informacion'],
                ]);
            } elseif ($tipoProductoPrincipal === 'mora') {
                $moraData = $request->input('mora_data', []);
                Mora::create([
                    'producto_id' => $producto->id,
                    'numero_pagina' => $moraData['numero_pagina'],
                    'clase' => $moraData['clase'] ?? null,
                    'informacion' => $moraData['informacion'],
                ]);
            } elseif ($tipoProductoPrincipal === 'videos') {
                $subtipoSeleccionado = $request->input('videos_data.tipo');
                $videoData = $request->input("videos_data.{$subtipoSeleccionado}", []);

                Video::create([
                    'producto_id' => $producto->id,
                    'user_id' => Auth::id(),
                    'autor' => $videoData['autor'],
                    'titulo' => $videoData['titulo'],
                    'descripcion' => $videoData['descripcion'] ?? null,
                    'rutaVideo' => $videoData['rutaVideo'],
                    'tipo' => $subtipoSeleccionado,
                ]);
            }

            // 7. Logica para enviar email a los operarios.
            $operarios = User::role('Operario')->get();
            foreach ($operarios as $operario) {
                Mail::to($operario->email)->send(new NuevaRevisionPendienteMail($producto, $tipoProductoPrincipal));
            }

            // 8. Redirigir con un mensaje de exito.
            return redirect()->route('productos.index')->with('success_message', 'Informacion guardada con exito y enviada a revision.');
        } catch (QueryException $e) {
            // Captura errores especificos de la base de datos
            Log::error('Error de base de datos al crear producto: ' . $e->getMessage());
            // Si el producto principal se creo pero la relacion fallo, eliminarlo
            if ($producto && $producto->exists) {
                $producto->delete();
            }
            // Si la imagen se subio antes de la falla de la DB, intentar eliminarla
            if ($imagen && Storage::disk('public')->exists($imagen)) {
                Storage::disk('public')->delete($imagen);
            }
            return redirect()->back()->with('error_message', 'Ocurrio un error de base de datos al crear el producto. Por favor, intentalo de nuevo.');
        } catch (\Exception $e) {
            // Captura cualquier otra excepcion inesperada
            Log::error('Error inesperado al crear producto: ' . $e->getMessage());
            // Si el producto principal se creo pero la relacion fallo, eliminarlo
            if ($producto && $producto->exists) {
                $producto->delete();
            }
            // Si la imagen se subio antes de la falla, intentar eliminarla
            if ($imagen && Storage::disk('public')->exists($imagen)) {
                Storage::disk('public')->delete($imagen);
            }
            return redirect()->back()->with('error_message', 'Ocurrio un error inesperado al crear el producto. Por favor, intentalo de nuevo.');
        }
    }

    /**
     * Muestra el formulario para editar un producto existente.
     */
    public function edit(Producto $producto)
    {
        Gate::authorize('editar producto');
        $producto->load([
            'cafe',
            'mora',
            'videos',
        ]);

        return view('productos.edit', compact('producto'));
    }

    /**
     * Actualiza el producto especificado en el almacenamiento.
     */
    public function update(Request $request, Producto $producto)
    {
        // Autorizar la accion de edicion (usando Laravel Gates)
        Gate::authorize('editar producto');

        // Inicializar variables para posible limpieza en caso de error
        $newImagenPath = null;
        $oldImagenPath = $producto->imagen; // Guarda la ruta de la imagen original

        try {
            // 1. Definir las reglas de validacion base para la actualizacion del producto.
            $rules = [
                'tipo' => 'required|string|in:cafe,mora,videos',
                'imagen' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
                'observaciones' => 'nullable|string',
            ];

            // 2. Anadir reglas de validacion condicionalmente segun el tipo del producto que se esta actualizando.
            $requestType = $request->input('tipo');

            if ($requestType === 'cafe') {
                $rules['cafe_data.numero_pagina'] = 'required|integer';
                $rules['cafe_data.clase'] = 'nullable|string|max:100';
                $rules['cafe_data.informacion'] = 'required|string';
                $rules['rutavideo'] = 'nullable|url|max:255';
            } elseif ($requestType === 'mora') {
                $rules['mora_data.numero_pagina'] = 'required|integer';
                $rules['mora_data.clase'] = 'nullable|string|max:100';
                $rules['mora_data.informacion'] = 'required|string';
                $rules['rutavideo'] = 'nullable|url|max:255';
            } elseif ($requestType === 'videos') {
                // Asegurate de que los valores aqui coincidan con los 'value' de tus <option> en el HTML
                $rules['videos_data.tipo'] = 'required|string|in:educativos,recomendados,insumos_y_abonos,cuidados_generales,preparacion_terreno_siembra,sugerencias_generales,metodos_recoleccion,cuidados_cosecha,buenas_practicas_agricolas';
                $subtipoSeleccionado = $request->input('videos_data.tipo');

                if ($subtipoSeleccionado) {
                    // Los nombres de los campos deben coincidir con como se enviaran desde el formulario
                    $rules["videos_data.{$subtipoSeleccionado}.autor"] = 'required|string|max:255';
                    $rules["videos_data.{$subtipoSeleccionado}.titulo"] = 'required|string|max:255';
                    $rules["videos_data.{$subtipoSeleccionado}.descripcion"] = 'nullable|string';
                    $rules["videos_data.{$subtipoSeleccionado}.rutaVideo"] = 'required|url|max:255';
                }
            }

            // 3. Aplicar las reglas de validacion.
            $request->validate($rules);

            // 4. Almacenar el estado original del producto ANTES de cualquier cambio.
            $originalEstado = $producto->estado;
            $originalTipoProducto = $producto->tipo;

            // 5. Actualizar la imagen si viene una nueva.
            if ($request->hasFile('imagen')) {
                $newImagenPath = $request->file('imagen')->store('productos', 'public');
                $producto->imagen = $newImagenPath; // Asigna la nueva ruta
            }

            // 6. Actualizar los demas campos del producto principal.
            $producto->observaciones = $request->observaciones;
            $producto->tipo = $requestType;

            if ($requestType === 'cafe' || $requestType === 'mora') {
                $producto->rutavideo = $request->rutavideo;
            } else {
                $producto->rutavideo = null;
            }

            // 7. Logica para cambiar el estado a 'pendiente' si el producto fue editado
            $estadoCambiadoAPendiente = false;
            if ($originalEstado === 'aprobado' || $originalEstado === 'rechazado') {
                $producto->estado = 'pendiente';
                $producto->observaciones = null;
                $estadoCambiadoAPendiente = true;
            }

            // Guarda los cambios en el producto principal
            $producto->save();

            // Si la imagen nueva se guardo y la operacion de DB fue exitosa, eliminar la antigua
            if ($request->hasFile('imagen') && $oldImagenPath && Storage::disk('public')->exists($oldImagenPath)) {
                Storage::disk('public')->delete($oldImagenPath);
            }

            // 8. Actualizar registros en las tablas de detalle segun el tipo del producto.
            // Eliminar relaciones antiguas si el tipo de producto ha cambiado
            if ($requestType !== $originalTipoProducto) {
                if ($originalTipoProducto === 'cafe' && $producto->cafe) {
                    $producto->cafe->delete();
                } elseif ($originalTipoProducto === 'mora' && $producto->mora) {
                    $producto->mora->delete();
                } elseif ($originalTipoProducto === 'videos' && $producto->videos) {
                    $producto->videos->delete();
                }
            }

            // Crear/Actualizar el registro de detalle segun el tipo actual del producto
            if ($requestType === 'cafe') {
                $cafe = Cafe::firstOrNew(['producto_id' => $producto->id]);
                $cafeData = $request->input('cafe_data', []);
                $cafe->numero_pagina = $cafeData['numero_pagina'];
                $cafe->clase = $cafeData['clase'] ?? null;
                $cafe->informacion = $cafeData['informacion'];
                $cafe->save();
            } elseif ($requestType === 'mora') {
                $mora = Mora::firstOrNew(['producto_id' => $producto->id]);
                $moraData = $request->input('mora_data', []);
                $mora->numero_pagina = $moraData['numero_pagina'];
                $mora->clase = $moraData['clase'] ?? null;
                $mora->informacion = $moraData['informacion'];
                $mora->save();
            } elseif ($requestType === 'videos') {
                $video = Video::firstOrNew(['producto_id' => $producto->id]);
                $subtipoSeleccionado = $request->input('videos_data.tipo');
                $videoData = $request->input("videos_data.{$subtipoSeleccionado}", []);
                $video->user_id = Auth::id();
                $video->autor = $videoData['autor'];
                $video->titulo = $videoData['titulo'];
                $video->descripcion = $videoData['descripcion'] ?? null;
                $video->rutaVideo = $videoData['rutaVideo'];
                $video->tipo = $subtipoSeleccionado;
                $video->save();
            }

            // 9. Logica para enviar email al operario si el estado cambio a pendiente.
            if ($estadoCambiadoAPendiente || $request->hasAny(['observaciones', 'imagen', 'cafe_data', 'mora_data', 'videos_data'])) {
                $operarios = User::role('Operario')->get();
                $itemTipo = $producto->tipo;

                foreach ($operarios as $operario) {
                    Mail::to($operario->email)->send(new NuevaRevisionPendienteMail($producto, $itemTipo));
                }
            }

            // 10. Redirigir con un mensaje de exito.
            return redirect()->route('productos.index')->with('success_message', 'Producto actualizado y enviado a revision.');
        } catch (QueryException $e) {
            Log::error('Error de base de datos al actualizar producto (ID: ' . $producto->id . '): ' . $e->getMessage());
            // Si se subio una nueva imagen y la DB fallo, intentar eliminarla
            if ($newImagenPath && Storage::disk('public')->exists($newImagenPath)) {
                Storage::disk('public')->delete($newImagenPath);
            }
            return redirect()->back()->with('error_message', 'Ocurrio un error de base de datos al actualizar el producto. Por favor, intentalo de nuevo.');
        } catch (\Exception $e) {
            Log::error('Error inesperado al actualizar producto (ID: ' . $producto->id . '): ' . $e->getMessage());
            // Si se subio una nueva imagen y la operacion fallo, intentar eliminarla
            if ($newImagenPath && Storage::disk('public')->exists($newImagenPath)) {
                Storage::disk('public')->delete($newImagenPath);
            }
            return redirect()->back()->with('error_message', 'Ocurrio un error inesperado al actualizar el producto. Por favor, intentalo de nuevo.');
        }
    }

    /**
     * Muestra los detalles de un producto especifico.
     */
    public function show(Producto $producto)
    {
        // Cargar las relaciones necesarias para mostrar los detalles.
        $producto->load([
            'user', // Para mostrar quien lo creo
            'cafe', // Carga el modelo Cafe relacionado
            'mora', // Carga el modelo Mora relacionado
            'videos', // Carga el modelo Video relacionado
            'validador', // Mantengo si estos modelos/relaciones existen en tu app
            'rechazador', // Mantengo si estos modelos/relaciones existen en tu app
        ]);

        return view('productos.show', compact('producto'));
    }

    public function destroy(Producto $producto)
    {
        Gate::authorize('eliminar producto');
        // Tu logica de borrado (manten la misma)
        $producto->delete();

        return redirect()->route('productos.index')->with('success', 'Producto eliminado.');
    }

    /**
     * Importa productos desde un archivo CSV.
     * Incluye validacion de datos, manejo de errores por fila y creacion de detalles especificos.
     */
    public function importarCSV(Request $request)
    {
        // 1. Validar la subida del archivo CSV
        $request->validate([
            'archivo_csv' => 'required|file|mimes:csv,txt|max:2048', // Max 2MB
        ]);

        $archivo = $request->file('archivo_csv');
        $ruta = $archivo->getRealPath();

        $file = fopen($ruta, 'r');
        if (!$file) {
            return redirect()->back()->withErrors(['csv_error' => "No se pudo abrir el archivo CSV."]);
        }

        // Leer la primera fila como encabezados
        $encabezados = fgetcsv($file);
        if ($encabezados === false) {
            fclose($file);
            return redirect()->back()->withErrors(['csv_error' => "El archivo CSV esta vacio o no se pudieron leer los encabezados."]);
        }

        // Normalizar encabezados (quitar espacios, convertir a minusculas, etc.) para una comparacion mas robusta
        $encabezados = array_map('trim', array_map('strtolower', $encabezados));

        // 2. Definir los campos que esperamos en el CSV y su mapeo a la base de datos
        // NOTA: Si tienes campos especificos para los subtipos de video (ej. primarios_campo1),
        // deberias anadirlos aqui y manejarlos en la logica de creacion de Video.
        $expectedCsvHeaders = [
            'tipo',
            'observaciones',
            'producto_rutavideo', // Nuevo encabezado para RutaVideo de la tabla productos
            'cafe_numero_pagina',
            'cafe_clase',
            'cafe_informacion',
            'mora_numero_pagina',
            'mora_clase',
            'mora_informacion',
            'videos_autor',
            'videos_titulo',
            'videos_descripcion',
            'videos_rutavideo', // 'rutaVideo' para la tabla 'videos'
            'videos_tipo', // Este es el subtipo de video (primarios, recomendados, categorias)
        ];

        // 3. Validar que todos los encabezados requeridos esten presentes en el CSV
        $missingHeaders = array_diff($expectedCsvHeaders, $encabezados);
        if (!empty($missingHeaders)) {
            fclose($file);
            return redirect()->back()->withErrors(['csv_error' => "Faltan los siguientes encabezados requeridos en el archivo CSV: '" . implode("', '", $missingHeaders) . "'."]);
        }

        $productosCreados = 0;
        $erroresPorFila = [];
        $filaNumero = 1; // Contador para el numero de fila en el CSV (despues de los encabezados)

        // Obtener los operarios una sola vez para enviar los correos
        $operarios = User::role('Operario')->get();

        // Itera sobre cada fila del CSV
        while (($filaRaw = fgetcsv($file)) !== false) {
            $filaNumero++; // Incrementa para cada fila de datos

            // Asegurarse de que el numero de columnas coincida
            if (count($filaRaw) !== count($encabezados)) {
                $erroresPorFila[] = "Fila {$filaNumero}: El numero de columnas no coincide con los encabezados. Se esperaban " . count($encabezados) . " columnas, se encontraron " . count($filaRaw) . ".";
                Log::warning("CSV Import - Column mismatch", ['fila' => $filaNumero, 'data' => $filaRaw, 'headers' => $encabezados]);
                continue; // Saltar esta fila y continuar con la siguiente
            }

            // Combinar encabezados (normalizados) con datos de la fila para un array asociativo
            $datosFila = array_combine($encabezados, array_map('trim', $filaRaw));

            // Iniciar una transaccion de base de datos para asegurar la atomicidad
            DB::beginTransaction();
            try {
                // Validar y obtener el tipo de producto
                $tipoProductoPrincipal = $datosFila['tipo'] ?? null;
                if (!in_array($tipoProductoPrincipal, ['cafe', 'mora', 'videos'])) {
                    throw new \Exception("Tipo de producto principal invalido: '{$tipoProductoPrincipal}'. Debe ser 'cafe', 'mora' o 'videos'.");
                }

                // Determinar el RutaVideo para la tabla 'productos'
                $productoRutaVideo = null;
                if ($tipoProductoPrincipal === 'cafe' || $tipoProductoPrincipal === 'mora') {
                    $productoRutaVideo = $datosFila['producto_rutavideo'] ?? null;
                    if ($productoRutaVideo && !filter_var($productoRutaVideo, FILTER_VALIDATE_URL)) {
                        throw new \Exception("URL de video de producto general invalida: '{$productoRutaVideo}'.");
                    }
                }

                // Crear el registro principal en la tabla 'productos'
                $producto = Producto::create([
                    'user_id' => Auth::id(), // Asume que el usuario esta autenticado
                    'estado' => 'pendiente',
                    'observaciones' => $datosFila['observaciones'] ?? null,
                    'imagen' => null, // Las imagenes no se importan desde CSV en esta logica
                    'tipo' => $tipoProductoPrincipal,
                    'RutaVideo' => $productoRutaVideo, // Se guarda solo si es cafe o mora
                ]);

                // Crear los datos especificos del producto (cafe, mora o videos)
                if ($tipoProductoPrincipal === 'cafe') {
                    $cafeData = [
                        'numero_pagina' => $datosFila['cafe_numero_pagina'] ?? null,
                        'clase' => $datosFila['cafe_clase'] ?? null,
                        'informacion' => $datosFila['cafe_informacion'] ?? null,
                    ];

                    // Validacion especifica para cafe
                    if (empty($cafeData['numero_pagina']) || empty($cafeData['informacion'])) {
                        throw new \Exception("Datos incompletos para cafe: 'cafe_numero_pagina' e 'cafe_informacion' son requeridos.");
                    }
                    if (!is_numeric($cafeData['numero_pagina'])) {
                        throw new \Exception("Numero de pagina de cafe invalido: '{$cafeData['numero_pagina']}'. Debe ser un numero.");
                    }

                    Cafe::create(array_merge(['producto_id' => $producto->id], $cafeData));
                } elseif ($tipoProductoPrincipal === 'mora') {
                    $moraData = [
                        'numero_pagina' => $datosFila['mora_numero_pagina'] ?? null,
                        'clase' => $datosFila['mora_clase'] ?? null,
                        'informacion' => $datosFila['mora_informacion'] ?? null,
                    ];

                    // Validacion especifica para mora
                    if (empty($moraData['numero_pagina']) || empty($moraData['informacion'])) {
                        throw new \Exception("Datos incompletos para mora: 'mora_numero_pagina' e 'mora_informacion' son requeridos.");
                    }
                    if (!is_numeric($moraData['numero_pagina'])) {
                        throw new \Exception("Numero de pagina de mora invalido: '{$moraData['numero_pagina']}'. Debe ser un numero.");
                    }

                    Mora::create(array_merge(['producto_id' => $producto->id], $moraData));
                } elseif ($tipoProductoPrincipal === 'videos') {
                    $videoData = [
                        'autor' => $datosFila['videos_autor'] ?? null,
                        'titulo' => $datosFila['videos_titulo'] ?? null,
                        'descripcion' => $datosFila['videos_descripcion'] ?? null,
                        'rutaVideo' => $datosFila['videos_rutavideo'] ?? null, // 'rutaVideo' para la tabla 'videos'
                        'tipo' => $datosFila['videos_tipo'] ?? null, // Este es el subtipo de video
                    ];

                    // Validacion especifica para videos
                    if (empty($videoData['autor']) || empty($videoData['titulo']) || empty($videoData['rutaVideo']) || empty($videoData['tipo'])) {
                        throw new \Exception("Datos incompletos para videos: 'videos_autor', 'videos_titulo', 'videos_rutavideo' y 'videos_tipo' son requeridos.");
                    }
                    if (!filter_var($videoData['rutaVideo'], FILTER_VALIDATE_URL)) {
                        throw new \Exception("URL de video especifica invalida: '{$videoData['rutaVideo']}'.");
                    }
                    // Validar que el subtipo sea uno de los esperados
                    if (!in_array($videoData['tipo'], ['primarios', 'recomendados', 'categorias'])) {
                        throw new \Exception("Subtipo de video invalido: '{$videoData['tipo']}'. Debe ser 'primarios', 'recomendados' o 'categorias'.");
                    }

                    Video::create(array_merge(['producto_id' => $producto->id, 'user_id' => Auth::id()], $videoData));
                }

                // Si todo fue bien, confirmar la transaccion
                DB::commit();
                $productosCreados++;

                // Logica para enviar email a los operarios (solo si el producto y sus detalles se crearon con exito)
                foreach ($operarios as $operario) {
                    Mail::to($operario->email)->send(new NuevaRevisionPendienteMail($producto, $tipoProductoPrincipal));
                }
            } catch (\Exception $e) {
                // Si algo falla, revertir la transaccion
                DB::rollBack();
                $erroresPorFila[] = "Fila {$filaNumero}: " . $e->getMessage();
                Log::error("Error al importar fila CSV: " . $e->getMessage(), ['fila' => $filaNumero, 'datos' => $datosFila]);
            }
        }

        fclose($file);

        // 7. Mensaje de exito o de errores
        if (!empty($erroresPorFila)) {
            $mensaje = "Se importaron **{$productosCreados}** productos con exito. Sin embargo, hubo **errores en algunas filas**:<br>" . implode('<br>', $erroresPorFila);
            // Usar 'html' como clave para permitir HTML en el mensaje de la sesion
            return redirect()->back()->with('warning', $mensaje); // Eliminado withInput para evitar rellenar el campo de archivo
        } else {
            return redirect()->back()->with('success', "Archivo CSV importado con exito. Se crearon **{$productosCreados}** productos.");
        }
    }

    /**
     * Exporta productos a un archivo CSV, aplicando filtros y cargando relaciones de detalles (cafe/mora/videos).
     *
     * @param Request $request
     * @param ProductService $productService // <-- Inyectar el servicio aquí
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function exportarCSV(Request $request, ProductService $productService)
    {
        // Obtener los mismos parámetros de filtro y ordenación que la tabla
        $querySearch = $request->input('q');
        $estadoFilter = $request->input('estado');
        $sortBy = $request->input('sort_by');
        $sortDirection = $request->input('sort_direction');

        // Llama al nuevo método del servicio para obtener los productos sin paginación
        // Este método ya se encarga de aplicar los filtros y el ordenamiento
        $productos = $productService->obtenerProductosFiltradosParaExportar($request);

        // Generar un nombre de archivo único para el CSV
        $nombreArchivo = 'productos_exportados_' . now()->format('Y-m-d_H-i-s') . '.csv';

        // Definir los encabezados HTTP necesarios para la descarga del archivo CSV
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8', // Aseguramos UTF-8
            'Content-Disposition' => "attachment; filename=\"$nombreArchivo\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        // Definir los nombres de las columnas que aparecerán en la primera fila del CSV
        $columnas = [
            'ID Producto',
            'Tipo Producto',
            'Estado',
            'Observaciones del Producto',
            'Ruta Imagen',
            'RutaVideo Producto', // Columna para RutaVideo de la tabla 'productos'
            'ID Usuario Creador',
            'Nombre Usuario Creador',
            'Email Usuario Creador',
            'Fecha de Creacion',
            // Campos específicos para Cafe
            'Cafe - Numero Pagina',
            'Cafe - Clase',
            'Cafe - Informacion',
            // Campos específicos para Mora
            'Mora - Numero Pagina',
            'Mora - Clase',
            'Mora - Informacion',
            // Campos específicos para Videos
            'Video - Autor',
            'Video - Titulo',
            'Video - Descripcion',
            'Video - RutaVideo', // 'rutaVideo' de la tabla 'videos' (es el subtipo)
            'Video - Subtipo', // Este es el subtipo de video
        ];

        // Definir la función de callback que generará el contenido del CSV
        $callback = function () use ($productos, $columnas) {
            $file = fopen('php://output', 'w');
            // Escribir la codificación UTF-8 BOM para asegurar la correcta lectura en Excel
            fputs($file, chr(0xEF) . chr(0xBB) . chr(0xBF)); // Usar fputs para el BOM
            fputcsv($file, $columnas);

            foreach ($productos as $producto) {
                // Datos base del producto
                $row = [
                    $producto->id,
                    $producto->tipo,
                    $producto->estado,
                    $producto->observaciones ?? '',
                    $producto->imagen ? asset('storage/' . $producto->imagen) : '', // URL completa de la imagen
                    $producto->RutaVideo ?? '', // RutaVideo de la tabla 'productos'
                    $producto->user_id,
                    optional($producto->user)->name ?? 'N/A', // Nombre del creador
                    optional($producto->user)->email ?? 'N/A', // Email del creador
                    $producto->created_at ? $producto->created_at->format('Y-m-d H:i:s') : '',
                ];

                // Añadir campos específicos de Cafe
                if ($producto->tipo === 'cafe' && $producto->cafe) {
                    $row[] = $producto->cafe->numero_pagina ?? '';
                    $row[] = $producto->cafe->clase ?? '';
                    // Limpiar información de HTML y saltos de línea
                    $infoCafe = strip_tags($producto->cafe->informacion ?? '');
                    $infoCafe = html_entity_decode($infoCafe, ENT_QUOTES | ENT_HTML5, 'UTF-8');
                    $infoCafe = str_replace(["\r", "\n"], " ", $infoCafe);
                    $row[] = $infoCafe;
                } else {
                    // Si no es cafe, añadir celdas vacías para las columnas de cafe para mantener la consistencia
                    $row = array_merge($row, array_fill(0, 3, '')); // 3 campos vacíos para Cafe
                }

                // Añadir campos específicos de Mora
                if ($producto->tipo === 'mora' && $producto->mora) {
                    $row[] = $producto->mora->numero_pagina ?? '';
                    $row[] = $producto->mora->clase ?? '';
                    // Limpiar información de HTML y saltos de línea
                    $infoMora = strip_tags($producto->mora->informacion ?? '');
                    $infoMora = html_entity_decode($infoMora, ENT_QUOTES | ENT_HTML5, 'UTF-8');
                    $infoMora = str_replace(["\r", "\n"], " ", $infoMora);
                    $row[] = $infoMora;
                } else {
                    // Si no es mora, añadir celdas vacías para las columnas de mora
                    $row = array_merge($row, array_fill(0, 3, '')); // 3 campos vacíos para Mora
                }

                // Añadir campos específicos de Videos
                if ($producto->tipo === 'videos' && $producto->videos) {
                    $row[] = $producto->videos->autor ?? '';
                    $row[] = $producto->videos->titulo ?? '';
                    // Limpiar descripción de HTML y saltos de línea
                    $descVideo = strip_tags($producto->videos->descripcion ?? '');
                    $descVideo = html_entity_decode($descVideo, ENT_QUOTES | ENT_HTML5, 'UTF-8');
                    $descVideo = str_replace(["\r", "\n"], " ", $descVideo);
                    $row[] = $descVideo;
                    $row[] = $producto->videos->rutaVideo ?? ''; // 'rutaVideo' de la tabla 'videos'
                    $row[] = $producto->videos->tipo ?? ''; // Este es el subtipo
                } else {
                    // Si no es videos, añadir celdas vacías para las columnas de videos
                    $row = array_merge($row, array_fill(0, 5, '')); // 5 campos vacíos para Video
                }

                fputcsv($file, $row);
            }

            fclose($file);
        };

        // Retornar la respuesta de streaming
        return Response::stream($callback, 200, $headers);
    }
}
