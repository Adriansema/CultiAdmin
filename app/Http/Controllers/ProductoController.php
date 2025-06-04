<?php

//actualizacion 09/04/2025

namespace App\Http\Controllers;

use App\Models\User; // Para buscar operadores
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
use Illuminate\Support\Facades\Mail; // Importa Mail
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response; // Importar la fachada Response para stream
use App\Mail\NuevaRevisionPendienteMail; // Importa la nueva Mailable

class ProductoController extends Controller
{
    public function index(Request $request, ProductService $productService)
    {
         // Llama al método del servicio para obtener los productos paginados
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

    public function store(Request $request)
    {
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
            // Asegúrate de que las relaciones existan antes de intentar acceder a ellas
            // Si un producto de tipo café no tiene un registro en 'cafe' o sus detalles, esto fallaría.
            // Esto debería estar cubierto por la lógica de 'store' al crear el producto.
            $cafe = $producto->cafe; // Obtiene el modelo Cafe relacionado

            if ($cafe) { // Verifica que el registro Cafe exista
                $cafInforData = $request->input('caf_infor', []);
                if ($cafe->cafInfor) {
                    $cafe->cafInfor->update(['informacion' => $cafInforData['informacion'] ?? '']);
                } else {
                    // Si no existe, créalo (esto no debería pasar si el store funciona bien)
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

            if ($mora) { // Verifica que el registro Mora exista
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
        $producto->delete();

        return redirect()->route('productos.index')->with('success', 'Producto eliminado.');
    }

    //importar archivo csv, para hacer automaticamente sin tener que escribir manualmente
    public function importarCSV(Request $request)
    {
        $request->validate([
            'archivo_csv' => 'required|file|mimes:csv,txt',
        ]);

        $archivo = $request->file('archivo_csv');
        $ruta = $archivo->getRealPath();

        $file = fopen($ruta, 'r');
        $encabezados = fgetcsv($file);

        // Campos técnicos (claves) y sus etiquetas legibles, desde configuración
        $claveLegible = config('claves_legibles');

        while (($fila = fgetcsv($file)) !== false) {
            $datos = array_combine($encabezados, $fila);

            // Construir el JSON con los campos que importan
            $detalles = [];
            foreach (array_keys($claveLegible) as $campo) {
                $detalles[$campo] = $datos[$campo] ?? '';
            }

            // Crear el producto con detalles JSON y otros campos
            Producto::create([
                'user_id' => Auth::id(),
                'detalles_json' => json_encode($detalles, JSON_UNESCAPED_UNICODE),
                'estado' => 'pendiente',
                'tipo' => $datos['tipo'] ?? 'sin_tipo',
                'observaciones' => $datos['observaciones'] ?? null,
                'imagen' => null, // no se importa desde CSV
            ]);
        }

        fclose($file);

        return redirect()->back()->with('success', 'Archivo CSV importado con éxito.');
    }

    public function exportarCSV(Request $request)
    {
        // 1. Obtener los parámetros de filtro de la solicitud
        // 'q' para una consulta general (ej. buscar por nombre o ID de producto)
        $query = $request->input('q');
        // 'estado' para filtrar por el estado del producto (ej. 'pendiente', 'aprobado')
        $estado = $request->input('estado');
        // 'user_id' para filtrar por el usuario que creó el producto
        $userIdFilter = $request->input('user_id');

        // 2. Iniciar la consulta Eloquent para el modelo Producto
        $productos = Producto::query();

        // 3. Aplicar los filtros dinámicamente a la consulta
        if ($query) {
            $productos->where(function ($q2) use ($query) {
                // Se cambió 'nombre' por 'tipo' ya que 'nombre' no existe en tu esquema.
                // Si 'tipo' no es el campo deseado para la búsqueda general,
                // deberías considerar añadir una columna de 'nombre' o 'titulo' a tu tabla.
                $q2->whereRaw('LOWER(tipo) LIKE ?', ['%' . strtolower($query) . '%'])
                   // También permite buscar por el ID del producto si la consulta es un número
                   ->orWhere('id', $query);
            });
        }

        if ($estado) {
            $productos->where('estado', $estado);
        }

        if ($userIdFilter) {
            $productos->where('user_id', $userIdFilter);
        }

        // 4. Obtener los productos que cumplen con los filtros
        // No se usa paginate() aquí porque queremos todos los resultados para la exportación.
        $productos = $productos->get();

        // 5. Generar un nombre de archivo único para el CSV
        $nombreArchivo = 'productos_' . now()->format('Y-m-d_H-i-s') . '.csv';

        // 6. Definir los encabezados HTTP necesarios para la descarga del archivo CSV
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$nombreArchivo\"",
        ];

        // 7. Definir los nombres de las columnas que aparecerán en la primera fila del CSV
        $columnas = [
            'ID Producto',
            'ID Usuario Creador',
            'Detalles (JSON)', // Se exportará el string JSON completo
            'Estado',
            'Observaciones',
            'Ruta Imagen', // Se exportará la ruta o URL de la imagen
            'Tipo de Producto',
            'Fecha de Creación'
        ];

        // 8. Definir la función de callback que generará el contenido del CSV
        // Esta función se ejecutará cuando Laravel transmita la respuesta.
        $callback = function () use ($productos, $columnas) {
            $file = fopen('php://output', 'w'); // Abrir el flujo de salida para escribir el CSV
            fputcsv($file, $columnas); // Escribir la fila de encabezados

            // Iterar sobre cada producto y escribir sus datos en el CSV
            foreach ($productos as $producto) {
                // Para 'detalles_json', se exporta el string JSON tal cual.
                // Si quisieras parsear el JSON y exportar campos específicos,
                // la lógica de parsing iría aquí antes de fputcsv.
                $detallesParaCSV = $producto->detalles_json;

                fputcsv($file, [
                    $producto->id,
                    $producto->user_id,
                    $detallesParaCSV,
                    $producto->estado,
                    $producto->observaciones,
                    $producto->imagen,
                    $producto->tipo,
                    // Formatear la fecha de creación; usar un string vacío si es nula
                    $producto->created_at ? $producto->created_at->format('Y-m-d H:i:s') : '',
                ]);
            }

            fclose($file); // Cerrar el archivo
        };

        // 9. Retornar la respuesta de streaming. Esto permite que el archivo CSV se genere
        // y se descargue sin cargar todos los datos en la memoria del servidor a la vez.
        return Response::stream($callback, 200, $headers);
    }

    public function generarCSV(Request $request)
    {
        $tipo = $request->input('tipo');

        // Obtenemos las claves ordenadas desde config
        $claveLegible = config('claves_legibles');
        $cabeceras = array_keys($claveLegible);

        // Datos de ejemplo por tipo
        $datosCafe = [
            'historia' => [
                "El café es una bebida milenaria, apreciada por su aroma envolvente y sabor inconfundible, resultado de un meticuloso proceso desde la semilla hasta la taza. Es símbolo de encuentro, tradición y cultura en innumerables regiones del mundo.",
                "Su historia se remonta al siglo IX en Etiopía, donde leyendas cuentan que un pastor notó el efecto energizante en sus cabras. A lo largo de los siglos, el cultivo se expandió por el mundo, dando forma a economías y paisajes enteros, especialmente en América Latina.",
            ],
            'productos_y_sus_características' => [
                "Las variedades más cultivadas son Arábica y Robusta, cada una con perfiles únicos. Arábica se distingue por su acidez brillante y aromas florales, mientras que Robusta ofrece un sabor más fuerte y mayor contenido de cafeína.",
                "Granos pequeños, forma ovalada y superficie lisa. Su color varía desde verde hasta marrón oscuro según el grado de tostado, cada etapa influye en el resultado final en la taza.",
                "Más allá de la bebida, el café se utiliza en cosméticos, aromaterapia y como componente en productos alimenticios innovadores.",
            ],
            'variantes' => [
                "Coffea arabica y Coffea canephora son las especies predominantes, con adaptaciones específicas a diferentes altitudes y climas que influyen directamente en el perfil sensorial del café.",
                "Coffea arabica",
            ],
            'enfermedades' => [
                "La roya y la broca del café son las principales amenazas, gestionadas mediante prácticas agrícolas sostenibles y manejo integrado de plagas.",
            ],
            'insumos' => [
                "Incluyen sombra controlada, podas regulares, fertilización orgánica y uso de variedades resistentes para mantener la productividad y calidad.",
                "Certificaciones como Fair Trade, Rainforest Alliance y orgánicas garantizan prácticas éticas y sostenibles en la producción.",
            ],
        ];

        $datosMora = [
            'historia' => [
                "La mora es un fruto silvestre con un intenso color y sabor dulce, apreciado por su versatilidad en gastronomía y beneficios para la salud, cultivada principalmente en zonas tropicales y subtropicales.",
                "Originaria de las regiones andinas, la mora ha sido parte fundamental de las culturas indígenas por siglos, utilizada en alimentación, medicina tradicional y rituales ancestrales.",
            ],
            'productos_y_sus_características' => [
                "Frutos jugosos, de forma redondeada y color oscuro intenso, con una pulpa rica en antioxidantes y vitaminas, perfectos para consumo fresco o procesado.",
                "Consumida fresca, en jugos, mermeladas y productos derivados como vinos y suplementos nutricionales.",
            ],
            'variantes' => [
                "Rubus glaucus y Rubus fruticosus son las especies más comunes, con características botánicas que determinan su sabor, tamaño y textura.",
                "Rubus glaucus",
            ],
            'enfermedades' => [
                "Pulgones y mildiu son las plagas más comunes, controladas mediante métodos biológicos y fitosanitarios adecuados.",
            ],
            'insumos' => [
                "Incluyen el uso de guías y espalderas, poda controlada y fertilización balanceada para maximizar rendimiento.",
                "Global GAP y certificaciones orgánicas respaldan la calidad y sostenibilidad del producto.",
            ],
        ];

        if ($tipo === 'café') {
            $datos = $datosCafe;
        } elseif ($tipo === 'mora') {
            $datos = $datosMora;
        } else {
            abort(404, 'Tipo no válido.');
        }

        return new \Symfony\Component\HttpFoundation\StreamedResponse(function () use ($cabeceras, $datos) {
            $handle = fopen('php://output', 'w');
            // Escribir encabezados
            fputcsv($handle, $cabeceras);

            // Calcular la cantidad máxima de filas para no perder líneas
            $maxFilas = 0;
            foreach ($datos as $campo => $filas) {
                $maxFilas = max($maxFilas, count($filas));
            }

            // Iterar fila a fila para armar cada línea del CSV
            for ($i = 0; $i < $maxFilas; $i++) {
                $filaCSV = [];
                foreach ($cabeceras as $campo) {
                    $filaCSV[] = $datos[$campo][$i] ?? '';
                }
                fputcsv($handle, $filaCSV);
            }

            fclose($handle);
        }, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="plantilla_producto_' . $tipo . '.csv"',
        ]);
    }
}
