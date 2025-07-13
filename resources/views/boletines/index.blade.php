@extends('layouts.app')

@section('content')
@can('crear boletin')
<div class="w-full px-4 py-6 md:px-8 lg:px-12">
    <div class="flex items-center space-x-4">
        <img src="{{ asset('images/reverse.svg') }}" class="w-4 h-4" alt="Icono de retroceso">
        <h1 class="text-xl font-bold sm:text-2xl lg:text-3xl whitespace-nowrap">Gestión de boletines</h1>
    </div>
    <div class="py-2 text-sm text-gray-600">
        {!! Breadcrumbs::render('boletines.index') !!}
    </div>
</div>

{{-- Contenedor principal de la página de gestión, con fondo y bordes redondeados --}}
<div class="w-full max-w-screen-2xl mx-auto bg-[var(--color-Gestion)] rounded-2xl p-4 mb-8 shadow-sm">
    <div class="flex flex-col justify-between gap-4 mb-4 md:flex-row md:items-center"> {{-- Ajustado para mejor responsividad y espacio entre elementos --}}
        {{-- Formulario de búsqueda --}}
        <form id="searchBoletinForm" action="{{ route('boletines.index') }}" method="GET"
            class="flex items-center w-full space-x-2 md:max-w-xl">
            @include('boletines.partials.search')
        </form>

        {{-- Contenedor de botones de acción --}}
        <div class="flex flex-col justify-end w-full py-2 space-y-2 sm:flex-row sm:items-center sm:py-5 sm:space-y-0 sm:space-x-2"> {{-- Mejoras para responsividad de los botones --}}
            <button id="resetFiltersButton"
                class="inline-flex items-center group justify-center px-6 py-3 space-x-2 transition-all duration-300 ease-in-out
                        bg-[var(--color-Gestion)] border border-[var(--color-ajustes)] hover:border-[#39A900] text-black rounded-full whitespace-nowrap">
                <span class="font-medium hover:text-[var(--color-hover)]">
                    {{ __('Restablecer filtros') }}
                </span>
            </button>

            <form method="GET" action="{{ route('boletines.exportarCSV') }}" id="exportCsvForm">
                <button type="submit" id="exportCsvButton"
                    class="inline-flex items-center group justify-center px-6 py-3 space-x-2 space-x-reverse transition-all duration-300 ease-in-out bg-[var(--color-Gestion)] border border-[var(--color-ajustes)] hover:border-[#39A900] text-black rounded-full w-full sm:w-auto"> {{-- w-full en móvil, auto en sm+ --}}
                    <span class="text-md font-medium hover:text-[var(--color-hover)]">
                        {{ __('Exportar Csv') }}
                    </span>
                    <img src="{{ asset('images/export.svg') }}"
                        class="relative inset-0 block w-5 h-4 group-hover:hidden" alt="Icono Exportar CSV">
                    <img src="{{ asset('images/export-hover.svg') }}"
                        class="relative inset-0 hidden w-5 h-4 group-hover:block" alt="Icono Exportar CSV">
                </button>
            </form>

            <button type="button" @click.prevent="window.openCreateBoletinModal()" class="inline-flex items-center px-6 py-3 space-x-2 transition-all duration-300 ease-in-out bg-[#39A900]
                        hover:bg-[#61BA33] text-white rounded-full whitespace-nowrap w-full sm:w-auto"> {{-- w-full en móvil, auto en sm+ --}}
                <img src="{{ asset('images/signo.svg') }}" class="w-4 h-5 mr-3 " alt="Icono signo de +">
                Crear/importar boletín
            </button>
        </div>
    </div>

    {{-- Modal de creación de boletines (se mantiene, su JS maneja la recarga) --}}
    @include('boletines.partials.modal-create')

    {{-- Mensajes de sesión de éxito o error --}}
    @if (session('success'))
    <div class="p-4 mb-4 text-green-800 bg-green-100 rounded shadow">{{ session('success') }}</div>
    @endif

    @if (session('error'))
    <div class="p-4 mb-4 text-red-800 bg-red-100 rounded shadow">{{ session('error') }}</div>
    @endif

    {{-- Esto incluye la tabla y su paginación. Laravel la renderiza con los datos actuales. --}}
    {{-- La responsividad de la tabla (overflow-x-auto) se maneja DENTRO de boletines.partials.tabla --}}
    @include('boletines.partials.tabla')

    {{-- Modales específicos para cada boletín (Ver, Editar, Eliminar, Validar, Rechazar) --}}
    @forelse ($boletines as $boletin)
    @include('boletines.partials.modal-views', ['boletin' => $boletin])
    @include('boletines.partials.modal-edit', ['boletin' => $boletin])
    @include('boletines.partials.modal-delete', ['boletin' => $boletin])
    {{-- Asegúrate de que estos partials existan y sean correctos, si no se usan, se pueden omitir o comentar --}}
    @include('pendientes.partials.modal-boletin-validar', ['boletin' => $boletin])
    @include('pendientes.partials.modal-boletin-rechazar', ['boletin' => $boletin])
    @empty
    {{-- Si no hay boletines, no se renderiza ningún modal específico de boletín. --}}
    @endforelse

    {{-- Modal global para mensajes de éxito/error (se mantiene) --}}
    @include('partials.global-message-modal')

    {{-- La paginación de Laravel --}}
    @if ($boletines->total() > 0 && $boletines->hasPages())
    <div class="p-2 mt-4 bg-white shadow-sm rounded-b-xl"> {{-- Añadido fondo blanco y padding a la paginación --}}
        {{ $boletines->links('vendor.pagination.tailwind') }}
    </div>
    @endif
</div>
@endcan
@endsection
