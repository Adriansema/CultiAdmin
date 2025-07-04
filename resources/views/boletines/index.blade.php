@extends('layouts.app')

@section('content')
    @can('crear boletin')
        {{-- Contenedor principal del encabezado (Boletines, Breadcrumbs) --}}
        <div class="inline-block px-20 py-6">
            <div class="flex items-center space-x-4">
                <img src="{{ asset('images/reverse.svg') }}" class="w-4 h-4" alt="Icono Nuevo Usuario">
                <h1 class="text-3xl whitespace-nowrap font-bold">Boletines</h1>
            </div>
            <div class="py-2">
            {!! Breadcrumbs::render('boletines.index') !!}
            </div>
        </div>

        {{-- Contenedor principal de la sección de gestión (fondo verde claro) --}}
        <div class="w-full max-w-screen-2xl mx-auto bg-[var(--color-Gestion)] rounded-3xl p-5 mb-6">

            {{-- Contenedor de búsqueda y grupo de botones de acción --}}
            <div class="flex flex-col sm:flex-row items-center justify-between mb-4 flex-wrap">

                {{-- Formulario de búsqueda --}}
                <form id="searchBol" action="{{ route('boletines.index') }}" method="GET"
                    class="flex items-center w-full max-w-full sm:max-w-xs md:max-w-sm lg:max-w-md xl:max-w-lg mb-4 sm:mb-0 mr-0 sm:mr-4">
                    @include('boletines.partials.search')
                </form>

                {{-- Grupo de los tres botones: Filtro, Exportar y Crear/Importar --}}
                <div class="flex items-center justify-center sm:justify-end flex-nowrap gap-2 py-4 sm:py-0">

                    {{-- Botón Filtrar --}}
                    <button type="button" id="filterBtn"
                        class="inline-flex group items-center justify-center px-4 py-2 space-x-2 space-x-reverse transition-all duration-300 ease-in-out bg-[var(--color-Gestion)] border border-[var(--color-ajustes)] hover:border-[#39A900] rounded-full whitespace-nowrap">
                        <span class="text-md font-medium text-black whitespace-nowrap hover:text-[var(--color-hover)]">
                            {{ __('Filtrar') }}
                        </span>
                        <img src="{{ asset('images/filtro.svg') }}" class="relative inset-0 block w-4 h-3 group-hover:hidden"
                            alt="Icono de filtro">
                        <img src="{{ asset('images/filtro-hover.svg') }}"
                            class="relative inset-0 hidden w-4 h-3 group-hover:block" alt="Icono de filtro hover">
                    </button>

                    {{-- Botón Exportar Csv (dentro de un form) --}}
                    <form method="GET" action="{{ route('boletines.exportarCSV') }}">
                        <x-responsive-nav-link href="#" onclick="this.closest('form').submit(); return false;"
                            class="inline-flex items-center group justify-center px-4 py-2 space-x-2 space-x-reverse transition-all duration-300 ease-in-out
                            bg-[var(--color-Gestion)] border border-[var(--color-ajustes)] hover:border-[#39A900] text-white rounded-full whitespace-nowrap">
                            <span class="text-md font-medium text-black whitespace-nowrap hover:text-[var(--color-hover)]">
                                {{ __('Exportar Csv') }}
                            </span>
                            <img src="{{ asset('images/export.svg') }}"
                                class="relative inset-0 block w-5 h-4 group-hover:hidden" alt="Icono Exportar CSV">
                            <img src="{{ asset('images/export-hover.svg') }}"
                                class="relative inset-0 hidden w-5 h-4 group-hover:block" alt="Icono Exportar CSV">
                        </x-responsive-nav-link>
                    </form>

                    <button type="button" @click.prevent="window.openCreateBoletinModal()"
                        class="inline-flex items-center px-4 py-2 space-x-2 transition-all duration-300 ease-in-out bg-[#39A900]
                hover:bg-[#61BA33] text-white rounded-full text-md whitespace-nowrap">
                        + Crear / Importar Boletín
                    </button>
                    @include('boletines.partials.modal-create')
                </div>
            </div>

            {{-- Mensajes de sesión de éxito o error --}}
            @if (session('success'))
                <div class="p-4 mb-4 text-green-800 bg-green-100 rounded shadow">{{ session('success') }}</div>
            @endif

            @if (session('error'))
                <div class="p-4 mb-4 text-red-800 bg-red-100 rounded shadow">{{ session('error') }}</div>
            @endif

            {{-- ¡Esto incluye la tabla y su paginación! --}}
            @include('boletines.partials.tabla')

            {{-- Modales de Ver, Editar, Eliminar (dependen de los boletines, se renderizan aquí) --}}
            @forelse ($boletines as $boletin)
                @include('boletines.partials.modal-views', ['boletin' => $boletin])
                @include('boletines.partials.modal-edit', ['boletin' => $boletin])
                @include('boletines.partials.modal-delete', ['boletin' => $boletin])
            @empty
                {{-- Si no hay boletines, no se renderiza ningún modal --}}
            @endforelse

            @include('partials.global-message-modal')
        </div>
    @endcan
@endsection
