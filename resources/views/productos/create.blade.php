@extends('layouts.app') {{-- Asume que tienes un layout base --}}

@section('content')
    <div class="container p-4 mx-auto">
        <h1 class="mb-4 text-2xl font-bold">Crear nuevo producto</h1>

        @if ($errors->any())
            <div class="relative px-4 py-3 mb-4 text-red-700 bg-red-100 border border-red-400 rounded" role="alert">
                <strong class="font-bold">¡Oops!</strong>
                <span class="block sm:inline">Hubo algunos problemas con tu entrada.</span>
                <ul class="mt-3 list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- El formulario ahora tiene un estilo de "caja sobresaliente" --}}
        <form action="{{ route('productos.store') }}" method="POST" enctype="multipart/form-data"
            class="p-8 mb-8 bg-white rounded-lg shadow-2xl"> {{-- Aumentado a shadow-2xl y p-8 --}}
            @csrf {{-- Protección CSRF obligatoria en Laravel --}}

            {{-- Campo de selección de Tipo de Producto (principal) --}}
            <div class="mb-4">
                <label for="tipo" class="block mb-2 text-sm font-bold text-gray-700">Tipo de producto:</label>
                <select name="tipo" id="tipo"
                    class="w-full px-3 py-2 leading-tight text-gray-700 border rounded shadow appearance-none focus:outline-none focus:shadow-outline">
                    <option value="">Seleccione un tipo</option>
                    <option value="café" {{ old('tipo') == 'café' ? 'selected' : '' }}>Café</option>
                    <option value="mora" {{ old('tipo') == 'mora' ? 'selected' : '' }}>Mora</option>
                    <option value="videos" {{ old('tipo') == 'videos' ? 'selected' : '' }}>Video</option>
                </select>
                @error('tipo')
                    <p class="text-xs italic text-red-500">{{ $message }}</p>
                @enderror
            </div>

            {{-- Nuevo div para agrupar Imagen y Observaciones --}}
            <div id="campos_comunes_no_video">
                {{-- Campo de Imagen (común a todos los productos, excepto videos) --}}
                <div class="mb-4">
                    <label for="imagen" class="block mb-2 text-sm font-bold text-gray-700">Imagen:</label>
                    <input type="file" name="imagen" id="imagen"
                        class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none">
                    @error('imagen')
                        <p class="text-xs italic text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Campo de Observaciones Generales (común a todos los productos, excepto videos) --}}
                <div class="mb-4">
                    <label for="observaciones" class="block mb-2 text-sm font-bold text-gray-700">Observaciones
                        generales:</label>
                    <textarea name="observaciones" id="observaciones" rows="3"
                        class="w-full px-3 py-2 leading-tight text-gray-700 border rounded shadow appearance-none focus:outline-none focus:shadow-outline">{{ old('observaciones') }}</textarea>
                    @error('observaciones')
                        <p class="text-xs italic text-red-500">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Campo RutaVideo para productos tipo Café o Mora --}}
            <div id="campos_ruta_video_producto" class="hidden">
                <div class="mb-4">
                    <label for="RutaVideo" class="block mb-2 text-sm font-bold text-gray-700">URL del Video (Producto General):</label>
                    <input type="url" name="RutaVideo" id="RutaVideo"
                        class="w-full px-3 py-2 leading-tight text-gray-700 border rounded shadow appearance-none focus:outline-none focus:shadow-outline"
                        value="{{ old('RutaVideo') }}" placeholder="https://ejemplo.com/tu-video-general.mp4">
                    @error('RutaVideo')
                        <p class="text-xs italic text-red-500">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Campos específicos para Café --}}
            <div id="campos_cafe" class="hidden pt-4 mt-6 border-t border-gray-200">
                <h2 class="mb-3 text-xl font-semibold">Detalles de café</h2>
                <div class="mb-4">
                    <label for="cafe_data_numero_pagina" class="block mb-2 text-sm font-bold text-gray-700">Número de
                        página:</label>
                    <input type="number" name="cafe_data[numero_pagina]" id="cafe_data_numero_pagina"
                        class="w-full px-3 py-2 leading-tight text-gray-700 border rounded shadow appearance-none focus:outline-none focus:shadow-outline"
                        value="{{ old('cafe_data.numero_pagina') }}">
                    @error('cafe_data.numero_pagina')
                        <p class="text-xs italic text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                <div class="mb-4">
                    <label for="cafe_data_clase" class="block mb-2 text-sm font-bold text-gray-700">Clase:</label>
                    <input type="text" name="cafe_data[clase]" id="cafe_data_clase"
                        class="w-full px-3 py-2 leading-tight text-gray-700 border rounded shadow appearance-none focus:outline-none focus:shadow-outline"
                        value="{{ old('cafe_data.clase') }}">
                    @error('cafe_data.clase')
                        <p class="text-xs italic text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                <div class="mb-4">
                    <label for="cafe_data_informacion" class="block mb-2 text-sm font-bold text-gray-700">Información de
                        café:</label>
                    <textarea name="cafe_data[informacion]" id="cafe_data_informacion" rows="5"
                        class="w-full px-3 py-2 leading-tight text-gray-700 border rounded shadow appearance-none focus:outline-none focus:shadow-outline">{{ old('cafe_data.informacion') }}</textarea>
                    @error('cafe_data.informacion')
                        <p class="text-xs italic text-red-500">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Campos específicos para Mora --}}
            <div id="campos_mora" class="hidden pt-4 mt-6 border-t border-gray-200">
                <h2 class="mb-3 text-xl font-semibold">Detalles de mora</h2>
                <div class="mb-4">
                    <label for="mora_data_numero_pagina" class="block mb-2 text-sm font-bold text-gray-700">Número de
                        página:</label>
                    <input type="number" name="mora_data[numero_pagina]" id="mora_data_numero_pagina"
                        class="w-full px-3 py-2 leading-tight text-gray-700 border rounded shadow appearance-none focus:outline-none focus:shadow-outline"
                        value="{{ old('mora_data.numero_pagina') }}">
                    @error('mora_data.numero_pagina')
                        <p class="text-xs italic text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                <div class="mb-4">
                    <label for="mora_data_clase" class="block mb-2 text-sm font-bold text-gray-700">Clase:</label>
                    <input type="text" name="mora_data[clase]" id="mora_data_clase"
                        class="w-full px-3 py-2 leading-tight text-gray-700 border rounded shadow appearance-none focus:outline-none focus:shadow-outline"
                        value="{{ old('mora_data.clase') }}">
                    @error('mora_data.clase')
                        <p class="text-xs italic text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                <div class="mb-4">
                    <label for="mora_data_informacion" class="block mb-2 text-sm font-bold text-gray-700">Información de
                        Mora:</label>
                    <textarea name="mora_data[informacion]" id="mora_data_informacion" rows="5"
                        class="w-full px-3 py-2 leading-tight text-gray-700 border rounded shadow appearance-none focus:outline-none focus:shadow-outline">{{ old('mora_data.informacion') }}</textarea>
                    @error('mora_data.informacion')
                        <p class="text-xs italic text-red-500">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Campos específicos para Videos (solo el selector de subtipo y su título) --}}
            <div id="campos_videos" class="hidden pt-4 mt-6 border-t border-gray-200">
                <h2 class="mb-3 text-xl font-semibold">Detalles de video</h2>

                {{-- Campo de selección de Subtipo de Video --}}
                <div class="mb-4">
                    <label for="subtipo_video" class="block mb-2 text-sm font-bold text-gray-700">Tipo de video (Subtipo):</label>
                    <select name="videos_data[tipo]" id="subtipo_video" {{-- ID ÚNICO y nombre para el subtipo --}}
                        class="w-full px-3 py-2 leading-tight text-gray-700 border rounded shadow appearance-none focus:outline-none focus:shadow-outline">
                        <option value="">Seleccione un subtipo</option>
                        <option value="primarios" {{ old('videos_data.tipo') == 'primarios' ? 'selected' : '' }}>Video Primarios</option>
                        <option value="secundarios" {{ old('videos_data.tipo') == 'secundarios' ? 'selected' : '' }}>Video Secundarios</option>
                        <option value="categorias" {{ old('videos_data.tipo') == 'categorias' ? 'selected' : '' }}>Video Categorias</option>
                    </select>
                    @error('videos_data.tipo')
                        <p class="text-xs italic text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Campos específicos para cada subtipo de video --}}
                <div id="campos_subtipo_primarios" class="hidden pt-4 mt-4 border-t border-gray-200">
                    <h3 class="mb-2 text-lg font-semibold">Campos para video primario</h3>
                    {{-- Campos generales de video movidos aquí --}}
                    <div class="mb-4">
                        <label for="primarios_autor" class="block mb-2 text-sm font-bold text-gray-700">Autor:</label>
                        <input type="text" name="videos_data[primarios][autor]" id="primarios_autor"
                            class="w-full px-3 py-2 leading-tight text-gray-700 border rounded shadow appearance-none focus:outline-none focus:shadow-outline"
                            value="{{ old('videos_data.primarios.autor') }}">
                        @error('videos_data.primarios.autor')
                            <p class="text-xs italic text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="mb-4">
                        <label for="primarios_titulo" class="block mb-2 text-sm font-bold text-gray-700">Titulo:</label>
                        <input type="text" name="videos_data[primarios][titulo]" id="primarios_titulo"
                            class="w-full px-3 py-2 leading-tight text-gray-700 border rounded shadow appearance-none focus:outline-none focus:shadow-outline"
                            value="{{ old('videos_data.primarios.titulo') }}">
                        @error('videos_data.primarios.titulo')
                            <p class="text-xs italic text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="mb-4">
                        <label for="primarios_descripcion"
                            class="block mb-2 text-sm font-bold text-gray-700">Descripción:</label>
                        <textarea name="videos_data[primarios][descripcion]" id="primarios_descripcion" rows="5"
                            class="w-full px-3 py-2 leading-tight text-gray-700 border rounded shadow appearance-none focus:outline-none focus:shadow-outline">{{ old('videos_data.primarios.descripcion') }}</textarea>
                        @error('videos_data.primarios.descripcion')
                            <p class="text-xs italic text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="mb-4">
                        <label for="primarios_rutaVideo" class="block mb-2 text-sm font-bold text-gray-700">URL del video (Específico):</label>
                        <input type="url" name="videos_data[primarios][rutaVideo]" id="primarios_rutaVideo"
                            class="w-full px-3 py-2 leading-tight text-gray-700 border rounded shadow appearance-none focus:outline-none focus:shadow-outline"
                            value="{{ old('videos_data.primarios.rutaVideo') }}" placeholder="https://ejemplo.com/tu-video-primario.mp4">
                        @error('videos_data.primarios.rutaVideo')
                            <p class="text-xs italic text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div id="campos_subtipo_secundarios" class="hidden pt-4 mt-4 border-t border-gray-200">
                    <h3 class="mb-2 text-lg font-semibold">Campos para video secundario</h3>
                    <div class="mb-4">
                        <label for="secundarios_autor" class="block mb-2 text-sm font-bold text-gray-700">Autor:</label>
                        <input type="text" name="videos_data[secundarios][autor]" id="secundarios_autor"
                            class="w-full px-3 py-2 leading-tight text-gray-700 border rounded shadow appearance-none focus:outline-none focus:shadow-outline"
                            value="{{ old('videos_data.secundarios.autor') }}">
                        @error('videos_data.secundarios.autor')
                            <p class="text-xs italic text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="mb-4">
                        <label for="secundarios_titulo" class="block mb-2 text-sm font-bold text-gray-700">Titulo:</label>
                        <input type="text" name="videos_data[secundarios][titulo]" id="secundarios_titulo"
                            class="w-full px-3 py-2 leading-tight text-gray-700 border rounded shadow appearance-none focus:outline-none focus:shadow-outline"
                            value="{{ old('videos_data.secundarios.titulo') }}">
                        @error('videos_data.secundarios.titulo')
                            <p class="text-xs italic text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="mb-4">
                        <label for="secundarios_descripcion"
                            class="block mb-2 text-sm font-bold text-gray-700">Descripción:</label>
                        <textarea name="videos_data[secundarios][descripcion]" id="secundarios_descripcion" rows="5"
                            class="w-full px-3 py-2 leading-tight text-gray-700 border rounded shadow appearance-none focus:outline-none focus:shadow-outline">{{ old('videos_data.secundarios.descripcion') }}</textarea>
                        @error('videos_data.secundarios.descripcion')
                            <p class="text-xs italic text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="mb-4">
                        <label for="secundarios_rutaVideo" class="block mb-2 text-sm font-bold text-gray-700">URL del video (Específico):</label>
                        <input type="url" name="videos_data[secundarios][rutaVideo]" id="secundarios_rutaVideo"
                            class="w-full px-3 py-2 leading-tight text-gray-700 border rounded shadow appearance-none focus:outline-none focus:shadow-outline"
                            value="{{ old('videos_data.secundarios.rutaVideo') }}" placeholder="https://ejemplo.com/tu-video-secundario.mp4">
                        @error('videos_data.secundarios.rutaVideo')
                            <p class="text-xs italic text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div id="campos_subtipo_categorias" class="hidden pt-4 mt-4 border-t border-gray-200">
                    <h3 class="mb-2 text-lg font-semibold">Campos para video de categoría</h3>
                    <div class="mb-4">
                        <label for="categorias_autor" class="block mb-2 text-sm font-bold text-gray-700">Autor:</label>
                        <input type="text" name="videos_data[categorias][autor]" id="categorias_autor"
                            class="w-full px-3 py-2 leading-tight text-gray-700 border rounded shadow appearance-none focus:outline-none focus:shadow-outline"
                            value="{{ old('videos_data.categorias.autor') }}">
                        @error('videos_data.categorias.autor')
                            <p class="text-xs italic text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="mb-4">
                        <label for="categorias_titulo" class="block mb-2 text-sm font-bold text-gray-700">Titulo:</label>
                        <input type="text" name="videos_data[categorias][titulo]" id="categorias_titulo"
                            class="w-full px-3 py-2 leading-tight text-gray-700 border rounded shadow appearance-none focus:outline-none focus:shadow-outline"
                            value="{{ old('videos_data.categorias.titulo') }}">
                        @error('videos_data.categorias.titulo')
                            <p class="text-xs italic text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="mb-4">
                        <label for="categorias_descripcion"
                            class="block mb-2 text-sm font-bold text-gray-700">Descripción:</label>
                        <textarea name="videos_data[categorias][descripcion]" id="categorias_descripcion" rows="5"
                            class="w-full px-3 py-2 leading-tight text-gray-700 border rounded shadow appearance-none focus:outline-none focus:shadow-outline">{{ old('videos_data.categorias.descripcion') }}</textarea>
                        @error('videos_data.categorias.descripcion')
                            <p class="text-xs italic text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="mb-4">
                        <label for="categorias_rutaVideo" class="block mb-2 text-sm font-bold text-gray-700">URL del video (Específico):</label>
                        <input type="url" name="videos_data[categorias][rutaVideo]" id="categorias_rutaVideo"
                            class="w-full px-3 py-2 leading-tight text-gray-700 border rounded shadow appearance-none focus:outline-none focus:shadow-outline"
                            value="{{ old('videos_data.categorias.rutaVideo') }}" placeholder="https://ejemplo.com/tu-video-categoria.mp4">
                        @error('videos_data.categorias.rutaVideo')
                            <p class="text-xs italic text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-between mt-6">
                <a href="{{ route('productos.index') }}"
                    class="inline-block text-sm font-bold text-blue-500 align-baseline hover:text-blue-800">
                    Cancelar
                </a>
                <button type="submit"
                    class="px-4 py-2 font-bold text-white bg-blue-500 rounded hover:bg-blue-700 focus:outline-none focus:shadow-outline">
                    Guardar producto
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
            const camposSubtipoPrimarios = document.getElementById('campos_subtipo_primarios');
            const camposSubtipoSecundarios = document.getElementById('campos_subtipo_secundarios');
            const camposSubtipoCategorias = document.getElementById('campos_subtipo_categorias');

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
                camposSubtipoPrimarios.classList.add('hidden');
                camposSubtipoSecundarios.classList.add('hidden');
                camposSubtipoCategorias.classList.add('hidden');


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
                camposSubtipoPrimarios.classList.add('hidden');
                camposSubtipoSecundarios.classList.add('hidden');
                camposSubtipoCategorias.classList.add('hidden');

                // Mostrar el div correspondiente al subtipo seleccionado
                if (selectedSubtype === 'primarios') {
                    camposSubtipoPrimarios.classList.remove('hidden');
                } else if (selectedSubtype === 'secundarios') {
                    camposSubtipoSecundarios.classList.remove('hidden');
                } else if (selectedSubtype === 'categorias') {
                    camposSubtipoCategorias.classList.remove('hidden');
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
