@extends('layouts.app')

@section('header')
    <h2 class="text-xl font-semibold">Listado de Boletines</h2>
@endsection

@section('content')
    <div class="max-w-6xl py-6 mx-auto">
        @if (session('success'))
            <div class="p-4 mb-4 text-green-800 bg-green-100 border border-green-300 rounded">
                {{ session('success') }}
            </div>
        @endif

        <div class="inline-block px-8 py-10">
            <div class="flex items-center space-x-2">
                <img src="{{ asset('images/reverse.svg') }}" class="w-4 h-4" alt="Icono Nuevo Usuario">
                <h1 class="text-3xl font-bold whitespace-nowrap">Boletines</h1>
            </div>
            {!! Breadcrumbs::render('boletines.index') !!}
        </div>

<div x-data="{ open: false }">
    <div class="mb-4">
        <button 
            @click="open = true" 
            class="px-4 py-2 text-white bg-green-600 rounded-3xl hover:bg-green-700"
        >
            + Crear / Importar Boletín
        </button>
    </div>

    <!-- Modal -->
    <div
        x-show="open"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50"
        style="display: none;"
    >
        <div class="w-full max-w-2xl p-6 bg-white rounded shadow-lg">
            <h3 class="mb-4 text-xl font-semibold">Crear Boletín o Importar desde PDF</h3>

           <!-- Crear boletín e importar PDF -->
<form action="{{ route('boletines.importarPdf') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="mb-4">
        <label class="block font-semibold">Contenido del boletín</label>
        <textarea name="contenido" required
            class="w-full px-3 py-2 border rounded"
            placeholder="Ej: Boletín de mora - semana 3"></textarea>
    </div>
    <div class="mb-4">
        <label class="block font-semibold">Importar archivo PDF</label>
        <input type="file" name="archivo" accept=".pdf" required
            class="w-full px-3 py-2 border rounded" />
    </div>
    <button type="submit" class="px-4 py-2 text-white bg-green-600 rounded hover:bg-green-700">
        Guardar Boletín
    </button>
</form>

            <hr class="my-4" />


            <button 
                @click="open = false" 
                class="mt-4 px-4 py-2 text-gray-700 bg-gray-200 rounded hover:bg-gray-300"
            >
                Cerrar
            </button>
        </div>
    </div>

    <!-- Tabla con listado de boletines -->
    <div class="overflow-x-auto bg-white rounded shadow">
        <table class="min-w-full table-auto">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-4 py-2 text-left">ID</th>
                    <th class="px-4 py-2 text-left">Descripción</th>
                    <th class="px-4 py-2 text-left">Contenido</th>
                    <th class="px-4 py-2 text-left">Fechas</th>
                    <th class="px-4 py-2 text-left">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($boletines as $boletin)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="px-4 py-2">{{ $loop->iteration }}</td>
                       <td class="px-4 py-2 font-semibold">{{ Str::limit($boletin->contenido, 60) }}</td>
                        <td class="max-w-xs px-4 py-2 text-sm text-gray-600 truncate">
                            {{ Str::limit($boletin->contenido, 60) }}
                        </td>

                        <td class="px-4 py-2 text-gray-600">
                                {{ $boletin->created_at->locale('es')->translatedFormat('d \d\e F \d\e\l Y h:i a') . ' (' . $boletin->created_at->diffForHumans() . ')' }}
                            </td>

                        
                        <td class="px-4 py-2 space-x-2">
                            <a href="{{ route('boletines.show', $boletin) }}"
                                class="px-3 py-1 text-sm text-white bg-indigo-600 rounded hover:bg-indigo-700">Ver</a>
                            <a href="{{ route('boletines.edit', $boletin) }}"
                                class="px-3 py-1 text-sm text-white bg-yellow-500 rounded hover:bg-yellow-600">Editar</a>
                            <form action="{{ route('boletines.destroy', $boletin) }}" method="POST" class="inline"
                                onsubmit="return confirm('¿Estás seguro de que deseas eliminar este boletín?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="px-3 py-1 text-sm text-white bg-red-600 rounded hover:bg-red-700">
                                    Eliminar
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-6 text-center text-gray-500">
                            No hay boletines registrados aún.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
@endsection