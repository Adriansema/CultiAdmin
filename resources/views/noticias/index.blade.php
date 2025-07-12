@extends('layouts.app') {{-- Asume que tienes un layout base --}}

@section('content')

    @can('crear noticia')
        <div class="inline-block px-20 py-6">
            <div class="flex items-center space-x-4">
                <img src="{{ asset('images/reverse.svg') }}" class="w-4 h-4" alt="Icono Nuevo Usuario">
                <h1 class="text-3xl whitespace-nowrap font-bold">Gestión de noticias</h1>
            </div>
            <div class="py-2">
                {!! Breadcrumbs::render('noticias.index') !!}
            </div>
        </div>

        <div class="w-full max-w-screen-2xl mx-auto bg-[var(--color-Gestion)] rounded-3xl p-4 mb-8">
            <div class="flex items-center justify-between">

                <form id="buscadorTabla" action="{{ route('noticias.index') }}" method="GET"
                    class="flex items-center w-full max-w-xl ">
                    @include('noticias.partials.search')
                </form>

                <div class="flex items-center justify-end py-4 space-x-2">
                    {{-- Botón para Restablecer Filtros --}}
                    <button id="resetFiltersButton"
                        class="inline-flex items-center group justify-center px-6 py-3 space-x-2 transition-all duration-300 ease-in-out
                        bg-[var(--color-Gestion)] border border-[var(--color-ajustes)] hover:border-[#39A900] text-white rounded-full whitespace-nowrap">
                        <span class="font-medium text-black whitespace-nowrap hover:text-[var(--color-hover)]">
                            {{ __('Restablecer filtros') }}
                        </span>
                    </button>

                    <form method="GET" action="{{ route('noticias.exportarCsv') }}" id="exportCsvForm">
                        <button type="submit" id="exportCsvButton"
                            class="inline-flex items-center group justify-center px-6 py-3 space-x-2 space-x-reverse transition-all duration-300 ease-in-out bg-[var(--color-Gestion)] border border-[var(--color-ajustes)] hover:border-[#39A900] text-white rounded-full w-auto">
                            <span class="text-md font-medium text-black whitespace-nowrap hover:text-[var(--color-hover)]">
                                {{ __('Exportar Csv') }}
                            </span>
                            <img src="{{ asset('images/export.svg') }}"
                                class="w-5 h-4 relative inset-0 block group-hover:hidden" alt="Icono Exportar CSV">
                            <img src="{{ asset('images/export-hover.svg') }}"
                                class="w-5 h-4 relative inset-0 hidden group-hover:block" alt="Icono Exportar CSV">
                        </button>
                    </form>

                    <x-responsive-nav-link href="{{ route('noticias.create') }}"
                        class="inline-flex items-center px-6 py-3 space-x-2 transition-all duration-300 ease-in-out bg-[#39A900] hover:bg-[#61BA33] text-white rounded-full w-auto">
                        <img src="{{ asset('images/signo.svg') }}" class="w-4 h-3" alt="Icono Nuevo Usuario">
                        <span class="text-md font-medium whitespace-nowrap">
                            {{ __('Nueva noticia') }}
                        </span>
                    </x-responsive-nav-link>
                </div>
            </div>

            @if (session('error'))
                <div class="p-4 mb-4 text-red-800 bg-red-100 rounded shadow">{{ session('error') }}</div>
            @endif
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show p-4 mb-4 text-green-800 bg-green-100 rounded shadow"
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
