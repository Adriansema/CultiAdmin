@extends('layouts.app') {{-- Asume que tienes un layout base --}}

@section('content')
    <div class="mb-6">
        <div class="flex items-center space-x-2">
            <h1 class="text-3xl font-bold text-gray-800 flex items-center space-x-2">
                <img src="{{ asset('images/reverse.svg') }}" alt="icono" class="w-5 h-5">
                <span>Noticias</span>
            </h1>
        </div>
    </div>
    <div class="w-full max-w-6xl px-6 py-1 mx-auto bg-[var(--color-Gestion)] rounded-xl">
        <div class="flex items-center justify-between">

            <form id="buscadorTabla" action="{{ route('noticias.noticias.index') }}" method="GET"
                class="flex items-center w-full max-w-xl">
                @include('noticias.partials.search')
            </form>

            <div class="flex items-center justify-end py-4 space-x-2">
                <button type="button" id="filtrosBotones"
                    class="inline-flex group items-center justify-center px-4 py-3 space-x-2 space-x-reverse transition-all duration-300 ease-in-out bg-[var(--color-Gestion)] border border-[var(--color-ajustes)] hover:border-[#39A900] rounded-full w-auto">
                    <span class="text-xs font-medium text-black whitespace-nowrap hover:text-[var(--color-hover)]">
                        {{ __('Filtrar') }}
                    </span>
                    <img src="{{ asset('images/filtro.svg') }}" class="w-4 h-3 relative inset-0 block group-hover:hidden"
                        alt="Icono de filtro">
                    <img src="{{ asset('images/filtro-hover.svg') }}"
                        class="w-4 h-3 relative inset-0 hidden group-hover:block" alt="Icono de filtro hover">
                </button>

                <form method="GET" action="{{ route('noticias.noticias.create') }}">
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

                <x-responsive-nav-link href="{{ route('noticias.noticias.create') }}" 
                    class="inline-flex items-center px-4 py-3 space-x-2 transition-all duration-300 ease-in-out bg-[#39A900] hover:bg-[#61BA33] text-white rounded-full w-auto">
                    <img src="{{ asset('images/signo.svg') }}" class="w-4 h-3" alt="Icono Nuevo Usuario">
                    <span class="text-xs font-medium whitespace-nowrap">
                        {{ __('Nueva Noticia') }}
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

        @include('noticias.partials.tabla')

        @if ($noticias->total() > 0 && $noticias->hasPages())
            <div class="p-4 mt-4 rounded-b-xl">
                {{ $noticias->links('vendor.pagination.tailwind') }}
            </div>
        @endif
    </div>
@endsection
