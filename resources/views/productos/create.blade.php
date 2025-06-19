@extends('layouts.app') {{-- Asume que tienes un layout base --}}

@section('content')
    <div class="container mx-auto p-4">
        <h1 class="text-2xl font-bold mb-4">Crear Nuevo Producto</h1>

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

        <form action="{{ route('productos.store') }}" method="POST" enctype="multipart/form-data"
            class="bg-white shadow-md rounded-lg p-6">
            @csrf {{-- Protección CSRF obligatoria en Laravel --}}
            <div class="mb-4">
                <label for="tipo" class="block text-gray-700 text-sm font-bold mb-2">Tipo de Producto:</label>
                <select name="tipo" id="tipo"
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    <option value="">Seleccione un tipo</option>
                    <option value="café" {{ old('tipo') == 'café' ? 'selected' : '' }}>Café</option>
                    <option value="mora" {{ old('tipo') == 'mora' ? 'selected' : '' }}>Mora</option>
                </select>
                @error('tipo')
                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="imagen" class="block text-gray-700 text-sm font-bold mb-2">Imagen:</label>
                <input type="file" name="imagen" id="imagen"
                    class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none">
                @error('imagen')
                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="observaciones" class="block text-gray-700 text-sm font-bold mb-2">Observaciones
                    Generales:</label>
                <textarea name="observaciones" id="observaciones" rows="3"
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">{{ old('observaciones') }}</textarea>
                @error('observaciones')
                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                @enderror
            </div>

            {{-- Campos específicos para Café --}}
            <div id="campos_cafe" class="hidden"> {{-- Usa JS para mostrar/ocultar esto basado en la selección del tipo --}}
                <h2 class="text-xl font-semibold mb-3">Detalles de Café</h2>
                <div class="mb-4">
                    <label for="cafe_data_numero_pagina" class="block text-gray-700 text-sm font-bold mb-2">Número de
                        Página:</label>
                    <input type="number" name="cafe_data[numero_pagina]" id="cafe_data_numero_pagina"
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                        value="{{ old('cafe_data.numero_pagina') }}">
                    @error('cafe_data.numero_pagina')
                        <p class="text-red-500 text-xs italic">{{ $message }}</p>
                    @enderror
                </div>
                <div class="mb-4">
                    <label for="cafe_data_clase" class="block text-gray-700 text-sm font-bold mb-2">Clase:</label>
                    <input type="text" name="cafe_data[clase]" id="cafe_data_clase"
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                        value="{{ old('cafe_data.clase') }}">
                    @error('cafe_data.clase')
                        <p class="text-red-500 text-xs italic">{{ $message }}</p>
                    @enderror
                </div>
                <div class="mb-4">
                    <label for="cafe_data_informacion" class="block text-gray-700 text-sm font-bold mb-2">Información de
                        Café:</label>
                    <textarea name="cafe_data[informacion]" id="cafe_data_informacion" rows="5"
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">{{ old('cafe_data.informacion') }}</textarea>
                    @error('cafe_data.informacion')
                        <p class="text-red-500 text-xs italic">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Campos específicos para Mora --}}
            <div id="campos_mora" class="hidden"> {{-- Usa JS para mostrar/ocultar esto basado en la selección del tipo --}}
                <h2 class="text-xl font-semibold mb-3">Detalles de Mora</h2>
                <div class="mb-4">
                    <label for="mora_data_numero_pagina" class="block text-gray-700 text-sm font-bold mb-2">Número de
                        Página:</label>
                    <input type="number" name="mora_data[numero_pagina]" id="mora_data_numero_pagina"
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                        value="{{ old('mora_data.numero_pagina') }}">
                    @error('mora_data.numero_pagina')
                        <p class="text-red-500 text-xs italic">{{ $message }}</p>
                    @enderror
                </div>
                <div class="mb-4">
                    <label for="mora_data_clase" class="block text-gray-700 text-sm font-bold mb-2">Clase:</label>
                    <input type="text" name="mora_data[clase]" id="mora_data_clase"
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                        value="{{ old('mora_data.clase') }}">
                    @error('mora_data.clase')
                        <p class="text-red-500 text-xs italic">{{ $message }}</p>
                    @enderror
                </div>
                <div class="mb-4">
                    <label for="mora_data_informacion" class="block text-gray-700 text-sm font-bold mb-2">Información de
                        Mora:</label>
                    <textarea name="mora_data[informacion]" id="mora_data_informacion" rows="5"
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">{{ old('mora_data.informacion') }}</textarea>
                    @error('mora_data.informacion')
                        <p class="text-red-500 text-xs italic">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="flex items-center justify-between">
                <button type="submit"
                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                    Guardar Producto
                </button>

                <a href="{{ route('productos.index') }}"
                    class="inline-block align-baseline font-bold text-sm text-blue-500 hover:text-blue-800">
                    Cancelar
                </a>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const tipoSelect = document.getElementById('tipo');
            const camposCafe = document.getElementById('campos_cafe');
            const camposMora = document.getElementById('campos_mora');

            function toggleProductFields() {
                const selectedType = tipoSelect.value;
                camposCafe.classList.add('hidden');
                camposMora.classList.add('hidden');

                if (selectedType === 'café') {
                    camposCafe.classList.remove('hidden');
                } else if (selectedType === 'mora') {
                    camposMora.classList.remove('hidden');
                }
            }

            tipoSelect.addEventListener('change', toggleProductFields);

            // Llamar en la carga inicial para manejar old() values
            toggleProductFields();
        });
    </script>
@endsection
