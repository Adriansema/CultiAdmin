@extends('layouts.app')

@section('content')

    @can('editar producto')
        <div class="container mx-auto p-4">
            <h1 class="text-2xl font-bold mb-4">Editar Producto: {{ $producto->tipo }} - ID: {{ $producto->id }}</h1>

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
                class="bg-white shadow-md rounded-lg p-6">
                @csrf
                @method('PUT') {{-- Método PUT para actualizaciones en Laravel --}}

                <div class="mb-4">
                    <label for="tipo" class="block text-gray-700 text-sm font-bold mb-2">Tipo de Producto:</label>
                    {{-- El tipo no se permite cambiar en la edición según tu controlador, así que lo mostramos deshabilitado --}}
                    <input type="text" name="tipo" id="tipo"
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 bg-gray-100 leading-tight focus:outline-none focus:shadow-outline"
                        value="{{ $producto->tipo }}" readonly>
                    @error('tipo')
                        <p class="text-red-500 text-xs italic">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="imagen" class="block text-gray-700 text-sm font-bold mb-2">Imagen Actual:</label>
                    @if ($producto->imagen)
                        <img src="{{ asset('storage/' . $producto->imagen) }}" alt="Imagen actual del producto"
                            class="w-32 h-32 object-cover rounded-lg mb-2">
                    @else
                        <p class="text-gray-600 text-sm mb-2">No hay imagen actual.</p>
                    @endif
                    <label for="nueva_imagen" class="block text-gray-700 text-sm font-bold mb-2">Subir Nueva Imagen
                        (Opcional):</label>
                    <input type="file" name="imagen" id="nueva_imagen"
                        class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none">
                    @error('imagen')
                        <p class="text-red-500 text-xs italic">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="observaciones" class="block text-gray-700 text-sm font-bold mb-2">Observaciones
                        Generales:</label>
                    <textarea name="observaciones" id="observaciones" rows="3"
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">{{ old('observaciones', $producto->observaciones) }}</textarea>
                    @error('observaciones')
                        <p class="text-red-500 text-xs italic">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Campos específicos para Café --}}
                @if ($producto->tipo === 'café' && $producto->cafe)
                    <div id="campos_cafe">
                        <h2 class="text-xl font-semibold mb-3">Detalles de Café</h2>
                        <div class="mb-4">
                            <label for="cafe_data_numero_pagina" class="block text-gray-700 text-sm font-bold mb-2">Número de
                                Página:</label>
                            <input type="number" name="cafe_data[numero_pagina]" id="cafe_data_numero_pagina"
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                value="{{ old('cafe_data.numero_pagina', $producto->cafe->numero_pagina) }}">
                            @error('cafe_data.numero_pagina')
                                <p class="text-red-500 text-xs italic">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="mb-4">
                            <label for="cafe_data_clase" class="block text-gray-700 text-sm font-bold mb-2">Clase:</label>
                            <input type="text" name="cafe_data[clase]" id="cafe_data_clase"
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                value="{{ old('cafe_data.clase', $producto->cafe->clase) }}">
                            @error('cafe_data.clase')
                                <p class="text-red-500 text-xs italic">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="mb-4">
                            <label for="cafe_data_informacion" class="block text-gray-700 text-sm font-bold mb-2">Información de
                                Café:</label>
                            <textarea name="cafe_data[informacion]" id="cafe_data_informacion" rows="5"
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">{{ old('cafe_data.informacion', $producto->cafe->informacion) }}</textarea>
                            @error('cafe_data.informacion')
                                <p class="text-red-500 text-xs italic">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="mb-4">
                            <label for="RutaVideo" class="block text-gray-700 text-sm font-bold mb-2">URL del Video:</label>
                            <input type="url" name="RutaVideo" id="RutaVideo"
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                value="{{ old('RutaVideo', $producto->RutaVideo) }}"
                                placeholder="https://ejemplo.com/tu-video.mp4">
                            @error('RutaVideo')
                                <p class="text-red-500 text-xs italic">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                @endif

                {{-- Campos específicos para Mora --}}
                @if ($producto->tipo === 'mora' && $producto->mora)
                    <div id="campos_mora">
                        <h2 class="text-xl font-semibold mb-3">Detalles de Mora</h2>
                        <div class="mb-4">
                            <label for="mora_data_numero_pagina" class="block text-gray-700 text-sm font-bold mb-2">Número de
                                Página:</label>
                            <input type="number" name="mora_data[numero_pagina]" id="mora_data_numero_pagina"
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                value="{{ old('mora_data.numero_pagina', $producto->mora->numero_pagina) }}">
                            @error('mora_data.numero_pagina')
                                <p class="text-red-500 text-xs italic">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="mb-4">
                            <label for="mora_data_clase" class="block text-gray-700 text-sm font-bold mb-2">Clase:</label>
                            <input type="text" name="mora_data[clase]" id="mora_data_clase"
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                value="{{ old('mora_data.clase', $producto->mora->clase) }}">
                            @error('mora_data.clase')
                                <p class="text-red-500 text-xs italic">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="mb-4">
                            <label for="mora_data_informacion" class="block text-gray-700 text-sm font-bold mb-2">Información
                                de
                                Mora:</label>
                            <textarea name="mora_data[informacion]" id="mora_data_informacion" rows="5"
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">{{ old('mora_data.informacion', $producto->mora->informacion) }}</textarea>
                            @error('mora_data.informacion')
                                <p class="text-red-500 text-xs italic">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="mb-4">
                            <label for="RutaVideo" class="block text-gray-700 text-sm font-bold mb-2">URL del Video:</label>
                            <input type="url" name="RutaVideo" id="RutaVideo"
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                value="{{ old('RutaVideo', $producto->RutaVideo) }}"
                                placeholder="https://ejemplo.com/tu-video.mp4">
                            @error('RutaVideo')
                                <p class="text-red-500 text-xs italic">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                @endif

                <div class="flex items-center justify-between">
                    <button type="submit"
                        class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                        Actualizar Producto
                    </button>

                    <a href="{{ route('productos.index') }}"
                        class="inline-block align-baseline font-bold text-sm text-blue-500 hover:text-blue-800">
                        Cancelar
                    </a>
                </div>
            </form>
        </div>
    @endcan
@endsection
