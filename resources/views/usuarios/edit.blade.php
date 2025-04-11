@extends('layouts.app')

@section('header')
    <h2 class="text-xl font-semibold">Editar Usuario</h2>
@endsection

@section('content')
    <div class="max-w-4xl py-6 mx-auto">
        <form action="{{ route('usuarios.update', $usuario) }}" method="POST">
            @csrf
            @method('PUT')
            @include('usuarios._form', ['usuario' => $usuario, 'roles' => $roles])
            <x-button class="mt-4">Actualizar</x-button>

                <a href="{{ route('usuarios.index') }}"
                    class="inline-flex items-center px-4 py-2 text-gray-800 transition bg-gray-200 rounded hover:bg-gray-300">
                   Volver
                </a>
        </form>
    </div>
@endsection
