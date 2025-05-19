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

        // Lista de campos que van dentro de detalles_json
        $camposDetalles = [
            'que_es', 'historia', 'variedad', 'especies', 'caracteristicas',
            'clima', 'suelo', 'riego', 'cosecha', 'postcosecha',
            'plagas', 'usos', 'valor_nutricional', 'impacto_economico',
            'tecnicas_cultivo', 'certificaciones', 'ubicacion_geografica',
            'nombre_cientifico',
        ];

        while (($fila = fgetcsv($file)) !== false) {
            $datos = array_combine($encabezados, $fila);

            // Armar array detalles_json
            $detalles = [];
            foreach ($camposDetalles as $campo) {
                $detalles[$campo] = $datos[$campo] ?? '';
            }

            // Crear producto
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

        $cabeceras = [
            'que_es', 'historia', 'variedad', 'especies', 'caracteristicas',
            'clima', 'suelo', 'riego', 'cosecha', 'postcosecha',
            'plagas', 'usos', 'valor_nutricional', 'impacto_economico',
            'tecnicas_cultivo', 'certificaciones', 'ubicacion_geografica',
            'nombre_cientifico', 'tipo', 'observaciones'
        ];

        $datosCafe = [
            [
                "El café es una bebida milenaria, apreciada por su aroma envolvente y sabor inconfundible, resultado de un meticuloso proceso desde la semilla hasta la taza. Es símbolo de encuentro, tradición y cultura en innumerables regiones del mundo.",
                "Su historia se remonta al siglo IX en Etiopía, donde leyendas cuentan que un pastor notó el efecto energizante en sus cabras. A lo largo de los siglos, el cultivo se expandió por el mundo, dando forma a economías y paisajes enteros, especialmente en América Latina.",
                "Las variedades más cultivadas son Arábica y Robusta, cada una con perfiles únicos. Arábica se distingue por su acidez brillante y aromas florales, mientras que Robusta ofrece un sabor más fuerte y mayor contenido de cafeína.",
                "Coffea arabica y Coffea canephora son las especies predominantes, con adaptaciones específicas a diferentes altitudes y climas que influyen directamente en el perfil sensorial del café.",
                "Granos pequeños, forma ovalada y superficie lisa. Su color varía desde verde hasta marrón oscuro según el grado de tostado, cada etapa influye en el resultado final en la taza.",
                "Prefiere climas templados entre 15°C y 24°C, con lluvias bien distribuidas. Las fluctuaciones extremas afectan negativamente su desarrollo y producción.",
                "Suelos volcánicos ricos en materia orgánica, con buen drenaje y pH ligeramente ácido, favorecen el crecimiento saludable de la planta.",
                "Requiere riego moderado y constante, evitando encharcamientos que pueden provocar enfermedades. Técnicas de riego por goteo son ideales para optimizar recursos.",
                "La cosecha se realiza manualmente para seleccionar solo los frutos maduros, generalmente entre abril y junio, asegurando la mejor calidad.",
                "Incluye el despulpado, fermentación, lavado, secado al sol y almacenamiento controlado, procesos cruciales para preservar las cualidades del grano.",
                "La roya y la broca del café son las principales amenazas, gestionadas mediante prácticas agrícolas sostenibles y manejo integrado de plagas.",
                "Más allá de la bebida, el café se utiliza en cosméticos, aromaterapia y como componente en productos alimenticios innovadores.",
                "Fuente moderada de antioxidantes y cafeína, contribuye a la energía y la concentración, con beneficios potenciales para la salud cuando se consume con moderación.",
                "Sector clave en la economía de países productores, genera empleo y desarrollo rural, aunque enfrenta desafíos por cambios climáticos y fluctuaciones del mercado.",
                "Incluyen sombra controlada, podas regulares, fertilización orgánica y uso de variedades resistentes para mantener la productividad y calidad.",
                "Certificaciones como Fair Trade, Rainforest Alliance y orgánicas garantizan prácticas éticas y sostenibles en la producción.",
                "Principalmente regiones montañosas entre 1200 y 1800 metros sobre el nivel del mar, con condiciones ideales en países como Colombia, Brasil y Etiopía.",
                "Coffea arabica",
                "café",
                "Cultivo tradicional con enfoque en sostenibilidad ambiental y social, buscando adaptarse a los retos del cambio climático."
            ],
        ];

        $datosMora = [
            [
                "La mora es un fruto silvestre con un intenso color y sabor dulce, apreciado por su versatilidad en gastronomía y beneficios para la salud, cultivada principalmente en zonas tropicales y subtropicales.",
                "Originaria de las regiones andinas, la mora ha sido parte fundamental de las culturas indígenas por siglos, utilizada en alimentación, medicina tradicional y rituales ancestrales.",
                "Las variedades Thornless Evergreen y Boysenberry destacan por su sabor y resistencia, adaptándose a diversos microclimas y suelos.",
                "Rubus glaucus y Rubus fruticosus son las especies más comunes, con características botánicas que determinan su sabor, tamaño y textura.",
                "Frutos jugosos, de forma redondeada y color oscuro intenso, con una pulpa rica en antioxidantes y vitaminas, perfectos para consumo fresco o procesado.",
                "Prefiere climas frescos y húmedos, con temperaturas entre 15°C y 22°C, y una distribución constante de lluvias durante el año.",
                "Suelos arcillosos y bien drenados, ricos en materia orgánica, son ideales para el desarrollo óptimo de la planta.",
                "Requiere riego frecuente pero evitando encharcamientos, con sistemas por goteo recomendados para optimizar el uso del agua.",
                "La recolección se realiza cuando los frutos alcanzan su color y tamaño óptimos, normalmente entre julio y septiembre, para garantizar su sabor y calidad.",
                "Incluye refrigeración inmediata y procesamiento rápido para preservar la frescura y evitar la fermentación indeseada.",
                "Pulgones y mildiu son las plagas más comunes, controladas mediante métodos biológicos y fitosanitarios adecuados.",
                "Consumida fresca, en jugos, mermeladas y productos derivados como vinos y suplementos nutricionales.",
                "Alta en vitamina C, fibra y antioxidantes naturales, aporta beneficios al sistema inmunológico y digestivo.",
                "Importante fuente de ingresos para comunidades rurales, con potencial creciente en mercados locales e internacionales.",
                "Incluyen el uso de guías y espalderas, poda controlada y fertilización balanceada para maximizar rendimiento.",
                "Global GAP y certificaciones orgánicas respaldan la calidad y sostenibilidad del producto.",
                "Zonas montañosas tropicales con altitudes entre 1800 y 2500 metros, como la región andina colombiana.",
                "Rubus glaucus",
                "mora",
                "Cultivo con gran potencial para exportación, enfocado en prácticas sostenibles y desarrollo rural."
            ],
        ];

        $datos = [];

        if ($tipo === 'café') {
            $datos = $datosCafe;
        } elseif ($tipo === 'mora') {
            $datos = $datosMora;
        } else {
            abort(404, 'Tipo no válido.');
        }

        $response = new \Symfony\Component\HttpFoundation\StreamedResponse(function () use ($cabeceras, $datos) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, $cabeceras);

            foreach ($datos as $fila) {
                fputcsv($handle, $fila);
            }

            fclose($handle);
        });

        $response->headers->set('Content-Type', 'text/csv; charset=UTF-8');
        $response->headers->set('Content-Disposition', 'attachment; filename="plantilla_producto_'.$tipo.'.csv"');

        return $response;
    }

}
