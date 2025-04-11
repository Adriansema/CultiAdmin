@extends('layouts.app')

@section('header')
    <h2 class="text-xl font-semibold">Editar Boletín</h2>
@endsection

@section('content')
    <div class="max-w-4xl py-6 mx-auto">
        <form action="{{ route('boletines.update', $boletin) }}" method="POST">
            @csrf
            @method('PUT')
            @include('boletines._form', ['boletin' => $boletin])
            <x-button class="mt-4">Actualizar</x-button>

            <a href="{{ route('boletines.index') }}"
                    class="inline-flex items-center px-4 py-2 text-gray-800 transition bg-gray-200 rounded hover:bg-gray-300">
                   Volver
            </a>
        </form>
    </div>
@endsection
