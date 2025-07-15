@extends('layouts.app')

@section('title', 'Gestión de usuarios') {{-- Título específico para usuarios --}}

@section('content')

@can('crear usuario')
{{-- Contenedor del título y breadcrumbs: ajustado para responsividad --}}
<div class="w-full px-4 py-6 md:px-8 lg:px-12"> {{-- Ajuste de padding para pantallas pequeñas y grandes --}}
    <div class="flex items-center space-x-4">
        <img src="{{ asset('images/reverse.svg') }}" class="w-4 h-4" alt="Icono">
        {{-- Título responsivo: cambia de tamaño según la pantalla --}}
        <h1 class="text-xl font-bold sm:text-2xl lg:text-3xl whitespace-nowrap">Gestión de usuarios</h1>
    </div>
    {{-- Breadcrumbs: texto más pequeño en móvil --}}
    <div class="py-2 text-sm text-gray-600">
        {!! Breadcrumbs::render('usuarios.index') !!}
    </div>
</div>

<div class="w-full px-4 mb-8 sm:px-6 lg:px-8">
    <div class="max-w-screen-2xl mx-auto bg-[var(--color-Gestion)] rounded-3xl p-4 sm:p-6 lg:p-8"> {{-- Añadido
        shadow-sm para consistencia --}}
        {{-- Contenedor de búsqueda y botones: ajustado para apilarse en móvil --}}
        <div class="flex flex-col justify-between gap-4 mb-4 md:flex-row md:items-center"> {{-- Se convierte en columna
            en móvil, y fila en md+ --}}

            {{-- FORMULARIO DE BUSQUEDA (solo input de texto) --}}
            <form id="BuscarUser" action="{{ route('usuarios.index') }}" method="GET"
                class="flex items-center w-full max-w-xl">
                @include('usuarios.partials.search')
            </form>

            {{-- Contenedor de filtros y botones de accion: se apilan en móvil, fila en sm+ --}}
            <div
                class="flex flex-col justify-end w-full py-2 space-y-2 sm:flex-row sm:items-center sm:py-5 sm:space-y-0 sm:space-x-2">
                {{-- space-y-2 para apilar, space-x-2 para fila --}}
                {{-- Botón para Restablecer Filtros --}}
                <button id="resetFiltersButton"
                    class="inline-flex items-center group justify-center px-6 py-3 space-x-2 transition-all duration-300 ease-in-out
                        bg-[var(--color-Gestion)] border border-[var(--color-ajustes)] hover:border-[#39A900] text-black rounded-full whitespace-nowrap w-full sm:w-auto">
                    {{-- w-full en móvil, w-auto en sm+ --}}
                    <span class="font-medium hover:text-[var(--color-hover)]">
                        {{ __('Restablecer filtros') }}
                    </span>
                </button>

                <form method="GET" action="{{ route('usuarios.exportar') }}" id="exportCsvForm"
                    class="w-full sm:w-auto"> {{-- w-full en móvil, w-auto en sm+ --}}
                    <button type="submit" id="exportCsvButton"
                        class="inline-flex items-center group justify-center px-6 py-3 space-x-2 space-x-reverse transition-all duration-300 ease-in-out bg-[var(--color-Gestion)] border border-[var(--color-ajustes)] hover:border-[#39A900] text-black rounded-full w-full sm:w-auto">
                        {{-- w-full en móvil, w-auto en sm+ --}}
                        <span class="text-md font-medium hover:text-[var(--color-hover)]">
                            {{ __('Exportar csv') }}
                        </span>
                        <img src="{{ asset('images/export.svg') }}"
                            class="relative inset-0 block w-5 h-4 group-hover:hidden" alt="Icono Exportar CSV"> {{--
                        Ajuste de tamaño de imagen --}}
                        <img src="{{ asset('images/export-hover.svg') }}"
                            class="relative inset-0 hidden w-5 h-4 group-hover:block" alt="Icono Exportar CSV"> {{--
                        Ajuste de tamaño de imagen --}}
                    </button>
                </form>

                @if (Auth::user()->hasAnyRole(['SuperAdmin', 'Administrador']))
                <button type="button" id="create-user-button" class="inline-flex items-center px-6 py-3 space-x-2 transition-all duration-300 ease-in-out bg-[#39A900]
                        hover:bg-[#61BA33] text-white rounded-full w-full sm:w-auto create-user-button"> {{-- w-full en
                    móvil, w-auto en sm+ --}}
                    <img src="{{ asset('images/signo.svg') }}" class="w-4 h-3 mr-3" alt="Icono Nuevo Usuario"> {{--
                    Añadido mr-3 para espacio --}}
                    <span class="font-medium text-md whitespace-nowrap">
                        {{ __('Crear usuario') }}
                    </span>
                </button>
                @endif
            </div>
        </div>

        {{-- Mensajes de éxito o error --}}
        @if (session('success'))
        <div class="p-4 mb-4 text-green-800 bg-green-100 rounded shadow">{{ session('success') }}</div>
        @endif

        @if (session('error'))
        <div class="p-4 mb-4 text-red-800 bg-red-100 rounded shadow">{{ session('error') }}</div>
        @endif

        @include('usuarios.partials.formulario', [
        'roles' => $roles,
        'permissions' => $permissions,
        ])

        {{-- Aquí se incluye la tabla. Asegúrate de que el partial 'usuarios.partials.tabla' tenga el 'overflow-x-auto'
        y 'min-w-max' --}}
        @include('usuarios.partials.tabla')

        @if ($usuarios->total() > 0 && $usuarios->hasPages())
        <div class="p-2 mt-6 bg-white shadow-sm rounded-b-xl"> {{-- Añadido p-2 bg-white shadow-sm para la paginación
            --}}
            {{ $usuarios->links('vendor.pagination.tailwind') }}
        </div>
        @endif
    </div>
    @endcan
    @endsection
