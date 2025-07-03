@extends('layouts.app')

@section('content')
    @can('crear boletin')
        {{-- Contenedor principal del encabezado (Boletines, Breadcrumbs) --}}
        <div class="max-w-7xl py-6 mx-auto px-4 sm:px-6 lg:px-8">
            <div>
                <h1 class="flex items-center space-x-2 text-3xl font-bold text-gray-800">
                    <img src="{{ asset('images/reverse.svg') }}" alt="icono" class="w-5 h-5">
                    <span>Boletines</span>
                </h1>
            </div>
            <div class="py-6">
                {!! Breadcrumbs::render('boletines.index') !!}
            </div>
        </div>

        {{-- Contenedor principal de la sección de gestión (fondo verde claro) --}}
        <div class="w-full max-w-7xl mx-auto bg-[var(--color-Gestion)] rounded-xl p-4 sm:p-6 lg:p-8 mb-8">

            {{-- Contenedor de búsqueda y grupo de botones de acción --}}
            <div class="flex flex-col sm:flex-row items-center justify-between mb-4 flex-wrap">

                {{-- Formulario de búsqueda --}}
                <form id="searchBol" action="{{ route('boletines.index') }}" method="GET"
                    class="flex items-center w-full max-w-full sm:max-w-xs md:max-w-sm lg:max-w-md xl:max-w-lg 
            mb-4 sm:mb-0 mr-0 sm:mr-4">
                    @include('boletines.partials.search')
                </form>

                {{-- Grupo de los tres botones: Filtro, Exportar y Crear/Importar --}}
                <div class="flex items-center justify-center sm:justify-end flex-nowrap gap-2 py-4 sm:py-0">

                    {{-- Botón Filtrar --}}
                    {{-- INICIO DEL CAMBIO: Reemplazo del Botón Filtrar por el Select de Estado --}}
                    <div class="inline-flex items-center"> {{-- Contenedor para el select y para mantener alineación --}}
                        <label for="filtro-estado" class="sr-only">Filtrar por Estado</label>
                        <select id="filtro-estado" name="estado"
                            class="block w-full px-4 py-3 text-xs font-medium text-black
                           bg-[var(--color-Gestion)] border border-[var(--color-ajustes)] rounded-full
                           focus:border-[#39A900] focus:ring-[#39A900] transition duration-300 ease-in-out
                           appearance-none pr-8">
                            {{-- pr-8 para dejar espacio al icono de la flecha del select --}}
                            <option value="todos" {{ request('estado') == 'todos' ? 'selected' : '' }}>Filtro por Estados
                            </option>
                            <option value="pendiente" {{ request('estado') == 'pendiente' ? 'selected' : '' }}>Pendiente
                            </option>
                            <option value="aceptado" {{ request('estado') == 'aceptado' ? 'selected' : '' }}>Aprovado</option>
                            <option value="rechazado" {{ request('estado') == 'rechazado' ? 'selected' : '' }}>Rechazado
                            </option>
                        </select>
                        {{-- Opcional: si quieres un ícono de filtro visualmente al lado del select --}}
                        {{-- <img src="{{ asset('images/filtro.svg') }}" class="relative -ml-6 w-4 h-3 pointer-events-none" alt="Icono de filtro"> --}}
                    </div>

                    

                    {{-- Botón Exportar Csv (dentro de un form) --}}
                    <form method="GET" action="{{ route('boletines.exportarCSV') }}">
                        <x-responsive-nav-link href="#" onclick="this.closest('form').submit(); return false;"
                            class="inline-flex items-center group justify-center px-4 py-3 space-x-2 space-x-reverse transition-all duration-300 ease-in-out
                            bg-[var(--color-Gestion)] border border-[var(--color-ajustes)] hover:border-[#39A900] text-white rounded-full whitespace-nowrap">
                            <span class="text-xs font-medium text-black whitespace-nowrap hover:text-[var(--color-hover)]">
                                {{ __('Exportar Csv') }}
                            </span>
                            <img src="{{ asset('images/export.svg') }}"
                                class="relative inset-0 block w-5 h-4 group-hover:hidden" alt="Icono Exportar CSV">
                            <img src="{{ asset('images/export-hover.svg') }}"
                                class="relative inset-0 hidden w-5 h-4 group-hover:block" alt="Icono Exportar CSV">
                        </x-responsive-nav-link>
                    </form>

                    {{-- *** Botón Crear / Importar Boletín (REEMPLAZADO con un simple div) *** --}}
                    <div class="p-0 inline-block"> {{-- Mantenemos p-0 y inline-block para la alineación --}}
                        <button type="button" @click.prevent="window.openCreateBoletinModal()"
                            class="inline-flex items-center px-4 py-3 space-x-2 transition-all duration-300 ease-in-out bg-[#39A900]
                hover:bg-[#61BA33] text-white rounded-full whitespace-nowrap">
                            + Crear / Importar Boletín
                        </button>
                        @include('boletines.partials.modal-create')
                    </div>
                    {{-- *** FIN REEMPLAZO *** --}}
                </div>
            </div>

            {{-- Contenedor de modales de éxito/error (Alpine.js) --}}
            <div x-data="{
                showSuccessModal: false,
                showErrorModal: false,
                modalMessage: '',
                showModal: function() {
                    if (this.showSuccessModal || this.showErrorModal) {
                        setTimeout(() => {
                            this.showSuccessModal = false;
                            this.showErrorModal = false;
                            this.modalMessage = '';
                        }, 2000);
                    }
                }
            }" x-init="$watch('showSuccessModal', showModal);
            $watch('showErrorModal', showModal);">

                {{-- Modales de Éxito y Error (controlados por Alpine.js) --}}
                <template x-if="showSuccessModal">
                    <div class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900 bg-opacity-50">
                        <div x-transition.opacity x-show="showSuccessModal"
                            class="relative max-w-lg p-6 text-center bg-white rounded-lg shadow-xl">
                            <button @click="showSuccessModal = false"
                                class="absolute text-2xl font-bold leading-none text-gray-500 top-3 right-3 hover:text-gray-700 focus:outline-none">
                                &times;
                            </button>
                            <img src="{{ asset('images/check.svg') }}" alt="Icono de éxito" class="w-24 h-24 mx-auto mb-4">
                            <h2 class="mb-4 text-2xl font-bold text-green-600">¡Éxito!</h2>
                            <p class="text-base text-gray-700" x-text="modalMessage"></p>
                        </div>
                    </div>
                </template>

                <template x-if="showErrorModal">
                    <div class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900 bg-opacity-50">
                        <div x-transition.opacity x-show="showErrorModal"
                            class="relative max-w-sm p-6 text-center bg-white rounded-lg shadow-xl">
                            <button @click="showErrorModal = false"
                                class="absolute text-2xl font-bold leading-none text-gray-500 top-3 right-3 hover:text-gray-700 focus:outline-none">
                                &times;
                            </button>
                            <img src="{{ asset('images/error.svg') }}" alt="Icono de error" class="w-24 h-24 mx-auto mb-4">
                            <h2 class="mb-4 text-2xl font-bold text-red-600">¡Error!</h2>
                            <p class="text-base text-gray-700" x-text="modalMessage"></p>
                        </div>
                    </div>
                </template>
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

        </div>
    @endcan
@endsection
