{{-- resources/views/productos/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Productos agrícolas')

@section('content')

@can('crear producto')
{{-- Contenedor del título y breadcrumbs (ya tiene buen responsive) --}}

<div class="w-full px-4 py-6 md:px-8 lg:px-12">
    <div class="flex items-center space-x-4">
        <img src="{{ asset('images/reverse.svg') }}" class="w-4 h-4" alt="Icono Nuevo Usuario">
        <h1 class="text-xl font-bold sm:text-2xl lg:text-3xl whitespace-nowrap">Gestión de productos</h1>
    </div>
    <div class="py-2 text-sm text-gray-600">
        {!! Breadcrumbs::render('productos.index') !!}
    </div>
</div>

{{-- Contenedor principal de la página de productos: ¡AJUSTE CRÍTICO DE PADDING AQUÍ! --}}
{{-- Quitamos p-4 y usamos padding responsivo directamente en este div. --}}
{{-- Este div es el que tiene el fondo bg-[var(--color-Gestion)] --}}
<div class="w-full px-4 mb-8 sm:px-6 lg:px-8">
    <div class="max-w-screen-2xl mx-auto bg-[var(--color-Gestion)] rounded-3xl p-4 sm:p-6 lg:p-8">

        {{-- Contenedor de búsqueda y botones (contenido interno del div principal) --}}
        <div class="flex flex-col justify-between gap-4 mb-4 md:flex-row md:items-center">

            <form id="searchForm" action="{{ route('productos.index') }}" method="GET"
                class="flex items-center w-full max-w-xl">
                @include('productos.partials.search')
            </form>

            {{-- Grupo de botones --}}
            <div
                class="flex flex-col justify-end w-full py-2 space-y-2 sm:flex-row sm:items-center sm:py-5 sm:space-y-0 sm:space-x-2">
                <button id="resetFiltersButton"
                    class="inline-flex items-center group justify-center px-6 py-3 space-x-2 transition-all duration-300 ease-in-out
                        bg-[var(--color-Gestion)] border border-[var(--color-ajustes)] hover:border-[#39A900] text-black rounded-full whitespace-nowrap w-full sm:w-auto">
                    <span class="font-medium hover:text-[var(--color-hover)]">
                        {{ __('Restablecer filtros') }}
                    </span>
                </button>

                <form method="GET" action="{{ route('productos.exportarCSV') }}" id="exportCsvForm">
                    <button type="submit" id="exportCsvButton"
                        class="inline-flex items-center group justify-center px-6 py-3 space-x-2 space-x-reverse transition-all duration-300 ease-in-out bg-[var(--color-Gestion)] border border-[var(--color-ajustes)] hover:border-[#39A900] text-black rounded-full w-full sm:w-auto">
                        <span class="text-md font-medium hover:text-[var(--color-hover)]">
                            {{ __('Exportar Csv') }}
                        </span>
                        <img src="{{ asset('images/export.svg') }}"
                            class="relative inset-0 block w-5 h-4 group-hover:hidden" alt="Icono Exportar CSV">
                        <img src="{{ asset('images/export-hover.svg') }}"
                            class="relative inset-0 hidden w-5 h-4 group-hover:block" alt="Icono Exportar CSV">
                    </button>
                </form>

                <x-responsive-nav-link href="{{ route('productos.create') }}" class="inline-flex items-center px-6 py-3 space-x-2 transition-all duration-300 ease-in-out
                        bg-[#39A900] hover:bg-[#61BA33] text-white rounded-full w-full sm:w-auto">
                    <img src="{{ asset('images/signo.svg') }}" class="w-4 h-3 mr-3" alt="Icono Nuevo Usuario">
                    <span class="font-medium whitespace-nowrap">
                        {{ __('Nuevo producto') }}
                    </span>
                </x-responsive-nav-link>
            </div>
        </div>

        @if (session('success'))
        <div class="p-4 mb-4 text-green-800 bg-green-100 rounded shadow">{{ session('success') }}</div>
        @endif

        @if (session('error'))
        <div class="p-4 mb-4 text-red-800 bg-red-100 rounded shadow">{{ session('error') }}</div>
        @endif

        {{-- El div con overflow-x-auto está bien, envuelve la tabla --}}
        <div class="overflow-x-auto rounded-lg shadow-md">
            @include('productos.partials.tabla')
        </div>

        @forelse ($productos as $producto)
        @include('productos.partials.modal-delete', ['producto' => $producto])
        @include('pendientes.partials.modal-producto-rechazar', ['producto' => $producto])
        @include('pendientes.partials.modal-producto-validar', ['producto' => $producto])
        @empty
        @endforelse

            @if ($productos->total() > 0 && $productos->hasPages())
                <div class="p-2 mt-4 rounded-b-xl"> {{-- Añadido p-2 bg-white shadow-sm para la paginación --}}
                    {{ $productos->links('vendor.pagination.tailwind') }}
                </div>
            @endif
        </div>
    @endcan
    @endsection
