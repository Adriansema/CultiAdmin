<div class="overflow-x-auto rounded-2xl">
    <table class="min-w-full divide-y divide-gray-100">
        <thead class="bg-[var(--color-tabla)]">
            <tr>
                <th class="px-6 py-3 font-medium text-left text-gray-600">Rol</th>
                <th class="px-6 py-3 font-medium text-left text-gray-600">Nombre</th>
                <th class="px-6 py-3 font-medium text-left text-gray-600">Email</th>
                <th class="px-6 py-3 font-medium text-left text-gray-600">Estado</th>
            </tr>
        </thead>

        <tbody id="usersTableBody">
            @if ($usuarios->total() === 0)
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
                        <td class="px-6 py-4 flex items-center group relative">
                            <span>{{ $usuario->roles->pluck('name')->join(', ') }}</span>
                            <img src="{{ asset('images/lapiz.svg') }}"
                                class="w-4 h-4 absolute left-[calc(60%+4px)] top-1/2 -translate-y-1/2 
                                        opacity-0 group-hover:opacity-100 
                                        transition-opacity duration-300 pointer-events-none group-hover:pointer-events-auto"
                                alt="editar">
                        </td>
                        <td class="px-6 py-4">{{ $usuario->name }}
                        </td>
                        <td class="px-6 py-4">{{ $usuario->email }}
                        </td>
                        <td class="px-6 py-4">
                            <form action="{{ route('usuarios.toggle', $usuario) }}" method="POST" class="inline-block">
                                @csrf
                                @method('PATCH')

                                <button type="submit"
                                    class="group relative px-4 py-2 text-sm rounded text-white transition-colors duration-300
                                            {{ $usuario->estado === 'activo' ? 'bg-green-600 hover:bg-red-600' : 'bg-gray-400 hover:bg-yellow-300 hover:text-black' }}
                                            inline-flex items-center justify-center">
                                    {{-- Usamos inline-flex para el botón --}}

                                    {{-- Contenido para el estado normal (Activo/Inactivo) --}}
                                    <span
                                        class="flex items-center space-x-2 transition-opacity duration-300
                                                        {{ $usuario->estado === 'activo' ? '' : 'text-black' }}
                                                        group-hover:opacity-0 pointer-events-none">
                                        {{-- Añadir pointer-events-none aquí también --}}
                                        <span>{{ ucfirst($usuario->estado) }}</span>
                                        <img src="{{ asset('images/RL.svg') }}" alt="Icono" class="w-4 h-4">
                                    </span>

                                    {{-- Contenido para el estado al hacer hover (Desactivar/Activar) --}}
                                    <span
                                        class="absolute inset-0 flex items-center justify-center space-x-2
                                                        opacity-0 group-hover:opacity-100 transition-opacity duration-300
                                                        {{ $usuario->estado === 'activo' ? 'text-white' : 'text-black' }}
                                                        pointer-events-none">
                                        <span>{{ $usuario->estado === 'activo' ? 'Desactivar' : 'Activar' }}</span>
                                        <img src="{{ asset('images/RL.svg') }}" alt="Icono Hover" class="w-4 h-4">
                                    </span>
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            @endif
        </tbody>
    </table>
</div>
