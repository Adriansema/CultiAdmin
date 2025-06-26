@extends('layouts.app')

@section('content')

@can('crear boletin')
<div class="max-w-6xl py-6 mx-auto">
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

<div class="w-full max-w-6xl px-6 py-1 mx-auto bg-[var(--color-Gestion)] rounded-xl">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

        <form id="searchBol" action="{{ route('boletines.index') }}" method="GET"
            class="flex items-center w-full max-w-xl">
            @include('boletines.partials.search')
        </form>

        <div class="flex flex-wrap items-center justify-end gap-2 py-4">
            <button type="button" id="filterBtn"
                class="inline-flex group items-center justify-center px-4 py-3 space-x-2 space-x-reverse transition-all duration-300 ease-in-out bg-[var(--color-Gestion)] border border-[var(--color-ajustes)] hover:border-[#39A900] rounded-full w-auto">
                <span class="text-xs font-medium text-black whitespace-nowrap hover:text-[var(--color-hover)]">
                    {{ __('Filtrar') }}
                </span>
                <img src="{{ asset('images/filtro.svg') }}" class="relative inset-0 block w-4 h-3 group-hover:hidden"
                    alt="Icono de filtro">
                <img src="{{ asset('images/filtro-hover.svg') }}"
                    class="relative inset-0 hidden w-4 h-3 group-hover:block" alt="Icono de filtro hover">
            </button>

            <form method="GET" action="{{ route('boletines.exportarCSV') }}">
                <x-responsive-nav-link href="#" onclick="this.closest('form').submit(); return false;"
                    class="inline-flex items-center group justify-center px-4 py-3 space-x-2 space-x-reverse transition-all duration-300 ease-in-out
                            bg-[var(--color-Gestion)] border border-[var(--color-ajustes)] hover:border-[#39A900] text-white rounded-full w-auto">
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
                {{-- Ya no necesitamos x-data en este div, ni x-ref en el modal, --}}
                <div>
                    <div class="mb-4">
                        <button type="button" @click="window.openCreateBoletinModal()" class="inline-flex items-center px-4 py-3 space-x-2 transition-all duration-300 ease-in-out bg-[#39A900]
                        hover:bg-[#61BA33] text-white rounded-full w-auto">
                            + Crear / Importar Boletín
                        </button>
                    </div>
                    @include('boletines.partials.modal-create')
                </div>
            </x-responsive-nav-link>
        </div>
    </div>

    <div x-data="{
                showSuccessModal: false,
                showErrorModal: false,
                modalMessage: '',
                showModal: function() {
                    if (this.showSuccessModal || this.showErrorModal) {
                        // Se asegura de que solo uno esté visible y maneja el cierre automático
                        setTimeout(() => {
                            this.showSuccessModal = false;
                            this.showErrorModal = false;
                            this.modalMessage = '';
                        }, 2000); // Se cierra automáticamente después de 2 segundos
                    }
                }
            }" x-init="$watch('showSuccessModal', showModal);
            $watch('showErrorModal', showModal);">

        {{-- Modal de Éxito (controlado por Alpine.js) --}}
        <template x-if="showSuccessModal">
            <div class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900 bg-opacity-50">
                <div x-transition.opacity x-show="showSuccessModal"
                    class="relative max-w-lg p-6 text-center bg-white rounded-lg shadow-xl">
                    {{-- Botón de cerrar (la "X") --}}
                    <button @click="showSuccessModal = false"
                        class="absolute text-2xl font-bold leading-none text-gray-500 top-3 right-3 hover:text-gray-700 focus:outline-none">
                        &times;
                    </button>

                    {{-- Icono de Éxito --}}
                    <img src="{{ asset('images/check.svg') }}" alt="Icono de éxito" class="w-24 h-24 mx-auto mb-4">
                    <h2 class="mb-4 text-2xl font-bold text-green-600">¡Éxito!</h2>
                    <p class="text-base text-gray-700" x-text="modalMessage"></p> {{-- Muestra el mensaje desde la
                    variable de Alpine --}}
                </div>
            </div>
        </template>

        {{-- Modal de Error --}}
        <template x-if="showErrorModal">
            <div class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900 bg-opacity-50">
                <div x-transition.opacity x-show="showErrorModal"
                    class="relative max-w-sm p-6 text-center bg-white rounded-lg shadow-xl">
                    {{-- Botón de cerrar (la "X") --}}
                    <button @click="showErrorModal = false"
                        class="absolute text-2xl font-bold leading-none text-gray-500 top-3 right-3 hover:text-gray-700 focus:outline-none">
                        &times;
                    </button>

                    {{-- Icono de Error --}}
                    <img src="{{ asset('images/error.svg') }}" alt="Icono de error" class="w-24 h-24 mx-auto mb-4">
                    <h2 class="mb-4 text-2xl font-bold text-red-600">¡Error!</h2>
                    <p class="text-base text-gray-700" x-text="modalMessage"></p>
                </div>
            </div>
        </template>
    </div>

    {{-- Mensajes de éxito o error --}}
    @if (session('success'))
    <div class="p-4 mb-4 text-green-800 bg-green-100 rounded shadow">{{ session('success') }}</div>
    @endif

    @if (session('error'))
    <div class="p-4 mb-4 text-red-800 bg-red-100 rounded shadow">{{ session('error') }}</div>
    @endif

    @include('boletines.partials.tabla')

    @forelse ($boletines as $boletin)
    @include('boletines.partials.modal-views', ['boletin' => $boletin])

    @include('boletines.partials.modal-edit', ['boletin' => $boletin])

    @include('boletines.partials.modal-delete', ['boletin' => $boletin])
    @empty
    {{-- Si no hay boletines, no se renderiza ningún modal --}}
    @endforelse

    @if ($boletines->total() > 0 && $boletines->hasPages())
    <div class="p-4 mt-4 rounded-b-xl">
        {{ $boletines->links('vendor.pagination.tailwind') }}
    </div>
    @endif
</div>
@endcan
@endsection
