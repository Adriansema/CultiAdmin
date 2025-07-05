@extends('layouts.app')

@section('title', 'Productos Agrícolas')

@section('content')

    @can('crear producto')
        <div class="inline-block px-10 py-6">
            <div class="flex items-center space-x-4">
                <img src="{{ asset('images/reverse.svg') }}" class="w-4 h-4" alt="Icono Nuevo Usuario">
                <h1 class="text-3xl font-bold whitespace-nowrap">Gestion de productos</h1>
            </div>
            <div class="py-2">
                {!! Breadcrumbs::render('productos.index') !!}
            </div>
        </div>

        <div class="w-full max-w-screen-2xl mx-auto bg-[var(--color-Gestion)] rounded-2xl p-4">
            <div class="flex items-center justify-between">

                <form id="searchForm" action="{{ route('productos.index') }}" method="GET"
                    class="flex items-center w-full max-w-xl">
                    @include('productos.partials.search')
                </form>

                <div class="flex items-center justify-end py-3 space-x-2">
                    <select id="filtro-estado" name="estado"
                        class="inline-flex items-center justify-center px-4 py-2 space-x-2 space-x-reverse transition-all duration-300 ease-in-out bg-[var(--color-Gestion)] border border-[var(--color-ajustes)] hover:border-[#39A900] rounded-full whitespace-nowrap text-md font-medium text-black mr-2 mb-2 md:mb-0">
                        <option value="">{{ __('Todos los estados') }}</option>
                        <option value="aprobado" {{ request('estado') == 'aprobado' ? 'selected' : '' }}>{{ __('Aprobado') }}
                        </option>
                        <option value="pendiente" {{ request('estado') == 'pendiente' ? 'selected' : '' }}>{{ __('Pendiente') }}
                        </option>
                        <option value="rechazado" {{ request('estado') == 'rechazado' ? 'selected' : '' }}>{{ __('Rechazado') }}
                        </option>
                    </select>

                    <button type="button" id="filterButton"
                        class="inline-flex group items-center justify-center px-4 py-2 space-x-2 space-x-reverse transition-all duration-300 ease-in-out bg-[var(--color-Gestion)] border border-[var(--color-ajustes)] hover:border-[#39A900] rounded-full w-auto">
                        <span class="text-md font-medium text-black whitespace-nowrap hover:text-[var(--color-hover)]">
                            {{ __('Filtrar') }}
                        </span>
                        <img src="{{ asset('images/filtro.svg') }}" class="relative inset-0 block w-4 h-3 group-hover:hidden"
                            alt="Icono de filtro">
                        <img src="{{ asset('images/filtro-hover.svg') }}"
                            class="relative inset-0 hidden w-4 h-3 group-hover:block" alt="Icono de filtro hover">
                    </button>

                    <form method="GET" action="{{ route('productos.exportarCSV') }}">
                        <x-responsive-nav-link href="#" onclick="this.closest('form').submit(); return false;"
                            class="inline-flex items-center group justify-center px-4 py-2 space-x-2 space-x-reverse transition-all duration-300 ease-in-out bg-[var(--color-Gestion)] border border-[var(--color-ajustes)] hover:border-[#39A900] text-white rounded-full w-auto">
                            <span class="text-md font-medium text-black whitespace-nowrap hover:text-[var(--color-hover)]">
                                {{ __('Exportar Csv') }}
                            </span>
                            <img src="{{ asset('images/export.svg') }}"
                                class="relative inset-0 block w-5 h-4 group-hover:hidden" alt="Icono Exportar CSV">
                            <img src="{{ asset('images/export-hover.svg') }}"
                                class="relative inset-0 hidden w-5 h-4 group-hover:block" alt="Icono Exportar CSV">
                        </x-responsive-nav-link>
                    </form>
                    <x-responsive-nav-link href="{{ route('productos.create') }}"
                        class="inline-flex items-center px-4 py-2 space-x-2 transition-all duration-300 ease-in-out bg-[#39A900] hover:bg-[#61BA33] text-white rounded-full w-auto">
                        <img src="{{ asset('images/signo.svg') }}" class="w-4 h-3" alt="Icono Nuevo Usuario">
                        <span class="font-medium text-md whitespace-nowrap">
                            {{ __('Nuevo Producto') }}
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
