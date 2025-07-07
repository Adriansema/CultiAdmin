@extends('layouts.app')

@section('content')
    @can('crear boletin')
        <div class="inline-block px-20 py-6">
            <div class="flex items-center space-x-4">
                <img src="{{ asset('images/reverse.svg') }}" class="w-4 h-4" alt="Icono Nuevo Usuario">
                <h1 class="text-3xl whitespace-nowrap font-bold">Boletines</h1>
            </div>
            <div class="py-2">
                {!! Breadcrumbs::render('boletines.index') !!}
            </div>
        </div>

        <div class="w-full max-w-screen-2xl mx-auto bg-[var(--color-Gestion)] rounded-2xl p-4">
            <div class="flex items-center justify-between">
                <form id="searchBoletinForm" action="{{ route('boletines.index') }}" method="GET"
                    class="flex items-center w-full max-w-xl space-x-2">
                    @include('boletines.partials.search')

                    <select id="filtro-estado" name="estado"
                        class="inline-flex items-center justify-center px-4 py-3 space-x-2 space-x-reverse transition-all
                        duration-300 ease-in-out bg-[var(--color-Gestion)] border border-[var(--color-ajustes)]
                        hover:border-[#39A900] rounded-full whitespace-nowrap text-md font-medium
                        text-black pr-10 form-control  hover:border-[var(--color-hover)] w-full
                        focus:border-[var(--color-hover)] focus:outline-none focus:ring-0">
                        <option value="">{{ __('Todos los estados') }}</option>
                        <option value="aprobado" {{ request('estado') == 'aprobado' ? 'selected' : '' }}>{{ __('Aprobado') }}
                        </option>
                        <option value="pendiente" {{ request('estado') == 'pendiente' ? 'selected' : '' }}>{{ __('Pendiente') }}
                        </option>
                        <option value="rechazado" {{ request('estado') == 'rechazado' ? 'selected' : '' }}>{{ __('Rechazado') }}
                        </option>
                    </select>

                    <select id="filtro-precio" name="precio"
                        class="inline-flex items-center justify-center px-4 py-3 space-x-2 space-x-reverse transition-all
                        duration-300 ease-in-out bg-[var(--color-Gestion)] border border-[var(--color-ajustes)]
                        hover:border-[#39A900] rounded-full whitespace-nowrap font-medium
                        hover:border-[var(--color-hover)]
                        focus:border-[var(--color-hover)] focus:outline-none focus:ring-0 pr-10">
                        <option value="">{{ __('Todos los precios') }}</option>
                        <option value="precio_alto_desc" {{ request('precio') == 'precio_alto_desc' ? 'selected' : '' }}>
                            {{ __('Precio Más Alto (Desc)') }}</option>
                        <option value="precio_alto_asc" {{ request('precio') == 'precio_alto_asc' ? 'selected' : '' }}>
                            {{ __('Precio Más Alto (Asc)') }}</option>
                        <option value="precio_bajo_desc" {{ request('precio') == 'precio_bajo_desc' ? 'selected' : '' }}>
                            {{ __('Precio Más Bajo (Desc)') }}</option>
                        <option value="precio_bajo_asc" {{ request('precio') == 'precio_bajo_asc' ? 'selected' : '' }}>
                            {{ __('Precio Más Bajo (Asc)') }}</option>
                    </select>
                </form>

                <div class="flex items-center justify-end py-5 space-x-2">
                    <button type="button" id="exportCsvButton"
                        class="inline-flex items-center group justify-center px-6 py-3 space-x-2 transition-all duration-300 ease-in-out
                        bg-[var(--color-Gestion)] border border-[var(--color-ajustes)] hover:border-[#39A900] text-white rounded-full whitespace-nowrap">
                        <span class="font-medium text-black whitespace-nowrap hover:text-[var(--color-hover)]">
                            {{ __('Exportar Csv') }}
                        </span>
                        <img src="{{ asset('images/export.svg') }}" class="relative inset-0 block w-6 h-6 group-hover:hidden"
                            alt="Icono Exportar CSV">
                        <img src="{{ asset('images/export-hover.svg') }}"
                            class="relative inset-0 hidden w-6 h-6 group-hover:block" alt="Icono Exportar CSV">
                    </button>

                    <button type="button" @click.prevent="window.openCreateBoletinModal()"
                        class="inline-flex items-center px-6 py-3 space-x-2 transition-all duration-300 ease-in-out bg-[#39A900]
                        hover:bg-[#61BA33] text-white rounded-full whitespace-nowrap">
                        <img src="{{ asset('images/signo.svg') }}" class=" w-4 h-5 mr-3" alt="Icono signo de +">
                        Crear/Importar Boletín
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
            @include('boletines.partials.tabla')

            @forelse ($boletines as $boletin)
                @include('boletines.partials.modal-views', ['boletin' => $boletin])
                @include('boletines.partials.modal-edit', ['boletin' => $boletin])
                @include('boletines.partials.modal-delete', ['boletin' => $boletin])
                {{-- Asegúrate de que estos partials existan y sean correctos --}}
                @include('pendientes.partials.modal-boletin-validar', ['boletin' => $boletin])
                @include('pendientes.partials.modal-boletin-rechazar', ['boletin' => $boletin])
            @empty
                {{-- Si no hay boletines, no se renderiza ningún modal específico de boletín. --}}
            @endforelse

            {{-- Modal global para mensajes de éxito/error (se mantiene) --}}
            @include('partials.global-message-modal')

            {{-- La paginación de Laravel --}}
            @if ($boletines->total() > 0 && $boletines->hasPages())
                <div class="mt-4 rounded-b-xl">
                    {{ $boletines->links('vendor.pagination.tailwind') }}
                </div>
            @endif
        </div>
    @endcan
@endsection
@push('scripts')
    <script>
        const exportCsvBoletinesRoute = "{{ route('boletines.exportarCSV') }}";
    </script>
@endpush
