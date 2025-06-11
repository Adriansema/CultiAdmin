@extends('layouts.app')

@section('content')
    <div class="max-w-xl py-6 mx-auto">
        <div class="p-6 space-y-6 bg-[var(--color-Gestion)] shadow-md rounded-2xl">

            {{-- Encabezado --}}
            <h1 class="text-3xl font-bold text-gray-700">Importar usuarios desde CSV</h1>

            <form action="{{ route('usuarios.importarCsv') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <div class="flex flex-col gap-4 sm:flex-row sm:items-end">
                    <div class="flex-1">
                        <x-label for="archivo" value="Seleccionar archivo CSV" />
                        <x-input id="archivo" class="block w-full mt-1 bg-gray-300" type="file" name="archivo" required />
                    </div>
                    <div>
                        <x-button class="w-full sm:w-auto">Importar Usuarios</x-button>
                    </div>
                </div>
            </form>


            {{-- Formulario de creación manual (ya protegido por el @can('create', ...) superior) --}}
            <form action="{{ route('usuarios.store') }}" method="POST" class="space-y-6">
                @csrf
                @include('usuarios._form', ['roles' => $roles])

                <div class="flex flex-col gap-4 sm:flex-row sm:justify-between sm:items-center">
                    <a href="{{ route('usuarios.index') }}"
                        class="px-4 py-2 text-center text-white rounded bg-[var(--color-iconos)] hover:bg-[var(--color-iconos6)]">
                        Volver a la lista
                    </a>

                    <x-button class="text-center bg-green-600 hover:bg-green-700">
                        Guardar
                    </x-button>
                </div>
            </form>
        </div>
    </div>
@endsection
