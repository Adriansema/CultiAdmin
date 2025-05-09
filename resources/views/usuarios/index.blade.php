@extends('layouts.app')

@section('header')
    <h2 class="text-xl font-semibold">Lista de Usuarios</h2>
@endsection

@section('content')
    <div class="max-w-6xl py-6 mx-auto">
        <a href="{{ route('usuarios.create') }}" class="inline-block px-4 py-2 mb-4 text-white bg-blue-600 rounded">Nuevo Usuario</a>

        @if(session('success'))
            <div class="p-4 mb-4 text-green-800 bg-green-100 rounded shadow">{{ session('success') }}</div>
        @endif

        @if(session('error'))
            <div class="p-4 mb-4 text-red-800 bg-red-100 rounded shadow">{{ session('error') }}</div>
        @endif

        <div class="overflow-x-auto bg-white rounded shadow">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 font-medium text-left text-gray-600">Nombre</th>
                        <th class="px-6 py-3 font-medium text-left text-gray-600">Email</th>
                        <th class="px-6 py-3 font-medium text-left text-gray-600">Rol</th>
                        <th class="px-6 py-3 font-medium text-left text-gray-600">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($usuarios as $usuario)
                        <tr class="hover:bg-gray-100">
                            <td class="px-6 py-4">{{ $usuario->name }}</td>
                            <td class="px-6 py-4">{{ $usuario->email }}</td>
                            <td class="px-6 py-4">
                                {{ $usuario->roles->pluck('name')->join(', ') }}
                            </td>

                            <td class="px-6 py-4 space-y-1">
                                <a href="{{ route('usuarios.show', $usuario) }}" class="block text-blue-600 hover:underline">Ver</a>
                                <a href="{{ route('usuarios.edit', $usuario) }}" class="block text-yellow-600 hover:underline">Editar</a>

                                <form action="{{ route('usuarios.toggle', $usuario) }}" method="POST" class="inline-block mt-1">
                                    @csrf
                                    @method('PATCH')
                                    <button class="px-3 py-1 text-sm rounded text-white {{ $usuario->estado ? 'bg-red-500' : 'bg-green-500' }}">
                                        {{ $usuario->estado ? 'Desactivar' : 'Activar' }}
                                    </button>
                                </form>

                                <form action="{{ route('usuarios.destroy', $usuario) }}" method="POST" class="inline-block mt-1">
                                    @csrf
                                    @method('DELETE')
                                    <button class="text-sm text-red-600 hover:underline" onclick="return confirm('¿Estás seguro?')">Eliminar</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                    @if ($usuarios->isEmpty())
                        <tr>
                            <td colspan="4" class="px-6 py-4 text-center text-gray-500">No hay usuarios registrados.</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
@endsection
