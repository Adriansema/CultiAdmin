{{-- @extends('layouts.app')

@section('header')
<h2 class="text-xl font-semibold">Detalle del Boletín</h2>
@endsection

@section('content')
<div class="max-w-4xl py-6 mx-auto">
    <div class="p-6 bg-white rounded shadow">
        <h3 class="text-lg font-bold">{{ $boletin->asunto }}</h3>
        <p class="mt-4 text-gray-700 whitespace-pre-line">{{ $boletin->contenido }}</p>

        <div class="mt-6">
            <a href="{{ route('boletines.edit', ['boletin' => $boletin->id]) }}"
                class="inline-block px-4 py-2 text-white bg-yellow-500 rounded hover:bg-yellow-600">Editar</a>
            <a href="{{ route('boletines.index') }}"
                class="inline-block px-4 py-2 ml-2 text-white bg-gray-500 rounded hover:bg-gray-600">Volver</a>
        </div>
    </div>
</div>
@endsection
--}}


{{-- resources/views/boletines/show.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto mt-10">
    <div class="p-6 bg-white rounded shadow">



        <p class="mt-4 text-gray-700 whitespace-pre-line">
            {{ $boletin->contenido }}
        </p>

        <div class="flex mt-6">
            <a href="{{ route('boletines.index') }}"
                class="inline-block px-4 py-2 ml-2 text-white bg-gray-500 rounded hover:bg-gray-600">Volver</a>
        </div>
    </div>
</div>
@endsection
