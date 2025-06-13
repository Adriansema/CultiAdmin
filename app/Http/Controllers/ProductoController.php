<?php

//actualizacion 09/04/2025 (y ahora con ProductPolicy 06/06/2025)

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Cafe;
use App\Models\Mora;
use App\Models\Producto;
use Illuminate\Http\Request;
use App\Services\ProductService;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;
use App\Mail\NuevaRevisionPendienteMail;

class ProductoController extends Controller
{
    // Opcional: Se puede inyectar la Policy si se va a usar mucho,
    // pero $this->authorize() ya se encarga de resolverla.

    public function index(Request $request, ProductService $productService)
    {

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


        // Asegúrate de que las vistas tienen las variables necesarias.
        // Las variables $tiposCafe, $cafeInformaciones, etc. no estaban aquí,
        // si tu vista 'productos.create' las necesita, deberás traerlas aquí.
        // Por ahora, solo se devuelve la vista.
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
        // Aquí se valida directamente los campos de las tablas 'cafe' o 'mora'.
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
        // Asume que el usuario está autenticado y su ID se obtiene con Auth::id().
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
            // Obtener los datos específicos del café desde el request
            $cafeData = $request->input('cafe_data', []);
            // Crear el registro en la tabla 'cafe' y vincularlo con el producto principal
            Cafe::create([
                'producto_id' => $producto->id, // Vincula con el ID del producto recién creado
                'numero_pagina' => $cafeData['numero_pagina'] ?? 1, // Usa el valor validado o un predeterminado
                'clase' => $cafeData['clase'] ?? null, // Usa el valor validado
                'informacion' => $cafeData['informacion'] ?? '', // Usa el valor validado
            ]);

        } elseif ($tipoProducto === 'mora') {
            // Obtener los datos específicos de la mora desde el request
            $moraData = $request->input('mora_data', []);
            // Crear el registro en la tabla 'mora' y vincularlo con el producto principal
            Mora::create([
                'producto_id' => $producto->id, // Vincula con el ID del producto recién creado
                'numero_pagina' => $moraData['numero_pagina'] ?? 1, // Usa el valor validado o un predeterminado
                'clase' => $moraData['clase'] ?? null, // Usa el valor validado
                'informacion' => $moraData['informacion'] ?? '', // Usa el valor validado
            ]);
        }

        // 7. Lógica para enviar email a los operadores (si aplica).
        // Busca usuarios con el rol 'operador' y les envía un correo.
        $operadores = User::role('operador')->get();
        foreach ($operadores as $operador) {
            Mail::to($operador->email)->send(new NuevaRevisionPendienteMail($producto, 'Noticia'));
        }

        // 8. Redirigir con un mensaje de éxito.
        return redirect()->route('productos.index')->with('success', 'Información guardada con éxito y enviada a revisión.');
    }

    /**
     * Muestra el formulario para editar un producto existente.
     */
    public function edit(Producto $producto)
    {

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


        // 1. Definir las reglas de validación base para la actualización del producto.
        $rules = [
            // El tipo de producto no debería cambiar en la edición, por lo que no se valida.
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
             $producto->observaciones = null;
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

        // 9. Lógica para enviar email al operador si el estado cambió a pendiente.
        if ($estadoCambiadoAPendiente) {
            $operadores = User::role('operador')->get();
            foreach ($operadores as $operador) {
                Mail::to($operador->email)->send(new NuevaRevisionPendienteMail($producto, 'Noticia'));
            }
        }

        // 10. Redirigir con un mensaje de éxito.
        return redirect()->route('productos.index')->with('success', 'Producto actualizado y enviado a revisión del operador.');
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
        /* dd($producto); */

        return view('productos.show', compact('producto'));
    }

    public function destroy(Producto $producto)
    {

        // Tu lógica de borrado (mantén la misma)
        $producto->delete();

        return redirect()->route('productos.index')->with('success', 'Producto eliminado.');
    }

    public function importarCSV(Request $request)
    {

        $request->validate([
            'archivo_csv' => 'required|file|mimes:csv,txt',
        ]);

        $archivo = $request->file('archivo_csv');
        $ruta = $archivo->getRealPath();

        $file = fopen($ruta, 'r');
        $encabezados = fgetcsv($file); // Lee la primera fila como encabezados

        $productosCreados = 0;
        $erroresPorFila = [];

        // Definir los campos que esperamos en el CSV y cómo mapearlos
        // Puedes refinar esto según los encabezados exactos de tu CSV
        $requiredCsvHeaders = [
            'tipo',
            'observaciones',
            'caf_infor_informacion', // Usar nombres planos para CSV
            'caf_insumos_informacion',
            'caf_patoge_informacion',
            'caf_patoge_patogeno',
            'mora_inf_informacion',
            'mora_insu_informacion',
            'mora_patoge_informacion',
            'mora_patoge_patogeno',
        ];

        // Validar que los encabezados esperados estén en el CSV
        foreach ($requiredCsvHeaders as $header) {
            if (!in_array($header, $encabezados)) {
                fclose($file);
                return redirect()->back()->withErrors(['csv_error' => "El archivo CSV debe contener el encabezado requerido: '{$header}'."]);
            }
        }


        // Itera sobre cada fila del CSV
        $filaNumero = 1; // Para seguimiento de errores
        while (($fila = fgetcsv($file)) !== false) {
            $filaNumero++;

            // Combinar encabezados con datos de la fila para un array asociativo
            $datosFila = array_combine($encabezados, $fila);

            try {
                // Validación básica por fila (puedes expandir esto)
                $tipo = $datosFila['tipo'] ?? null;
                if (!in_array($tipo, ['café', 'mora'])) {
                    throw new \Exception("Tipo de producto inválido en la fila {$filaNumero}: '{$tipo}'. Debe ser 'café' o 'mora'.");
                }

                // 1. Crear el registro principal en la tabla 'productos'
                $producto = Producto::create([
                    'user_id' => Auth::id(),
                    'estado' => 'pendiente',
                    'observaciones' => $datosFila['observaciones'] ?? null,
                    'imagen' => null, // No se importa desde CSV
                    'tipo' => $tipo,
                ]);

                $productosCreados++;

                // Lógica para enviar email al operador (solo si se creó el producto con éxito)
                $operadores = User::role('operador')->get();
                foreach ($operadores as $operador) {
                    Mail::to($operador->email)->send(new NuevaRevisionPendienteMail($producto, 'Noticia'));
                }

            } catch (\Exception $e) {
                // Capturar errores por fila y almacenarlos
                $erroresPorFila[] = "Fila {$filaNumero}: " . $e->getMessage();
                // Opcional: Registrar el error en los logs de Laravel
                \Log::error("Error al importar fila CSV: " . $e->getMessage(), ['fila' => $filaNumero, 'datos' => $datosFila]);
            }
        }

        fclose($file);

        // Mensaje de éxito o de errores
        if (!empty($erroresPorFila)) {
            $mensaje = "Se importaron {$productosCreados} productos. Hubo errores en algunas filas:<br>" . implode('<br>', $erroresPorFila);
            return redirect()->back()->with('warning', $mensaje);
        } else {
            return redirect()->back()->with('success', "Archivo CSV importado con éxito. Se crearon {$productosCreados} productos.");
        }
    }

    public function exportarCSV(Request $request)
    {

        // 1. Obtener los parámetros de filtro de la solicitud
        $query = $request->input('q');
        $estado = $request->input('estado');
        $userIdFilter = $request->input('user_id');

        // 2. Iniciar la consulta Eloquent para el modelo Producto
        $productosQuery = Producto::query();

        // 3. Aplicar los filtros dinámicamente a la consulta
        if ($query) {
            $productosQuery->where(function ($q2) use ($query) {
                // Se cambió 'nombre' por 'tipo' ya que 'nombre' no existe en tu esquema.
                // Si 'tipo' no es el campo deseado para la búsqueda general,
                // deberías considerar añadir una columna de 'nombre' o 'titulo' a tu tabla.
                $q2->whereRaw('LOWER(tipo) LIKE ?', ['%' . strtolower($query) . '%'])
                   // También permite buscar por el ID del producto si la consulta es un número
                   ->orWhere('id', $query);
            });
        }

        if ($estado) {
            $productosQuery->where('estado', $estado);
        }

        if ($userIdFilter) {
            $productosQuery->where('user_id', $userIdFilter);
        }

        // 4. Cargar las relaciones anidadas DESPUÉS de aplicar los filtros, y luego obtener los productos
        $productos = $productosQuery->with([
            'user', // Para obtener el creador
            'cafe.cafInfor',
            'cafe.cafInsumos',
            'cafe.cafPatoge',
            'mora.moraInf',
            'mora.moraInsu',
            'mora.moraPatoge',
        ])->get();

        // 5. Generar un nombre de archivo único para el CSV
        $nombreArchivo = 'productos_exportados_' . now()->format('Y-m-d_H-i-s') . '.csv';

        // 6. Definir los encabezados HTTP necesarios para la descarga del archivo CSV
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$nombreArchivo\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        // 7. Definir los nombres de las columnas que aparecerán en la primera fila del CSV
        // ¡Aquí se define la estructura de las columnas de salida!
        $columnas = [
            'ID Producto',
            'ID Usuario Creador',
            'Email Usuario Creador', // Nuevo campo
            'Tipo Producto',
            'Estado',
            'Observaciones',
            'Ruta Imagen',
            'Fecha de Creacion',
            // Campos específicos para Café
            'Cafe - Info General', // Ajuste de nombre para claridad
            'Cafe - Info Insumos',
            'Cafe - Info Patogenos',
            'Cafe - Nombre Patogeno',
            // Campos específicos para Mora
            'Mora - Info General', // Ajuste de nombre para claridad
            'Mora - Info Insumos',
            'Mora - Info Patogenos',
            'Mora - Nombre Patogeno',
        ];

        // 8. Definir la función de callback que generará el contenido del CSV
        $callback = function () use ($productos, $columnas) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columnas);

            foreach ($productos as $producto) {
                // Base de la fila para el CSV
                $row = [
                    $producto->id,
                    $producto->user_id,
                    $producto->user->email ?? 'N/A', // Email del creador
                    $producto->tipo,
                    $producto->estado,
                    $producto->observaciones,
                    $producto->imagen,
                    $producto->created_at ? $producto->created_at->format('Y-m-d H:i:s') : '',
                ];

                // Añadir campos específicos de Café
                if ($producto->tipo === 'café' && $producto->cafe) {
                    $row[] = $producto->cafe->cafInfor->informacion ?? '';
                    $row[] = $producto->cafe->cafInsumos->informacion ?? '';
                    $row[] = $producto->cafe->cafPatoge->informacion ?? '';
                    $row[] = $producto->cafe->cafPatoge->patogeno ?? '';
                } else {
                    // Si no es café, añadir celdas vacías para las columnas de café para mantener la consistencia
                    $row = array_merge($row, array_fill(0, 4, '')); // 4 campos vacíos para Café
                }

                // Añadir campos específicos de Mora
                if ($producto->tipo === 'mora' && $producto->mora) {
                    $row[] = $producto->mora->moraInf->informacion ?? '';
                    $row[] = $producto->mora->moraInsu->informacion ?? '';
                    $row[] = $producto->mora->moraPatoge->informacion ?? '';
                    $row[] = $producto->mora->moraPatoge->patogeno ?? '';
                } else {
                    // Si no es mora, añadir celdas vacías para las columnas de mora
                    $row = array_merge($row, array_fill(0, 4, '')); // 4 campos vacíos para Mora
                }

                fputcsv($file, $row);
            }

            fclose($file);
        };

        // 9. Retornar la respuesta de streaming
        return Response::stream($callback, 200, $headers);
    }
}