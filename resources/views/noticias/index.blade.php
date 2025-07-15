@extends('layouts.app') {{-- Asume que tienes un layout base --}}

@section('content')
    @can('crear noticia')
        {{-- TITULO Y BREADCRUMB --}}
        <div class="w-full px-4 py-6 md:px-8 lg:px-12">
            <div class="flex items-center space-x-4">
                <img src="{{ asset('images/reverse.svg') }}" class="w-4 h-4" alt="Icono Nuevo Usuario">
                <h1 class="text-xl font-bold sm:text-2xl lg:text-3xl whitespace-nowrap">Gestión de noticias</h1>
            </div>
            <div class="py-2 text-sm text-gray-600"> {!! Breadcrumbs::render('noticias.index') !!}
            </div>
        </div>

        {{-- CONTENEDOR PRINCIPAL DE LA SECCIÓN DE BÚSQUEDA, BOTONES Y TABLA --}}
        <div class="w-full max-w-screen-2xl mx-auto bg-[var(--color-Gestion)] rounded-3xl p-4 mb-8">

            {{-- SECCIÓN DE BÚSQUEDA Y BOTONES DE ACCIÓN (ESTE ES EL DIV CLAVE AJUSTADO) --}}
            <div class="flex flex-col mb-4 md:flex-row md:items-center md:justify-between md:mb-0">
                {{-- Aquí se apilan en móvil (flex-col) y se ponen en fila en pantallas medianas (md:flex-row) --}}

                {{-- Formulario de búsqueda --}}
                <form id="buscadorTabla" action="{{ route('noticias.index') }}" method="GET"
                    class="flex items-center w-full mb-4 md:max-w-xl md:mb-0">
                    {{-- `mb-4` para espacio debajo del buscador en móvil, `md:mb-0` lo quita en desktop --}}
                    @include('noticias.partials.search')
                </form>

                {{-- Contenedor de botones de acción --}}
                <div
                    class="flex flex-col items-stretch justify-end w-full py-4 space-y-2 sm:flex-row sm:items-center sm:space-y-0 sm:space-x-2 md:w-auto">
                    {{-- `flex-col` para apilar botones en móvil, `sm:flex-row` para ponerlos en fila en sm --}}
                    {{-- `space-y-2` para espacio vertical en móvil, `sm:space-y-0 sm:space-x-2` para espacio horizontal en sm --}}
                    {{-- `items-stretch` para que los botones llenen el ancho en móvil, `w-full md:w-auto` para el ancho --}}

                    {{-- Botón para Restablecer Filtros --}}
                    <button id="resetFiltersButton"
                        class="inline-flex items-center group justify-center px-6 py-3 space-x-2 transition-all duration-300 ease-in-out
                bg-[var(--color-Gestion)] border border-[var(--color-ajustes)] hover:border-[#39A900] text-white rounded-full whitespace-nowrap
                w-full sm:w-auto">
                        <span class="font-medium text-black whitespace-nowrap hover:text-[var(--color-hover)]">
                            {{ __('Restablecer filtros') }}
                        </span>
                    </button>

                    {{-- Form Exportar Csv --}}
                    <form method="GET" action="{{ route('noticias.exportarCsv') }}" id="exportCsvForm"
                        class="w-full sm:w-auto">
                        <button type="submit" id="exportCsvButton"
                            class="inline-flex items-center group justify-center px-6 py-3 space-x-2 space-x-reverse transition-all duration-300 ease-in-out bg-[var(--color-Gestion)] border border-[var(--color-ajustes)] hover:border-[#39A900] text-white rounded-full
                    w-full sm:w-auto">
                            <span class="text-md font-medium text-black whitespace-nowrap hover:text-[var(--color-hover)]">
                                {{ __('Exportar Csv') }}
                            </span>
                            <img src="{{ asset('images/export.svg') }}"
                                class="relative inset-0 block w-5 h-4 group-hover:hidden" alt="Icono Exportar CSV">
                            <img src="{{ asset('images/export-hover.svg') }}"
                                class="relative inset-0 hidden w-5 h-4 group-hover:block" alt="Icono Exportar CSV">
                        </button>
                    </form>

                    {{-- Botón Nueva noticia (x-responsive-nav-link) --}}
                    <x-responsive-nav-link href="{{ route('noticias.create') }}"
                        class="inline-flex items-center px-6 py-3 space-x-2 transition-all duration-300 ease-in-out bg-[#39A900] hover:bg-[#61BA33] text-white rounded-full
                w-full sm:w-auto">
                        <img src="{{ asset('images/signo.svg') }}" class="w-4 h-3" alt="Icono Nuevo Usuario">
                        <span class="font-medium text-md whitespace-nowrap">
                            {{ __('Nueva noticia') }}
                        </span>
                    </x-responsive-nav-link>
                </div>
            </div>
            {{-- FIN DE LA SECCIÓN DE BÚSQUEDA Y BOTONES DE ACCIÓN --}}


            @if (session('error'))
                <div class="p-4 mb-4 text-red-800 bg-red-100 rounded shadow">{{ session('error') }}</div>
            @endif
            @if (session('success'))
                <div class="p-4 mb-4 text-green-800 bg-green-100 rounded shadow alert alert-success alert-dismissible fade show"
                    role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @include('noticias.partials.tabla')

            @forelse ($noticias as $noticia)
                @include('noticias.partials.modal-delete', ['noticia' => $noticia])
                @include('pendientes.partials.modal-noticia-rechazar')
                @include('pendientes.partials.modal-noticia-validar')
            @empty
                {{-- Si no hay noticias, no se renderiza ningun modal aqui --}}
            @endforelse

            @if ($noticias->total() > 0 && $noticias->hasPages())
                <div class="mt-4 rounded-b-xl">
                    {{ $noticias->links('vendor.pagination.tailwind') }}
                </div>
            @endif
        </div>
    @endcan
@endsection
