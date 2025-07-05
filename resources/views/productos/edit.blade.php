@extends('layouts.app')

@section('content')

    @can('editar producto')
    <div class="inline-block px-20 py-6">
            <div class="flex items-center space-x-4">
                <img src="{{ asset('images/reverse.svg') }}" class="w-4 h-4" alt="Icono Nuevo Usuario">
                <h1 class="text-3xl font-bold whitespace-nowrap">Editar producto</h1>
            </div>
            <div class="py-2">
            {!! Breadcrumbs::render('productos.edit', $producto) !!}
            </div>
        </div>

        <div class="container p-4 mx-auto">
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

            <form action="{{ route('productos.update', $producto) }}" method="POST" enctype="multipart/form-data"
                class="p-8 mb-8 bg-white rounded-lg shadow-2xl"> {{-- Estilo de caja sobresaliente --}}
                @csrf
                @method('PUT') {{-- Método PUT para actualizaciones en Laravel --}}

                <div class="mb-4">
                    <label for="tipo" class="block mb-2 text-sm font-bold text-gray-700">Tipo de producto:</label>
                    {{-- El tipo no se permite cambiar en la edición según tu controlador, así que lo mostramos deshabilitado --}}
                    <input type="text" name="tipo" id="tipo"
                        class="w-full px-3 py-2 leading-tight text-gray-700 bg-gray-100 border rounded shadow appearance-none focus:outline-none focus:shadow-outline"
                        value="{{ $producto->tipo }}" readonly>
                    @error('tipo')
                        <p class="text-xs italic text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Nuevo div para agrupar Imagen y Observaciones (solo para Café/Mora) --}}
                <div id="campos_comunes_no_video_edit">
                    <div class="mb-4">
                        <label for="imagen" class="block mb-2 text-sm font-bold text-gray-700">Imagen Actual:</label>
                        @if ($producto->imagen)
                            <img src="{{ asset('storage/' . $producto->imagen) }}" alt="Imagen actual del producto"
                                class="object-cover w-32 h-32 mb-2 rounded-lg">
                        @else
                            <p class="mb-2 text-sm text-gray-600">No hay imagen actual.</p>
                        @endif
                        <label for="nueva_imagen" class="block mb-2 text-sm font-bold text-gray-700">Subir nueva imagen
                            (Opcional):</label>
                        <input type="file" name="imagen" id="nueva_imagen"
                            class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none">
                        @error('imagen')
                            <p class="text-xs italic text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="observaciones" class="block mb-2 text-sm font-bold text-gray-700">Observaciones
                            generales:</label>
                        <textarea name="observaciones" id="observaciones" rows="3"
                            class="w-full px-3 py-2 leading-tight text-gray-700 border rounded shadow appearance-none focus:outline-none focus:shadow-outline">{{ old('observaciones', $producto->observaciones) }}</textarea>
                        @error('observaciones')
                            <p class="text-xs italic text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Campo RutaVideo para productos tipo Café o Mora (PRODUCTO GENERAL) --}}
                {{-- Este div se mostrará/ocultará con JS --}}
                <div id="campos_ruta_video_producto" class="hidden pt-4 mt-6 border-t border-gray-200">
                    <div class="mb-4">
                        <label for="RutaVideo" class="block mb-2 text-sm font-bold text-gray-700">URL del video (Producto General):</label>
                        <input type="url" name="RutaVideo" id="RutaVideo"
                            class="w-full px-3 py-2 leading-tight text-gray-700 border rounded shadow appearance-none focus:outline-none focus:shadow-outline"
                            value="{{ old('RutaVideo', $producto->RutaVideo) }}"
                            placeholder="https://ejemplo.com/tu-video-general.mp4">
                        @error('RutaVideo')
                            <p class="text-xs italic text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Campos específicos para Café --}}
                @if ($producto->tipo === 'café')
                    <div id="campos_cafe" class="pt-4 mt-6 border-t border-gray-200">
                        <h2 class="mb-3 text-xl font-semibold">Detalles de Café</h2>
                        <div class="mb-4">
                            <label for="cafe_data_numero_pagina" class="block mb-2 text-sm font-bold text-gray-700">Número de
                                página:</label>
                            <input type="number" name="cafe_data[numero_pagina]" id="cafe_data_numero_pagina"
                                class="w-full px-3 py-2 leading-tight text-gray-700 border rounded shadow appearance-none focus:outline-none focus:shadow-outline"
                                value="{{ old('cafe_data.numero_pagina', $producto->cafe->numero_pagina ?? '') }}">
                            @error('cafe_data.numero_pagina')
                                <p class="text-xs italic text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="mb-4">
                            <label for="cafe_data_clase" class="block mb-2 text-sm font-bold text-gray-700">Clase:</label>
                            <input type="text" name="cafe_data[clase]" id="cafe_data_clase"
                                class="w-full px-3 py-2 leading-tight text-gray-700 border rounded shadow appearance-none focus:outline-none focus:shadow-outline"
                                value="{{ old('cafe_data.clase', $producto->cafe->clase ?? '') }}">
                            @error('cafe_data.clase')
                                <p class="text-xs italic text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="mb-4">
                            <label for="cafe_data_informacion" class="block mb-2 text-sm font-bold text-gray-700">Información de
                                café:</label>
                            <textarea name="cafe_data[informacion]" id="cafe_data_informacion" rows="5"
                                class="w-full px-3 py-2 leading-tight text-gray-700 border rounded shadow appearance-none focus:outline-none focus:shadow-outline">{{ old('cafe_data.informacion', $producto->cafe->informacion ?? '') }}</textarea>
                            @error('cafe_data.informacion')
                                <p class="text-xs italic text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                @endif

                {{-- Campos específicos para Mora --}}
                @if ($producto->tipo === 'mora')
                    <div id="campos_mora" class="pt-4 mt-6 border-t border-gray-200">
                        <h2 class="mb-3 text-xl font-semibold">Detalles de mora</h2>
                        <div class="mb-4">
                            <label for="mora_data_numero_pagina" class="block mb-2 text-sm font-bold text-gray-700">Número de
                                página:</label>
                            <input type="number" name="mora_data[numero_pagina]" id="mora_data_numero_pagina"
                                class="w-full px-3 py-2 leading-tight text-gray-700 border rounded shadow appearance-none focus:outline-none focus:shadow-outline"
                                value="{{ old('mora_data.numero_pagina', $producto->mora->numero_pagina ?? '') }}">
                            @error('mora_data.numero_pagina')
                                <p class="text-xs italic text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="mb-4">
                            <label for="mora_data_clase" class="block mb-2 text-sm font-bold text-gray-700">Clase:</label>
                            <input type="text" name="mora_data[clase]" id="mora_data_clase"
                                class="w-full px-3 py-2 leading-tight text-gray-700 border rounded shadow appearance-none focus:outline-none focus:shadow-outline"
                                value="{{ old('mora_data.clase', $producto->mora->clase ?? '') }}">
                            @error('mora_data.clase')
                                <p class="text-xs italic text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="mb-4">
                            <label for="mora_data_informacion" class="block mb-2 text-sm font-bold text-gray-700">Información
                                de
                                mora:</label>
                            <textarea name="mora_data[informacion]" id="mora_data_informacion" rows="5"
                                class="w-full px-3 py-2 leading-tight text-gray-700 border rounded shadow appearance-none focus:outline-none focus:shadow-outline">{{ old('mora_data.informacion', $producto->mora->informacion ?? '') }}</textarea>
                            @error('mora_data.informacion')
                                <p class="text-xs italic text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                @endif

                {{-- Campos específicos para Videos --}}
                @if ($producto->tipo === 'videos')
                    <div id="campos_videos" class="pt-4 mt-6 border-t border-gray-200">
                        <h2 class="mb-3 text-xl font-semibold">Detalles de video</h2>

                        {{-- Campo de selección de Subtipo de Video --}}
                        <div class="mb-4">
                            <label for="subtipo_video" class="block mb-2 text-sm font-bold text-gray-700">Tipo de video (Subtipo):</label>
                            <select name="videos_data[tipo]" id="subtipo_video"
                                class="w-full px-3 py-2 leading-tight text-gray-700 border rounded shadow appearance-none focus:outline-none focus:shadow-outline">
                                <option value="">Seleccione un subtipo</option>
                                <option value="primarios" {{ old('videos_data.tipo', $producto->videos->tipo ?? '') == 'primarios' ? 'selected' : '' }}>Video primarios</option>
                                <option value="secundarios" {{ old('videos_data.tipo', $producto->videos->tipo ?? '') == 'secundarios' ? 'selected' : '' }}>Video secundarios</option>
                                <option value="categorias" {{ old('videos_data.tipo', $producto->videos->tipo ?? '') == 'categorias' ? 'selected' : '' }}>Video categorias</option>
                            </select>
                            @error('videos_data.tipo')
                                <p class="text-xs italic text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Campos específicos para cada subtipo de video --}}
                        <div id="campos_subtipo_primarios" class="hidden pt-4 mt-4 border-t border-gray-200">
                            <h3 class="mb-2 text-lg font-semibold">Campos para video primario</h3>
                            <div class="mb-4">
                                <label for="primarios_autor" class="block mb-2 text-sm font-bold text-gray-700">Autor:</label>
                                <input type="text" name="videos_data[primarios][autor]" id="primarios_autor"
                                    class="w-full px-3 py-2 leading-tight text-gray-700 border rounded shadow appearance-none focus:outline-none focus:shadow-outline"
                                    value="{{ old('videos_data.primarios.autor', $producto->videos->autor ?? '') }}">
                                @error('videos_data.primarios.autor')
                                    <p class="text-xs italic text-red-500">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="mb-4">
                                <label for="primarios_titulo" class="block mb-2 text-sm font-bold text-gray-700">Titulo:</label>
                                <input type="text" name="videos_data[primarios][titulo]" id="primarios_titulo"
                                    class="w-full px-3 py-2 leading-tight text-gray-700 border rounded shadow appearance-none focus:outline-none focus:shadow-outline"
                                    value="{{ old('videos_data.primarios.titulo', $producto->videos->titulo ?? '') }}">
                                @error('videos_data.primarios.titulo')
                                    <p class="text-xs italic text-red-500">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="mb-4">
                                <label for="primarios_descripcion"
                                    class="block mb-2 text-sm font-bold text-gray-700">Descripción:</label>
                                <textarea name="videos_data[primarios][descripcion]" id="primarios_descripcion" rows="5"
                                    class="w-full px-3 py-2 leading-tight text-gray-700 border rounded shadow appearance-none focus:outline-none focus:shadow-outline">{{ old('videos_data.primarios.descripcion', $producto->videos->descripcion ?? '') }}</textarea>
                                @error('videos_data.primarios.descripcion')
                                    <p class="text-xs italic text-red-500">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="mb-4">
                                <label for="primarios_rutaVideo" class="block mb-2 text-sm font-bold text-gray-700">URL del video (Específico):</label>
                                <input type="url" name="videos_data[primarios][rutaVideo]" id="primarios_rutaVideo"
                                    class="w-full px-3 py-2 leading-tight text-gray-700 border rounded shadow appearance-none focus:outline-none focus:shadow-outline"
                                    value="{{ old('videos_data.primarios.rutaVideo', $producto->videos->rutaVideo ?? '') }}" placeholder="https://ejemplo.com/tu-video-primario.mp4">
                                @error('videos_data.primarios.rutaVideo')
                                    <p class="text-xs italic text-red-500">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div id="campos_subtipo_secundarios" class="hidden pt-4 mt-4 border-t border-gray-200">
                            <h3 class="mb-2 text-lg font-semibold">Campos para video Secundario</h3>
                            <div class="mb-4">
                                <label for="secundarios_autor" class="block mb-2 text-sm font-bold text-gray-700">Autor:</label>
                                <input type="text" name="videos_data[secundarios][autor]" id="secundarios_autor"
                                    class="w-full px-3 py-2 leading-tight text-gray-700 border rounded shadow appearance-none focus:outline-none focus:shadow-outline"
                                    value="{{ old('videos_data.secundarios.autor', $producto->videos->autor ?? '') }}">
                                @error('videos_data.secundarios.autor')
                                    <p class="text-xs italic text-red-500">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="mb-4">
                                <label for="secundarios_titulo" class="block mb-2 text-sm font-bold text-gray-700">Titulo:</label>
                                <input type="text" name="videos_data[secundarios][titulo]" id="secundarios_titulo"
                                    class="w-full px-3 py-2 leading-tight text-gray-700 border rounded shadow appearance-none focus:outline-none focus:shadow-outline"
                                    value="{{ old('videos_data.secundarios.titulo', $producto->videos->titulo ?? '') }}">
                                @error('videos_data.secundarios.titulo')
                                    <p class="text-xs italic text-red-500">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="mb-4">
                                <label for="secundarios_descripcion"
                                    class="block mb-2 text-sm font-bold text-gray-700">Descripción:</label>
                                <textarea name="videos_data[secundarios][descripcion]" id="secundarios_descripcion" rows="5"
                                    class="w-full px-3 py-2 leading-tight text-gray-700 border rounded shadow appearance-none focus:outline-none focus:shadow-outline">{{ old('videos_data.secundarios.descripcion', $producto->videos->descripcion ?? '') }}</textarea>
                                @error('videos_data.secundarios.descripcion')
                                    <p class="text-xs italic text-red-500">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="mb-4">
                                <label for="secundarios_rutaVideo" class="block mb-2 text-sm font-bold text-gray-700">URL del video (Específico):</label>
                                <input type="url" name="videos_data[secundarios][rutaVideo]" id="secundarios_rutaVideo"
                                    class="w-full px-3 py-2 leading-tight text-gray-700 border rounded shadow appearance-none focus:outline-none focus:shadow-outline"
                                    value="{{ old('videos_data.secundarios.rutaVideo', $producto->videos->rutaVideo ?? '') }}" placeholder="https://ejemplo.com/tu-video-secundario.mp4">
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
                                    value="{{ old('videos_data.categorias.autor', $producto->videos->autor ?? '') }}">
                                @error('videos_data.categorias.autor')
                                    <p class="text-xs italic text-red-500">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="mb-4">
                                <label for="categorias_titulo" class="block mb-2 text-sm font-bold text-gray-700">Titulo:</label>
                                <input type="text" name="videos_data[categorias][titulo]" id="categorias_titulo"
                                    class="w-full px-3 py-2 leading-tight text-gray-700 border rounded shadow appearance-none focus:outline-none focus:shadow-outline"
                                    value="{{ old('videos_data.categorias.titulo', $producto->videos->titulo ?? '') }}">
                                @error('videos_data.categorias.titulo')
                                    <p class="text-xs italic text-red-500">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="mb-4">
                                <label for="categorias_descripcion"
                                    class="block mb-2 text-sm font-bold text-gray-700">Descripción:</label>
                                <textarea name="videos_data[categorias][descripcion]" id="categorias_descripcion" rows="5"
                                    class="w-full px-3 py-2 leading-tight text-gray-700 border rounded shadow appearance-none focus:outline-none focus:shadow-outline">{{ old('videos_data.categorias.descripcion', $producto->videos->descripcion ?? '') }}</textarea>
                                @error('videos_data.categorias.descripcion')
                                    <p class="text-xs italic text-red-500">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="mb-4">
                                <label for="categorias_rutaVideo" class="block mb-2 text-sm font-bold text-gray-700">URL del video (Específico):</label>
                                <input type="url" name="videos_data[categorias][rutaVideo]" id="categorias_rutaVideo"
                                    class="w-full px-3 py-2 leading-tight text-gray-700 border rounded shadow appearance-none focus:outline-none focus:shadow-outline"
                                    value="{{ old('videos_data.categorias.rutaVideo', $producto->videos->rutaVideo ?? '') }}" placeholder="https://ejemplo.com/tu-video-categoria.mp4">
                                @error('videos_data.categorias.rutaVideo')
                                    <p class="text-xs italic text-red-500">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                @endif

                <div class="flex items-center justify-between mt-6">
                    <button type="submit"
                        class="px-4 py-2 font-bold text-white bg-green-500 rounded hover:bg-green-700 focus:outline-none focus:shadow-outline">
                        Actualizar producto
                    </button>

                    <a href="{{ route('productos.index') }}"
                        class="inline-block text-sm font-bold text-blue-500 align-baseline hover:text-blue-800">
                        Cancelar
                    </a>
                </div>
            </form>
        </div>
    @endcan

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const tipoInput = document.getElementById('tipo'); // Es un input readonly ahora
            const camposComunesNoVideoEdit = document.getElementById('campos_comunes_no_video_edit'); // Nuevo elemento
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
                const selectedType = tipoInput.value; // Usamos el valor del input readonly

                // Ocultar todos los campos específicos y el RutaVideo del producto general
                // Esto es importante para el caso de que el tipo de producto se haya cambiado
                // en el controlador (aunque el input sea readonly aquí, la lógica de JS
                // debe ser robusta para la carga inicial).
                if (camposCafe) camposCafe.classList.add('hidden');
                if (camposMora) camposMora.classList.add('hidden');
                if (camposVideos) camposVideos.classList.add('hidden');
                if (camposRutaVideoProducto) camposRutaVideoProducto.classList.add('hidden');

                // Ocultar los campos comunes no-video por defecto (Imagen y Observaciones)
                if (camposComunesNoVideoEdit) camposComunesNoVideoEdit.classList.add('hidden'); // Ocultar por defecto

                // Ocultar todos los campos de subtipo de video por defecto
                if (camposSubtipoPrimarios) camposSubtipoPrimarios.classList.add('hidden');
                if (camposSubtipoSecundarios) camposSubtipoSecundarios.classList.add('hidden');
                if (camposSubtipoCategorias) camposSubtipoCategorias.classList.add('hidden');


                if (selectedType === 'café') {
                    if (camposComunesNoVideoEdit) camposComunesNoVideoEdit.classList.remove('hidden'); // Mostrar para Café
                    if (camposCafe) camposCafe.classList.remove('hidden');
                    if (camposRutaVideoProducto) camposRutaVideoProducto.classList.remove('hidden'); // Mostrar RutaVideo para Café
                } else if (selectedType === 'mora') {
                    if (camposComunesNoVideoEdit) camposComunesNoVideoEdit.classList.remove('hidden'); // Mostrar para Mora
                    if (camposMora) camposMora.classList.remove('hidden');
                    if (camposRutaVideoProducto) camposRutaVideoProducto.classList.remove('hidden'); // Mostrar RutaVideo para Mora
                } else if (selectedType === 'videos') {
                    // camposComunesNoVideoEdit se mantiene oculto para videos
                    if (camposVideos) camposVideos.classList.remove('hidden');
                    // No mostrar camposRutaVideoProducto para 'videos'
                    // Y llamar a la función para mostrar/ocultar subtipos de video
                    toggleSubtypeFields();
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
                if (camposSubtipoPrimarios) camposSubtipoPrimarios.classList.add('hidden');
                if (camposSubtipoSecundarios) camposSubtipoSecundarios.classList.add('hidden');
                if (camposSubtipoCategorias) camposSubtipoCategorias.classList.add('hidden');

                // Mostrar el div correspondiente al subtipo seleccionado
                if (selectedSubtype === 'primarios') {
                    if (camposSubtipoPrimarios) camposSubtipoPrimarios.classList.remove('hidden');
                } else if (selectedSubtype === 'secundarios') {
                    if (camposSubtipoSecundarios) camposSubtipoSecundarios.classList.remove('hidden');
                } else if (selectedSubtype === 'categorias') {
                    if (camposSubtipoCategorias) camposSubtipoCategorias.classList.remove('hidden');
                }
            }

            // No hay un event listener para tipoInput porque es readonly.
            // La visibilidad inicial se gestiona en la carga del DOM.

            // Escuchar cambios en el select de subtipo de video
            if (subtipoVideoSelect) {
                subtipoVideoSelect.addEventListener('change', toggleSubtypeFields);
            }

            // Llamar a las funciones en la carga inicial para manejar los valores existentes del producto
            toggleProductFields();
            // Si el tipo de producto es 'videos' al cargar la página, también inicializa los subtipos
            if (tipoInput.value === 'videos') {
                toggleSubtypeFields();
            }
        });
    </script>
@endsection
