@extends('layouts.app')

@section('content')

    @can('crear usuario')
        <div class="inline-block px-20 py-6">
            <div class="flex items-center space-x-4">
                <img src="{{ asset('images/reverse.svg') }}" class="w-4 h-4" alt="Icono Nuevo Usuario">
                <h1 class="text-3xl whitespace-nowrap font-bold">Gestión de Usuarios</h1>
            </div>
            <div class="py-2">
                {!! Breadcrumbs::render('usuarios.index') !!}
            </div>
        </div>

        <div class="w-full max-w-screen-2xl mx-auto bg-[var(--color-Gestion)] rounded-3xl p-4 mb-8">
            <div class="flex items-center justify-between flex-wrap"> {{-- Añadido flex-wrap para responsividad --}}

                {{-- FORMULARIO DE BÚSQUEDA (solo input de texto) --}}
                <form id="BuscarUser" action="{{ route('usuarios.index') }}" method="GET"
                    class="flex items-center w-full max-w-xl mb-4 md:mb-0"> {{-- Ajuste de margen para responsividad --}}
                    @include('usuarios.partials.search')
                </form>

                {{-- Contenedor de filtros y botones de acción --}}
                <div class="flex items-center justify-end py-6 space-x-2 flex-wrap ml-auto"> {{-- Añadido ml-auto y flex-wrap --}}
                    {{-- SELECT para filtrar por ESTADO --}}
                    <select id="filtrarEstado" name="estado"
                        class="inline-flex group items-center justify-center px-4 py-2 space-x-2 space-x-reverse transition-all duration-300 ease-in-out bg-[var(--color-Gestion)] border border-[var(--color-ajustes)] hover:border-[#39A900] rounded-full w-auto text-md font-medium text-black mr-2 mb-2 md:mb-0">
                        <option value="">{{ __('Todos los estados') }}</option>
                        <option value="activo" {{ request('estado') == 'activo' ? 'selected' : '' }}>{{ __('Activo') }}</option>
                        <option value="inactivo" {{ request('estado') == 'inactivo' ? 'selected' : '' }}>{{ __('Inactivo') }}</option>
                    </select>

                    {{-- SELECT para filtrar por ROL --}}
                    <select id="filtrarRol" name="rol"
                        class="inline-flex group items-center justify-center px-4 py-2 space-x-2 space-x-reverse transition-all duration-300 ease-in-out bg-[var(--color-Gestion)] border border-[var(--color-ajustes)] hover:border-[#39A900] rounded-full w-auto text-md font-medium text-black mr-2 mb-2 md:mb-0">
                        <option value="">{{ __('Todos los roles') }}</option>
                        @foreach ($roles as $role)
                            <option value="{{ $role->name }}" {{ request('rol') == $role->name ? 'selected' : '' }}>
                                {{ $role->name }}
                            </option>
                        @endforeach
                    </select>

                    <form method="GET" action="{{ route('usuarios.exportar') }}" class="mr-2 mb-2 md:mb-0">
                        <x-responsive-nav-link href="#" onclick="this.closest('form').submit(); return false;"
                            class="inline-flex items-center group justify-center px-4 py-2 space-x-2 space-x-reverse transition-all duration-300 ease-in-out bg-[var(--color-Gestion)] border border-[var(--color-ajustes)] hover:border-[#39A900] text-white rounded-full w-auto">
                            <span class="text-md font-medium text-black whitespace-nowrap hover:text-[var(--color-hover)]">
                                {{ __('Exportar Csv') }}
                            </span>
                            <img src="{{ asset('images/export.svg') }}"
                                class="w-5 h-4 relative inset-0 block group-hover:hidden" alt="Icono Exportar CSV">
                            <img src="{{ asset('images/export-hover.svg') }}"
                                class="w-5 h-4 relative inset-0 hidden group-hover:block" alt="Icono Exportar CSV">
                        </x-responsive-nav-link>
                    </form>

                    <button type="button" id="create-user-button"
                        class="inline-flex items-center px-4 py-2 space-x-2 transition-all duration-300 ease-in-out bg-[#39A900]
                        hover:bg-[#61BA33] text-white rounded-full w-auto create-user-button">
                        <img src="{{ asset('images/signo.svg') }}" class="w-4 h-3" alt="Icono Nuevo Usuario">
                        <span class="text-md font-medium whitespace-nowrap">
                            {{ __('Crear Usuario') }}
                        </span>
                    </button>
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
                'roles' => $roles, // Estos sí se pasan, son roles del sistema
                'permissions' => $permissions, // Estos también, son permisos del sistema
            ])

            @include('usuarios.partials.tabla')

            @if ($usuarios->total() > 0 && $usuarios->hasPages())
                <div class="mt-6 rounded-b-xl">
                    {{ $usuarios->links('vendor.pagination.tailwind') }}
                </div>
            @endif
        </div>
    @endcan
@endsection