@extends('layouts.app')

@section('content')
<div class="py-6 mx-auto max-w-7xl">

    @if (session('success'))
        <div class="p-4 mb-4 text-green-800 bg-green-200 rounded">
            {{ session('success') }}
        </div>
    @endif

    {{-- Productos Pendientes --}}
    <div class="mb-10">
        <h3 class="mb-3 text-lg font-bold">Productos Pendientes</h3>
        <div class="p-6 bg-green-100 rounded shadow">
            <table class="w-full table-auto">
                <thead>
                    <tr class="bg-gray-200">
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
                                    <img src="{{ asset('storage/' . $producto->imagen) }}" class="object-cover w-24 h-24 rounded" alt="Imagen de {{ $producto->nombre }}">
                                @else
                                    <span class="text-gray-400">Sin imagen</span>
                                @endif
                            </td>
                            <td class="px-4 py-2 space-y-1 border">
                                <form action="{{ route('operador.productos.validar', $producto->id) }}" method="POST" onsubmit="return confirm('¿Estás seguro de validar este producto?')">
                                    @csrf
                                    <x-button class="w-full bg-green-600 hover:bg-green-700">Validar</x-button>
                                </form>

                                <button onclick="mostrarModal('producto', '{{ $producto->id }}')" class="w-full px-4 py-2 mt-1 text-white bg-red-600 rounded hover:bg-red-700">Rechazar</button>

                                <!-- Modal de rechazo -->
                                <div id="modal-producto-{{ $producto->id }}" class="hidden">
                                    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
                                        <div class="w-full max-w-md p-6 bg-white rounded-lg shadow-lg">
                                            <h3 class="mb-4 text-lg font-bold text-gray-800">Observaciones del rechazo</h3>
                                            <form action="{{ route('operador.productos.rechazar', $producto->id) }}" method="POST">
                                                @csrf
                                                <textarea name="observaciones" class="w-full p-2 border border-gray-300 rounded-md" rows="4" required></textarea>
                                                @error('observaciones')
                                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                                @enderror
                                                <div class="flex justify-end mt-4 space-x-2">
                                                    <button type="button" onclick="ocultarModal('producto', '{{ $producto->id }}')" class="px-4 py-2 text-gray-700 bg-gray-200 rounded hover:bg-gray-300">Cancelar</button>
                                                    <x-button class="bg-red-600 hover:bg-red-700">Rechazar</x-button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="py-4 text-center text-gray-500">No hay productos pendientes.</td></tr>
                    @endforelse
                </tbody>
            </table>
            <div class="mt-4">
                {{ $productos->links() }}
            </div>
        </div>
    </div>

    {{-- Boletines Pendientes --}}
    <div>
        <h3 class="mb-3 text-lg font-bold">Boletines Pendientes</h3>
        <div class="p-6 bg-green-100 rounded shadow">
            <table class="w-full table-auto">
                <thead>
                    <tr class="bg-gray-200">
                        <th class="px-4 py-2">Asunto</th>
                        <th class="px-4 py-2">Contenido</th>
                        <th class="px-4 py-2">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($boletines as $boletin)
                        <tr>
                            <td class="px-4 py-2 border">{{ $boletin->asunto }}</td>
                            <td class="px-4 py-2 border">{{ Str::limit(strip_tags($boletin->contenido), 50) }}</td>
                            <td class="px-4 py-2 space-y-1 border">
                                <form action="{{ route('operador.boletines.validar', $boletin->id) }}" method="POST" onsubmit="return confirm('¿Estás seguro de validar este boletín?')">
                                    @csrf
                                    <x-button class="w-full bg-green-600 hover:bg-green-700">Validar</x-button>
                                </form>

                                <button onclick="mostrarModal('boletin', '{{ $boletin->id }}')" class="w-full px-4 py-2 mt-1 text-white bg-red-600 rounded hover:bg-red-700">Rechazar</button>

                                <!-- Modal de rechazo -->
                                <div id="modal-boletin-{{ $boletin->id }}" class="hidden">
                                    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
                                        <div class="w-full max-w-md p-6 bg-white rounded-lg shadow-lg">
                                            <h3 class="mb-4 text-lg font-bold text-gray-800">Observaciones del rechazo</h3>
                                            <form action="{{ route('operador.boletines.rechazar', $boletin->id) }}" method="POST">
                                                @csrf
                                                <textarea name="observaciones" class="w-full p-2 border border-gray-300 rounded-md" rows="4" required></textarea>
                                                @error('observaciones')
                                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                                @enderror
                                                <div class="flex justify-end mt-4 space-x-2">
                                                    <button type="button" onclick="ocultarModal('boletin', '{{ $boletin->id }}')" class="px-4 py-2 text-gray-700 bg-gray-200 rounded hover:bg-gray-300">Cancelar</button>
                                                    <x-button class="bg-red-600 hover:bg-red-700">Rechazar</x-button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="py-4 text-center text-gray-500">No hay boletines pendientes.</td></tr>
                    @endforelse
                </tbody>
            </table>
            <div class="mt-4">
                {{ $boletines->links() }}
            </div>
        </div>
    </div>
</div>

{{-- Scripts para modales --}}
<script>
    function mostrarModal(tipo, id) {
        const modal = document.getElementById(`modal-${tipo}-${id}`);
        modal.classList.remove('hidden');
    }

    function ocultarModal(tipo, id) {
        const modal = document.getElementById(`modal-${tipo}-${id}`);
        modal.classList.add('hidden');
    }
</script>
@endsection
