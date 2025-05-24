@extends('layouts.app')

{{-- TODO: Por el momento no esta en uso, pero se puede reutilizar para juntarlo con la vista show.blade.php para realizar el modal al pasar por encima de la columna rol --}}

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
