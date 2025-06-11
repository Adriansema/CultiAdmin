@extends('layouts.app')

@section('title', 'Productos Agrícolas')

@section('content')
    <div class="mb-6">
        <div class="flex items-center space-x-2">
            <h1 class="text-3xl font-bold text-gray-800 flex items-center space-x-2">
                <img src="{{ asset('images/reverse.svg') }}" alt="icono" class="w-5 h-5">
                <span>Productos Agrícolas</span>
            </h1>
        </div>

        <div class="py-2">
            {!! Breadcrumbs::render('productos.index') !!}
        </div>
    </div>

    <div class="w-full max-w-6xl px-6 py-1 mx-auto bg-[var(--color-Gestion)] rounded-xl">
        <div class="flex items-center justify-between">

            <form id="searchForm" action="{{ route('productos.index') }}" method="GET"
                class="flex items-center w-full max-w-xl">
                @include('productos.partials.search')
            </form>

            <div class="flex items-center justify-end py-4 space-x-2">
                <button type="button" id="filterButton"
                    class="inline-flex group items-center justify-center px-4 py-3 space-x-2 space-x-reverse transition-all duration-300 ease-in-out bg-[var(--color-Gestion)] border border-[var(--color-ajustes)] hover:border-[#39A900] rounded-full w-auto">
                    <span class="text-xs font-medium text-black whitespace-nowrap hover:text-[var(--color-hover)]">
                        {{ __('Filtrar') }}
                    </span>
                    <img src="{{ asset('images/filtro.svg') }}" class="w-4 h-3 relative inset-0 block group-hover:hidden"
                        alt="Icono de filtro">
                    <img src="{{ asset('images/filtro-hover.svg') }}"
                        class="w-4 h-3 relative inset-0 hidden group-hover:block" alt="Icono de filtro hover">
                </button>

                {{-- Botón para Exportar CSV: Visible solo si el usuario puede 'exportar productos' --}}
                @can('export', App\Models\Producto::class)
                    <form method="GET" action="{{ route('productos.exportarCSV') }}">
                        {{-- ! Exportar CSV: Un botón para exportar la lista de productos a un archivo CSV, también con un efecto de cambio de icono al hacer hover. Este botón, aunque es un enlace de navegación, está dentro de un formulario que envía una solicitud GET para la exportación. --}}
                        <x-responsive-nav-link href="#" onclick="this.closest('form').submit(); return false;"
                            class="inline-flex items-center group justify-center px-4 py-3 space-x-2 space-x-reverse transition-all duration-300 ease-in-out bg-[var(--color-Gestion)] border border-[var(--color-ajustes)] hover:border-[#39A900] text-white rounded-full w-auto">
                            <span class="text-xs font-medium text-black whitespace-nowrap hover:text-[var(--color-hover)]">
                                {{ __('Exportar Csv') }}
                            </span>
                            <img src="{{ asset('images/export.svg') }}"
                                class="w-5 h-4 relative inset-0 block group-hover:hidden" alt="Icono Exportar CSV">
                            <img src="{{ asset('images/export-hover.svg') }}"
                                class="w-5 h-4 relative inset-0 hidden group-hover:block" alt="Icono Exportar CSV">
                        </x-responsive-nav-link>
                    </form>
                @endcan

                {{-- Botón para Crear Nuevo Producto: Visible solo si el usuario puede 'crear productos' --}}
                @can('create', App\Models\Producto::class)
                    <x-responsive-nav-link href="{{ route('productos.create') }}" {{-- ! Nuevo Usuario: Un botón para crear un nuevo usuario, que tiene un icono y un cambio de color al pasar el ratón. --}}
                        class="inline-flex items-center px-4 py-3 space-x-2 transition-all duration-300 ease-in-out bg-[#39A900] hover:bg-[#61BA33] text-white rounded-full w-auto">
                        <img src="{{ asset('images/signo.svg') }}" class="w-4 h-3" alt="Icono Nuevo Usuario">
                        <span class="text-xs font-medium whitespace-nowrap">
                            {{ __('Nuevo Producto') }}
                        </span>
                    </x-responsive-nav-link>
                @endcan
            </div>
        </div>

        {{-- Mensajes de éxito o error --}}
        @if (session('success'))
            <div class="p-4 mb-4 text-green-800 bg-green-100 rounded shadow">{{ session('success') }}</div>
        @endif

        @if (session('error'))
            <div class="p-4 mb-4 text-red-800 bg-red-100 rounded shadow">{{ session('error') }}</div>
        @endif

        @include('productos.partials.tabla')

        @if ($productos->total() > 0 && $productos->hasPages())
            <div class="p-4 mt-4 rounded-b-xl">
                {{ $productos->links('vendor.pagination.tailwind') }}
            </div>
        @endif
    </div>
@endsection
