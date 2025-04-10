<x-app-layout>
    <x-slot name="header"><h2 class="text-xl font-semibold">Productos Pendientes</h2></x-slot>

    <!-- actualizacion 09/04/2025 -->

    <div class="py-6 mx-auto max-w-7xl">
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
                        <th class="px-4 py-2">Descripción</th>
                        <th class="px-4 py-2">Imagen</th>
                        <th class="px-4 py-2">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($productos as $producto)
                        <tr>
                            <td class="px-4 py-2 border">{{ $producto->nombre }}</td>
                            <td class="px-4 py-2 border">{{ Str::limit($producto->descripcion, 50) }}</td>
                            <td class="px-4 py-2 border">
                                @if($producto->imagen)
                                    <img src="{{ asset('storage/' . $producto->imagen) }}" alt="Imagen del producto {{ $producto->nombre }}" class="object-cover w-24 h-24 rounded">
                                @else
                                    <span class="text-gray-400">Sin imagen</span>
                                @endif
                            </td>
                            <td class="px-4 py-2 space-y-1 border">
                                <form action="{{ route('operador.productos.validar', $producto->id) }}" method="POST">
                                    @csrf
                                    <x-button class="w-full bg-green-600 hover:bg-green-700" aria-label="Validar {{ $producto->nombre }}">Validar</x-button>
                                </form>

                                <button onclick="mostrarModal('{{ $producto->id }}')" class="w-full px-4 py-2 mt-1 text-white bg-red-600 rounded hover:bg-red-700" aria-label="Rechazar {{ $producto->nombre }}">
                                    Rechazar
                                </button>

                                <!-- Modal de Observaciones -->
                                <div id="modal-rechazo-{{ $producto->id }}" class="fixed inset-0 z-50 items-center justify-center hidden transition-opacity duration-200 bg-black bg-opacity-50">
                                    <div class="max-w-md p-6 bg-white rounded-lg shadow-lg">
                                        <h3 class="mb-2 text-lg font-bold">Observación de rechazo</h3>
                                        <form action="{{ route('operador.productos.rechazar', $producto->id) }}" method="POST">
                                            @csrf
                                            <textarea name="observaciones" class="w-full border-gray-300 rounded shadow-sm" rows="4" required></textarea>
                                            <div class="flex justify-end mt-4 space-x-2">
                                                <button type="button" onclick="ocultarModal('{{ $producto->id }}')" class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">Cancelar</button>
                                                <x-button class="bg-red-600 hover:bg-red-700">Rechazar</x-button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="py-4 text-center">No hay productos pendientes.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <script>
        function mostrarModal(id) {
            const modal = document.getElementById('modal-rechazo-' + id);
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            document.body.classList.add('overflow-hidden');
        }

        function ocultarModal(id) {
            const modal = document.getElementById('modal-rechazo-' + id);
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            document.body.classList.remove('overflow-hidden');
        }
    </script>
</x-app-layout>
