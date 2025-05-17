@extends('layouts.app')

@section('content')
    <div class="inline-block px-8 py-10">
        <div class="flex items-center space-x-2">
            <img src="{{ asset('images/reverse.svg') }}" class="w-4 h-4" alt="Icono Nuevo Usuario">
            <h1 class="text-3xl whitespace-nowrap font-bold">Gestión de Usuarios</h1>
        </div>
        {!! Breadcrumbs::render('usuarios.index') !!}
    </div>

    <div class="w-full max-w-6xl px-6 py-1 mx-auto bg-gray-200 rounded-2xl">
        <div class="flex items-center justify-between">
            <div class="flex items-center justify-start px-2 py-4">
                <form action="{{ route('usuarios.index') }}" method="GET" class="relative w-full md:w-auto md:flex-grow">
                    {{-- Campo de búsqueda unificado --}}
                    <input type="text" name="q" id="site-search" placeholder="Buscar por nombre o email"
                        value="{{ request('q') }}"
                        class="block w-full py-2 pl-10 pr-3 leading-5 text-xs placeholder-gray-500 bg-white border border-gray-300 rounded-full focus:outline-none focus:ring focus:border-[var(--color-Gestion)] sm:text-sm" />

                    {{-- Icono de lupa --}}
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <img src="{{ asset('images/search.svg') }}" class="w-5 h-5 text-gray-400" alt="Icono de búsqueda">
                    </div>

                    {{-- Resultados en vivo si deseas usarlo en el futuro --}}
                    <div id="search-results"
                        class="absolute z-10 hidden w-full mt-1 bg-white border border-gray-300 rounded-md shadow-lg">
                    </div>
                </form>
            </div>

            <div class="flex items-center justify-end py-4 space-x-2">
                {{-- FILTRAR --}}
                <x-responsive-nav-link href="{{ route('usuarios.index') }}"
                    class="inline-flex items-center justify-center px-4 py-2 space-x-2 space-x-reverse transition-all duration-300 ease-in-out bg-gray-300 hover:bg-[var(--color-Gestion)] text-white rounded-full w-auto">
                    <span class="text-xs font-medium text-black whitespace-nowrap">
                        {{ __('Filtrar') }}
                    </span>
                    <img src="{{ asset('images/filtro.svg') }}" class="w-4 h-3" alt="Icono Nuevo Usuario">
                </x-responsive-nav-link>

                {{-- FILTRAR --}}
                {{-- <form method="GET" action="{{ route('usuarios.index') }}"
                    class="mb-4 flex flex-col md:flex-row md:items-center gap-2">

                    {{-- Filtro por rol --
                    <select name="rol"
                        class="text-xs border border-gray-300 rounded-full px-4 py-2 focus:outline-none focus:ring focus:border-[var(--color-Gestion)] w-full md:w-auto">
                        <option value="">-- Rol --</option>
                        <option value="administrador" {{ request('rol') == 'administrador' ? 'selected' : '' }}>
                            Administrador</option>
                        <option value="operador" {{ request('rol') == 'operador' ? 'selected' : '' }}>Operador</option>
                    </select>

                    {{-- Filtro por estado --
                    <select name="estado"
                        class="text-xs border border-gray-300 rounded-full px-4 py-2 focus:outline-none focus:ring focus:border-[var(--color-Gestion)] w-full md:w-auto">
                        <option value="">-- Estado --</option>
                        <option value="Activo" {{ request('estado') == 'Activo' ? 'selected' : '' }}>Activo</option>
                        <option value="Inactivo" {{ request('estado') == 'Inactivo' ? 'selected' : '' }}>Inactivo</option>
                    </select>

                    {{-- Botón de filtrar con tu estilo --
                    <button type="submit"
                        class="inline-flex items-center justify-center px-4 py-2 space-x-2 space-x-reverse transition-all duration-300 ease-in-out bg-gray-300 hover:bg-[var(--color-Gestion)] text-white rounded-full w-auto">
                        <span class="text-xs font-medium text-black whitespace-nowrap">Filtrar</span>
                        <img src="{{ asset('images/filtro.svg') }}" class="w-4 h-3" alt="Icono Filtro">
                    </button>
                </form> --}}

                {{-- EXPORTAR CSV --}}
                <form method="GET" action="{{ route('usuarios.exportar') }}">
                    <input type="hidden" name="q" value="{{ request('q') }}">
                    <input type="hidden" name="rol" value="{{ request('rol') }}">
                    <input type="hidden" name="estado" value="{{ request('estado') }}">

                    <x-responsive-nav-link href="#" onclick="this.closest('form').submit(); return false;"
                        class="inline-flex items-center justify-center px-4 py-2 space-x-2 space-x-reverse transition-all duration-300 ease-in-out bg-gray-300 hover:bg-[var(--color-Gestion)] text-white rounded-full w-auto">
                        <span class="text-xs font-medium text-black whitespace-nowrap">
                            {{ __('Exportar Csv') }}
                        </span>
                        <img src="{{ asset('images/export.svg') }}" class="w-4 h-3" alt="Icono Exportar CSV">
                    </x-responsive-nav-link>
                </form>

                {{-- NUEVO USUARIO --}}
                <x-responsive-nav-link href="{{ route('usuarios.create') }}"
                    class="inline-flex items-center px-4 py-2 space-x-2 transition-all duration-300 ease-in-out bg-[#39A900] hover:bg-[#61BA33] text-white rounded-full w-auto">
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

        <div class="overflow-x-auto bg-gray-200 rounded-xl">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-blue-200">
                    <tr>
                        <th class="px-6 py-3 font-medium text-left text-gray-600">Rol</th>
                        <th class="px-6 py-3 font-medium text-left text-gray-600">Nombre</th>
                        <th class="px-6 py-3 font-medium text-left text-gray-600">Email</th>
                        <th class="px-6 py-3 font-medium text-left text-gray-600">Estado</th>
                    </tr>
                </thead>

                <tbody>
                    @if ($usuarios->isEmpty())
                        <tr>
                            <td colspan="4" class="px-6 py-4 text-center text-gray-500">
                                @if (request()->has('q') && !empty(request()->get('q')))
                                    No se encontraron usuarios que coincidan con
                                    "{{ htmlspecialchars(request()->get('q')) }}".
                                @else
                                    No hay usuarios registrados.
                                @endif
                            </td>
                        </tr>
                    @else
                        @foreach ($usuarios as $usuario)
                            <tr class="bg-white hover:bg-gray-300">
                                <td class="px-6 py-4">
                                    {{ $usuario->roles->pluck('name')->join(', ') }}
                                </td>
                                <td class="px-6 py-4">{{ $usuario->name }}
                                    <img src="{{ asset('images/person(1).svg') }}" class="w-4 h-4" alt="usuario">
                                </td>
                                <td class="px-6 py-4">{{ $usuario->email }}
                                    <img src="{{ asset('images/lapiz.svg') }}" class="w-4 h-4" alt="editar">
                                </td>
                                <td class="px-6 py-4">
                                    <form action="{{ route('usuarios.toggle', $usuario) }}" method="POST"
                                        class="inline-block">
                                        @csrf
                                        @method('PATCH')

                                        <button
                                            class="px-3 py-1 text-sm rounded text-white transition-colors duration-700
                                    @if ($usuario->estado === 'activo') bg-green-600 hover:bg-red-600 hover:text-white
                                    @else
                                        bg-gray-400 hover:bg-yellow-300 hover:text-black @endif"
                                            title="{{ $usuario->estado === 'activo' ? 'Desactivar' : 'Activar' }}">
                                            {{ ucfirst($usuario->estado) }}
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>
            {{-- Asegúrate de añadir los enlaces de paginación si estás usando `paginate()` en el controlador --}}
            @if ($usuarios->hasPages())
                <div class="p-4 mt-4 bg-white rounded-b-xl">
                    {{ $usuarios->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
