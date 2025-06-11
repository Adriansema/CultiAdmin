@extends('layouts.app')

{{-- TODO: Por el momento no está en uso, pero se puede reutilizar para hacer el modal para cuando se pase el cursor por encima de la columna rol --}}

@section('content')
    <div class="max-w-4xl py-6 mx-auto">
        <div class="p-6 bg-white rounded shadow">
            <h2 class="mb-4 text-2xl font-semibold">Detalles del Usuario</h2>

            <div class="grid gap-4">
                <!-- Nombre -->
                <div>
                    <label class="block text-sm text-gray-500">Nombre</label>
                    <div class="text-lg font-medium text-gray-800">{{ $usuario->name }}</div>
                </div>

                <!-- Correo -->
                <div>
                    <label class="block text-sm text-gray-500">Correo electrónico</label>
                    <div class="text-lg text-gray-800">{{ $usuario->email }}</div>
                </div>

                <!-- Roles -->
                <div>
                    <label class="block text-sm text-gray-500">Roles asignados</label>
                    <div class="flex flex-wrap gap-2 mt-1">
                        @forelse ($usuario->roles as $role)
                            <span
                                class="px-3 py-1 text-sm text-blue-800 bg-blue-100 rounded-full">{{ $role->name }}</span>
                        @empty
                            <span class="text-sm text-gray-400">Sin roles</span>
                        @endforelse
                    </div>
                </div>

                <!-- Estado -->
                <div>
                    <label class="block text-sm text-gray-500">Estado</label>
                    <div class="text-lg font-semibold {{ $usuario->activo ? 'text-green-600' : 'text-red-600' }}">
                        {{ $usuario->activo ? 'Activo' : 'Inactivo' }}
                    </div>
                </div>
            </div>

            <!-- Botón de volver -->
            <div class="mt-6">
                <a href="{{ route('usuarios.index') }}"
                    class="inline-block px-4 py-2 text-white bg-gray-600 rounded hover:bg-gray-700">
                    ← Volver a la lista
                </a>
            </div>
        </div>
    </div>
@endsection
