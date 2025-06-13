@extends('layouts.app') {{-- Asume que tienes un layout base --}}

@section('content')
<div class="container mx-auto p-4">
    <h1 class="text-2xl font-bold mb-4">Editar Noticia: {{ $noticia->titulo ?? 'Sin Título' }}</h1>

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

    <form action="{{ route('noticias.noticias.update', $noticia->id_noticias) }}" method="POST" enctype="multipart/form-data" class="bg-white shadow-md rounded-lg p-6">
        @csrf
        @method('PUT') {{-- Método PUT para actualizaciones en Laravel --}}

        <div class="mb-4">
            <label for="tipo" class="block text-gray-700 text-sm font-bold mb-2">Tipo de Producto:</label>
            <select name="tipo" id="tipo" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                <option value="">Seleccione un tipo</option>
                <option value="café" {{ old('tipo') == 'café' ? 'selected' : '' }}>Café</option>
                <option value="mora" {{ old('tipo') == 'mora' ? 'selected' : '' }}>Mora</option>
            </select>
            @error('tipo')
                <p class="text-red-500 text-xs italic">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label for="titulo" class="block text-gray-700 text-sm font-bold mb-2">Título:</label>
            <input type="text" name="titulo" id="titulo" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" value="{{ old('titulo', $noticia->titulo) }}">
            @error('titulo')
                <p class="text-red-500 text-xs italic">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label for="clase" class="block text-gray-700 text-sm font-bold mb-2">Clase (Opcional):</label>
            <input type="text" name="clase" id="clase" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" value="{{ old('clase', $noticia->clase) }}">
            @error('clase')
                <p class="text-red-500 text-xs italic">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label for="autor" class="block text-gray-700 text-sm font-bold mb-2">Autor Acreditado (Opcional):</label> {{-- ¡NUEVO CAMPO! --}}
            <input type="text" name="autor" id="autor" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" value="{{ old('autor', $noticia->autor) }}" placeholder="Ej. El aduanero viejo">
            @error('autor')
                <p class="text-red-500 text-xs italic">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label for="imagen" class="block text-gray-700 text-sm font-bold mb-2">Imagen Actual:</label>
            @if ($noticia->imagen)
                <img src="{{ asset('storage/' . $noticia->imagen) }}" alt="Imagen actual de la noticia" class="w-32 h-32 object-cover rounded-lg mb-2">
            @else
                <p class="text-gray-600 text-sm mb-2">No hay imagen actual.</p>
            @endif
            <label for="nueva_imagen" class="block text-gray-700 text-sm font-bold mb-2">Subir Nueva Imagen (Opcional):</label>
            <input type="file" name="imagen" id="nueva_imagen" class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none">
            @error('imagen')
                <p class="text-red-500 text-xs italic">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label for="informacion" class="block text-gray-700 text-sm font-bold mb-2">Información:</label>
            <textarea name="informacion" id="informacion" rows="5" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">{{ old('informacion', $noticia->informacion) }}</textarea>
            @error('informacion')
                <p class="text-red-500 text-xs italic">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label for="numero_pagina" class="block text-gray-700 text-sm font-bold mb-2">Número de Página:</label>
            <input type="number" name="numero_pagina" id="numero_pagina" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" value="{{ old('numero_pagina', $noticia->numero_pagina) }}">
            @error('numero_pagina')
                <p class="text-red-500 text-xs italic">{{ $message }}</p>
            @enderror
        </div>

        {{-- El estado 'pendiente' es el valor por defecto y podría ser gestionado por un administrador --}}
        {{-- Si el estado es editable por el usuario, descomenta el siguiente bloque --}}
        {{--
        <div class="mb-4">
            <label for="estado" class="block text-gray-700 text-sm font-bold mb-2">Estado:</label>
            <select name="estado" id="estado" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                <option value="pendiente" {{ old('estado', $noticia->estado) == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                <option value="aprobada" {{ old('estado', $noticia->estado) == 'aprobada' ? 'selected' : '' }}>Aprobada</option>
                <option value="rechazada" {{ old('estado', $noticia->estado) == 'rechazada' ? 'selected' : '' }}>Rechazada</option>
            </select>
            @error('estado')
                <p class="text-red-500 text-xs italic">{{ $message }}</p>
            @enderror
        </div>
        --}}

        <div class="flex items-center justify-between">
            <button type="submit" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                Actualizar Noticia
            </button>
            <a href="{{ route('noticias.noticias.index') }}" class="inline-block align-baseline font-bold text-sm text-blue-500 hover:text-blue-800">
                Cancelar
            </a>
        </div>
    </form>
</div>
@endsection
