@extends('layouts.app')

@section('title', 'Productos agrícolas')

@section('content')

    @can('crear producto')
        <div class="inline-block px-20 py-6">
            <div class="flex items-center space-x-4">
                <img src="{{ asset('images/reverse.svg') }}" class="w-4 h-4" alt="Icono Nuevo Usuario">
                <h1 class="text-3xl whitespace-nowrap font-bold">Gestión de productos</h1>
            </div>
            <div class="py-2">
                {!! Breadcrumbs::render('productos.index') !!}
            </div>
        </div>

        <div class="w-full max-w-screen-2xl mx-auto bg-[var(--color-Gestion)] rounded-2xl p-4 mb-8">
            <div class="flex items-center justify-between">

                <form id="searchForm" action="{{ route('productos.index') }}" method="GET"
                    class="flex items-center w-full max-w-xl">
                    @include('productos.partials.search')
                </form>

                <div class="flex items-center justify-end py-3 space-x-2">
                    {{-- Botón para Restablecer Filtros --}}
                    <button id="resetFiltersButton"
                        class="inline-flex items-center group justify-center px-6 py-3 space-x-2 transition-all duration-300 ease-in-out
                        bg-[var(--color-Gestion)] border border-[var(--color-ajustes)] hover:border-[#39A900] text-white rounded-full whitespace-nowrap">
                        <span class="font-medium text-black whitespace-nowrap hover:text-[var(--color-hover)]">
                            {{ __('Restablecer filtros') }}
                        </span>
                    </button>

                    <form method="GET" action="{{ route('productos.exportarCSV') }}" id="exportCsvForm">
                        <button type="submit" id="exportCsvButton"
                            class="p-2 inline-flex items-center group justify-center px-6 py-3 space-x-2 transition-all duration-300 ease-in-out bg-[var(--color-Gestion)] border border-[var(--color-ajustes)] hover:border-[#39A900] text-white rounded-full">
                            <span class="font-medium text-black whitespace-nowrap hover:text-[var(--color-hover)]">
                                {{ __('Exportar csv') }}
                            </span>
                            <img src="{{ asset('images/export.svg') }}"
                                class="w-6 h-6 relative inset-0 block group-hover:hidden" alt="Icono Exportar CSV">
                            <img src="{{ asset('images/export-hover.svg') }}"
                                class="w-6 h-6 relative inset-0 hidden group-hover:block" alt="Icono Exportar CSV">
                        </button>
                    </form>

                    <x-responsive-nav-link href="{{ route('productos.create') }}"
                        class="inline-flex items-center px-6 py-3 space-x-2 transition-all duration-300 ease-in-out 
                        bg-[#39A900] hover:bg-[#61BA33] text-white rounded-full">
                        <img src="{{ asset('images/signo.svg') }}" class="w-4 h-3" alt="Icono Nuevo Usuario">
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

            @include('productos.partials.tabla')

            @forelse ($productos as $producto)
                @include('productos.partials.modal-delete', ['producto' => $producto]) {{-- Asegúrate de pasar la variable producto --}}
                @include('pendientes.partials.modal-producto-rechazar') {{-- Posiblemente necesite revisión --}}
                @include('pendientes.partials.modal-producto-validar') {{-- Posiblemente necesite revisión --}}
            @empty
                {{-- Si no hay boletines, no se renderiza ningún modal --}}
            @endforelse

            @if ($productos->total() > 0 && $productos->hasPages())
                <div class="mt-4 rounded-b-xl">
                    {{ $productos->links('vendor.pagination.tailwind') }}
                </div>
            @endif
        </div>
    @endcan
@endsection
