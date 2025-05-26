@extends('layouts.app')

@section('header')
<h2 class="text-xl font-semibold">Crear Usuario</h2>
@endsection

@section('content')
<div class="max-w-4xl py-6 mx-auto">
    <div class="p-6 space-y-6 bg-gray-100 shadow-md rounded-xl">

        {{-- Encabezado --}}
        <h3 class="text-lg font-semibold text-gray-700">📥 Importar usuarios desde CSV</h3>

        {{-- Formulario de importación CSV --}}
        <form action="{{ route('usuarios.importarCsv') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <div class="flex flex-col gap-4 sm:flex-row sm:items-end">
                <div class="flex-1">
                    <x-label for="archivo" value="Seleccionar archivo CSV" />
                    <x-input id="archivo" class="block w-full mt-1" type="file" name="archivo" required />
                </div>
                <div>
                    <x-button class="w-full sm:w-auto">Importar Usuarios</x-button>
                </div>
            </div>
        </form>

        {{-- Separador visual opcional --}}
        <hr class="border-t border-gray-300">

        {{-- Formulario de creación manual --}}
        <form action="{{ route('usuarios.store') }}" method="POST" class="space-y-6">
            @csrf
            @include('usuarios._form', ['roles' => $roles])

            <div class="flex flex-col gap-4 sm:flex-row sm:justify-between sm:items-center">
                <a href="{{ route('usuarios.index') }}"
                   class="px-4 py-2 text-center text-white bg-gray-600 rounded hover:bg-gray-700">
                   ← Volver a la lista
                </a>

                <x-button class="text-center">Guardar</x-button>
            </div>
        </form>

    </div>
</div>
@endsection
