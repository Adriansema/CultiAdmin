@extends('layouts.app')

@section('header')
<h2 class="text-xl font-semibold">Listado de Boletines</h2>
@endsection

@section('content')
<div class="max-w-6xl py-6 mx-auto">
    @if (session('success'))
    <div class="p-4 mb-4 text-green-800 bg-green-100 border border-green-300 rounded">
        {{ session('success') }}
    </div>
    @endif

    <div>
        <h1 class="flex items-center space-x-2 text-3xl font-bold text-gray-800">
            <img src="{{ asset('images/reverse.svg') }}" alt="icono" class="w-5 h-5">
            <span>Boletines</span>
        </h1>
    </div>

    <div class="py-6">
        {!! Breadcrumbs::render('boletines.index') !!}
    </div>

    <!-- Aquí puedes pegar el nuevo contenedor de buscador y botones que hablamos antes -->

</div>



<div class="w-full max-w-6xl px-6 py-1 mx-auto bg-[var(--color-Gestion)] rounded-xl">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        @include('boletines.partials.search')

       <div class="flex flex-wrap items-center justify-end gap-2 py-4">

            {{-- ? Este código genera una barra de herramientas con tres botones de acción, ubicados a la derecha de la
            pantalla: --}}
            <x-responsive-nav-link href="{{ route('boletines.index') }}" {{-- ! Filtrar: Un botón para filtrar
                boletines, que al pasar el ratón muestra un icono diferente. --}}
                class="inline-flex group items-center justify-center px-4 py-3 space-x-2 space-x-reverse transition-all duration-300 ease-in-out bg-[var(--color-Gestion)] border border-[var(--color-ajustes)] hover:border-[#39A900] rounded-full w-auto">
                <span class="text-xs font-medium text-black whitespace-nowrap hover:text-[var(--color-hover)]">
                    {{ __('Filtrar') }}
                </span>
                <img src="{{ asset('images/filtro.svg') }}" class="relative inset-0 block w-4 h-3 group-hover:hidden"
                    alt="Icono Nuevo Usuario">
                <img src="{{ asset('images/filtro-hover.svg') }}"
                    class="relative inset-0 hidden w-4 h-3 group-hover:block" alt="Icono Nuevo Usuario">
            </x-responsive-nav-link>

            <form method="GET" action="{{ route('boletines.exportarCSV') }}">
                {{-- ! Exportar CSV: Un botón para exportar la lista de boletines a un archivo CSV, también con un
                efecto de cambio de icono al hacer hover. Este botón, aunque es un enlace de navegación, está dentro de
                un formulario que envía una solicitud GET para la exportación. --}}
                <x-responsive-nav-link href="#" onclick="this.closest('form').submit(); return false;"
                    class="inline-flex items-center group justify-center px-4 py-3 space-x-2 space-x-reverse transition-all duration-300 ease-in-out bg-[var(--color-Gestion)] border border-[var(--color-ajustes)] hover:border-[#39A900] text-white rounded-full w-auto">
                    <span class="text-xs font-medium text-black whitespace-nowrap hover:text-[var(--color-hover)]">
                        {{ __('Exportar Csv') }}
                    </span>
                    <img src="{{ asset('images/export.svg') }}"
                        class="relative inset-0 block w-5 h-4 group-hover:hidden" alt="Icono Exportar CSV">
                    <img src="{{ asset('images/export-hover.svg') }}"
                        class="relative inset-0 hidden w-5 h-4 group-hover:block" alt="Icono Exportar CSV">
                </x-responsive-nav-link>
            </form>

            <x-responsive-nav-link>
                {{--
                ! Nuevo Usuario: Un botón para crear un nuevo boletín, que tiene un icono y un cambio de color al pasar
                el ratón.
                class="inline-flex items-center px-4 py-3 space-x-2 transition-all duration-300 ease-in-out bg-[#39A900]
                hover:bg-[#61BA33] text-white rounded-full w-auto">
                <img src="{{ asset('images/signo.svg') }}" class="w-4 h-3" alt="Icono Nuevo Usuario">
                <span class="text-xs font-medium whitespace-nowrap">
                    {{ __('Nuevo Usuario') }}
                </span>
                --}}

                <div x-data="{ open: false }">
                    <div class="mb-4">
                        <button @click="open = true"
                            class="inline-flex items-center px-3 py-2 space-x-2 transition-all duration-300 ease-in-out bg-[#39A900]
                hover:bg-[#61BA33] text-white rounded-full w-auto">
                            + Crear / Importar Boletín
                        </button>
                    </div>

                    @include('boletines.partials.modal-create')
                </div>
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

    @include('boletines.partials.tabla')

    @if ($boletines->total() > 0 && $boletines->hasPages())
    <div class="p-4 mt-4 rounded-b-xl">
        {{ $boletines->links('vendor.pagination.tailwind') }}
    </div>
    @endif
</div>

@endsection
