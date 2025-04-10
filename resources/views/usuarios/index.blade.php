<x-app-layout>
    <x-slot name="header"><h2 class="text-xl font-semibold">Lista de Usuarios</h2></x-slot>

    <div class="py-6 mx-auto max-w-7xl">
        <a href="{{ route('usuarios.create') }}" class="inline-block px-4 py-2 mb-4 text-white bg-blue-600 rounded hover:bg-blue-700">
            + Nuevo Usuario
        </a>

        <!-- actualizacion 09/04/2025 -->

        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif


        <div class="p-6 bg-white rounded shadow">
            <table class="w-full table-auto">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="px-4 py-2">Nombre</th>
                        <th class="px-4 py-2">Correo</th>
                        <th class="px-4 py-2">Rol</th>
                        <th class="px-4 py-2">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($usuarios as $usuario)
                        <tr>
                            <td class="px-4 py-2 border">{{ $usuario->name }}</td>
                            <td class="px-4 py-2 border">{{ $usuario->email }}</td>
                            <td class="px-4 py-2 border">{{ $usuario->roles->pluck('name')->first() ?? 'Sin rol' }}</td>
                            <td class="px-4 py-2 space-x-2 border">
                                <a href="{{ route('usuarios.edit', $usuario) }}" class="text-yellow-600 hover:underline">Editar</a>
                                <form action="{{ route('usuarios.destroy', $usuario) }}" method="POST" class="inline-block" onsubmit="return confirm('¿Eliminar este usuario?')">
                                    @csrf @method('DELETE')
                                    <button class="text-red-600 hover:underline">Eliminar</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="py-4 text-center">No hay usuarios aún.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
