<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            Productos Pendientes por Validar
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="p-6 bg-white rounded-lg shadow-xl">
                @if ($productos->count() > 0)
                    <table class="w-full text-sm text-left text-gray-600">
                        <thead>
                            <tr>
                                <th class="px-4 py-2">Nombre</th>
                                <th class="px-4 py-2">Tipo</th>
                                <th class="px-4 py-2">Suelo</th>
                                <th class="px-4 py-2">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($productos as $producto)
                                <tr class="border-t">
                                    <td class="px-4 py-2">{{ $producto->nombre }}</td>
                                    <td class="px-4 py-2">{{ $producto->tipo }}</td>
                                    <td class="px-4 py-2">{{ $producto->suelo }}</td>
                                    <td class="flex px-4 py-2 space-x-2">
                                        <!-- Validar -->
                                        <form action="{{ route('productos.validar', $producto->id) }}" method="POST">
                                            @csrf
                                            <button class="px-3 py-1 text-white bg-green-600 rounded hover:bg-green-700">
                                                Validar
                                            </button>
                                        </form>

                                        <!-- Rechazar (mostrar modal) -->
                                        <button
                                            @click="open = true; selectedProductId = {{ $producto->id }}"
                                            class="px-3 py-1 text-white bg-red-600 rounded hover:bg-red-700">
                                            Rechazar
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <p class="text-gray-500">No hay productos pendientes por validar.</p>
                @endif
            </div>
        </div>
    </div>

    <!-- Modal para observaciones al rechazar -->
    <div x-data="{ open: false, selectedProductId: null }">
        <div x-show="open" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
            <form
                method="POST"
                :action="'/productos/' + selectedProductId + '/rechazar'"
                class="w-1/3 p-6 bg-white rounded-lg"
                @submit="open = false">
                @csrf
                <h2 class="mb-4 text-lg font-semibold text-gray-800">Observación de Rechazo</h2>
                <textarea name="observacion" rows="4" class="w-full p-2 border rounded" placeholder="Escribe el motivo del rechazo..." required></textarea>
                <div class="flex justify-end mt-4 space-x-2">
                    <button type="button" @click="open = false" class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">
                        Cancelar
                    </button>
                    <button type="submit" class="px-4 py-2 text-white bg-red-600 rounded hover:bg-red-700">
                        Rechazar Producto
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
