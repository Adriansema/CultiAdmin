{{-- resources/views/VistasUsers/index.blade.php --}}
@extends('layouts.app')

@section('header')
    <h2 class="text-xl font-semibold">Lista de Usuarios</h2>
@endsection

@section('content')
<div class="px-6 py-4">
    <a href="{{ route('view-user.create') }}" class="inline-block px-4 py-2 mb-4 text-white bg-blue-600 rounded">+ Nuevo Usuario</a>

    @if(session('success'))
        <div class="p-3 mb-3 bg-green-200 rounded">{{ session('success') }}</div>
    @endif

    <table class="w-full border table-auto">
        <thead>
            <tr class="bg-gray-100">
                <th class="px-4 py-2">Nombre</th>
                <th>Email</th>
                <th>Teléfono</th>
                <th>Estado</th>
                <th>Rol</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach($usuarios as $usuario)
                <tr class="border-t">
                    <td class="px-4 py-2">{{ $usuario->name }}</td>
                    <td>{{ $usuario->email }}</td>
                    <td>{{ $usuario->telefono }}</td>
                    <td>{{ $usuario->estado }}</td>
                    <td>{{ $usuario->roles->pluck('name')->join(', ') }}</td>
                    <td class="flex py-2 space-x-2">
                        <a href="{{ route('view-user.show', $usuario->id) }}" class="text-blue-500">Ver</a>
                        <a href="{{ route('view-user.edit', $usuario->id) }}" class="text-yellow-500">Editar</a>
                        <a href="{{ route('view-user.historial', $usuario->id) }}" class="text-purple-600">Historial</a>
                        <form action="{{ route('view-user.destroy', $usuario->id) }}" method="POST" onsubmit="return confirm('¿Eliminar?')" class="inline">
                            @csrf @method('DELETE')
                            <button class="text-red-600">Eliminar</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="mt-4">
        {{ $usuarios->links() }}
    </div>
</div>
@endsection
