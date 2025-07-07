@extends('layouts.app') {{-- Asume que tienes un layout base --}}

@section('content')
    <div class="inline-block px-10 py-6">
        <div class="flex items-center space-x-4">
            <img src="{{ asset('images/reverse.svg') }}" class="w-4 h-4" alt="Icono Nuevo Usuario">
            <h1 class="text-3xl whitespace-nowrap font-bold">Crear nuevo producto</h1>
        </div>
        <div class="py-2">
            {!! Breadcrumbs::render('productos.create') !!}
        </div>
    </div>

    <div class="container mx-auto p-4">
        <form action="{{ route('productos.store') }}" method="POST" enctype="multipart/form-data"
            class="bg-[var(--color-Gestion)] shadow-2xl rounded-2xl p-8 mb-8">
            @csrf

            {{-- Campo de selección de Tipo de Producto (principal) --}}
            <div class="mb-4">
                <label for="tipo" class="block text-gray-700 text-sm font-bold mb-2">
                    Tipo de producto: <span class="text-red-500">*</span>
                </label>
                <select name="tipo" id="tipo"
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('tipo') border-red-500 @else border-gray-300 @enderror">
                    <option value="">Seleccione un tipo</option>
                    <option value="café" {{ old('tipo') == 'café' ? 'selected' : '' }}>Café</option>
                    <option value="mora" {{ old('tipo') == 'mora' ? 'selected' : '' }}>Mora</option>
                    <option value="videos" {{ old('tipo') == 'videos' ? 'selected' : '' }}>Video</option>
                </select>
                @error('tipo')
                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                @enderror
            </div>

            {{-- Div para agrupar Imagen y Observaciones --}}
            <div id="campos_comunes_no_video">
                <div class="mb-4">
                    <label for="imagen" class="block text-gray-700 text-sm font-bold mb-2">
                        Imagen: <span class="text-red-500">*</span>
                    </label>
                    <input type="file" name="imagen" id="imagen"
                        class="block w-full text-sm text-gray-900 border rounded-lg cursor-pointer bg-gray-50 focus:outline-none @error('imagen') border-red-500 @else border-gray-300 @enderror">
                    @error('imagen')
                        <p class="text-red-500 text-xs italic">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Campo rutavideo para productos tipo Café o Mora --}}
            <div id="campos_ruta_video_producto" class="hidden">
                <div class="mb-4">
                    <label for="rutavideo" class="block text-gray-700 text-sm font-bold mb-2">URL del video (Producto
                        general):<span class="text-red-500">*</span></label>
                    <input type="url" name="rutavideo" id="rutavideo"
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('rutavideo') border-red-500 @else border-gray-300 @enderror"
                        value="{{ old('rutavideo') }}" placeholder="https://ejemplo.com/tu-video-general.mp4">
                    @error('rutavideo')
                        <p class="text-red-500 text-xs italic">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Campos específicos para Café --}}
            <div id="campos_cafe" class="hidden mt-6 pt-4 border-t border-gray-200">
                <h2 class="text-xl font-semibold mb-3">Detalles de café</h2>
                <div class="mb-4">
                    <label for="cafe_data_numero_pagina" class="block text-gray-700 text-sm font-bold mb-2">
                        Número de página: <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="cafe_data[numero_pagina]" id="cafe_data_numero_pagina"
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('cafe_data.numero_pagina') border-red-500 @else border-gray-300 @enderror"
                        value="{{ old('cafe_data.numero_pagina') }}">
                    @error('cafe_data.numero_pagina')
                        <p class="text-red-500 text-xs italic">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="cafe_data_clase" class="block text-gray-700 text-sm font-bold mb-2">Clase: <span
                            class="text-red-500">*</span></label>
                    <input type="text" name="cafe_data[clase]" id="cafe_data_clase"
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('cafe_data.clase') border-red-500 @else border-gray-300 @enderror"
                        value="{{ old('cafe_data.clase') }}">
                    @error('cafe_data.clase')
                        <p class="text-red-500 text-xs italic">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="cafe_data_informacion" class="block text-gray-700 text-sm font-bold mb-2">Información de
                        café: <span class="text-red-500">*</span></label>
                    <textarea name="cafe_data[informacion]" id="cafe_data_informacion" rows="5"
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">{{ old('cafe_data.informacion') }}</textarea>
                    @error('cafe_data.informacion')
                        <p class="text-red-500 text-xs italic">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Campos específicos para Mora --}}
            <div id="campos_mora" class="hidden mt-6 pt-4 border-t border-gray-200">
                <h2 class="text-xl font-semibold mb-3">Detalles de mora</h2>
                <div class="mb-4">
                    <label for="mora_data_numero_pagina" class="block text-gray-700 text-sm font-bold mb-2">
                        Número de página: <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="mora_data[numero_pagina]" id="mora_data_numero_pagina"
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('mora_data.numero_pagina') border-red-500 @else border-gray-300 @enderror"
                        value="{{ old('mora_data.numero_pagina') }}">
                    @error('mora_data.numero_pagina')
                        <p class="text-red-500 text-xs italic">{{ $message }}</p>
                    @enderror
                </div>
                <div class="mb-4">
                    <label for="mora_data_clase" class="block text-gray-700 text-sm font-bold mb-2">Clase:<span
                            class="text-red-500">*</span></label>
                    <input type="text" name="mora_data[clase]" id="mora_data_clase"
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('mora_data.clase') border-red-500 @else border-gray-300 @enderror"
                        value="{{ old('mora_data.clase') }}">
                    @error('mora_data.clase')
                        <p class="text-red-500 text-xs italic">{{ $message }}</p>
                    @enderror
                </div>
                <div class="mb-4">
                    <label for="mora_data_informacion" class="block text-gray-700 text-sm font-bold mb-2">
                        Información de mora:<span class="text-red-500">*</span>
                    </label>
                    <textarea name="mora_data[informacion]" id="mora_data_informacion" rows="5"
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('mora_data.informacion') border-red-500 @else border-gray-300 @enderror">{{ old('mora_data.informacion') }}</textarea>
                    @error('mora_data.informacion')
                        <p class="text-red-500 text-xs italic">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Campos específicos para Videos --}}
            <div id="campos_videos" class="hidden mt-6 pt-4 border-t border-gray-200">
                <h2 class="text-xl font-semibold mb-3">Detalles de video</h2>
                <div class="mb-4">
                    <label for="subtipo_video" class="block text-gray-700 text-sm font-bold mb-2">
                        Tipo de video (Subtipo): <span class="text-red-500">*</span>
                    </label>

                    <select name="videos_data[tipo]" id="subtipo_video"
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('videos_data.tipo') border-red-500 @else border-gray-300 @enderror">
                        <option value="">Seleccione un subtipo</option>

                        <option value="Educativo" {{ old('videos_data.tipo') == 'Educativo' ? 'selected' : '' }}>Video
                            Educativo</option>
                        <option value="Secundarios" {{ old('videos_data.tipo') == 'Secundarios' ? 'selected' : '' }}>Video
                            Secundarios</option>
                        <option value="Insumos_y_abonos"
                            {{ old('videos_data.tipo') == 'Insumos_y_abonos' ? 'selected' : '' }}>Video
                            Insumos y Abonos</option>
                        <option value="Cuidados_generales"
                            {{ old('videos_data.tipo') == 'Cuidados_generales' ? 'selected' : '' }}>Video
                            Cuidados Generales</option>
                        <option value="Preparacion_terreno_siembra"
                            {{ old('videos_data.tipo') == 'Preparacion_terreno_siembra' ? 'selected' : '' }}>Video
                            Preparacion del terreno y siembra</option>
                        <option value="Sugerencias_generales"
                            {{ old('videos_data.tipo') == 'Sugerencias_generales' ? 'selected' : '' }}>Video
                            Sugerencias generales</option>
                        <option value="Metodos_recoleccion"
                            {{ old('videos_data.tipo') == 'Metodos_recoleccion' ? 'selected' : '' }}>Video
                            Metodos de recoleccion</option>
                        <option value="Cuidados_cosecha"
                            {{ old('videos_data.tipo') == 'Cuidados_cosecha' ? 'selected' : '' }}>Video
                            Cuidados de la cosecha</option>
                        <option value="Buenas_practicas_agricolas"
                            {{ old('videos_data.tipo') == 'Buenas_practicas_agricolas' ? 'selected' : '' }}>Video
                            Buenas Practicas Agricolas</option>
                    </select>

                    <div id="campos_subtipo_Educativo" class="hidden mt-4 pt-4 border-t border-gray-200">
                        <h3 class="text-lg font-semibold mb-2">Campos para Video Educativo</h3>
                        <div class="mb-4">
                            <label for="Educativo_titulo" class="block text-gray-700 text-sm font-bold mb-2">Título: <span
                                    class="text-red-500">*</span></label>
                            <input type="text" name="videos_data[Educativo][titulo]" id="Educativo_titulo"
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('videos_data.Educativo.titulo') border-red-500 @else border-gray-300 @enderror"
                                value="{{ old('videos_data.Educativo.titulo') }}">
                            @error('videos_data.Educativo.titulo')
                                <p class="text-red-500 text-xs italic">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="mb-4">
                            <label for="Educativo_autor" class="block text-gray-700 text-sm font-bold mb-2">Autor: <span
                                    class="text-red-500">*</span></label>
                            <input type="text" name="videos_data[Educativo][autor]" id="Educativo_autor"
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('videos_data.Educativo.autor') border-red-500 @else border-gray-300 @enderror"
                                value="{{ old('videos_data.Educativo.autor') }}">
                            @error('videos_data.Educativo.autor')
                                <p class="text-red-500 text-xs italic">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="mb-4">
                            <label for="Educativo_descripcion"
                                class="block text-gray-700 text-sm font-bold mb-2">Descripción:<span
                                    class="text-red-500">*</span></label>
                            <textarea name="videos_data[Educativo][descripcion]" id="Educativo_descripcion" rows="5"
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('videos_data.Educativo.descripcion') border-red-500 @else border-gray-300 @enderror">{{ old('videos_data.Educativo.descripcion') }}</textarea>
                            @error('videos_data.Educativo.descripcion')
                                <p class="text-red-500 text-xs italic">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="mb-4">
                            <label for="Educativo_rutaVideo" class="block text-gray-700 text-sm font-bold mb-2">URL del
                                video: <span class="text-red-500">*</span></label>
                            <input type="url" name="videos_data[Educativo][rutaVideo]" id="Educativo_rutaVideo"
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('videos_data.Educativo.rutaVideo') border-red-500 @else border-gray-300 @enderror"
                                value="{{ old('videos_data.Educativo.rutaVideo') }}"
                                placeholder="https://ejemplo.com/tu-video-primario.mp4">
                            @error('videos_data.Educativo.rutaVideo')
                                <p class="text-red-500 text-xs italic">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div id="campos_subtipo_Secundarios" class="hidden mt-4 pt-4 border-t border-gray-200">
                        <h3 class="text-lg font-semibold mb-2">Campos para Video Secundarios</h3>
                        <div class="mb-4">
                            <label for="Secundarios_titulo" class="block text-gray-700 text-sm font-bold mb-2">Título:
                                <span class="text-red-500">*</span></label>
                            <input type="text" name="videos_data[Secundarios][titulo]" id="Secundarios_titulo"
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('videos_data.Secundarios.titulo') border-red-500 @else border-gray-300 @enderror"
                                value="{{ old('videos_data.Secundarios.titulo') }}">
                            @error('videos_data.Secundarios.titulo')
                                <p class="text-red-500 text-xs italic">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="mb-4">
                            <label for="Secundarios_autor" class="block text-gray-700 text-sm font-bold mb-2">Autor: <span
                                    class="text-red-500">*</span></label>
                            <input type="text" name="videos_data[Secundarios][autor]" id="Secundarios_autor"
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('videos_data.Secundarios.autor') border-red-500 @else border-gray-300 @enderror"
                                value="{{ old('videos_data.Secundarios.autor') }}">
                            @error('videos_data.Secundarios.autor')
                                <p class="text-red-500 text-xs italic">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="mb-4">
                            <label for="Secundarios_descripcion"
                                class="block text-gray-700 text-sm font-bold mb-2">Descripción:<span
                                    class="text-red-500">*</span></label>
                            <textarea name="videos_data[Secundarios][descripcion]" id="Secundarios_descripcion" rows="5"
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('videos_data.Secundarios.descripcion') border-red-500 @else border-gray-300 @enderror">{{ old('videos_data.Secundarios.descripcion') }}</textarea>
                            @error('videos_data.Secundarios.descripcion')
                                <p class="text-red-500 text-xs italic">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="mb-4">
                            <label for="Secundarios_rutaVideo" class="block text-gray-700 text-sm font-bold mb-2">URL del
                                video: <span class="text-red-500">*</span></label>
                            <input type="url" name="videos_data[Secundarios][rutaVideo]" id="Secundarios_rutaVideo"
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('videos_data.Secundarios.rutaVideo') border-red-500 @else border-gray-300 @enderror"
                                value="{{ old('videos_data.Secundarios.rutaVideo') }}"
                                placeholder="https://ejemplo.com/tu-video-secundario.mp4">
                            @error('videos_data.Secundarios.rutaVideo')
                                <p class="text-red-500 text-xs italic">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div id="campos_subtipo_Insumos_y_abonos" class="hidden mt-4 pt-4 border-t border-gray-200">
                        <h3 class="text-lg font-semibold mb-2">Campos para Video Insumos y Abonos</h3>
                        <div class="mb-4">
                            <label for="Insumos_y_abonos_titulo"
                                class="block text-gray-700 text-sm font-bold mb-2">Título: <span
                                    class="text-red-500">*</span></label>
                            <input type="text" name="videos_data[Insumos_y_abonos][titulo]"
                                id="Insumos_y_abonos_titulo"
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('videos_data.Insumos_y_abonos.titulo') border-red-500 @else border-gray-300 @enderror"
                                value="{{ old('videos_data.Insumos_y_abonos.titulo') }}">
                            @error('videos_data.Insumos_y_abonos.titulo')
                                <p class="text-red-500 text-xs italic">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="mb-4">
                            <label for="Insumos_y_abonos_autor" class="block text-gray-700 text-sm font-bold mb-2">Autor:
                                <span class="text-red-500">*</span></label>
                            <input type="text" name="videos_data[Insumos_y_abonos][autor]" id="Insumos_y_abonos_autor"
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('videos_data.Insumos_y_abonos.autor') border-red-500 @else border-gray-300 @enderror"
                                value="{{ old('videos_data.Insumos_y_abonos.autor') }}">
                            @error('videos_data.Insumos_y_abonos.autor')
                                <p class="text-red-500 text-xs italic">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="mb-4">
                            <label for="Insumos_y_abonos_descripcion"
                                class="block text-gray-700 text-sm font-bold mb-2">Descripción:<span
                                    class="text-red-500">*</span></label>
                            <textarea name="videos_data[Insumos_y_abonos][descripcion]" id="Insumos_y_abonos_descripcion" rows="5"
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('videos_data.Insumos_y_abonos.descripcion') border-red-500 @else border-gray-300 @enderror">{{ old('videos_data.Insumos_y_abonos.descripcion') }}</textarea>
                            @error('videos_data.Insumos_y_abonos.descripcion')
                                <p class="text-red-500 text-xs italic">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="mb-4">
                            <label for="Insumos_y_abonos_rutaVideo" class="block text-gray-700 text-sm font-bold mb-2">URL
                                del
                                video: <span class="text-red-500">*</span></label>
                            <input type="url" name="videos_data[Insumos_y_abonos][rutaVideo]"
                                id="Insumos_y_abonos_rutaVideo"
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('videos_data.Insumos_y_abonos.rutaVideo') border-red-500 @else border-gray-300 @enderror"
                                value="{{ old('videos_data.Insumos_y_abonos.rutaVideo') }}"
                                placeholder="https://ejemplo.com/tu-video-insumos.mp4">
                            @error('videos_y_abonos.rutaVideo')
                                <p class="text-red-500 text-xs italic">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div id="campos_subtipo_Cuidados_generales" class="hidden mt-4 pt-4 border-t border-gray-200">
                        <h3 class="text-lg font-semibold mb-2">Campos para Video Cuidados Generales</h3>
                        <div class="mb-4">
                            <label for="Cuidados_generales_titulo"
                                class="block text-gray-700 text-sm font-bold mb-2">Título: <span
                                    class="text-red-500">*</span></label>
                            <input type="text" name="videos_data[Cuidados_generales][titulo]"
                                id="Cuidados_generales_titulo"
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('videos_data.Cuidados_generales.titulo') border-red-500 @else border-gray-300 @enderror"
                                value="{{ old('videos_data.Cuidados_generales.titulo') }}">
                            @error('videos_data.Cuidados_generales.titulo')
                                <p class="text-red-500 text-xs italic">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="mb-4">
                            <label for="Cuidados_generales_autor"
                                class="block text-gray-700 text-sm font-bold mb-2">Autor: <span
                                    class="text-red-500">*</span></label>
                            <input type="text" name="videos_data[Cuidados_generales][autor]"
                                id="Cuidados_generales_autor"
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('videos_data.Cuidados_generales.autor') border-red-500 @else border-gray-300 @enderror"
                                value="{{ old('videos_data.Cuidados_generales.autor') }}">
                            @error('videos_data.Cuidados_generales.autor')
                                <p class="text-red-500 text-xs italic">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="mb-4">
                            <label for="Cuidados_generales_descripcion"
                                class="block text-gray-700 text-sm font-bold mb-2">Descripción:<span
                                    class="text-red-500">*</span></label>
                            <textarea name="videos_data[Cuidados_generales][descripcion]" id="Cuidados_generales_descripcion" rows="5"
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('videos_data.Cuidados_generales.descripcion') border-red-500 @else border-gray-300 @enderror">{{ old('videos_data.Cuidados_generales.descripcion') }}</textarea>
                            @error('videos_data.Cuidados_generales.descripcion')
                                <p class="text-red-500 text-xs italic">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="mb-4">
                            <label for="Cuidados_generales_rutaVideo"
                                class="block text-gray-700 text-sm font-bold mb-2">URL del
                                video: <span class="text-red-500">*</span></label>
                            <input type="url" name="videos_data[Cuidados_generales][rutaVideo]"
                                id="Cuidados_generales_rutaVideo"
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('videos_data.Cuidados_generales.rutaVideo') border-red-500 @else border-gray-300 @enderror"
                                value="{{ old('videos_data.Cuidados_generales.rutaVideo') }}"
                                placeholder="https://ejemplo.com/tu-video-cuidados-generales.mp4">
                            @error('videos_data.Cuidados_generales.rutaVideo')
                                <p class="text-red-500 text-xs italic">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div id="campos_subtipo_Preparacion_terreno_siembra"
                        class="hidden mt-4 pt-4 border-t border-gray-200">
                        <h3 class="text-lg font-semibold mb-2">Campos para Video Preparación del Terreno y Siembra</h3>
                        <div class="mb-4">
                            <label for="Preparacion_terreno_siembra_titulo"
                                class="block text-gray-700 text-sm font-bold mb-2">Título: <span
                                    class="text-red-500">*</span></label>
                            <input type="text" name="videos_data[Preparacion_terreno_siembra][titulo]"
                                id="Preparacion_terreno_siembra_titulo"
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('videos_data.Preparacion_terreno_siembra.titulo') border-red-500 @else border-gray-300 @enderror"
                                value="{{ old('videos_data.Preparacion_terreno_siembra.titulo') }}">
                            @error('videos_data.Preparacion_terreno_siembra.titulo')
                                <p class="text-red-500 text-xs italic">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="mb-4">
                            <label for="Preparacion_terreno_siembra_autor"
                                class="block text-gray-700 text-sm font-bold mb-2">Autor: <span
                                    class="text-red-500">*</span></label>
                            <input type="text" name="videos_data[Preparacion_terreno_siembra][autor]"
                                id="Preparacion_terreno_siembra_autor"
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('videos_data.Preparacion_terreno_siembra.autor') border-red-500 @else border-gray-300 @enderror"
                                value="{{ old('videos_data.Preparacion_terreno_siembra.autor') }}">
                            @error('videos_data.Preparacion_terreno_siembra.autor')
                                <p class="text-red-500 text-xs italic">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="mb-4">
                            <label for="Preparacion_terreno_siembra_descripcion"
                                class="block text-gray-700 text-sm font-bold mb-2">Descripción:<span
                                    class="text-red-500">*</span></label>
                            <textarea name="videos_data[Preparacion_terreno_siembra][descripcion]" id="Preparacion_terreno_siembra_descripcion"
                                rows="5"
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('videos_data.Preparacion_terreno_siembra.descripcion') border-red-500 @else border-gray-300 @enderror">{{ old('videos_data.Preparacion_terreno_siembra.descripcion') }}</textarea>
                            @error('videos_data.Preparacion_terreno_siembra.descripcion')
                                <p class="text-red-500 text-xs italic">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="mb-4">
                            <label for="Preparacion_terreno_siembra_rutaVideo"
                                class="block text-gray-700 text-sm font-bold mb-2">URL del
                                video: <span class="text-red-500">*</span></label>
                            <input type="url" name="videos_data[Preparacion_terreno_siembra][rutaVideo]"
                                id="Preparacion_terreno_siembra_rutaVideo"
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('videos_data.Preparacion_terreno_siembra.rutaVideo') border-red-500 @else border-gray-300 @enderror"
                                value="{{ old('videos_data.Preparacion_terreno_siembra.rutaVideo') }}"
                                placeholder="https://ejemplo.com/tu-video-preparacion.mp4">
                            @error('videos_data.Preparacion_terreno_siembra.rutaVideo')
                                <p class="text-red-500 text-xs italic">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div id="campos_subtipo_Sugerencias_generales" class="hidden mt-4 pt-4 border-t border-gray-200">
                        <h3 class="text-lg font-semibold mb-2">Campos para Video Sugerencias Generales</h3>
                        <div class="mb-4">
                            <label for="Sugerencias_generales_titulo"
                                class="block text-gray-700 text-sm font-bold mb-2">Título: <span
                                    class="text-red-500">*</span></label>
                            <input type="text" name="videos_data[Sugerencias_generales][titulo]"
                                id="Sugerencias_generales_titulo"
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('videos_data.Sugerencias_generales.titulo') border-red-500 @else border-gray-300 @enderror"
                                value="{{ old('videos_data.Sugerencias_generales.titulo') }}">
                            @error('videos_data.Sugerencias_generales.titulo')
                                <p class="text-red-500 text-xs italic">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="mb-4">
                            <label for="Sugerencias_generales_autor"
                                class="block text-gray-700 text-sm font-bold mb-2">Autor: <span
                                    class="text-red-500">*</span></label>
                            <input type="text" name="videos_data[Sugerencias_generales][autor]"
                                id="Sugerencias_generales_autor"
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('videos_data.Sugerencias_generales.autor') border-red-500 @else border-gray-300 @enderror"
                                value="{{ old('videos_data.Sugerencias_generales.autor') }}">
                            @error('videos_data.Sugerencias_generales.autor')
                                <p class="text-red-500 text-xs italic">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="mb-4">
                            <label for="Sugerencias_generales_descripcion"
                                class="block text-gray-700 text-sm font-bold mb-2">Descripción:<span
                                    class="text-red-500">*</span></label>
                            <textarea name="videos_data[Sugerencias_generales][descripcion]" id="Sugerencias_generales_descripcion"
                                rows="5"
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('videos_data.Sugerencias_generales.descripcion') border-red-500 @else border-gray-300 @enderror">{{ old('videos_data.Sugerencias_generales.descripcion') }}</textarea>
                            @error('videos_data.Sugerencias_generales.descripcion')
                                <p class="text-red-500 text-xs italic">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="mb-4">
                            <label for="Sugerencias_generales_rutaVideo"
                                class="block text-gray-700 text-sm font-bold mb-2">URL del
                                video: <span class="text-red-500">*</span></label>
                            <input type="url" name="videos_data[Sugerencias_generales][rutaVideo]"
                                id="Sugerencias_generales_rutaVideo"
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('videos_data.Sugerencias_generales.rutaVideo') border-red-500 @else border-gray-300 @enderror"
                                value="{{ old('videos_data.Sugerencias_generales.rutaVideo') }}"
                                placeholder="https://ejemplo.com/tu-video-sugerencias.mp4">
                            @error('videos_data.Sugerencias_generales.rutaVideo')
                                <p class="text-red-500 text-xs italic">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div id="campos_subtipo_Metodos_recoleccion" class="hidden mt-4 pt-4 border-t border-gray-200">
                        <h3 class="text-lg font-semibold mb-2">Campos para Video Métodos de Recolección</h3>
                        <div class="mb-4">
                            <label for="Metodos_recoleccion_titulo"
                                class="block text-gray-700 text-sm font-bold mb-2">Título: <span
                                    class="text-red-500">*</span></label>
                            <input type="text" name="videos_data[Metodos_recoleccion][titulo]"
                                id="Metodos_recoleccion_titulo"
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('videos_data.Metodos_recoleccion.titulo') border-red-500 @else border-gray-300 @enderror"
                                value="{{ old('videos_data.Metodos_recoleccion.titulo') }}">
                            @error('videos_data.Metodos_recoleccion.titulo')
                                <p class="text-red-500 text-xs italic">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="mb-4">
                            <label for="Metodos_recoleccion_autor"
                                class="block text-gray-700 text-sm font-bold mb-2">Autor: <span
                                    class="text-red-500">*</span></label>
                            <input type="text" name="videos_data[Metodos_recoleccion][autor]"
                                id="Metodos_recoleccion_autor"
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('videos_data.Metodos_recoleccion.autor') border-red-500 @else border-gray-300 @enderror"
                                value="{{ old('videos_data.Metodos_recoleccion.autor') }}">
                            @error('videos_data.Metodos_recoleccion.autor')
                                <p class="text-red-500 text-xs italic">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="mb-4">
                            <label for="Metodos_recoleccion_descripcion"
                                class="block text-gray-700 text-sm font-bold mb-2">Descripción:<span
                                    class="text-red-500">*</span></label>
                            <textarea name="videos_data[Metodos_recoleccion][descripcion]" id="Metodos_recoleccion_descripcion" rows="5"
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('videos_data.Metodos_recoleccion.descripcion') border-red-500 @else border-gray-300 @enderror">{{ old('videos_data.Metodos_recoleccion.descripcion') }}</textarea>
                            @error('videos_data.Metodos_recoleccion.descripcion')
                                <p class="text-red-500 text-xs italic">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="mb-4">
                            <label for="Metodos_recoleccion_rutaVideo"
                                class="block text-gray-700 text-sm font-bold mb-2">URL del
                                video: <span class="text-red-500">*</span></label>
                            <input type="url" name="videos_data[Metodos_recoleccion][rutaVideo]"
                                id="Metodos_recoleccion_rutaVideo"
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('videos_data.Metodos_recoleccion.rutaVideo') border-red-500 @else border-gray-300 @enderror"
                                value="{{ old('videos_data.Metodos_recoleccion.rutaVideo') }}"
                                placeholder="https://ejemplo.com/tu-video-recoleccion.mp4">
                            @error('videos_data.Metodos_recoleccion.rutaVideo')
                                <p class="text-red-500 text-xs italic">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div id="campos_subtipo_Cuidados_cosecha" class="hidden mt-4 pt-4 border-t border-gray-200">
                        <h3 class="text-lg font-semibold mb-2">Campos para Video Cuidados de la Cosecha</h3>
                        <div class="mb-4">
                            <label for="Cuidados_cosecha_titulo"
                                class="block text-gray-700 text-sm font-bold mb-2">Título: <span
                                    class="text-red-500">*</span></label>
                            <input type="text" name="videos_data[Cuidados_cosecha][titulo]"
                                id="Cuidados_cosecha_titulo"
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('videos_data.Cuidados_cosecha.titulo') border-red-500 @else border-gray-300 @enderror"
                                value="{{ old('videos_data.Cuidados_cosecha.titulo') }}">
                            @error('videos_data.Cuidados_cosecha.titulo')
                                <p class="text-red-500 text-xs italic">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="mb-4">
                            <label for="Cuidados_cosecha_autor" class="block text-gray-700 text-sm font-bold mb-2">Autor:
                                <span class="text-red-500">*</span></label>
                            <input type="text" name="videos_data[Cuidados_cosecha][autor]" id="Cuidados_cosecha_autor"
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('videos_data.Cuidados_cosecha.autor') border-red-500 @else border-gray-300 @enderror"
                                value="{{ old('videos_data.Cuidados_cosecha.autor') }}">
                            @error('videos_data.Cuidados_cosecha.autor')
                                <p class="text-red-500 text-xs italic">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="mb-4">
                            <label for="Cuidados_cosecha_descripcion"
                                class="block text-gray-700 text-sm font-bold mb-2">Descripción:<span
                                    class="text-red-500">*</span></label>
                            <textarea name="videos_data[Cuidados_cosecha][descripcion]" id="Cuidados_cosecha_descripcion" rows="5"
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('videos_data.Cuidados_cosecha.descripcion') border-red-500 @else border-gray-300 @enderror">{{ old('videos_data.Cuidados_cosecha.descripcion') }}</textarea>
                            @error('videos_data.Cuidados_cosecha.descripcion')
                                <p class="text-red-500 text-xs italic">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="mb-4">
                            <label for="Cuidados_cosecha_rutaVideo" class="block text-gray-700 text-sm font-bold mb-2">URL
                                del
                                video: <span class="text-red-500">*</span></label>
                            <input type="url" name="videos_data[Cuidados_cosecha][rutaVideo]"
                                id="Cuidados_cosecha_rutaVideo"
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('videos_data.Cuidados_cosecha.rutaVideo') border-red-500 @else border-gray-300 @enderror"
                                value="{{ old('videos_data.Cuidados_cosecha.rutaVideo') }}"
                                placeholder="https://ejemplo.com/tu-video-cosecha.mp4">
                            @error('videos_data.Cuidados_cosecha.rutaVideo')
                                <p class="text-red-500 text-xs italic">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div id="campos_subtipo_Buenas_practicas_agricolas" class="hidden mt-4 pt-4 border-t border-gray-200">
                        <h3 class="text-lg font-semibold mb-2">Campos para Video Buenas Prácticas Agrícolas</h3>
                        <div class="mb-4">
                            <label for="Buenas_practicas_agricolas_titulo"
                                class="block text-gray-700 text-sm font-bold mb-2">Título: <span
                                    class="text-red-500">*</span></label>
                            <input type="text" name="videos_data[Buenas_practicas_agricolas][titulo]"
                                id="Buenas_practicas_agricolas_titulo"
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('videos_data.Buenas_practicas_agricolas.titulo') border-red-500 @else border-gray-300 @enderror"
                                value="{{ old('videos_data.Buenas_practicas_agricolas.titulo') }}">
                            @error('videos_data.Buenas_practicas_agricolas.titulo')
                                <p class="text-red-500 text-xs italic">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="mb-4">
                            <label for="Buenas_practicas_agricolas_autor"
                                class="block text-gray-700 text-sm font-bold mb-2">Autor: <span
                                    class="text-red-500">*</span></label>
                            <input type="text" name="videos_data[Buenas_practicas_agricolas][autor]"
                                id="Buenas_practicas_agricolas_autor"
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('videos_data.Buenas_practicas_agricolas.autor') border-red-500 @else border-gray-300 @enderror"
                                value="{{ old('videos_data.Buenas_practicas_agricolas.autor') }}">
                            @error('videos_data.Buenas_practicas_agricolas.autor')
                                <p class="text-red-500 text-xs italic">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="mb-4">
                            <label for="Buenas_practicas_agricolas_descripcion"
                                class="block text-gray-700 text-sm font-bold mb-2">Descripción:<span
                                    class="text-red-500">*</span></label>
                            <textarea name="videos_data[Buenas_practicas_agricolas][descripcion]" id="Buenas_practicas_agricolas_descripcion"
                                rows="5"
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('videos_data.Buenas_practicas_agricolas.descripcion') border-red-500 @else border-gray-300 @enderror">{{ old('videos_data.Buenas_practicas_agricolas.descripcion') }}</textarea>
                            @error('videos_data.Buenas_practicas_agricolas.descripcion')
                                <p class="text-red-500 text-xs italic">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="mb-4">
                            <label for="Buenas_practicas_agricolas_rutaVideo"
                                class="block text-gray-700 text-sm font-bold mb-2">URL del
                                video: <span class="text-red-500">*</span></label>
                            <input type="url" name="videos_data[Buenas_practicas_agricolas][rutaVideo]"
                                id="Buenas_practicas_agricolas_rutaVideo"
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('videos_data.Buenas_practicas_agricolas.rutaVideo') border-red-500 @else border-gray-300 @enderror"
                                value="{{ old('videos_data.Buenas_practicas_agricolas.rutaVideo') }}"
                                placeholder="https://ejemplo.com/tu-video-bpa.mp4">
                            @error('videos_data.Buenas_practicas_agricolas.rutaVideo')
                                <p class="text-red-500 text-xs italic">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="flex items-center justify-between mt-6">
                <a href="{{ route('productos.index') }}"
                    class="bg-[var(--color-textmarca)] hover:bg-[var(--color-texthovermarca)] py-3 px-4 rounded-full text-md font-bold text-white focus:outline-none focus:shadow-outline inline-flex items-center transition duration-150 ease-in-out transform hover:-translate-x-1 shadow-md">
                    <img src="{{ asset('images/regresar.svg') }}" alt="Regresar" class="w-5 h-6 mr-2">
                    <span class="whitespace-nowrap text-inherit">{{ __('Cancelar') }}</span>
                </a>

                <button type="submit"
                    class="bg-[var(--color-sgt)] hover:bg-[var(--color-hoversgt)] py-3 px-4 rounded-full text-md font-bold text-white focus:outline-none focus:shadow-outline inline-flex items-center transition duration-150 ease-in-out transform hover:translate-x-1 shadow-md">
                    <span class="whitespace-nowrap text-inherit">{{ __('Crear producto') }}</span>
                    <img src="{{ asset('images/siguiente.svg') }}" alt="siguiente" class="w-5 h-6 ml-2">
                </button>
            </div>
        </form>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const tipoSelect = document.getElementById('tipo');
            const camposComunesNoVideo = document.getElementById('campos_comunes_no_video'); // Nuevo elemento
            const camposCafe = document.getElementById('campos_cafe');
            const camposMora = document.getElementById('campos_mora');
            const camposVideos = document.getElementById('campos_videos');
            const camposRutaVideoProducto = document.getElementById('campos_ruta_video_producto');

            const subtipoVideoSelect = document.getElementById('subtipo_video');

            // Seleccionar todos los divs de subtipo de video de una vez
            const allSubtypeFields = document.querySelectorAll(
                '#campos_subtipo_Educativo, ' +
                '#campos_subtipo_Secundarios, ' +
                '#campos_subtipo_Insumos_y_abonos, ' +
                '#campos_subtipo_Cuidados_generales, ' +
                '#campos_subtipo_Preparacion_terreno_siembra, ' +
                '#campos_subtipo_Sugerencias_generales, ' +
                '#campos_subtipo_Metodos_recoleccion, ' +
                '#campos_subtipo_Cuidados_cosecha, ' +
                '#campos_subtipo_Buenas_practicas_agricolas'
            );

            /**
             * Muestra/oculta los campos específicos del producto (Café, Mora, Videos)
             * y el campo RutaVideo del producto general.
             */
            function toggleProductFields() {
                const selectedType = tipoSelect.value;

                // Ocultar todos los campos específicos y el RutaVideo del producto general
                camposCafe.classList.add('hidden');
                camposMora.classList.add('hidden');
                camposVideos.classList.add('hidden');
                camposRutaVideoProducto.classList.add('hidden');

                // Ocultar los campos comunes no-video por defecto (Imagen y Observaciones)
                camposComunesNoVideo.classList.add('hidden');

                // Ocultar todos los campos de subtipo de video por defecto
                allSubtypeFields.forEach(field => {
                    field.classList.add('hidden');
                });


                if (selectedType === 'café') {
                    camposComunesNoVideo.classList.remove('hidden'); // Mostrar para Café
                    camposCafe.classList.remove('hidden');
                    camposRutaVideoProducto.classList.remove('hidden'); // Mostrar RutaVideo para Café
                } else if (selectedType === 'mora') {
                    camposComunesNoVideo.classList.remove('hidden'); // Mostrar para Mora
                    camposMora.classList.remove('hidden');
                    camposRutaVideoProducto.classList.remove('hidden'); // Mostrar RutaVideo para Mora
                } else if (selectedType === 'videos') {
                    // camposComunesNoVideo se mantiene oculto para videos
                    camposVideos.classList.remove('hidden');
                    // No mostrar camposRutaVideoProducto para 'videos'
                    // Y llamar a la función para mostrar/ocultar subtipos de video
                    toggleSubtypeFields();
                } else {
                    // Si no se ha seleccionado ningún tipo, ocultar todo
                    camposComunesNoVideo.classList.add('hidden');
                }
            }

            /**
             * Muestra/oculta los campos específicos del subtipo de video.
             */
            function toggleSubtypeFields() {
                const selectedSubtype = subtipoVideoSelect.value;

                // Ocultar todos los campos de subtipo de video
                allSubtypeFields.forEach(field => {
                    field.classList.add('hidden');
                });

                // Mostrar el div correspondiente al subtipo seleccionado, si existe
                if (selectedSubtype) {
                    const targetField = document.getElementById('campos_subtipo_' + selectedSubtype);
                    if (targetField) {
                        targetField.classList.remove('hidden');
                    }
                }
            }

            // Escuchar cambios en el select principal de tipo de producto
            tipoSelect.addEventListener('change', toggleProductFields);

            // Escuchar cambios en el select de subtipo de video
            subtipoVideoSelect.addEventListener('change', toggleSubtypeFields);

            // Llamar a las funciones en la carga inicial para manejar los valores 'old()'
            toggleProductFields();
            // Si el tipo de producto ya es 'videos' al cargar la página, también inicializa los subtipos
            if (tipoSelect.value === 'videos') {
                toggleSubtypeFields();
            }
        });
    </script>
@endsection
