<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Cafe;
use App\Models\Mora;
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
     * Guarda un nuevo producto (café o mora) en la base de datos.
     * Incluye validación de datos y manejo de carga de imágenes.
     */
    public function store(Request $request)
    {
        // 1. Definir las reglas de validación base para el producto.
        $rules = [
            'tipo' => 'required|string|in:café,mora', // Asegura que el tipo sea 'café' o 'mora'
            'imagen' => 'nullable|image|mimes:jpeg,png,jpg|max:2048', // Validación para la imagen
            'observaciones' => 'nullable|string', // Campo opcional de observaciones
        ];

        // 2. Añadir reglas de validación condicionalmente según el tipo de producto.
        if ($request->input('tipo') === 'café') {
            $rules['cafe_data.numero_pagina'] = 'required|integer';
            $rules['cafe_data.clase'] = 'nullable|string|max:100';
            $rules['cafe_data.informacion'] = 'required|string';
        } elseif ($request->input('tipo') === 'mora') {
            $rules['mora_data.numero_pagina'] = 'required|integer';
            $rules['mora_data.clase'] = 'nullable|string|max:100';
            $rules['mora_data.informacion'] = 'required|string';
        }

        // 3. Aplicar las reglas de validación.
        $request->validate($rules);

        // 4. Lógica para guardar la imagen (si se ha subido).
        $imagen = null;
        if ($request->hasFile('imagen')) {
            $imagen = $request->file('imagen')->store('productos', 'public');
        }

        // 5. Crear el registro principal en la tabla 'productos'.
        $producto = Producto::create([
            'user_id' => Auth::id(),
            'estado' => 'pendiente', // Establece un estado inicial para el producto
            'observaciones' => $request->observaciones,
            'imagen' => $imagen,
            'tipo' => $request->tipo,
        ]);

        $tipoProducto = $request->input('tipo');

        // 6. Guardar los datos específicos del producto (café o mora) en sus tablas correspondientes.
        if ($tipoProducto === 'café') {
            $cafeData = $request->input('cafe_data', []);
            // Crear el registro en la tabla 'cafe' y vincularlo con el producto principal
            Cafe::create([
                'producto_id' => $producto->id, // Vincula con el ID del producto recién creado
                'numero_pagina' => $cafeData['numero_pagina'] ?? 1, // Usa el valor validado o un predeterminado
                'clase' => $cafeData['clase'] ?? null, // Usa el valor validado
                'informacion' => $cafeData['informacion'] ?? '', // Usa el valor validado
            ]);
        } elseif ($tipoProducto === 'mora') {
            $moraData = $request->input('mora_data', []);
            // Crear el registro en la tabla 'mora' y vincularlo con el producto principal
            Mora::create([
                'producto_id' => $producto->id, // Vincula con el ID del producto recién creado
                'numero_pagina' => $moraData['numero_pagina'] ?? 1, // Usa el valor validado o un predeterminado
                'clase' => $moraData['clase'] ?? null, // Usa el valor validado
                'informacion' => $moraData['informacion'] ?? '', // Usa el valor validado
            ]);
        }

        // 7. Lógica para enviar email a los operarios.
        // Busca usuarios con el rol 'operario' y les envía un correo.
        $operarios = User::role('Operario')->get(); // Cambiado de 'operador' a 'operario'
        foreach ($operarios as $operario) {
            // Pasa el producto principal y los detalles específicos (café/mora) al Mailable
            Mail::to($operario->email)->send(new NuevaRevisionPendienteMail($producto, $tipoProducto));
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
        // Cargar las relaciones necesarias para la vista de edición.
        // Solo necesitamos cargar 'cafe' o 'mora' directamente, no sus sub-relaciones.
        $producto->load([
            'cafe', // Carga el modelo Cafe relacionado si existe
            'mora', // Carga el modelo Mora relacionado si existe
        ]);

        return view('productos.edit', compact('producto'));
    }

    /**
     * Actualiza el producto especificado en el almacenamiento.
     */
    public function update(Request $request, Producto $producto)
    {
        Gate::authorize('actualizar producto');
        // 1. Definir las reglas de validación base para la actualización del producto.
        $rules = [
            'imagen' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'observaciones' => 'nullable|string',
        ];

        // 2. Añadir reglas de validación condicionalmente según el tipo ACTUAL del producto.
        if ($producto->tipo === 'café') {
            $rules['cafe_data.numero_pagina'] = 'required|integer';
            $rules['cafe_data.clase'] = 'nullable|string|max:100';
            $rules['cafe_data.informacion'] = 'required|string';
        } elseif ($producto->tipo === 'mora') {
            $rules['mora_data.numero_pagina'] = 'required|integer';
            $rules['mora_data.clase'] = 'nullable|string|max:100';
            $rules['mora_data.informacion'] = 'required|string';
        }

        // 3. Aplicar las reglas de validación.
        $request->validate($rules);

        // 4. Almacenar el estado original del producto ANTES de cualquier cambio.
        $originalEstado = $producto->estado;

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

        // 7. Lógica para cambiar el estado a 'pendiente' si el producto fue editado
        // y su estado anterior era 'aprobado' o 'rechazado'.
        $estadoCambiadoAPendiente = false;
        if ($originalEstado === 'aprobado' || $originalEstado === 'rechazado') {
            $producto->estado = 'pendiente';
            // Opcional: limpiar la observación del operador al volver a pendiente.
            $producto->observaciones_operador = null; // Asumiendo que tienes un campo para esto
            $estadoCambiadoAPendiente = true;
        }

        $producto->save(); // Guarda los cambios en el producto principal

        // 8. Actualizar registros en las tablas de detalle según el tipo del producto.
        if ($producto->tipo === 'café') {
            // Obtener o crear el registro de Cafe asociado al producto
            $cafe = Cafe::firstOrNew(['producto_id' => $producto->id]);
            $cafeData = $request->input('cafe_data', []);

            // Actualizar los campos del modelo Cafe
            $cafe->numero_pagina = $cafeData['numero_pagina'] ?? 1;
            $cafe->clase = $cafeData['clase'] ?? null;
            $cafe->informacion = $cafeData['informacion'] ?? '';
            $cafe->save(); // Guarda los cambios en el registro de Cafe

        } elseif ($producto->tipo === 'mora') {
            // Obtener o crear el registro de Mora asociado al producto
            $mora = Mora::firstOrNew(['producto_id' => $producto->id]);
            $moraData = $request->input('mora_data', []);

            // Actualizar los campos del modelo Mora
            $mora->numero_pagina = $moraData['numero_pagina'] ?? 1;
            $mora->clase = $moraData['clase'] ?? null;
            $mora->informacion = $moraData['informacion'] ?? '';
            $mora->save(); // Guarda los cambios en el registro de Mora
        }

        // 9. Lógica para enviar email al operario si el estado cambió a pendiente.
        // O si simplemente se actualizó el contenido, aunque el estado sea ya pendiente
        // podrías querer notificar una edición. Adaptar según tu flujo.
        if ($estadoCambiadoAPendiente || $request->hasAny(['observaciones', 'imagen', 'cafe_data', 'mora_data'])) {
            // Obtener los operarios
            $operarios = User::role('Operario')->get(); // Cambiado de 'operador' a 'operario'
            $itemTipo = $producto->tipo; // Obtenemos el tipo de producto ('café' o 'mora')

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
        // Solo cargamos las relaciones directas que existen.
        $producto->load([
            'user', // Para mostrar quién lo creó
            'cafe', // Carga el modelo Cafe relacionado
            'mora', // Carga el modelo Mora relacionado
            // 'validador', // Mantengo si estos modelos/relaciones existen en tu app
            // 'rechazador', // Mantengo si estos modelos/relaciones existen en tu app
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
     * Incluye validación de datos, manejo de errores por fila y creación de detalles específicos (café/mora).
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
        $expectedCsvHeaders = [
            'tipo',
            'observaciones',
            'cafe_numero_pagina',
            'cafe_clase',
            'cafe_informacion',
            'mora_numero_pagina',
            'mora_clase',
            'mora_informacion',
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
        // Asegúrate de que el rol esté correctamente en mayúscula/minúscula según tu DB y config.
        $operarios = User::role('Operario')->get(); // Usando 'Operario' con 'O' mayúscula según tu corrección

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

            // Iniciar una transacción de base de datos para asegurar la atomicidad de la creación de producto y sus detalles
            DB::beginTransaction();
            try {
                // Validar y obtener el tipo de producto
                $tipo = $datosFila['tipo'] ?? null;
                if (!in_array($tipo, ['café', 'mora'])) {
                    throw new \Exception("Tipo de producto inválido: '{$tipo}'. Debe ser 'café' o 'mora'.");
                }

                // 4. Crear el registro principal en la tabla 'productos'
                $producto = Producto::create([
                    'user_id' => Auth::id(), // Asume que el usuario está autenticado
                    'estado' => 'pendiente',
                    'observaciones' => $datosFila['observaciones'] ?? null,
                    'imagen' => null, // Las imágenes no se importan desde CSV en esta lógica
                    'tipo' => $tipo,
                ]);

                // No necesitamos almacenar $detallesProducto en una variable si no la usaremos después
                // o si la usamos solo para el Mailable, y el Mailable ya no la requiere.

                // 5. Crear los datos específicos del producto (café o mora)
                if ($tipo === 'café') {
                    $cafeData = [
                        'numero_pagina' => $datosFila['cafe_numero_pagina'] ?? null,
                        'clase' => $datosFila['cafe_clase'] ?? null,
                        'informacion' => $datosFila['cafe_informacion'] ?? null,
                    ];

                    // Validación específica para café
                    if (empty($cafeData['numero_pagina']) || empty($cafeData['informacion'])) {
                        throw new \Exception("Datos incompletos para café: 'numero_pagina' e 'informacion' son requeridos.");
                    }
                    if (!is_numeric($cafeData['numero_pagina'])) {
                        throw new \Exception("Número de página de café inválido: '{$cafeData['numero_pagina']}'. Debe ser un número.");
                    }

                    Cafe::create(array_merge(['producto_id' => $producto->id], $cafeData));
                } elseif ($tipo === 'mora') {
                    $moraData = [
                        'numero_pagina' => $datosFila['mora_numero_pagina'] ?? null,
                        'clase' => $datosFila['mora_clase'] ?? null,
                        'informacion' => $datosFila['mora_informacion'] ?? null,
                    ];

                    // Validación específica para mora
                    if (empty($moraData['numero_pagina']) || empty($moraData['informacion'])) {
                        throw new \Exception("Datos incompletos para mora: 'numero_pagina' e 'informacion' son requeridos.");
                    }
                    if (!is_numeric($moraData['numero_pagina'])) {
                        throw new \Exception("Número de página de mora inválido: '{$moraData['numero_pagina']}'. Debe ser un número.");
                    }

                    Mora::create(array_merge(['producto_id' => $producto->id], $moraData));
                }

                // Si todo fue bien, confirmar la transacción
                DB::commit();
                $productosCreados++;

                // 6. Lógica para enviar email a los operarios (solo si el producto y sus detalles se crearon con éxito)
                foreach ($operarios as $operario) {
                    // ¡AQUÍ ESTÁ EL CAMBIO CLAVE! Elimina el segundo argumento ($detallesProducto)
                    Mail::to($operario->email)->send(new NuevaRevisionPendienteMail($producto, $tipo));
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
            return redirect()->back()->with('warning', $mensaje)->withInput($request->only('archivo_csv'));
        } else {
            return redirect()->back()->with('success', "Archivo CSV importado con éxito. Se crearon **{$productosCreados}** productos.");
        }
    }
    /**
     * Exporta productos a un archivo CSV, aplicando filtros y cargando relaciones de detalles (café/mora).
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
                // Si quieres buscar por campos de Cafe/Mora, necesitarías joins o whereHas,
                // pero eso puede afectar el rendimiento en grandes datasets para exportación.
                // Para simplificar, nos centramos en el modelo principal.
            });
        }

        if ($estadoFilter) {
            $productosQuery->where('estado', $estadoFilter);
        }

        if ($userIdFilter) {
            $productosQuery->where('user_id', $userIdFilter);
        }

        if ($tipoFilter && in_array($tipoFilter, ['café', 'mora'])) {
            $productosQuery->where('tipo', $tipoFilter);
        }

        // 4. Cargar las relaciones anidadas y luego obtener los productos
        // Se cargan 'cafe' y 'mora' directamente, ya que estas son las tablas de detalle.
        // No necesitamos 'cafInfor', 'cafInsumos', etc., si los campos están directamente en 'cafe'/'mora'.
        $productos = $productosQuery->with(['user', 'cafe', 'mora'])->get();

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
        // Esto refleja las columnas directas en tus tablas 'cafe' y 'mora'
        $columnas = [
            'ID Producto',
            'Tipo Producto',
            'Estado',
            'Observaciones del Producto',
            'Ruta Imagen',
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
                    $row = array_merge($row, array_fill(0, 3, '')); // 3 campos vacíos para Café
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

                fputcsv($file, $row);
            }

            fclose($file);
        };

        // 9. Retornar la respuesta de streaming
        return Response::stream($callback, 200, $headers);
    }
}
