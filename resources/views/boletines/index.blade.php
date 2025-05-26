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
        <div x-data="{ open: false }">
            <div class="flex items-center justify-between mb-6">
                <h1 class="flex items-center space-x-2 text-3xl font-bold text-gray-800">
                    <img src="{{ asset('images/reverse.svg') }}" alt="icono" class="w-5 h-5">
                    <span>Boletines</span>
                </h1>
                <div class="mb-4">
                    <button @click="open = true" class="px-4 py-2 text-white bg-green-600 rounded-3xl hover:bg-green-700">
                        + Crear / Importar Boletín
                    </button>
                </div>
            </div>

            <div class="py-6">
                {!! Breadcrumbs::render('boletines.index') !!}
            </div>

            @include('boletines.partials.modal')

            @include('boletines.partials.tabla')
        </div>
    </div>
@endsection
