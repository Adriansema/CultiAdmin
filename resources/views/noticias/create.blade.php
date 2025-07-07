@extends('layouts.app') {{-- Asume que tienes un layout base --}}

@section('content')
    <div class="inline-block px-10 py-6">
        <div class="flex items-center space-x-4">
            <img src="{{ asset('images/reverse.svg') }}" class="w-4 h-4" alt="Icono Nuevo Usuario">
            <h1 class="text-3xl whitespace-nowrap font-bold">Crear nueva noticia</h1>
        </div>
        <div class="py-2">
            {!! Breadcrumbs::render('noticias.create') !!}
        </div>
    </div>

    <div class="container mx-auto p-4">
        <form action="{{ route('noticias.store') }}" method="POST" enctype="multipart/form-data"
            class="bg-[var(--color-Gestion)] shadow-2xl rounded-2xl p-6 md:p-10 border border-gray-300">
            @csrf

            <!-- Campo: Tipo de Producto (Requerido) -->
            <div class="mb-4">
                <label for="tipo" class="block text-gray-700 text-sm font-bold mb-2">
                    Tipo de producto: <span class="text-red-500">*</span>
                </label>
                <select name="tipo" id="tipo"
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('tipo') border-red-500 @enderror">
                    <option value="">Seleccione un tipo</option>
                    <option value="café" {{ old('tipo') == 'café' ? 'selected' : '' }}>Café</option>
                    <option value="mora" {{ old('tipo') == 'mora' ? 'selected' : '' }}>Mora</option>
                </select>
                @error('tipo')
                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                @enderror
            </div>

            <!-- Campo: Título (Requerido) -->
            <div class="mb-4">
                <label for="titulo" class="block text-gray-700 text-sm font-bold mb-2">
                    Título: <span class="text-red-500">*</span>
                </label>
                <input type="text" name="titulo" id="titulo"
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('titulo') border-red-500 @enderror"
                    value="{{ old('titulo') }}">
                @error('titulo')
                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                @enderror
            </div>

            <!-- Campo: Clase (Requerido) -->
            <div class="mb-4">
                <label for="clase" class="block text-gray-700 text-sm font-bold mb-2">
                    Clase: <span class="text-red-500">*</span>
                </label>
                <input type="text" name="clase" id="clase"
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('clase') border-red-500 @enderror"
                    value="{{ old('clase') }}">
                @error('clase')
                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                @enderror
            </div>

            <!-- Campo: Autor (Requerido) -->
            <div class="mb-4">
                <label for="autor" class="block text-gray-700 text-sm font-bold mb-2">
                    Autor acreditado: <span class="text-red-500">*</span>
                </label>
                <input type="text" name="autor" id="autor"
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('autor') border-red-500 @enderror"
                    value="{{ old('autor') }}" placeholder="Ej. El aduanero viejo">
                @error('autor')
                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                @enderror
            </div>

            <!-- Campo: Imagen (Requerido) -->
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

            <!-- Campo: Información (Requerido) -->
            <div class="mb-4">
                <label for="informacion" class="block text-gray-700 text-sm font-bold mb-2">
                    Información: <span class="text-red-500">*</span>
                </label>
                <textarea name="informacion" id="informacion" rows="5"
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('informacion') border-red-500 @enderror">{{ old('informacion') }}</textarea>
                @error('informacion')
                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                @enderror
            </div>

            <!-- Campo: Número de Página (Requerido) -->
            <div class="mb-4">
                <label for="numero_pagina" class="block text-gray-700 text-sm font-bold mb-2">
                    Número de página: <span class="text-red-500">*</span>
                </label>
                <input type="number" name="numero_pagina" id="numero_pagina"
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('numero_pagina') border-red-500 @enderror"
                    value="{{ old('numero_pagina') }}">
                @error('numero_pagina')
                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                @enderror
            </div>


            <div class="flex items-center justify-between">

                <a href="{{ route('noticias.index') }}"
                    class="bg-[var(--color-textmarca)] hover:bg-[var(--color-texthovermarca)] py-3 px-4 rounded-full text-md font-bold text-white focus:outline-none focus:shadow-outline inline-flex items-center transition duration-150 ease-in-out transform hover:-translate-x-1">
                    <img src="{{ asset('images/regresar.svg') }}" alt="Regresar" class="w-5 h-6 mr-2">
                    <span class="whitespace-nowrap text-inherit">{{ __('Cancelar') }}</span>
                </a>
                <button type="submit"
                    class="bg-[var(--color-sgt)] hover:bg-[var(--color-hoversgt)] py-3 px-4 rounded-full text-md font-bold text-white focus:outline-none focus:shadow-outline inline-flex items-center transition duration-150 ease-in-out transform hover:translate-x-1">
                    <span class="whitespace-nowrap text-inherit">{{ __('Crear noticia') }}</span>
                    <img src="{{ asset('images/siguiente.svg') }}" alt="siguiente" class="w-5 h-6 ml-2">
                </button>
            </div>
        </form>
    </div>
@endsection
