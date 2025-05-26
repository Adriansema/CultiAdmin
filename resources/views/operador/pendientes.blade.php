@extends('layouts.app')

@section('content')
    <div class="container mx-auto py-6" x-data="{ tab: 'productos' }">
        <div class="inline-block px-8 py-10">
            <div class="flex items-center space-x-2">
                <img src="{{ asset('images/reverse.svg') }}" class="w-4 h-4" alt="Icono Nuevo Usuario">
                <h1 class="text-3xl whitespace-nowrap font-bold">Gestión de Validaciones</h1>
            </div>
            {!! Breadcrumbs::render('operador.pendientes') !!}
        </div>

        @if (session('success'))
            <div class="p-4 mb-4 text-green-800 bg-green-200 rounded">
                {{ session('success') }}
            </div>
        @endif

        <div class="flex border-b mb-6">
            <button class="px-4 py-2 text-sm font-medium transition duration-200 ease-in-out transform text-shadow"
                :class="{
                    'border-b-2 border-green-600 text-green-600': tab === 'productos',
                    'text-gray-600 hover:text-green-500 hover:-translate-y-0.5 hover:scale-105 text-shadow-hover': tab !== 'productos'
                }"
                @click="tab = 'productos'">
                Productos
            </button>

            <button class="px-4 py-2 text-sm font-medium ml-4 transition duration-200 ease-in-out transform text-shadow"
                :class="{
                    'border-b-2 border-green-600 text-green-600': tab === 'boletines',
                    'text-gray-600 hover:text-green-500 hover:-translate-y-0.5 hover:scale-105 text-shadow-hover': tab !== 'boletines'
                }"
                @click="tab = 'boletines'">
                Boletines
            </button>
        </div>

        <!-- Contenido dinámico -->
        <div x-show="tab === 'productos'">
            @include('operador.partials.productos')
        </div>

        <div x-show="tab === 'boletines'" {{-- x-cloak --}}>
            @include('operador.partials.boletines')
        </div>

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
