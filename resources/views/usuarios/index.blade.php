@extends('layouts.app')

@section('content')
    <div class="inline-block px-8 py-10">
        <div class="flex items-center space-x-2">
            <img src="{{ asset('images/reverse.svg') }}" class="w-4 h-4" alt="Icono Nuevo Usuario">
            <h1 class="text-3xl whitespace-nowrap font-bold">Gestión de Usuarios</h1>
        </div>
        {!! Breadcrumbs::render('usuarios.index') !!}
    </div>

    <div class="w-full max-w-6xl px-6 py-1 mx-auto bg-[var(--color-Gestion)] rounded-xl">
        <div class="flex items-center justify-between">

            @include('usuarios.partials.search')

            <div class="flex items-center justify-end py-4 space-x-2">
                {{-- ? Este código genera una barra de herramientas con tres botones de acción, ubicados a la derecha de la pantalla: --}}
                <x-responsive-nav-link href="{{ route('usuarios.index') }}"
                {{-- ! Filtrar: Un botón para filtrar usuarios, que al pasar el ratón muestra un icono diferente. --}}
                    class="inline-flex group items-center justify-center px-4 py-3 space-x-2 space-x-reverse transition-all duration-300 ease-in-out bg-[var(--color-Gestion)] border border-[var(--color-ajustes)] hover:border-[#39A900] rounded-full w-auto">
                    <span class="text-xs font-medium text-black whitespace-nowrap hover:text-[var(--color-hover)]">
                        {{ __('Filtrar') }}
                    </span>
                    <img src="{{ asset('images/filtro.svg') }}" class="w-4 h-3 relative inset-0 block group-hover:hidden" alt="Icono Nuevo Usuario">
                    <img src="{{ asset('images/filtro-hover.svg') }}" class="w-4 h-3 relative inset-0 hidden group-hover:block" alt="Icono Nuevo Usuario">
                </x-responsive-nav-link>

                <form method="GET" action="{{ route('usuarios.exportar') }}">
                    {{-- ! Exportar CSV: Un botón para exportar la lista de usuarios a un archivo CSV, también con un efecto de cambio de icono al hacer hover. Este botón, aunque es un enlace de navegación, está dentro de un formulario que envía una solicitud GET para la exportación. --}}
                    <x-responsive-nav-link href="#" onclick="this.closest('form').submit(); return false;"
                        class="inline-flex items-center group justify-center px-4 py-3 space-x-2 space-x-reverse transition-all duration-300 ease-in-out bg-[var(--color-Gestion)] border border-[var(--color-ajustes)] hover:border-[#39A900] text-white rounded-full w-auto">
                        <span class="text-xs font-medium text-black whitespace-nowrap hover:text-[var(--color-hover)]">
                            {{ __('Exportar Csv') }}
                        </span>
                        <img src="{{ asset('images/export.svg') }}" class="w-5 h-4 relative inset-0 block group-hover:hidden" alt="Icono Exportar CSV">
                        <img src="{{ asset('images/export-hover.svg') }}" class="w-5 h-4 relative inset-0 hidden group-hover:block" alt="Icono Exportar CSV">
                    </x-responsive-nav-link>
                </form>

                <x-responsive-nav-link href="{{ route('usuarios.create') }}"
                {{-- ! Nuevo Usuario: Un botón para crear un nuevo usuario, que tiene un icono y un cambio de color al pasar el ratón. --}}
                    class="inline-flex items-center px-4 py-3 space-x-2 transition-all duration-300 ease-in-out bg-[#39A900] hover:bg-[#61BA33] text-white rounded-full w-auto">
                    <img src="{{ asset('images/signo.svg') }}" class="w-4 h-3" alt="Icono Nuevo Usuario">
                    <span class="text-xs font-medium whitespace-nowrap">
                        {{ __('Nuevo Usuario') }}
                    </span>
                </x-responsive-nav-link>
            </div>
        </div>

        {{-- Mensajes de éxito o error --}}
        @if (session('success'))
            <div class="p-4 mb-4 text-green-800 bg-green-100 rounded shadow">{{ session('success') }}</div>
        @endif

        @if (session('error'))
            <div class="p-4 mb-4 text-red-800 bg-red-100 rounded shadow">{{ session('error') }}</div>
        @endif

        @include('usuarios.partials.tabla')

        @if ($usuarios->total() > 0 && $usuarios->hasPages())
            <div class="p-4 mt-4 rounded-b-xl">
                {{ $usuarios->links('vendor.pagination.tailwind') }}
            </div>
        @endif
    </div>
@endsection
