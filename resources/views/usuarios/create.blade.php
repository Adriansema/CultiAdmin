@extends('layouts.app')

@section('header')
    <h2 class="text-xl font-semibold">Crear Usuario</h2>
@endsection

@section('content')
    <div class="max-w-4xl py-6 mx-auto">
        <form action="{{ route('usuarios.store') }}" method="POST">
            @include('usuarios._form', ['roles' => $roles])
            <x-button class="mt-4">Guardar</x-button>
        </form>
    </div>
@endsection
