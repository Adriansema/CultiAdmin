<?php

//actualizacion 09/04/2025 (y ahora con ProductPolicy 06/06/2025)

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Cafe;
use App\Models\Mora;
use App\Models\Producto;
use App\Models\CafInfor;
use App\Models\CafInsumos;
use App\Models\CafPatoge;
use App\Models\MoraInf;
use App\Models\MoraInsu;
use App\Models\MoraPatoge;
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
        /* dd($request->all()); */
        // Autorización: El usuario debe tener permiso para ver cualquier producto (para la lista).
        // Si el 'before' de la Policy permite al administrador pasar, esta línea será ignorada para él.
        $this->authorize('viewAny', Producto::class);

        $productos = $productService->obtenerProductosFiltrados($request);
        return view('productos.index', compact('productos'));
    }

    // Si también necesitas una respuesta JSON (ej. para una API o Vue/React):
    public function getFilteredProducts(Request $request, ProductService $productService)
    {
        // Autorización: Mismo permiso que viewAny, ya que es para ver la lista filtrada.
        $this->authorize('viewAny', Producto::class);

        $productos = $productService->obtenerProductosFiltrados($request);
        return response()->json($productos);
    }

    public function create()
    {
        // Autorización: El usuario debe tener permiso para crear productos.
        $this->authorize('create', Producto::class);

        // Asegúrate de que las vistas tienen las variables necesarias.
        // Las variables $tiposCafe, $cafeInformaciones, etc. no estaban aquí,
        // si tu vista 'productos.create' las necesita, deberás traerlas aquí.
        // Por ahora, solo se devuelve la vista.
        return view('productos.create');
    }

    public function store(Request $request)
    {
        // Autorización: El usuario debe tener permiso para crear productos.
        $this->authorize('create', Producto::class);

        // Validación de los campos
        $rules = [
            'tipo' => 'required|string|in:café,mora',
            'imagen' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'observaciones' => 'nullable|string',
        ];

        // Añadir reglas de validación condicionalmente según el tipo seleccionado
        if ($request->input('tipo') === 'café') {
            $rules['caf_infor.informacion'] = 'required|string';
            $rules['caf_insumos.informacion'] = 'required|string';
            $rules['caf_patoge.informacion'] = 'required|string';
            $rules['caf_patoge.patogeno'] = 'required|string|max:255';
        } elseif ($request->input('tipo') === 'mora') {
            $rules['mora_inf.informacion'] = 'required|string';
            $rules['mora_insu.informacion'] = 'required|string';
            $rules['mora_patoge.informacion'] = 'required|string';
            $rules['mora_patoge.patogeno'] = 'required|string|max:255';
        }

        $request->validate($rules); // Aplica las reglas de validación

        // Lógica para guardar la imagen
        $imagen = null;
        if ($request->hasFile('imagen')) {
            $imagen = $request->file('imagen')->store('productos', 'public');
        }

        // 1. Crear el registro principal en la tabla 'productos'
        $producto = Producto::create([
            'user_id' => Auth::id(), // Asume que el usuario está autenticado
            'estado' => 'pendiente', // O el estado inicial que desees
            'observaciones' => $request->observaciones,
            'imagen' => $imagen,
            'tipo' => $request->tipo,
        ]);

        $tipoProducto = $request->input('tipo');

        if ($tipoProducto === 'café') {
            // Crear los registros de detalle de Café
            $cafInforData = $request->input('caf_infor', []);
            $cafInfor = CafInfor::create([
                'numero_pagina' => 1, // Valor predeterminado
                'informacion' => $cafInforData['informacion'] ?? '',
            ]);

            $cafInsumosData = $request->input('caf_insumos', []);
            $cafInsumos = CafInsumos::create([
                'numero_pagina' => 1,
                'informacion' => $cafInsumosData['informacion'] ?? '',
            ]);

            $cafPatogeData = $request->input('caf_patoge', []);
            $cafPatoge = CafPatoge::create([
                'numero_pagina' => 1,
                'patogeno' => $cafPatogeData['patogeno'] ?? 'General',
                'informacion' => $cafPatogeData['informacion'] ?? '',
            ]);

            // Crear el registro en la tabla 'cafe' y vincularlo con el producto principal
            // y con los registros de detalle de café
            Cafe::create([
                'producto_id' => $producto->id,
                'id_caf' => $cafInfor->id_caf,
                'id_insumos' => $cafInsumos->id_insumos,
                'id_patoge' => $cafPatoge->id_patoge,
            ]);

        } elseif ($tipoProducto === 'mora') {
            // Crear los registros de detalle de Mora
            $moraInfData = $request->input('mora_inf', []);
            $moraInf = MoraInf::create([
                'numero_pagina' => 1,
                'informacion' => $moraInfData['informacion'] ?? '',
            ]);

            $moraInsuData = $request->input('mora_insu', []);
            $moraInsu = MoraInsu::create([
                'numero_pagina' => 1,
                'informacion' => $moraInsuData['informacion'] ?? '',
            ]);

            $moraPatogeData = $request->input('mora_patoge', []);
            $moraPatoge = MoraPatoge::create([
                'numero_pagina' => 1,
                'patogeno' => $moraPatogeData['patogeno'] ?? 'General',
                'informacion' => $moraPatogeData['informacion'] ?? '',
            ]);

            // Crear el registro en la tabla 'mora' y vincularlo con el producto principal
            // y con los registros de detalle de mora
            Mora::create([
                'producto_id' => $producto->id,
                'id_info' => $moraInf->id_info,
                'id_insu' => $moraInsu->id_insu,
                'id_pat' => $moraPatoge->id_pat,
            ]);
        }

        // Lógica para enviar email al operador
        $operadores = User::role('operador')->get();
        foreach ($operadores as $operador) {
            Mail::to($operador->email)->send(new NuevaRevisionPendienteMail($producto, 'Noticia'));
        }

        return redirect()->route('productos.index')->with('success', 'Información guardada con éxito.');
    }

    public function edit(Producto $producto)
    {
        // Autorización: El usuario debe tener permiso para actualizar este producto específico.
        $this->authorize('update', $producto);

        // Cargar las relaciones necesarias para la vista de edición
        $producto->load([
            'cafe.cafInfor',
            'cafe.cafInsumos',
            'cafe.cafPatoge',
            'mora.moraInf',
            'mora.moraInsu',
            'mora.moraPatoge',
        ]);

        return view('productos.edit', compact('producto'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Producto $producto)
    {
        // Autorización: El usuario debe tener permiso para actualizar este producto específico.
        $this->authorize('update', $producto);

        $rules = [
            'tipo' => 'required|string|in:café,mora', // El tipo no debería cambiar en la edición, pero se valida
            'imagen' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'observaciones' => 'nullable|string',
        ];

        // Añadir reglas de validación condicionalmente según el tipo del producto
        // Usamos $producto->tipo porque el campo 'tipo' en la vista estará deshabilitado
        if ($producto->tipo === 'café') {
            $rules['caf_infor.informacion'] = 'required|string';
            $rules['caf_insumos.informacion'] = 'required|string';
            $rules['caf_patoge.informacion'] = 'required|string';
            $rules['caf_patoge.patogeno'] = 'required|string|max:255';
        } elseif ($producto->tipo === 'mora') {
            $rules['mora_inf.informacion'] = 'required|string';
            $rules['mora_insu.informacion'] = 'required|string';
            $rules['mora_patoge.informacion'] = 'required|string';
            $rules['mora_patoge.patogeno'] = 'required|string|max:255';
        }

        $request->validate($rules);

        // Almacenar el estado original del producto ANTES de cualquier cambio
        $originalEstado = $producto->estado;

        // 1. Actualizar campos principales del Producto
        // Actualizar imagen si viene una nueva
        if ($request->hasFile('imagen')) {
            // Eliminar imagen anterior si existe
            if ($producto->imagen && Storage::disk('public')->exists($producto->imagen)) {
                Storage::disk('public')->delete($producto->imagen);
            }
            $imagen = $request->file('imagen')->store('productos', 'public');
            $producto->imagen = $imagen;
        }

        // Actualizar los demás campos del producto principal
        // El tipo no se actualiza directamente aquí si lo deshabilitaste en la vista
        // $producto->tipo = $request->tipo; // Descomentar si permites cambiar el tipo
        $producto->observaciones = $request->observaciones;

        // Lógica para cambiar el estado a 'pendiente' si el producto fue editado
        $estadoCambiadoAPendiente = false;
        if ($originalEstado === 'aprobado' || $originalEstado === 'rechazado') {
             $producto->estado = 'pendiente';
             // Opcional: limpiar la observación del operador al volver a pendiente.
             $producto->observaciones = null; // Limpiar observación del operador
             $estadoCambiadoAPendiente = true;
        }

        $producto->save(); // Guarda los cambios en el producto principal

        // 2. Actualizar registros en las tablas de detalle según el tipo
        if ($producto->tipo === 'café') {
            $cafe = $producto->cafe; // Obtiene el modelo Cafe relacionado

            if ($cafe) {
                $cafInforData = $request->input('caf_infor', []);
                if ($cafe->cafInfor) {
                    $cafe->cafInfor->update(['informacion' => $cafInforData['informacion'] ?? '']);
                } else {
                    $cafInfor = CafInfor::create(['numero_pagina' => 1, 'informacion' => $cafInforData['informacion'] ?? '']);
                    $cafe->update(['id_caf' => $cafInfor->id_caf]);
                }

                $cafInsumosData = $request->input('caf_insumos', []);
                if ($cafe->cafInsumos) {
                    $cafe->cafInsumos->update(['informacion' => $cafInsumosData['informacion'] ?? '']);
                } else {
                    $cafInsumos = CafInsumos::create(['numero_pagina' => 1, 'informacion' => $cafInsumosData['informacion'] ?? '']);
                    $cafe->update(['id_insumos' => $cafInsumos->id_insumos]);
                }

                $cafPatogeData = $request->input('caf_patoge', []);
                if ($cafe->cafPatoge) {
                    $cafe->cafPatoge->update([
                        'patogeno' => $cafPatogeData['patogeno'] ?? 'General',
                        'informacion' => $cafPatogeData['informacion'] ?? '']);
                } else {
                    $cafPatoge = CafPatoge::create([
                        'numero_pagina' => 1,
                        'patogeno' => $cafPatogeData['patogeno'] ?? 'General',
                        'informacion' => $cafPatogeData['informacion'] ?? '']);
                    $cafe->update(['id_patoge' => $cafPatoge->id_patoge]);
                }
            }

        } elseif ($producto->tipo === 'mora') {
            $mora = $producto->mora; // Obtiene el modelo Mora relacionado

            if ($mora) {
                $moraInfData = $request->input('mora_inf', []);
                if ($mora->moraInf) {
                    $mora->moraInf->update(['informacion' => $moraInfData['informacion'] ?? '']);
                } else {
                    $moraInf = MoraInf::create(['numero_pagina' => 1, 'informacion' => $moraInfData['informacion'] ?? '']);
                    $mora->update(['id_info' => $moraInf->id_info]);
                }

                $moraInsuData = $request->input('mora_insu', []);
                if ($mora->moraInsu) {
                    $mora->moraInsu->update(['informacion' => $moraInsuData['informacion'] ?? '']);
                } else {
                    $moraInsu = MoraInsu::create(['numero_pagina' => 1, 'informacion' => $moraInsuData['informacion'] ?? '']);
                    $mora->update(['id_insu' => $moraInsu->id_insu]);
                }

                $moraPatogeData = $request->input('mora_patoge', []);
                if ($mora->moraPatoge) {
                    $mora->moraPatoge->update([
                        'patogeno' => $moraPatogeData['patogeno'] ?? 'General',
                        'informacion' => $moraPatogeData['informacion'] ?? '',
                    ]);
                } else {
                    $moraPatoge = MoraPatoge::create([
                        'numero_pagina' => 1,
                        'patogeno' => $moraPatogeData['patogeno'] ?? 'General',
                        'informacion' => $moraPatogeData['informacion'] ?? '',
                    ]);
                    $mora->update(['id_pat' => $moraPatoge->id_pat]);
                }
            }
        }

        // Lógica para enviar email al operador
        if ($estadoCambiadoAPendiente) {
            $operadores = User::role('operador')->get();
            foreach ($operadores as $operador) {
                Mail::to($operador->email)->send(new NuevaRevisionPendienteMail($producto, 'Noticia'));
            }
        }

        return redirect()->route('productos.index')->with('success', 'Producto actualizado y enviado a revisión del operador.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Producto $producto)
    {
        // Autorización: El usuario debe tener permiso para ver este producto específico.
        $this->authorize('view', $producto);

        // Cargar las relaciones necesarias para mostrar los detalles
        $producto->load([
            'user', // Para mostrar quién lo creó
            'cafe.cafInfor',
            'cafe.cafInsumos',
            'cafe.cafPatoge',
            'mora.moraInf',
            'mora.moraInsu',
            'mora.moraPatoge',
            'validador',
            'rechazador',
        ]);
        /* dd($producto); */

        return view('productos.show', compact('producto'));
    }

    public function destroy(Producto $producto)
    {
        // Autorización: El usuario debe tener permiso para eliminar este producto específico.
        $this->authorize('delete', $producto);

        // Tu lógica de borrado (mantén la misma)
        $producto->delete();

        return redirect()->route('productos.index')->with('success', 'Producto eliminado.');
    }

    public function importarCSV(Request $request)
    {
        // Autorización: El usuario debe tener permiso para importar/crear productos.
        $this->authorize('import', Producto::class);

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

                // 2. Crear las relaciones anidadas según el tipo
                if ($tipo === 'café') {
                    // Crear los registros de detalle de Café
                    $cafInfor = CafInfor::create([
                        'numero_pagina' => 1,
                        'informacion' => $datosFila['caf_infor_informacion'] ?? '',
                    ]);

                    $cafInsumos = CafInsumos::create([
                        'numero_pagina' => 1,
                        'informacion' => $datosFila['caf_insumos_informacion'] ?? '',
                    ]);

                    $cafPatoge = CafPatoge::create([
                        'numero_pagina' => 1,
                        'patogeno' => $datosFila['caf_patoge_patogeno'] ?? 'General',
                        'informacion' => $datosFila['caf_patoge_informacion'] ?? '',
                    ]);

                    // Crear el registro en la tabla 'cafe' y vincularlo
                    Cafe::create([
                        'producto_id' => $producto->id,
                        'id_caf' => $cafInfor->id_caf,
                        'id_insumos' => $cafInsumos->id_insumos,
                        'id_patoge' => $cafPatoge->id_patoge,
                    ]);

                } elseif ($tipo === 'mora') {
                    // Crear los registros de detalle de Mora
                    $moraInf = MoraInf::create([
                        'numero_pagina' => 1,
                        'informacion' => $datosFila['mora_inf_informacion'] ?? '',
                    ]);

                    $moraInsu = MoraInsu::create([
                        'numero_pagina' => 1,
                        'informacion' => $datosFila['mora_insu_informacion'] ?? '',
                    ]);

                    $moraPatoge = MoraPatoge::create([
                        'numero_pagina' => 1,
                        'patogeno' => $datosFila['mora_patoge_patogeno'] ?? 'General',
                        'informacion' => $datosFila['mora_patoge_informacion'] ?? '',
                    ]);

                    // Crear el registro en la tabla 'mora' y vincularlo
                    Mora::create([
                        'producto_id' => $producto->id,
                        'id_info' => $moraInf->id_info,
                        'id_insu' => $moraInsu->id_insu,
                        'id_pat' => $moraPatoge->id_pat,
                    ]);
                }

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
        // Autorización: El usuario debe tener permiso para exportar productos.
        $this->authorize('export', Producto::class);

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