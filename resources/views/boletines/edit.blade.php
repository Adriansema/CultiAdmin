{{-- resources/views/boletines/edit.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto mt-10">
    <div class="p-6 bg-white rounded shadow">
        <h2 class="mb-4 text-xl font-bold">Editar Boletín</h2>

        <form action="{{ route('boletines.update', $boletin) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label for="archivo" class="block font-semibold">Archivo (URL o nombre):</label>
                <input type="text" name="archivo" id="archivo" value="{{ old('archivo', $boletin->archivo) }}"
                    class="w-full p-2 mt-1 border rounded" required>
                @error('archivo')
                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="contenido" class="block font-semibold">Contenido:</label>
                <textarea name="contenido" id="contenido" rows="8" class="w-full p-2 mt-1 border rounded"
                    required>{{ old('contenido', $boletin->contenido) }}</textarea>
                @error('contenido')
                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center justify-between mb-6">
                <a href="{{ route('boletines.index') }}"
                    class="inline-block px-4 py-2 text-white bg-gray-600 rounded hover:bg-gray-700">
                    Volver al listado
                </a>
                <button type="submit" class="px-4 py-2 text-white bg-green-600 rounded hover:bg-green-700">
                    Actualizar
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
