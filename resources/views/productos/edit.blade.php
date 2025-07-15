@extends('layouts.app')

@section('content')

    @can('editar producto')
        <div class="inline-block px-10 py-6">
            <div class="flex items-center space-x-4">
                <img src="{{ asset('images/reverse.svg') }}" class="w-4 h-4" alt="Icono Nuevo Usuario">
                {{-- CORRECCIÓN AQUÍ: "Editar de producto" -> "Edición de producto" --}}
                <h1 class="text-3xl whitespace-nowrap font-bold">Edición de producto</h1>
            </div>
            <div class="py-2">
                {!! Breadcrumbs::render('productos.edit', $producto) !!}
            </div>
        </div>

        <div class="container mx-auto p-4">
            @if ($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <strong class="font-bold">¡Oops!</strong>
                    <span class="block sm:inline">Hubo algunos problemas con tu entrada.</span>
                    <ul class="mt-3 list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('productos.update', $producto) }}" method="POST" enctype="multipart/form-data"
                class="bg-[var(-)] shadow-2xl rounded-lg p-8 mb-8 border border-gray-400"> {{-- Estilo de caja sobresaliente --}}
                @csrf
                @method('PUT') {{-- Método PUT para actualizaciones en Laravel --}}

                <div class="mb-6">
                    <label for="tipo" class="block text-gray-700 text-sm font-semibold mb-2">
                        Tipo de producto:
                    </label>
                    <div class="relative">
                        <input type="text" name="tipo" id="tipo"
                            class="block w-full py-3 px-4 text-gray-700 bg-gray-100 border border-gray-200 rounded-lg cursor-not-allowed
                               focus:outline-none focus:ring-1 focus:ring-gray-300 transition-all duration-200"
                            value="{{ $producto->tipo }}" readonly aria-describedby="tipo-help">
                        <span id="tipo-help" class="absolute top-1/2 right-4 -translate-y-1/2 text-gray-500 text-sm italic">
                            No editable
                        </span>
                    </div>
                    @error('tipo')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Nuevo div para agrupar Imagen y Observaciones (solo para cafe/mora) --}}
                <div id="campos_comunes_no_video_edit">
                    <div class="mb-4">
                        <label for="imagen" class="block text-gray-700 text-sm font-bold mb-2">Imagen Actual:</label>
                        @if ($producto->imagen)
                            <img src="{{ asset('storage/' . $producto->imagen) }}" alt="Imagen actual del producto"
                                class="w-32 h-32 object-cover rounded-lg mb-2">
                        @else
                            <p class="text-gray-600 text-sm mb-2">No hay imagen actual.</p>
                        @endif
                        <label for="nueva_imagen" class="block text-gray-700 text-sm font-bold mb-2">Subir nueva imagen:</label>
                        <input type="file" name="imagen" id="nueva_imagen"
                            class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none">
                        @error('imagen')
                            <p class="text-red-500 text-xs italic">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Campo RutaVideo para productos tipo cafe o mora (producto general) --}}
                <div id="campos_ruta_video_producto" class="hidden mt-6 pt-4 border-t border-gray-200">
                    <div class="mb-4">
                        <label for="rutavideo" class="block text-gray-700 text-sm font-bold mb-2">URL del video</label>
                        <input type="url" name="rutavideo" id="rutavideo"
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                            value="{{ old('rutavideo', $producto->rutavideo) }}"
                            placeholder="https://ejemplo.com/tu-video-general.mp4">
                        @error('rutavideo')
                            <p class="text-red-500 text-xs italic">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Campos específicos para cafe --}}
                @if ($producto->tipo === 'cafe')
                    <div id="campos_cafe" class="mt-6 pt-4 border-t border-gray-200">
                        <h2 class="text-xl font-semibold mb-3">Detalles de cafe</h2>
                        <div class="mb-4">
                            <label for="cafe_data_numero_pagina" class="block text-gray-700 text-sm font-bold mb-2">Número de
                                página:</label>
                            <input type="number" name="cafe_data[numero_pagina]" id="cafe_data_numero_pagina"
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                value="{{ old('cafe_data.numero_pagina', $producto->cafe->numero_pagina ?? '') }}">
                            @error('cafe_data.numero_pagina')
                                <p class="text-red-500 text-xs italic">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="mb-4">
                            <label for="cafe_data_clase" class="block text-gray-700 text-sm font-bold mb-2">Clase:</label>
                            <input type="text" name="cafe_data[clase]" id="cafe_data_clase"
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                value="{{ old('cafe_data.clase', $producto->cafe->clase ?? '') }}">
                            @error('cafe_data.clase')
                                <p class="text-red-500 text-xs italic">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="mb-4">
                            <label for="cafe_data_informacion" class="block text-gray-700 text-sm font-bold mb-2">Información de
                                cafe:</label>
                            <textarea name="cafe_data[informacion]" id="cafe_data_informacion" rows="5"
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">{{ old('cafe_data.informacion', $producto->cafe->informacion ?? '') }}</textarea>
                            @error('cafe_data.informacion')
                                <p class="text-red-500 text-xs italic">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                @endif

                {{-- Campos específicos para mora --}}
                @if ($producto->tipo === 'mora')
                    <div id="campos_mora" class="mt-6 pt-4 border-t border-gray-200">
                        <h2 class="text-xl font-semibold mb-3">Detalles de mora</h2>
                        <div class="mb-4">
                            <label for="mora_data_numero_pagina" class="block text-gray-700 text-sm font-bold mb-2">Número de
                                página:</label>
                            <input type="number" name="mora_data[numero_pagina]" id="mora_data_numero_pagina"
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                value="{{ old('mora_data.numero_pagina', $producto->mora->numero_pagina ?? '') }}">
                            @error('mora_data.numero_pagina')
                                <p class="text-red-500 text-xs italic">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="mb-4">
                            <label for="mora_data_clase" class="block text-gray-700 text-sm font-bold mb-2">Clase:</label>
                            <input type="text" name="mora_data[clase]" id="mora_data_clase"
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                value="{{ old('mora_data.clase', $producto->mora->clase ?? '') }}">
                            @error('mora_data.clase')
                                <p class="text-red-500 text-xs italic">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="mb-4">
                            <label for="mora_data_informacion" class="block text-gray-700 text-sm font-bold mb-2">Información
                                de mora:</label>
                            <textarea name="mora_data[informacion]" id="mora_data_informacion" rows="5"
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">{{ old('mora_data.informacion', $producto->mora->informacion ?? '') }}</textarea>
                            @error('mora_data.informacion')
                                <p class="text-red-500 text-xs italic">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                @endif

                {{-- Campos específicos para Videos --}}
                @if ($producto->tipo === 'videos')
                    <div id="campos_videos" class="mt-6 pt-4 border-t border-gray-200">
                        <h2 class="text-xl font-semibold mb-3">Detalles de video</h2>

                        {{-- Campo de selección de Subtipo de Video --}}
                        <div class="mb-4">
                            <label for="subtipo_video" class="block text-gray-700 text-sm font-bold mb-2">Tipo de video
                                (Subtipo):</label>
                            <select name="videos_data[tipo]" id="subtipo_video"
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('videos_data.tipo') border-red-500 @else border-gray-300 @enderror">

                                <option value="">Seleccione un subtipo</option>

                                <option value="educativos"
                                    {{ old('videos_data.tipo', $producto->videos->tipo ?? '') == 'educativos' ? 'selected' : '' }}>
                                    Video educativos</option>

                                <option value="recomendados"
                                    {{ old('videos_data.tipo', $producto->videos->tipo ?? '') == 'recomendados' ? 'selected' : '' }}>
                                    Video recomendados</option>

                                <option value="insumos_y_abonos"
                                    {{ old('videos_data.tipo', $producto->videos->tipo ?? '') == 'insumos_y_abonos' ? 'selected' : '' }}>
                                    Video insumos y abonos</option>

                                <option value="cuidados_generales"
                                    {{ old('videos_data.tipo', $producto->videos->tipo ?? '') == 'cuidados_generales' ? 'selected' : '' }}>
                                    Video cuidados generales</option>

                                <option value="preparacion_terreno_siembra"
                                    {{ old('videos_data.tipo', $producto->videos->tipo ?? '') == 'preparacion_terreno_siembra' ? 'selected' : '' }}>
                                    Video preparación terreno siembra</option>

                                <option value="sugerencias_generales"
                                    {{ old('videos_data.tipo', $producto->videos->tipo ?? '') == 'sugerencias_generales' ? 'selected' : '' }}>
                                    Video sugerencias generales</option>

                                <option value="metodos_recoleccion"
                                    {{ old('videos_data.tipo', $producto->videos->tipo ?? '') == 'metodos_recoleccion' ? 'selected' : '' }}>
                                    Video metodos recolección</option>

                                <option value="cuidados_cosecha"
                                    {{ old('videos_data.tipo', $producto->videos->tipo ?? '') == 'cuidados_cosecha' ? 'selected' : '' }}>
                                    Video cuidados cosecha</option>

                                <option value="buenas_practicas_agricolas"
                                    {{ old('videos_data.tipo', $producto->videos->tipo ?? '') == 'buenas_practicas_agricolas' ? 'selected' : '' }}>
                                    Video buenas prácticas agrícolas</option>
                            </select>
                            @error('videos_data.tipo')
                                <p class="text-red-500 text-xs italic">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Campos específicos para cada subtipo de video --}}
                        <div id="campos_subtipo_educativos" class="hidden mt-4 pt-4 border-t border-gray-200">
                            <h3 class="text-lg font-semibold mb-2">Campos para Video educativos</h3>
                            <div class="mb-4">
                                <label for="educativos_titulo" class="block text-gray-700 text-sm font-bold mb-2">Título: <span
                                        class="text-red-500">*</span></label>
                                <input type="text" name="videos_data[educativos][titulo]" id="educativos_titulo"
                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('videos_data.educativos.titulo') border-red-500 @else border-gray-300 @enderror"
                                    value="{{ old('videos_data.educativos.titulo', $producto->videos->tipo == 'educativos' ? $producto->videos->titulo ?? '' : '') }}">
                                @error('videos_data.educativos.titulo')
                                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="mb-4">
                                <label for="educativos_autor" class="block text-gray-700 text-sm font-bold mb-2">Autor: <span
                                        class="text-red-500">*</span></label>
                                <input type="text" name="videos_data[educativos][autor]" id="educativos_autor"
                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('videos_data.educativos.autor') border-red-500 @else border-gray-300 @enderror"
                                    value="{{ old('videos_data.educativos.autor', $producto->videos->tipo == 'educativos' ? $producto->videos->autor ?? '' : '') }}">
                                @error('videos_data.educativos.autor')
                                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="mb-4">
                                <label for="educativos_descripcion"
                                    class="block text-gray-700 text-sm font-bold mb-2">Descripción:<span
                                        class="text-red-500">*</span></label>
                                <textarea name="videos_data[educativos][descripcion]" id="educativos_descripcion" rows="5"
                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('videos_data.educativos.descripcion') border-red-500 @else border-gray-300 @enderror">{{ old('videos_data.educativos.descripcion', $producto->videos->tipo == 'educativos' ? $producto->videos->descripcion ?? '' : '') }}</textarea>
                                @error('videos_data.educativos.descripcion')
                                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="mb-4">
                                <label for="educativos_rutaVideo" class="block text-gray-700 text-sm font-bold mb-2">URL del
                                    video: <span class="text-red-500">*</span></label>
                                <input type="url" name="videos_data[educativos][rutaVideo]" id="educativos_rutaVideo"
                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('videos_data.educativos.rutaVideo') border-red-500 @else border-gray-300 @enderror"
                                    value="{{ old('videos_data.educativos.rutaVideo', $producto->videos->tipo == 'educativos' ? $producto->videos->rutaVideo ?? '' : '') }}"
                                    placeholder="https://ejemplo.com/tu-video-primario.mp4">
                                @error('videos_data.educativos.rutaVideo')
                                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div id="campos_subtipo_recomendados" class="hidden mt-4 pt-4 border-t border-gray-200">
                            <h3 class="text-lg font-semibold mb-2">Campos para video recomendados</h3>
                            <div class="mb-4">
                                <label for="recomendados_titulo" class="block text-gray-700 text-sm font-bold mb-2">Título:
                                    <span class="text-red-500">*</span></label>
                                <input type="text" name="videos_data[recomendados][titulo]" id="recomendados_titulo"
                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('videos_data.recomendados.titulo') border-red-500 @else border-gray-300 @enderror"
                                    value="{{ old('videos_data.recomendados.titulo', $producto->videos->tipo == 'recomendados' ? $producto->videos->titulo ?? '' : '') }}">
                                @error('videos_data.recomendados.titulo')
                                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="mb-4">
                                <label for="recomendados_autor" class="block text-gray-700 text-sm font-bold mb-2">Autor: <span
                                        class="text-red-500">*</span></label>
                                <input type="text" name="videos_data[recomendados][autor]" id="recomendados_autor"
                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('videos_data.recomendados.autor') border-red-500 @else border-gray-300 @enderror"
                                    value="{{ old('videos_data.recomendados.autor', $producto->videos->tipo == 'recomendados' ? $producto->videos->autor ?? '' : '') }}">
                                @error('videos_data.recomendados.autor')
                                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="mb-4">
                                <label for="recomendados_descripcion"
                                    class="block text-gray-700 text-sm font-bold mb-2">Descripción:<span
                                        class="text-red-500">*</span></label>
                                <textarea name="videos_data[recomendados][descripcion]" id="recomendados_descripcion" rows="5"
                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('videos_data.recomendados.descripcion') border-red-500 @else border-gray-300 @enderror">{{ old('videos_data.recomendados.descripcion', $producto->videos->tipo == 'recomendados' ? $producto->videos->descripcion ?? '' : '') }}</textarea>
                                @error('videos_data.recomendados.descripcion')
                                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="mb-4">
                                <label for="recomendados_rutaVideo" class="block text-gray-700 text-sm font-bold mb-2">URL del
                                    video: <span class="text-red-500">*</span></label>
                                <input type="url" name="videos_data[recomendados][rutaVideo]" id="recomendados_rutaVideo"
                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('videos_data.recomendados.rutaVideo') border-red-500 @else border-gray-300 @enderror"
                                    value="{{ old('videos_data.recomendados.rutaVideo', $producto->videos->tipo == 'recomendados' ? $producto->videos->rutaVideo ?? '' : '') }}"
                                    placeholder="https://ejemplo.com/tu-video-secundario.mp4">
                                @error('videos_data.recomendados.rutaVideo')
                                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div id="campos_subtipo_insumos_y_abonos" class="hidden mt-4 pt-4 border-t border-gray-200">
                            {{-- CORRECCIÓN: "insumos_y_abonos" -> "Insumos y Abonos" --}}
                            <h3 class="text-lg font-semibold mb-2">Campos para Video Insumos y Abonos</h3>
                            <div class="mb-4">
                                <label for="insumos_y_abonos_titulo"
                                    class="block text-gray-700 text-sm font-bold mb-2">Título: <span
                                        class="text-red-500">*</span></label>
                                <input type="text" name="videos_data[insumos_y_abonos][titulo]"
                                    id="insumos_y_abonos_titulo"
                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('videos_data.insumos_y_abonos.titulo') border-red-500 @else border-gray-300 @enderror"
                                    value="{{ old('videos_data.insumos_y_abonos.titulo', $producto->videos->tipo == 'insumos_y_abonos' ? $producto->videos->titulo ?? '' : '') }}">
                                @error('videos_data.insumos_y_abonos.titulo')
                                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="mb-4">
                                <label for="insumos_y_abonos_autor" class="block text-gray-700 text-sm font-bold mb-2">Autor:
                                    <span class="text-red-500">*</span></label>
                                <input type="text" name="videos_data[insumos_y_abonos][autor]" id="insumos_y_abonos_autor"
                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('videos_data.insumos_y_abonos.autor') border-red-500 @else border-gray-300 @enderror"
                                    value="{{ old('videos_data.insumos_y_abonos.autor', $producto->videos->tipo == 'insumos_y_abonos' ? $producto->videos->autor ?? '' : '') }}">
                                @error('videos_data.insumos_y_abonos.autor')
                                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="mb-4">
                                <label for="insumos_y_abonos_descripcion"
                                    class="block text-gray-700 text-sm font-bold mb-2">Descripción:<span
                                        class="text-red-500">*</span></label>
                                <textarea name="videos_data[insumos_y_abonos][descripcion]" id="insumos_y_abonos_descripcion" rows="5"
                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('videos_data.insumos_y_abonos.descripcion') border-red-500 @else border-gray-300 @enderror">{{ old('videos_data.insumos_y_abonos.descripcion', $producto->videos->tipo == 'insumos_y_abonos' ? $producto->videos->descripcion ?? '' : '') }}</textarea>
                                @error('videos_data.insumos_y_abonos.descripcion')
                                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="mb-4">
                                <label for="insumos_y_abonos_rutaVideo" class="block text-gray-700 text-sm font-bold mb-2">URL
                                    del
                                    video: <span class="text-red-500">*</span></label>
                                <input type="url" name="videos_data[insumos_y_abonos][rutaVideo]"
                                    id="insumos_y_abonos_rutaVideo"
                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('videos_data.insumos_y_abonos.rutaVideo') border-red-500 @else border-gray-300 @enderror"
                                    value="{{ old('videos_data.insumos_y_abonos.rutaVideo', $producto->videos->tipo == 'insumos_y_abonos' ? $producto->videos->rutaVideo ?? '' : '') }}"
                                    {{-- CORRECCIÓN: Placeholder más específico --}} placeholder="https://ejemplo.com/tu-video-insumos-y-abonos.mp4">
                                @error('videos_data.insumos_y_abonos.rutaVideo')
                                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div id="campos_subtipo_cuidados_generales" class="hidden mt-4 pt-4 border-t border-gray-200">
                            {{-- CORRECCIÓN: "cuidados_generales" -> "Cuidados Generales" --}}
                            <h3 class="text-lg font-semibold mb-2">Campos para Video Cuidados Generales</h3>
                            <div class="mb-4">
                                <label for="cuidados_generales_titulo"
                                    class="block text-gray-700 text-sm font-bold mb-2">Título: <span
                                        class="text-red-500">*</span></label>
                                <input type="text" name="videos_data[cuidados_generales][titulo]"
                                    id="cuidados_generales_titulo"
                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('videos_data.cuidados_generales.titulo') border-red-500 @else border-gray-300 @enderror"
                                    value="{{ old('videos_data.cuidados_generales.titulo', $producto->videos->tipo == 'cuidados_generales' ? $producto->videos->titulo ?? '' : '') }}">
                                @error('videos_data.cuidados_generales.titulo')
                                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="mb-4">
                                <label for="cuidados_generales_autor"
                                    class="block text-gray-700 text-sm font-bold mb-2">Autor: <span
                                        class="text-red-500">*</span></label>
                                <input type="text" name="videos_data[cuidados_generales][autor]"
                                    id="cuidados_generales_autor"
                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('videos_data.cuidados_generales.autor') border-red-500 @else border-gray-300 @enderror"
                                    value="{{ old('videos_data.cuidados_generales.autor', $producto->videos->tipo == 'cuidados_generales' ? $producto->videos->autor ?? '' : '') }}">
                                @error('videos_data.cuidados_generales.autor')
                                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="mb-4">
                                <label for="cuidados_generales_descripcion"
                                    class="block text-gray-700 text-sm font-bold mb-2">Descripción:<span
                                        class="text-red-500">*</span></label>
                                <textarea name="videos_data[cuidados_generales][descripcion]" id="cuidados_generales_descripcion" rows="5"
                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('videos_data.cuidados_generales.descripcion') border-red-500 @else border-gray-300 @enderror">{{ old('videos_data.cuidados_generales.descripcion', $producto->videos->tipo == 'cuidados_generales' ? $producto->videos->descripcion ?? '' : '') }}</textarea>
                                @error('videos_data.cuidados_generales.descripcion')
                                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="mb-4">
                                <label for="cuidados_generales_rutaVideo"
                                    class="block text-gray-700 text-sm font-bold mb-2">URL del
                                    video: <span class="text-red-500">*</span></label>
                                <input type="url" name="videos_data[cuidados_generales][rutaVideo]"
                                    id="cuidados_generales_rutaVideo"
                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('videos_data.cuidados_generales.rutaVideo') border-red-500 @else border-gray-300 @enderror"
                                    value="{{ old('videos_data.cuidados_generales.rutaVideo', $producto->videos->tipo == 'cuidados_generales' ? $producto->videos->rutaVideo ?? '' : '') }}"
                                    placeholder="https://ejemplo.com/tu-video-cuidados-generales.mp4">
                                @error('videos_data.cuidados_generales.rutaVideo')
                                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div id="campos_subtipo_preparacion_terreno_siembra"
                            class="hidden mt-4 pt-4 border-t border-gray-200">
                            <h3 class="text-lg font-semibold mb-2">Campos para video preparación del erreno y siembra</h3>
                            <div class="mb-4">
                                <label for="preparacion_terreno_siembra_titulo"
                                    class="block text-gray-700 text-sm font-bold mb-2">Título:
                                    <span class="text-red-500">*</span></label>
                                <input type="text" name="videos_data[preparacion_terreno_siembra][titulo]"
                                    id="preparacion_terreno_siembra_titulo"
                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('videos_data.preparacion_terreno_siembra.titulo') border-red-500 @else border-gray-300 @enderror"
                                    value="{{ old('videos_data.preparacion_terreno_siembra.titulo', $producto->videos->tipo == 'preparacion_terreno_siembra' ? $producto->videos->titulo ?? '' : '') }}">
                                @error('videos_data.preparacion_terreno_siembra.titulo')
                                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="mb-4">
                                <label for="preparacion_terreno_siembra_autor"
                                    class="block text-gray-700 text-sm font-bold mb-2">Autor:
                                    <span class="text-red-500">*</span></label>
                                <input type="text" name="videos_data[preparacion_terreno_siembra][autor]"
                                    id="preparacion_terreno_siembra_autor"
                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('videos_data.preparacion_terreno_siembra.autor') border-red-500 @else border-gray-300 @enderror"
                                    value="{{ old('videos_data.preparacion_terreno_siembra.autor', $producto->videos->tipo == 'preparacion_terreno_siembra' ? $producto->videos->autor ?? '' : '') }}">
                                @error('videos_data.preparacion_terreno_siembra.autor')
                                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="mb-4">
                                <label for="preparacion_terreno_siembra_descripcion"
                                    class="block text-gray-700 text-sm font-bold mb-2">Descripción:<span
                                        class="text-red-500">*</span></label>
                                <textarea name="videos_data[preparacion_terreno_siembra][descripcion]" id="preparacion_terreno_siembra_descripcion"
                                    rows="5"
                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('videos_data.preparacion_terreno_siembra.descripcion') border-red-500 @else border-gray-300 @enderror">{{ old('videos_data.preparacion_terreno_siembra.descripcion', $producto->videos->tipo == 'preparacion_terreno_siembra' ? $producto->videos->descripcion ?? '' : '') }}</textarea>
                                @error('videos_data.preparacion_terreno_siembra.descripcion')
                                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="mb-4">
                                <label for="preparacion_terreno_siembra_rutaVideo"
                                    class="block text-gray-700 text-sm font-bold mb-2">URL del
                                    video: <span class="text-red-500">*</span></label>
                                <input type="url" name="videos_data[preparacion_terreno_siembra][rutaVideo]"
                                    id="preparacion_terreno_siembra_rutaVideo"
                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('videos_data.preparacion_terreno_siembra.rutaVideo') border-red-500 @else border-gray-300 @enderror"
                                    value="{{ old('videos_data.preparacion_terreno_siembra.rutaVideo', $producto->videos->tipo == 'preparacion_terreno_siembra' ? $producto->videos->rutaVideo ?? '' : '') }}"
                                    placeholder="https://ejemplo.com/tu-video-preparación-terreno.mp4">
                                @error('videos_data.preparacion_terreno_siembra.rutaVideo')
                                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div id="campos_subtipo_sugerencias_generales" class="hidden mt-4 pt-4 border-t border-gray-200">
                            <h3 class="text-lg font-semibold mb-2">Campos para video sugerencias generales</h3>
                            <div class="mb-4">
                                <label for="sugerencias_generales_titulo"
                                    class="block text-gray-700 text-sm font-bold mb-2">Título: <span
                                        class="text-red-500">*</span></label>
                                <input type="text" name="videos_data[sugerencias_generales][titulo]"
                                    id="sugerencias_generales_titulo"
                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('videos_data.sugerencias_generales.titulo') border-red-500 @else border-gray-300 @enderror"
                                    value="{{ old('videos_data.sugerencias_generales.titulo', $producto->videos->tipo == 'sugerencias_generales' ? $producto->videos->titulo ?? '' : '') }}">
                                @error('videos_data.sugerencias_generales.titulo')
                                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="mb-4">
                                <label for="sugerencias_generales_autor"
                                    class="block text-gray-700 text-sm font-bold mb-2">Autor: <span
                                        class="text-red-500">*</span></label>
                                <input type="text" name="videos_data[sugerencias_generales][autor]"
                                    id="sugerencias_generales_autor"
                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('videos_data.sugerencias_generales.autor') border-red-500 @else border-gray-300 @enderror"
                                    value="{{ old('videos_data.sugerencias_generales.autor', $producto->videos->tipo == 'sugerencias_generales' ? $producto->videos->autor ?? '' : '') }}">
                                @error('videos_data.sugerencias_generales.autor')
                                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="mb-4">
                                <label for="sugerencias_generales_descripcion"
                                    class="block text-gray-700 text-sm font-bold mb-2">Descripción:<span
                                        class="text-red-500">*</span></label>
                                <textarea name="videos_data[sugerencias_generales][descripcion]" id="sugerencias_generales_descripcion"
                                    rows="5"
                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('videos_data.sugerencias_generales.descripcion') border-red-500 @else border-gray-300 @enderror">{{ old('videos_data.sugerencias_generales.descripcion', $producto->videos->tipo == 'sugerencias_generales' ? $producto->videos->descripcion ?? '' : '') }}</textarea>
                                @error('videos_data.sugerencias_generales.descripcion')
                                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="mb-4">
                                <label for="sugerencias_generales_rutaVideo"
                                    class="block text-gray-700 text-sm font-bold mb-2">URL del
                                    video: <span class="text-red-500">*</span></label>
                                <input type="url" name="videos_data[sugerencias_generales][rutaVideo]"
                                    id="sugerencias_generales_rutaVideo"
                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('videos_data.sugerencias_generales.rutaVideo') border-red-500 @else border-gray-300 @enderror"
                                    value="{{ old('videos_data.sugerencias_generales.rutaVideo', $producto->videos->tipo == 'sugerencias_generales' ? $producto->videos->rutaVideo ?? '' : '') }}"
                                    placeholder="https://ejemplo.com/tu-video-sugerencias-generales.mp4">
                                @error('videos_data.sugerencias_generales.rutaVideo')
                                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div id="campos_subtipo_metodos_recoleccion" class="hidden mt-4 pt-4 border-t border-gray-200">
                            <h3 class="text-lg font-semibold mb-2">Campos para video métodos de recolección</h3>
                            <div class="mb-4">
                                <label for="metodos_recoleccion_titulo"
                                    class="block text-gray-700 text-sm font-bold mb-2">Título: <span
                                        class="text-red-500">*</span></label>
                                <input type="text" name="videos_data[metodos_recoleccion][titulo]"
                                    id="metodos_recoleccion_titulo"
                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('videos_data.metodos_recoleccion.titulo') border-red-500 @else border-gray-300 @enderror"
                                    value="{{ old('videos_data.metodos_recoleccion.titulo', $producto->videos->tipo == 'metodos_recoleccion' ? $producto->videos->titulo ?? '' : '') }}">
                                @error('videos_data.metodos_recoleccion.titulo')
                                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="mb-4">
                                <label for="metodos_recoleccion_autor"
                                    class="block text-gray-700 text-sm font-bold mb-2">Autor: <span
                                        class="text-red-500">*</span></label>
                                <input type="text" name="videos_data[metodos_recoleccion][autor]"
                                    id="metodos_recoleccion_autor"
                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('videos_data.metodos_recoleccion.autor') border-red-500 @else border-gray-300 @enderror"
                                    value="{{ old('videos_data.metodos_recoleccion.autor', $producto->videos->tipo == 'metodos_recoleccion' ? $producto->videos->autor ?? '' : '') }}">
                                @error('videos_data.metodos_recoleccion.autor')
                                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="mb-4">
                                <label for="metodos_recoleccion_descripcion"
                                    class="block text-gray-700 text-sm font-bold mb-2">Descripción:<span
                                        class="text-red-500">*</span></label>
                                <textarea name="videos_data[metodos_recoleccion][descripcion]" id="metodos_recoleccion_descripcion" rows="5"
                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('videos_data.metodos_recoleccion.descripcion') border-red-500 @else border-gray-300 @enderror">{{ old('videos_data.metodos_recoleccion.descripcion', $producto->videos->tipo == 'metodos_recoleccion' ? $producto->videos->descripcion ?? '' : '') }}</textarea>
                                @error('videos_data.metodos_recoleccion.descripcion')
                                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="mb-4">
                                <label for="metodos_recoleccion_rutaVideo"
                                    class="block text-gray-700 text-sm font-bold mb-2">URL del
                                    video: <span class="text-red-500">*</span></label>
                                <input type="url" name="videos_data[metodos_recoleccion][rutaVideo]"
                                    id="metodos_recoleccion_rutaVideo"
                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('videos_data.metodos_recoleccion.rutaVideo') border-red-500 @else border-gray-300 @enderror"
                                    value="{{ old('videos_data.metodos_recoleccion.rutaVideo', $producto->videos->tipo == 'metodos_recoleccion' ? $producto->videos->rutaVideo ?? '' : '') }}"
                                    placeholder="https://ejemplo.com/tu-video-métodos-recolección.mp4">
                                @error('videos_data.metodos_recoleccion.rutaVideo')
                                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div id="campos_subtipo_cuidados_cosecha" class="hidden mt-4 pt-4 border-t border-gray-200">
                            <h3 class="text-lg font-semibold mb-2">Campos para video cuidados de cosecha</h3>
                            <div class="mb-4">
                                <label for="cuidados_cosecha_titulo"
                                    class="block text-gray-700 text-sm font-bold mb-2">Título: <span
                                        class="text-red-500">*</span></label>
                                <input type="text" name="videos_data[cuidados_cosecha][titulo]"
                                    id="cuidados_cosecha_titulo"
                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('videos_data.cuidados_cosecha.titulo') border-red-500 @else border-gray-300 @enderror"
                                    value="{{ old('videos_data.cuidados_cosecha.titulo', $producto->videos->tipo == 'cuidados_cosecha' ? $producto->videos->titulo ?? '' : '') }}">
                                @error('videos_data.cuidados_cosecha.titulo')
                                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="mb-4">
                                <label for="cuidados_cosecha_autor" class="block text-gray-700 text-sm font-bold mb-2">Autor:
                                    <span class="text-red-500">*</span></label>
                                <input type="text" name="videos_data[cuidados_cosecha][autor]" id="cuidados_cosecha_autor"
                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('videos_data.cuidados_cosecha.autor') border-red-500 @else border-gray-300 @enderror"
                                    value="{{ old('videos_data.cuidados_cosecha.autor', $producto->videos->tipo == 'cuidados_cosecha' ? $producto->videos->autor ?? '' : '') }}">
                                @error('videos_data.cuidados_cosecha.autor')
                                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="mb-4">
                                <label for="cuidados_cosecha_descripcion"
                                    class="block text-gray-700 text-sm font-bold mb-2">Descripción:<span
                                        class="text-red-500">*</span></label>
                                <textarea name="videos_data[cuidados_cosecha][descripcion]" id="cuidados_cosecha_descripcion" rows="5"
                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('videos_data.cuidados_cosecha.descripcion') border-red-500 @else border-gray-300 @enderror">{{ old('videos_data.cuidados_cosecha.descripcion', $producto->videos->tipo == 'cuidados_cosecha' ? $producto->videos->descripcion ?? '' : '') }}</textarea>
                                @error('videos_data.cuidados_cosecha.descripcion')
                                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="mb-4">
                                <label for="cuidados_cosecha_rutaVideo" class="block text-gray-700 text-sm font-bold mb-2">URL
                                    del
                                    video: <span class="text-red-500">*</span></label>
                                <input type="url" name="videos_data[cuidados_cosecha][rutaVideo]"
                                    id="cuidados_cosecha_rutaVideo"
                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('videos_data.cuidados_cosecha.rutaVideo') border-red-500 @else border-gray-300 @enderror"
                                    value="{{ old('videos_data.cuidados_cosecha.rutaVideo', $producto->videos->tipo == 'cuidados_cosecha' ? $producto->videos->rutaVideo ?? '' : '') }}"
                                    placeholder="https://ejemplo.com/tu-video-cuidados-cosecha.mp4">
                                @error('videos_data.cuidados_cosecha.rutaVideo')
                                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div id="campos_subtipo_buenas_practicas_agricolas" class="hidden mt-4 pt-4 border-t border-gray-200">
                            <h3 class="text-lg font-semibold mb-2">Campos para video buenas prácticas agrícolas</h3>
                            <div class="mb-4">
                                <label for="buenas_practicas_agricolas_titulo"
                                    class="block text-gray-700 text-sm font-bold mb-2">Título:
                                    <span class="text-red-500">*</span></label>
                                <input type="text" name="videos_data[buenas_practicas_agricolas][titulo]"
                                    id="buenas_practicas_agricolas_titulo"
                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('videos_data.buenas_practicas_agricolas.titulo') border-red-500 @else border-gray-300 @enderror"
                                    value="{{ old('videos_data.buenas_practicas_agricolas.titulo', $producto->videos->tipo == 'buenas_practicas_agricolas' ? $producto->videos->titulo ?? '' : '') }}">
                                @error('videos_data.buenas_practicas_agricolas.titulo')
                                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="mb-4">
                                <label for="buenas_practicas_agricolas_autor"
                                    class="block text-gray-700 text-sm font-bold mb-2">Autor:
                                    <span class="text-red-500">*</span></label>
                                <input type="text" name="videos_data[buenas_practicas_agricolas][autor]"
                                    id="buenas_practicas_agricolas_autor"
                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('videos_data.buenas_practicas_agricolas.autor') border-red-500 @else border-gray-300 @enderror"
                                    value="{{ old('videos_data.buenas_practicas_agricolas.autor', $producto->videos->tipo == 'buenas_practicas_agricolas' ? $producto->videos->autor ?? '' : '') }}">
                                @error('videos_data.buenas_practicas_agricolas.autor')
                                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="mb-4">
                                <label for="buenas_practicas_agricolas_descripcion"
                                    class="block text-gray-700 text-sm font-bold mb-2">Descripción:<span
                                        class="text-red-500">*</span></label>
                                <textarea name="videos_data[buenas_practicas_agricolas][descripcion]" id="buenas_practicas_agricolas_descripcion"
                                    rows="5"
                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('videos_data.buenas_practicas_agricolas.descripcion') border-red-500 @else border-gray-300 @enderror">{{ old('videos_data.buenas_practicas_agricolas.descripcion', $producto->videos->tipo == 'buenas_practicas_agricolas' ? $producto->videos->descripcion ?? '' : '') }}</textarea>
                                @error('videos_data.buenas_practicas_agricolas.descripcion')
                                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="mb-4">
                                <label for="buenas_practicas_agricolas_rutaVideo"
                                    class="block text-gray-700 text-sm font-bold mb-2">URL del
                                    video: <span class="text-red-500">*</span></label>
                                <input type="url" name="videos_data[buenas_practicas_agricolas][rutaVideo]"
                                    id="buenas_practicas_agricolas_rutaVideo"
                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('videos_data.buenas_practicas_agricolas.rutaVideo') border-red-500 @else border-gray-300 @enderror"
                                    value="{{ old('videos_data.buenas_practicas_agricolas.rutaVideo', $producto->videos->tipo == 'buenas_practicas_agricolas' ? $producto->videos->rutaVideo ?? '' : '') }}"
                                    placeholder="https://ejemplo.com/tu-video-bpa.mp4">
                                @error('videos_data.buenas_practicas_agricolas.rutaVideo')
                                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                @endif

                <div class="flex items-center justify-between mt-6">
                    <a href="{{ route('productos.index') }}"
                        class="bg-[var(--color-textmarca)] hover:bg-[var(--color-texthovermarca)] py-3 px-4 rounded-full text-md font-bold text-white focus:outline-none focus:shadow-outline inline-flex items-center transition duration-150 ease-in-out transform hover:-translate-x-1 shadow-md">
                        <img src="{{ asset('images/regresar.svg') }}" alt="Regresar" class="w-5 h-6 mr-2">
                        <span class="whitespace-nowrap text-inherit">{{ __('Cancelar') }}</span>
                    </a>
                    <button type="submit"
                        class="bg-[var(--color-sgt)] hover:bg-[var(--color-hoversgt)] py-3 px-4 rounded-full text-md font-bold text-white focus:outline-none focus:shadow-outline inline-flex items-center transition duration-150 ease-in-out transform hover:translate-x-1 shadow-md">
                        <span class="whitespace-nowrap text-inherit">{{ __('Actualizar producto') }}</span>
                        <img src="{{ asset('images/siguiente.svg') }}" alt="siguiente" class="w-5 h-6 ml-2">
                    </button>
                </div>
            </form>
        </div>
    @endcan

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const tipoInput = document.getElementById('tipo');
            const camposComunesNoVideoEdit = document.getElementById('campos_comunes_no_video_edit');
            const camposCafe = document.getElementById('campos_cafe');
            const camposMora = document.getElementById('campos_mora');
            const camposVideos = document.getElementById('campos_videos');
            const camposRutaVideoproducto = document.getElementById('campos_ruta_video_producto');

            const subtipoVideoSelect = document.getElementById('subtipo_video');

            // Función para normalizar el nombre del subtipo a un ID HTML válido
            function normalizeSubtypeForId(subtypeName) {
                // Reemplaza espacios y caracteres especiales (excepto _ y letras/números) por guiones bajos
                return subtypeName.replace(/[^a-zA-Z0-9_]/g, '_');
            }

            // Seleccionar todos los divs de subtipo de video de una vez.
            const allSubtypeFields = document.querySelectorAll(
                '#campos_subtipo_educativos, ' +
                '#campos_subtipo_recomendados, ' +
                '#campos_subtipo_insumos_y_abonos, ' +
                '#campos_subtipo_cuidados_generales, ' +
                '#campos_subtipo_preparacion_terreno_siembra, ' +
                '#campos_subtipo_sugerencias_generales, ' +
                '#campos_subtipo_metodos_recoleccion, ' +
                '#campos_subtipo_cuidados_cosecha, ' +
                '#campos_subtipo_buenas_practicas_agricolas'
            );

            /**
             * Muestra/oculta los campos específicos del producto (cafe, Mora, Videos)
             * y el campo RutaVideo del producto general.
             */
            function toggleProductFields() {
                const selectedType = tipoInput.value; // Usamos el valor del input readonly

                // Ocultar todos los campos específicos y el RutaVideo del producto general
                if (camposCafe) camposCafe.classList.add('hidden');
                if (camposMora) camposMora.classList.add('hidden');
                if (camposVideos) camposVideos.classList.add('hidden');
                if (camposRutaVideoproducto) camposRutaVideoproducto.classList.add('hidden');

                // Ocultar los campos comunes no-video por defecto (Imagen y Observaciones)
                if (camposComunesNoVideoEdit) camposComunesNoVideoEdit.classList.add('hidden');

                // Ocultar todos los campos de subtipo de video por defecto
                allSubtypeFields.forEach(field => {
                    field.classList.add('hidden');
                });

                if (selectedType === 'cafe') {
                    if (camposComunesNoVideoEdit) camposComunesNoVideoEdit.classList.remove(
                        'hidden'); // Mostrar para cafe
                    if (camposCafe) camposCafe.classList.remove('hidden');
                    if (camposRutaVideoproducto) camposRutaVideoproducto.classList.remove(
                        'hidden'); // Mostrar RutaVideo para cafe
                } else if (selectedType === 'mora') {
                    if (camposComunesNoVideoEdit) camposComunesNoVideoEdit.classList.remove(
                        'hidden'); // Mostrar para Mora
                    if (camposMora) camposMora.classList.remove('hidden');
                    if (camposRutaVideoproducto) camposRutaVideoproducto.classList.remove(
                        'hidden'); // Mostrar RutaVideo para Mora
                } else if (selectedType === 'videos') {
                    // camposComunesNoVideoEdit se mantiene oculto para videos
                    if (camposVideos) camposVideos.classList.remove('hidden');
                    // No mostrar camposRutaVideoproducto para 'videos'
                    // Y llamar a la función para mostrar/ocultar subtipos de video
                    toggleSubtypeFields();
                } else {
                    // Si no se ha seleccionado ningún tipo, ocultar todo
                    if (camposComunesNoVideoEdit) camposComunesNoVideoEdit.classList.add('hidden');
                }
            }

            /**
             * Muestra/oculta los campos específicos del subtipo de video.
             */
            function toggleSubtypeFields() {
                // Solo ejecutar si el select de subtipo de video existe
                if (!subtipoVideoSelect) return;

                const selectedSubtype = subtipoVideoSelect.value;

                // Ocultar todos los campos de subtipo de video
                allSubtypeFields.forEach(field => {
                    field.classList.add('hidden');
                });

                // Mostrar el div correspondiente al subtipo seleccionado, si existe
                if (selectedSubtype) {
                    // Normaliza el valor seleccionado para construir el ID esperado
                    const normalizedSubtypeId = normalizeSubtypeForId(selectedSubtype);
                    const targetField = document.getElementById('campos_subtipo_' + normalizedSubtypeId);
                    if (targetField) {
                        targetField.classList.remove('hidden');
                    }
                }
            }

            // No hay un event listener para tipoInput porque es readonly en edit.
            // La visibilidad inicial se gestiona en la carga del DOM, basada en $producto->tipo.

            // Escuchar cambios en el select de subtipo de video
            if (subtipoVideoSelect) {
                subtipoVideoSelect.addEventListener('change', toggleSubtypeFields);
            }

            // Llamar a las funciones en la carga inicial para manejar los valores existentes del producto
            toggleProductFields();
            // Si el tipo de producto ya es 'videos' al cargar la página, también inicializa los subtipos
            // Esto es importante para el caso de edición, donde el select de subtipo ya puede tener un valor.
            if (tipoInput.value === 'videos') {
                toggleSubtypeFields();
            }
        });
    </script>
@endsection
