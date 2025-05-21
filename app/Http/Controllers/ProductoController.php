<?php

//actualizacion 09/04/2025

namespace App\Http\Controllers;

use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductoController extends Controller
{
    public function index()
    {
        $productos = Producto::all();
        return view('productos.index', compact('productos'));
    }

    public function create()
    {
        return view('productos.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'detalles' => 'required|array',
            'tipo' => 'required|string',
            'imagen' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'observaciones' => 'nullable|string',
        ]);

        $imagen = null;
        if ($request->hasFile('imagen')) {
            $imagen = $request->file('imagen')->store('productos', 'public');
        }

        Producto::create([
            'user_id' => Auth::id(),
            'detalles_json' => json_encode($request->detalles, JSON_UNESCAPED_UNICODE),
            'estado' => 'pendiente',
            'observaciones' => $request->observaciones,
            'imagen' => $imagen,
            'tipo' => $request->tipo,
        ]);

        return redirect()->route('productos.index')->with('success', 'Producto creado con éxito.');
    }


    public function show(Producto $producto)
    {
        return view('productos.show', compact('producto'));
    }

    public function edit(Producto $producto)
    {
        return view('productos.edit', compact('producto'));
    }

    public function update(Request $request, Producto $producto)
    {
        $request->validate([
            'detalles' => 'required|array',
            'tipo' => 'required|string',
            'imagen' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'observaciones' => 'nullable|string',
        ]);

        // Actualizar imagen si viene una nueva
        if ($request->hasFile('imagen')) {
            // Opcional: eliminar imagen anterior si querés
            // Storage::disk('public')->delete($producto->imagen);

            $imagen = $request->file('imagen')->store('productos', 'public');
            $producto->imagen = $imagen;
        }

        // Actualizar los demás campos
        $producto->tipo = $request->tipo;
        $producto->detalles_json = json_encode($request->detalles, JSON_UNESCAPED_UNICODE);
        $producto->observaciones = $request->observaciones;

        $producto->save();

        return redirect()->route('productos.index')->with('success', 'Producto actualizado con éxito.');
    }


    public function destroy(Producto $producto)
    {
        $producto->delete();

        return redirect()->route('productos.index')->with('success', 'Producto eliminado.');
    }

    public function cafe()
    {
        // puedes pasar datos a la vista si necesitas
        return view('productos.cafe');
    }

    public function mora()
    {
        return view('productos.mora');
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
