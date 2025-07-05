@extends('layouts.app') 
@section('content')
    <div class="container p-4 mx-auto">
        <h1 class="mb-4 text-2xl font-bold">Crear nueva noticia</h1>

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

        <form action="{{ route('noticias.store') }}" method="POST" enctype="multipart/form-data"
            class="p-6 bg-white rounded-lg shadow-md">
            @csrf

            <div class="mb-4">
                <label for="tipo" class="block mb-2 text-sm font-bold text-gray-700">Tipo de producto:</label>
                <select name="tipo" id="tipo"
                    class="w-full px-3 py-2 leading-tight text-gray-700 border rounded shadow appearance-none focus:outline-none focus:shadow-outline">
                    <option value="">Seleccione un tipo</option>
                    <option value="café" {{ old('tipo') == 'café' ? 'selected' : '' }}>Café</option>
                    <option value="mora" {{ old('tipo') == 'mora' ? 'selected' : '' }}>Mora</option>
                </select>
                @error('tipo')
                    <p class="text-xs italic text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="titulo" class="block mb-2 text-sm font-bold text-gray-700">Título:</label>
                <input type="text" name="titulo" id="titulo"
                    class="w-full px-3 py-2 leading-tight text-gray-700 border rounded shadow appearance-none focus:outline-none focus:shadow-outline"
                    value="{{ old('titulo') }}">
                @error('titulo')
                    <p class="text-xs italic text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="clase" class="block mb-2 text-sm font-bold text-gray-700">Clase (opcional):</label>
                <input type="text" name="clase" id="clase"
                    class="w-full px-3 py-2 leading-tight text-gray-700 border rounded shadow appearance-none focus:outline-none focus:shadow-outline"
                    value="{{ old('clase') }}">
                @error('clase')
                    <p class="text-xs italic text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="autor" class="block mb-2 text-sm font-bold text-gray-700">Autor acreditado
                    (opcional):</label>
                <input type="text" name="autor" id="autor"
                    class="w-full px-3 py-2 leading-tight text-gray-700 border rounded shadow appearance-none focus:outline-none focus:shadow-outline"
                    value="{{ old('autor') }}" placeholder="Ej. El aduanero viejo">
                @error('autor')
                    <p class="text-xs italic text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="imagen" class="block mb-2 text-sm font-bold text-gray-700">Imagen (opcional):</label>
                <input type="file" name="imagen" id="imagen"
                    class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none">
                @error('imagen')
                    <p class="text-xs italic text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="informacion" class="block mb-2 text-sm font-bold text-gray-700">Información:</label>
                <textarea name="informacion" id="informacion" rows="5"
                    class="w-full px-3 py-2 leading-tight text-gray-700 border rounded shadow appearance-none focus:outline-none focus:shadow-outline">{{ old('informacion') }}</textarea>
                @error('informacion')
                    <p class="text-xs italic text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="numero_pagina" class="block mb-2 text-sm font-bold text-gray-700">Número de página:</label>
                <input type="number" name="numero_pagina" id="numero_pagina"
                    class="w-full px-3 py-2 leading-tight text-gray-700 border rounded shadow appearance-none focus:outline-none focus:shadow-outline"
                    value="{{ old('numero_pagina') }}">
                @error('numero_pagina')
                    <p class="text-xs italic text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center justify-between">

                <button type="submit"
                    class="px-4 py-2 font-bold text-white bg-blue-500 rounded hover:bg-blue-700 focus:outline-none focus:shadow-outline">
                    Guardar noticia
                </button>

                <a href="{{ route('noticias.index') }}"
                    class="inline-block text-sm font-bold text-blue-500 align-baseline hover:text-blue-800">
                    Cancelar
                </a>
            </div>
        </form>
    </div>
@endsection
