<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Cafe;
use App\Models\Mora;
use App\Models\Video;
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
use App\Mail\NuevaRevisionPendienteMail;

class ProductoController extends Controller
{
    public function index(Request $request, ProductService $productService)
    {
        Gate::authorize('crear producto');
        $productos = $productService->obtenerProductosFiltrados($request);
        return view('productos.index', compact('productos'));
    }

    // Si también necesitas una respuesta JSON (ej. para una API o Vue/React):
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
     * Guarda un nuevo producto (café o mora o video) en la base de datos.
     * Incluye validación de datos y manejo de carga de imágenes.
     */
    public function store(Request $request)
    {
        // 1. Definir las reglas de validación base para el producto.
        $rules = [
            'tipo' => 'required|string|in:café,mora,videos', // El tipo principal de producto
            'imagen' => 'nullable|image|mimes:jpeg,png,jpg|max:2048', // Validación para la imagen
            'observaciones' => 'nullable|string', // Campo opcional de observaciones
        ];

        // 2. Añadir reglas de validación condicionalmente según el tipo de producto.
        $tipoProductoPrincipal = $request->input('tipo');

        if ($tipoProductoPrincipal === 'café') {
            $rules['cafe_data.numero_pagina'] = 'required|integer';
            $rules['cafe_data.clase'] = 'nullable|string|max:100';
            $rules['cafe_data.informacion'] = 'required|string';
            // RutaVideo para café/mora:
            $rules['RutaVideo'] = 'nullable|url|max:255'; // Se aplica solo si el tipo es café

        } elseif ($tipoProductoPrincipal === 'mora') {
            $rules['mora_data.numero_pagina'] = 'required|integer';
            $rules['mora_data.clase'] = 'nullable|string|max:100';
            $rules['mora_data.informacion'] = 'required|string';
            // RutaVideo para café/mora:
            $rules['RutaVideo'] = 'nullable|url|max:255'; // Se aplica solo si el tipo es mora

        } elseif ($tipoProductoPrincipal === 'videos') {
            // Regla para el subtipo de video (primarios, secundarios, categorias)
            $rules['videos_data.tipo'] = 'required|string|in:primarios,secundarios,categorias';

            // Agrega reglas de validación para los campos generales de video,
            // pero ahora ANIDADOS bajo el subtipo seleccionado.
            // Usamos $request->input('videos_data.tipo') para determinar el prefijo.
            $subtipoSeleccionado = $request->input('videos_data.tipo');

            // Validar los campos comunes de video que ahora están anidados por subtipo
            if ($subtipoSeleccionado) { // Solo si se ha seleccionado un subtipo
                $rules["videos_data.{$subtipoSeleccionado}.autor"] = 'required|string|max:255';
                $rules["videos_data.{$subtipoSeleccionado}.titulo"] = 'required|string|max:255';
                $rules["videos_data.{$subtipoSeleccionado}.descripcion"] = 'nullable|string';
                $rules["videos_data.{$subtipoSeleccionado}.rutaVideo"] = 'required|url|max:255';

                // Si tienes campos adicionales específicos para cada subtipo (ej. campo1),
                // asegúrate de incluirlos aquí con su respectiva anidación.
                // Por ejemplo:
                // $rules["videos_data.{$subtipoSeleccionado}.campo1"] = 'required|string';
            }
        }

        // 3. Aplicar las reglas de validación.
        $request->validate($rules);

        // 4. Lógica para guardar la imagen (si se ha subido).
        $imagen = null;
        if ($request->hasFile('imagen')) {
            $imagen = $request->file('imagen')->store('productos', 'public');
        }

        // Determinar la RutaVideo para la tabla 'productos'
        $productoRutaVideo = null;
        if ($tipoProductoPrincipal === 'café' || $tipoProductoPrincipal === 'mora') {
            $productoRutaVideo = $request->RutaVideo;
        }

        // 5. Crear el registro principal en la tabla 'productos'.
        $producto = Producto::create([
            'user_id' => Auth::id(),
            'estado' => 'pendiente',
            'observaciones' => $request->observaciones,
            'imagen' => $imagen,
            'tipo' => $tipoProductoPrincipal, // Este 'tipo' es 'café', 'mora' o 'videos'
            'RutaVideo' => $productoRutaVideo, // Se guarda solo si es café o mora
        ]);

        // 6. Guardar los datos específicos del producto en sus tablas correspondientes.
        if ($tipoProductoPrincipal === 'café') {
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
            // Acceder a los datos de video a través del subtipo seleccionado
            $videoData = $request->input("videos_data.{$subtipoSeleccionado}", []);

            // Si tienes campos adicionales específicos para cada subtipo,
            // asegúrate de recuperarlos aquí.
            // Ejemplo: $campo1Especifico = $videoData['campo1'] ?? null;

            Video::create([
                'producto_id' => $producto->id,
                'user_id' => Auth::id(),
                'autor' => $videoData['autor'],
                'titulo' => $videoData['titulo'],
                'descripcion' => $videoData['descripcion'] ?? null,
                'rutaVideo' => $videoData['rutaVideo'],
                'tipo' => $subtipoSeleccionado, // Este campo 'tipo' de Video guardará el subtipo (primarios, secundarios, categorias)
                // Si tienes campos específicos por subtipo y los guardas en una columna JSON/TEXT:
                // 'datos_extras' => json_encode(['campo1' => $campo1Especifico]),
            ]);
        }

        // 7. Lógica para enviar email a los operarios.
        $operarios = User::role('Operario')->get();
        foreach ($operarios as $operario) {
            Mail::to($operario->email)->send(new NuevaRevisionPendienteMail($producto, $tipoProductoPrincipal));
        }

        // 8. Redirigir con un mensaje de éxito.
        return redirect()->route('productos.index')->with('success', 'Información guardada con éxito y enviada a revisión.');
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
        // Autorizar la acción de edición (usando Laravel Gates)
        Gate::authorize('editar producto');

        // 1. Definir las reglas de validación base para la actualización del producto.
        $rules = [
            // El tipo de producto principal debe incluir 'videos'
            'tipo' => 'required|string|in:café,mora,videos',
            'imagen' => 'nullable|image|mimes:jpeg,png,jpg|max:2048', // Validación para la imagen
            'observaciones' => 'nullable|string', // Campo opcional de observaciones
        ];

        // 2. Añadir reglas de validación condicionalmente según el tipo del producto que se está actualizando.
        // Usamos $request->input('tipo') para la validación, ya que el usuario podría cambiar el tipo
        // en el formulario de edición (aunque el input sea readonly en la vista, la lógica del controlador
        // debe ser robusta por si se manipula la petición o se permite el cambio de tipo en el futuro).
        $requestType = $request->input('tipo');

        if ($requestType === 'café') {
            $rules['cafe_data.numero_pagina'] = 'required|integer';
            $rules['cafe_data.clase'] = 'nullable|string|max:100';
            $rules['cafe_data.informacion'] = 'required|string';
            // RutaVideo para café/mora:
            $rules['RutaVideo'] = 'nullable|url|max:255'; // Se aplica solo si el tipo es café

        } elseif ($requestType === 'mora') {
            $rules['mora_data.numero_pagina'] = 'required|integer';
            $rules['mora_data.clase'] = 'nullable|string|max:100';
            $rules['mora_data.informacion'] = 'required|string';
            // RutaVideo para café/mora:
            $rules['RutaVideo'] = 'nullable|url|max:255'; // Se aplica solo si el tipo es mora

        } elseif ($requestType === 'videos') {
            // Regla para el subtipo de video (primarios, secundarios, categorias)
            $rules['videos_data.tipo'] = 'required|string|in:primarios,secundarios,categorias';

            // Agrega reglas de validación para los campos generales de video,
            // pero ahora ANIDADOS bajo el subtipo seleccionado.
            // Usamos $request->input('videos_data.tipo') para determinar el prefijo.
            $subtipoSeleccionado = $request->input('videos_data.tipo');

            // Validar los campos comunes de video que ahora están anidados por subtipo
            if ($subtipoSeleccionado) { // Solo si se ha seleccionado un subtipo
                $rules["videos_data.{$subtipoSeleccionado}.autor"] = 'required|string|max:255';
                $rules["videos_data.{$subtipoSeleccionado}.titulo"] = 'required|string|max:255';
                $rules["videos_data.{$subtipoSeleccionado}.descripcion"] = 'nullable|string';
                $rules["videos_data.{$subtipoSeleccionado}.rutaVideo"] = 'required|url|max:255';

                // Si tienes campos adicionales específicos para cada subtipo (ej. campo1),
                // asegúrate de incluirlos aquí con su respectiva anidación.
                // Por ejemplo:
                // $rules["videos_data.{$subtipoSeleccionado}.campo1"] = 'nullable|string'; // O 'required' si es el caso
            }
        }

        // 3. Aplicar las reglas de validación.
        $request->validate($rules);

        // 4. Almacenar el estado original del producto ANTES de cualquier cambio.
        $originalEstado = $producto->estado;
        // Guardar el tipo original del producto para la lógica de eliminación de relaciones
        $originalTipoProducto = $producto->tipo;

        // 5. Actualizar la imagen si viene una nueva.
        if ($request->hasFile('imagen')) {
            // Eliminar imagen anterior si existe.
            if ($producto->imagen && Storage::disk('public')->exists($producto->imagen)) {
                Storage::disk('public')->delete($producto->imagen);
            }
            $imagen = $request->file('imagen')->store('productos', 'public');
            $producto->imagen = $imagen;
        }

        // 6. Actualizar los demás campos del producto principal.
        $producto->observaciones = $request->observaciones;
        $producto->tipo = $requestType; // Asegura que el tipo del producto se actualice si cambia en el formulario

        // Actualizar RutaVideo solo si el tipo es café o mora
        if ($requestType === 'café' || $requestType === 'mora') {
            $producto->RutaVideo = $request->RutaVideo;
        } else {
            // Si el tipo es 'videos' o cualquier otro, asegúrate de que RutaVideo en Producto sea null
            // Esto es importante si el tipo de producto cambia de café/mora a videos
            $producto->RutaVideo = null;
        }


        // 7. Lógica para cambiar el estado a 'pendiente' si el producto fue editado
        // y su estado anterior era 'aprobado' o 'rechazado'.
        $estadoCambiadoAPendiente = false;
        if ($originalEstado === 'aprobado' || $originalEstado === 'rechazado') {
            $producto->estado = 'pendiente';
            $producto->observaciones_operador = null; // Asumiendo que tienes un campo para esto
            $estadoCambiadoAPendiente = true;
        }

        $producto->save(); // Guarda los cambios en el producto principal

        // 8. Actualizar registros en las tablas de detalle según el tipo del producto.
        // Es crucial manejar la posibilidad de que el tipo de producto haya cambiado.
        // Si el tipo de producto cambia, debemos eliminar el registro antiguo y crear uno nuevo.

        // Eliminar relaciones antiguas si el tipo de producto ha cambiado
        if ($requestType !== $originalTipoProducto) { // Comparar el tipo actual del request con el tipo ORIGINAL del producto
            if ($originalTipoProducto === 'café' && $producto->cafe) {
                $producto->cafe->delete();
            } elseif ($originalTipoProducto === 'mora' && $producto->mora) {
                $producto->mora->delete();
            } elseif ($originalTipoProducto === 'videos' && $producto->videos) {
                $producto->videos->delete();
            }
        }

        // Crear/Actualizar el registro de detalle según el tipo actual del producto
        if ($requestType === 'café') {
            // Obtener o crear el registro de Cafe asociado al producto
            $cafe = Cafe::firstOrNew(['producto_id' => $producto->id]);
            $cafeData = $request->input('cafe_data', []);

            // Actualizar los campos del modelo Cafe
            $cafe->numero_pagina = $cafeData['numero_pagina']; // Ya validado como required
            $cafe->clase = $cafeData['clase'] ?? null;
            $cafe->informacion = $cafeData['informacion']; // Ya validado como required
            $cafe->save(); // Guarda los cambios en el registro de Cafe

        } elseif ($requestType === 'mora') {
            // Obtener o crear el registro de Mora asociado al producto
            $mora = Mora::firstOrNew(['producto_id' => $producto->id]);
            $moraData = $request->input('mora_data', []);

            // Actualizar los campos del modelo Mora
            $mora->numero_pagina = $moraData['numero_pagina'];
            $mora->clase = $moraData['clase'] ?? null;
            $mora->informacion = $moraData['informacion'];
            $mora->save(); // Guarda los cambios en el registro de Mora

        } elseif ($requestType === 'videos') {
            // Obtener o crear el registro de Video asociado al producto
            $video = Video::firstOrNew(['producto_id' => $producto->id]);

            $subtipoSeleccionado = $request->input('videos_data.tipo');
            // Acceder a los datos de video a través del subtipo seleccionado
            $videoData = $request->input("videos_data.{$subtipoSeleccionado}", []);

            // Si tienes campos adicionales específicos para cada subtipo,
            // asegúrate de recuperarlos aquí.
            // Ejemplo: $campo1Especifico = $videoData['campo1'] ?? null;

            // Actualizar los campos del modelo Video
            $video->user_id = Auth::id(); // Asegúrate de vincularlo al usuario también
            $video->autor = $videoData['autor'];
            $video->titulo = $videoData['titulo'];
            $video->descripcion = $videoData['descripcion'] ?? null;
            $video->rutaVideo = $videoData['rutaVideo']; // Esta es la ruta para la tabla 'videos'
            $video->tipo = $subtipoSeleccionado; // Este campo 'tipo' de Video guardará el subtipo
            // Opcional: Si tienes una columna 'datos_extras' para JSON
            // $video->datos_extras = json_encode(['campo1' => $campo1Especifico]);
            $video->save(); // Guarda los cambios en el registro de Video
        }

        // 9. Lógica para enviar email al operario si el estado cambió a pendiente.
        // O si simplemente se actualizó el contenido, aunque el estado sea ya pendiente
        // podrías querer notificar una edición. Adaptar según tu flujo.
        if ($estadoCambiadoAPendiente || $request->hasAny(['observaciones', 'imagen', 'cafe_data', 'mora_data', 'videos_data'])) {
            // Obtener los operarios
            $operarios = User::role('Operario')->get();
            $itemTipo = $producto->tipo; // Obtenemos el tipo de producto ('café' o 'mora' o 'videos')

            foreach ($operarios as $operario) {
                // Envía el correo con el producto principal, los detalles específicos y el tipo
                Mail::to($operario->email)->send(new NuevaRevisionPendienteMail($producto, $itemTipo));
            }
        }

        // 10. Redirigir con un mensaje de éxito.
        return redirect()->route('productos.index')->with('success', 'Producto actualizado y enviado a revisión del operario.');
    }

    /**
     * Muestra los detalles de un producto específico.
     */
    public function show(Producto $producto)
    {
        // Cargar las relaciones necesarias para mostrar los detalles.
        $producto->load([
            'user', // Para mostrar quién lo creó
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
        // Tu lógica de borrado (mantén la misma)
        $producto->delete();

        return redirect()->route('productos.index')->with('success', 'Producto eliminado.');
    }

    /**
     * Importa productos desde un archivo CSV.
     * Incluye validación de datos, manejo de errores por fila y creación de detalles específicos.
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
            return redirect()->back()->withErrors(['csv_error' => "El archivo CSV está vacío o no se pudieron leer los encabezados."]);
        }

        // Normalizar encabezados (quitar espacios, convertir a minúsculas, etc.) para una comparación más robusta
        $encabezados = array_map('trim', array_map('strtolower', $encabezados));

        // 2. Definir los campos que esperamos en el CSV y su mapeo a la base de datos
        // NOTA: Si tienes campos específicos para los subtipos de video (ej. primarios_campo1),
        // deberías añadirlos aquí y manejarlos en la lógica de creación de Video.
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
            'videos_tipo', // Este es el subtipo de video (primarios, secundarios, categorias)
        ];

        // 3. Validar que todos los encabezados requeridos estén presentes en el CSV
        $missingHeaders = array_diff($expectedCsvHeaders, $encabezados);
        if (!empty($missingHeaders)) {
            fclose($file);
            return redirect()->back()->withErrors(['csv_error' => "Faltan los siguientes encabezados requeridos en el archivo CSV: '" . implode("', '", $missingHeaders) . "'."]);
        }

        $productosCreados = 0;
        $erroresPorFila = [];
        $filaNumero = 1; // Contador para el número de fila en el CSV (después de los encabezados)

        // Obtener los operarios una sola vez para enviar los correos
        $operarios = User::role('Operario')->get();

        // Itera sobre cada fila del CSV
        while (($filaRaw = fgetcsv($file)) !== false) {
            $filaNumero++; // Incrementa para cada fila de datos

            // Asegurarse de que el número de columnas coincida
            if (count($filaRaw) !== count($encabezados)) {
                $erroresPorFila[] = "Fila {$filaNumero}: El número de columnas no coincide con los encabezados. Se esperaban " . count($encabezados) . " columnas, se encontraron " . count($filaRaw) . ".";
                Log::warning("CSV Import - Column mismatch", ['fila' => $filaNumero, 'data' => $filaRaw, 'headers' => $encabezados]);
                continue; // Saltar esta fila y continuar con la siguiente
            }

            // Combinar encabezados (normalizados) con datos de la fila para un array asociativo
            $datosFila = array_combine($encabezados, array_map('trim', $filaRaw));

            // Iniciar una transacción de base de datos para asegurar la atomicidad
            DB::beginTransaction();
            try {
                // Validar y obtener el tipo de producto
                $tipoProductoPrincipal = $datosFila['tipo'] ?? null;
                if (!in_array($tipoProductoPrincipal, ['café', 'mora', 'videos'])) {
                    throw new \Exception("Tipo de producto principal inválido: '{$tipoProductoPrincipal}'. Debe ser 'café', 'mora' o 'videos'.");
                }

                // Determinar el RutaVideo para la tabla 'productos'
                $productoRutaVideo = null;
                if ($tipoProductoPrincipal === 'café' || $tipoProductoPrincipal === 'mora') {
                    $productoRutaVideo = $datosFila['producto_rutavideo'] ?? null;
                    if ($productoRutaVideo && !filter_var($productoRutaVideo, FILTER_VALIDATE_URL)) {
                        throw new \Exception("URL de video de producto general inválida: '{$productoRutaVideo}'.");
                    }
                }

                // Crear el registro principal en la tabla 'productos'
                $producto = Producto::create([
                    'user_id' => Auth::id(), // Asume que el usuario está autenticado
                    'estado' => 'pendiente',
                    'observaciones' => $datosFila['observaciones'] ?? null,
                    'imagen' => null, // Las imágenes no se importan desde CSV en esta lógica
                    'tipo' => $tipoProductoPrincipal,
                    'RutaVideo' => $productoRutaVideo, // Se guarda solo si es café o mora
                ]);

                // Crear los datos específicos del producto (café, mora o videos)
                if ($tipoProductoPrincipal === 'café') {
                    $cafeData = [
                        'numero_pagina' => $datosFila['cafe_numero_pagina'] ?? null,
                        'clase' => $datosFila['cafe_clase'] ?? null,
                        'informacion' => $datosFila['cafe_informacion'] ?? null,
                    ];

                    // Validación específica para café
                    if (empty($cafeData['numero_pagina']) || empty($cafeData['informacion'])) {
                        throw new \Exception("Datos incompletos para café: 'cafe_numero_pagina' e 'cafe_informacion' son requeridos.");
                    }
                    if (!is_numeric($cafeData['numero_pagina'])) {
                        throw new \Exception("Número de página de café inválido: '{$cafeData['numero_pagina']}'. Debe ser un número.");
                    }

                    Cafe::create(array_merge(['producto_id' => $producto->id], $cafeData));
                } elseif ($tipoProductoPrincipal === 'mora') {
                    $moraData = [
                        'numero_pagina' => $datosFila['mora_numero_pagina'] ?? null,
                        'clase' => $datosFila['mora_clase'] ?? null,
                        'informacion' => $datosFila['mora_informacion'] ?? null,
                    ];

                    // Validación específica para mora
                    if (empty($moraData['numero_pagina']) || empty($moraData['informacion'])) {
                        throw new \Exception("Datos incompletos para mora: 'mora_numero_pagina' e 'mora_informacion' son requeridos.");
                    }
                    if (!is_numeric($moraData['numero_pagina'])) {
                        throw new \Exception("Número de página de mora inválido: '{$moraData['numero_pagina']}'. Debe ser un número.");
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

                    // Validación específica para videos
                    if (empty($videoData['autor']) || empty($videoData['titulo']) || empty($videoData['rutaVideo']) || empty($videoData['tipo'])) {
                        throw new \Exception("Datos incompletos para videos: 'videos_autor', 'videos_titulo', 'videos_rutavideo' y 'videos_tipo' son requeridos.");
                    }
                    if (!filter_var($videoData['rutaVideo'], FILTER_VALIDATE_URL)) {
                        throw new \Exception("URL de video específica inválida: '{$videoData['rutaVideo']}'.");
                    }
                    // Validar que el subtipo sea uno de los esperados
                    if (!in_array($videoData['tipo'], ['primarios', 'secundarios', 'categorias'])) {
                        throw new \Exception("Subtipo de video inválido: '{$videoData['tipo']}'. Debe ser 'primarios', 'secundarios' o 'categorias'.");
                    }

                    Video::create(array_merge(['producto_id' => $producto->id, 'user_id' => Auth::id()], $videoData));
                }

                // Si todo fue bien, confirmar la transacción
                DB::commit();
                $productosCreados++;

                // Lógica para enviar email a los operarios (solo si el producto y sus detalles se crearon con éxito)
                foreach ($operarios as $operario) {
                    Mail::to($operario->email)->send(new NuevaRevisionPendienteMail($producto, $tipoProductoPrincipal));
                }
            } catch (\Exception $e) {
                // Si algo falla, revertir la transacción
                DB::rollBack();
                $erroresPorFila[] = "Fila {$filaNumero}: " . $e->getMessage();
                Log::error("Error al importar fila CSV: " . $e->getMessage(), ['fila' => $filaNumero, 'datos' => $datosFila]);
            }
        }

        fclose($file);

        // 7. Mensaje de éxito o de errores
        if (!empty($erroresPorFila)) {
            $mensaje = "Se importaron **{$productosCreados}** productos con éxito. Sin embargo, hubo **errores en algunas filas**:<br>" . implode('<br>', $erroresPorFila);
            // Usar 'html' como clave para permitir HTML en el mensaje de la sesión
            return redirect()->back()->with('warning', $mensaje); // Eliminado withInput para evitar rellenar el campo de archivo
        } else {
            return redirect()->back()->with('success', "Archivo CSV importado con éxito. Se crearon **{$productosCreados}** productos.");
        }
    }

    /**
     * Exporta productos a un archivo CSV, aplicando filtros y cargando relaciones de detalles (café/mora/videos).
     */
    public function exportarCSV(Request $request)
    {
        // 1. Obtener los parámetros de filtro de la solicitud
        $querySearch = $request->input('q'); // Usamos 'q' para búsqueda general
        $estadoFilter = $request->input('estado');
        $userIdFilter = $request->input('user_id');
        $tipoFilter = $request->input('tipo'); // Nuevo filtro para el tipo de producto

        // 2. Iniciar la consulta Eloquent para el modelo Producto
        $productosQuery = Producto::query();

        // 3. Aplicar los filtros dinámicamente a la consulta
        if ($querySearch) {
            $productosQuery->where(function ($q) use ($querySearch) {
                // Búsqueda por ID de producto o por observaciones (si es relevante)
                $q->where('id', $querySearch)
                    ->orWhereRaw('LOWER(observaciones) LIKE ?', ['%' . strtolower($querySearch) . '%']);
            });
        }

        if ($estadoFilter) {
            $productosQuery->where('estado', $estadoFilter);
        }

        if ($userIdFilter) {
            $productosQuery->where('user_id', $userIdFilter);
        }

        // Si se filtra por tipo, aplicar el filtro
        if ($tipoFilter && in_array($tipoFilter, ['café', 'mora', 'videos'])) {
            $productosQuery->where('tipo', $tipoFilter);
        }

        // 4. Cargar las relaciones anidadas y luego obtener los productos
        // Asegúrate de cargar todas las relaciones de detalle relevantes
        $productos = $productosQuery->with(['user', 'cafe', 'mora', 'videos'])->get();

        // 5. Generar un nombre de archivo único para el CSV
        $nombreArchivo = 'productos_exportados_' . now()->format('Y-m-d_H-i-s') . '.csv';

        // 6. Definir los encabezados HTTP necesarios para la descarga del archivo CSV
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8', // Aseguramos UTF-8
            'Content-Disposition' => "attachment; filename=\"$nombreArchivo\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        // 7. Definir los nombres de las columnas que aparecerán en la primera fila del CSV
        // Esto refleja las columnas directas en tus tablas 'cafe', 'mora' y 'videos'
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
            // Campos específicos para Café
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

        // 8. Definir la función de callback que generará el contenido del CSV
        $callback = function () use ($productos, $columnas) {
            $file = fopen('php://output', 'w');
            // Escribir la codificación UTF-8 BOM para asegurar la correcta lectura en Excel
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));
            fputcsv($file, $columnas);

            foreach ($productos as $producto) {
                // Datos base del producto
                $row = [
                    $producto->id,
                    $producto->tipo,
                    $producto->estado,
                    $producto->observaciones ?? '',
                    $producto->imagen ?? '', // Asegura que no sea null
                    $producto->RutaVideo ?? '', // RutaVideo de la tabla 'productos'
                    $producto->user_id,
                    $producto->user->name ?? 'N/A', // Nombre del creador
                    $producto->user->email ?? 'N/A', // Email del creador
                    $producto->created_at ? $producto->created_at->format('Y-m-d H:i:s') : '',
                ];

                // Añadir campos específicos de Café
                if ($producto->tipo === 'café' && $producto->cafe) {
                    $row[] = $producto->cafe->numero_pagina ?? '';
                    $row[] = $producto->cafe->clase ?? '';
                    $row[] = $producto->cafe->informacion ?? '';
                } else {
                    // Si no es café, añadir celdas vacías para las columnas de café para mantener la consistencia
                    $row = array_merge($row, array_fill(0, 3, '')); // 3 campos vacíos para Cafe
                }

                // Añadir campos específicos de Mora
                if ($producto->tipo === 'mora' && $producto->mora) {
                    $row[] = $producto->mora->numero_pagina ?? '';
                    $row[] = $producto->mora->clase ?? '';
                    $row[] = $producto->mora->informacion ?? '';
                } else {
                    // Si no es mora, añadir celdas vacías para las columnas de mora
                    $row = array_merge($row, array_fill(0, 3, '')); // 3 campos vacíos para Mora
                }

                // Añadir campos específicos de Videos
                if ($producto->tipo === 'videos' && $producto->videos) {
                    $row[] = $producto->videos->autor ?? '';
                    $row[] = $producto->videos->titulo ?? '';
                    $row[] = $producto->videos->descripcion ?? '';
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

        // 9. Retornar la respuesta de streaming
        return Response::stream($callback, 200, $headers);
    }
}
