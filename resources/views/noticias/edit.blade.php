@extends('layouts.app') {{-- Asume que tienes un layout base --}}

@section('content')
    <div class="inline-block px-20 py-6">
        <div class="flex items-center space-x-4">
            <img src="{{ asset('images/reverse.svg') }}" class="w-4 h-4" alt="Icono Nuevo Usuario">
            <h1 class="text-3xl whitespace-nowrap font-bold"> Editar noticia</h1>
        </div>
        <div class="py-2">
            {!! Breadcrumbs::render('noticias.edit', $noticia) !!}
        </div>
    </div>

    <div class="container mx-auto p-10">
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

        <form action="{{ route('noticias.update', $noticia->id_noticias) }}" method="POST" enctype="multipart/form-data"
            class="bg-white shadow-2xl rounded-2xl p-6 md:p-10 border border-gray-300">
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
                        value="{{ $noticia->tipo }}" readonly aria-describedby="tipo-help">
                    <span id="tipo-help" class="absolute top-1/2 right-4 -translate-y-1/2 text-gray-500 text-sm italic">
                        No editable
                    </span>
                </div>
                @error('tipo')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label for="titulo" class="block text-gray-700 text-sm font-bold mb-2">Título:</label>
                <input type="text" name="titulo" id="titulo"
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                    value="{{ old('titulo', $noticia->titulo) }}">
                @error('titulo')
                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label for="clase" class="block text-gray-700 text-sm font-bold mb-2">Clase:</label>
                <input type="text" name="clase" id="clase"
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                    value="{{ old('clase', $noticia->clase) }}">
                @error('clase')
                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label for="autor" class="block text-gray-700 text-sm font-bold mb-2">Autor acréditado:</label>
                <input type="text" name="autor" id="autor"
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                    value="{{ old('autor', $noticia->autor) }}" placeholder="Ej. El aduanero viejo">
                @error('autor')
                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label for="imagen" class="block text-gray-700 text-sm font-bold mb-2">Imagen actual:</label>
                @if ($noticia->imagen)
                    <img src="{{ asset('storage/' . $noticia->imagen) }}" alt="Imagen actual de la noticia"
                        class="w-32 h-32 object-cover rounded-lg mb-2">
                @else
                    <p class="text-gray-600 text-sm mb-2">No hay imagen actual.</p>
                @endif
                <label for="nueva_imagen" class="block text-gray-700 text-sm font-bold mb-2">Subir nueva imagen</label>
                <input type="file" name="imagen" id="nueva_imagen"
                    class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none">
                @error('imagen')
                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label for="informacion" class="block text-gray-700 text-sm font-bold mb-2">Información:</label>
                <textarea name="informacion" id="informacion" rows="5"
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">{{ old('informacion', $noticia->informacion) }}</textarea>
                @error('informacion')
                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-10">
                <label for="numero_pagina" class="block text-gray-700 text-sm font-bold mb-2">Número de página:</label>
                <input type="number" name="numero_pagina" id="numero_pagina"
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                    value="{{ old('numero_pagina', $noticia->numero_pagina) }}">
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
                    <span class="whitespace-nowrap text-inherit">{{ __('Actualizar') }}</span>
                    <img src="{{ asset('images/siguiente.svg') }}" alt="siguiente" class="w-5 h-6 ml-2">
                </button>
            </div>
        </form>
    </div>
@endsection
