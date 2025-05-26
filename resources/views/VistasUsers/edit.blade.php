@extends('layouts.app')

@section('header')
    <h2 class="text-xl font-semibold">Editar Usuario</h2>
@endsection

@section('content')
<div class="px-6 py-4">
    @include('components.validation-errors')

    <form action="{{ route('view-user.update', $usuario->id) }}" method="POST">
        @csrf @method('PUT')
        @include('VistasUsers._form', ['usuario' => $usuario, 'roles' => $roles])
        <button class="px-4 py-2 mt-4 text-white bg-yellow-500 rounded">Actualizar</button>
    </form>
    <a href="{{ route('view-user.index') }}"
        class="inline-block px-4 py-2 text-white bg-gray-600 rounded hover:bg-gray-700">
        ← Volver a la lista
    </a>
</div>
@endsection
