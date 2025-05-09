@extends('layouts.app')

@section('header')
<h2 class="text-xl font-semibold">Crear Usuario</h2>
@endsection

@section('content')
<div class="max-w-4xl py-6 mx-auto">
    <hr class="my-6 border-gray-300">

    <h3 class="text-lg font-semibold text-gray-700">Importar usuarios desde CSV</h3>
    <form action="{{ route('usuarios.importarCsv') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <x-label for="archivo" value="Importar archivo CSV" />
        <x-input id="archivo" class="block w-full mt-1" type="file" name="archivo" required />

        <x-button class="mt-4">
            Importar Usuarios
        </x-button>
    </form>
    <form action="{{ route('usuarios.store') }}" method="POST">
        @include('usuarios._form', ['roles' => $roles])
        <x-button class="mt-4">Guardar</x-button>
    </form>
    <a href="{{ route('usuarios.index') }}"
        class="inline-block px-4 py-2 text-white bg-gray-600 rounded hover:bg-gray-700">
        ← Volver a la lista
    </a>
</div>
@endsection
