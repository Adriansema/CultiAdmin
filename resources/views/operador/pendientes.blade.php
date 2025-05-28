@extends('layouts.app')

@section('content')
    <div class="container mx-auto py-6" x-data="{ tab: 'productos' }">
        <div class="inline-block px-8 py-10">
            <div class="flex items-center space-x-2">
                <img src="{{ asset('images/reverse.svg') }}" class="w-4 h-4" alt="Icono Nuevo Usuario">
                <h1 class="text-3xl whitespace-nowrap font-bold">Gestión de Validaciones</h1>
            </div>
            {!! Breadcrumbs::render('operador.pendientes') !!}
        </div>

        @if (session('success'))
            <div class="p-4 mb-4 text-green-800 bg-green-200 rounded">
                {{ session('success') }}
            </div>
        @endif

        <div class="flex border-b mb-6">
            <button class="px-4 py-2 text-sm font-medium transition duration-200 ease-in-out transform text-shadow"
                :class="{
                    'border-b-2 border-green-600 text-green-600': tab === 'productos',
                    'text-gray-600 hover:text-green-500 hover:-translate-y-0.5 hover:scale-105 text-shadow-hover': tab !== 'productos'
                }"
                @click="tab = 'productos'">
                Productos
            </button>

            <button class="px-4 py-2 text-sm font-medium ml-4 transition duration-200 ease-in-out transform text-shadow"
                :class="{
                    'border-b-2 border-green-600 text-green-600': tab === 'boletines',
                    'text-gray-600 hover:text-green-500 hover:-translate-y-0.5 hover:scale-105 text-shadow-hover': tab !== 'boletines'
                }"
                @click="tab = 'boletines'">
                Boletines
            </button>
        </div>

        {{-- Bloque para Productos --}}
        <div x-show="tab === 'productos'" class="w-full max-w-6xl px-6 py-1 mx-auto bg-[var(--color-Gestion)] rounded-xl">
            <div class="flex items-center justify-between">
                @include('operador.partials.pro-search')
                <div class="flex items-center justify-end py-4 space-x-2">
                    <x-responsive-nav-link href="{{ route('operador.pendientes') }}"
                        class="inline-flex group items-center justify-center px-4 py-3 space-x-2 space-x-reverse transition-all duration-300 ease-in-out bg-[var(--color-Gestion)] border border-[var(--color-ajustes)] hover:border-[#39A900] rounded-full w-auto">
                        <span class="text-xs font-medium text-black whitespace-nowrap hover:text-[var(--color-hover)]">
                            {{ __('Filtrar') }}
                        </span>
                        <img src="{{ asset('images/filtro.svg') }}"
                            class="w-4 h-3 relative inset-0 block group-hover:hidden" alt="Icono Nuevo Usuario">
                        <img src="{{ asset('images/filtro-hover.svg') }}"
                            class="w-4 h-3 relative inset-0 hidden group-hover:block" alt="Icono Nuevo Usuario">
                    </x-responsive-nav-link>

                    <form method="GET" action="{{ route('operador.pendientes') }}">
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
                </div>
            </div>

            {{-- Mensajes de éxito o error --}}
            @if (session('success'))
                <div class="p-4 mb-4 text-green-800 bg-green-100 rounded shadow">{{ session('success') }}</div>
            @endif

            @if (session('error'))
                <div class="p-4 mb-4 text-red-800 bg-red-100 rounded shadow">{{ session('error') }}</div>
            @endif

            @include('operador.partials.productos')

            @if ($productos->total() > 0 && $productos->hasPages())
                <div class="p-4 mt-4 rounded-b-xl">
                    {{ $productos->links('vendor.pagination.tailwind', ['paginationName' => 'productos_page'])->withQueryString() }}
                </div>
            @endif
        </div>

        {{-- Bloque para Boletines --}}
        <div x-show="tab === 'boletines'" x-cloak class="w-full max-w-6xl px-6 py-1 mx-auto bg-[var(--color-Gestion)] rounded-xl">
            <div class="flex items-center justify-between">
                @include('operador.partials.bol-search')
                <div class="flex items-center justify-end py-4 space-x-2">
                    <x-responsive-nav-link href="{{ route('operador.pendientes') }}"
                        class="inline-flex group items-center justify-center px-4 py-3 space-x-2 space-x-reverse transition-all duration-300 ease-in-out bg-[var(--color-Gestion)] border border-[var(--color-ajustes)] hover:border-[#39A900] rounded-full w-auto">
                        <span class="text-xs font-medium text-black whitespace-nowrap hover:text-[var(--color-hover)]">
                            {{ __('Filtrar') }}
                        </span>
                        <img src="{{ asset('images/filtro.svg') }}"
                            class="w-4 h-3 relative inset-0 block group-hover:hidden" alt="Icono Nuevo Usuario">
                        <img src="{{ asset('images/filtro-hover.svg') }}"
                            class="w-4 h-3 relative inset-0 hidden group-hover:block" alt="Icono Nuevo Usuario">
                    </x-responsive-nav-link>

                    <form method="GET" action="{{ route('operador.pendientes') }}">
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
                </div>
            </div>

            {{-- Mensajes de éxito o error --}}
            @if (session('success'))
                <div class="p-4 mb-4 text-green-800 bg-green-100 rounded shadow">{{ session('success') }}</div>
            @endif

            @if (session('error'))
                <div class="p-4 mb-4 text-red-800 bg-red-100 rounded shadow">{{ session('error') }}</div>
            @endif

            @include('operador.partials.boletines')

            @if ($boletines->total() > 0 && $boletines->hasPages())
                <div class="p-4 mt-4 rounded-b-xl">
                    {{ $boletines->links('vendor.pagination.tailwind', ['paginationName' => 'boletines_page'])->withQueryString() }}
                </div>
            @endif
        </div>

    </div>

    {{-- Scripts para modales --}}
    <script>
        function mostrarModal(tipo, id) {
            const modal = document.getElementById(`modal-${tipo}-${id}`);
            modal.classList.remove('hidden');
        }

        function ocultarModal(tipo, id) {
            const modal = document.getElementById(`modal-${tipo}-${id}`);
            modal.classList.add('hidden');
        }
    </script>
@endsection