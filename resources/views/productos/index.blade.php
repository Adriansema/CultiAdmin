@extends('layouts.app')

@section('title', 'Productos Agrícolas')

@section('content')
    <div class="container max-w-6xl py-6 mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-800 flex items-center space-x-2">
                <img src="{{ asset('images/reverse.svg') }}" alt="icono" class="w-5 h-5">
                <span>Productos Agrícolas</span>
            </h1>
            <a href="{{ route('productos.create') }}"
                class="px-4 py-2 text-white bg-green-600 rounded-3xl hover:bg-green-700">
                + Nuevo Producto
            </a>
        </div>

        <div class="py-6">
            {!! Breadcrumbs::render('productos.index') !!}
        </div>

        @if (session('success'))
            <div class="p-3 mb-4 text-green-700 bg-green-100 rounded shadow">
                {{ session('success') }}
            </div>
        @endif

        @include('productos.partials.tabla')
        
    </div>
    {{-- Scripts para modales --}}
    <script>
        function mostrarModal(tipo, id) {
            const modal = document.getElementById(`modal-${tipo}-${id}`);
            modal.classList.remove('hidden');
        }

        function ocultarModal(tipo, id) {
            const modal = document.getElementById(`modal-${tipo}-${id}`);
            modal.classList.add('hidden');
        }
    </script>
@endsection
