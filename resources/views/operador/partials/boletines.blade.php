<h3 class="mb-3 text-lg font-bold">Boletines Pendientes</h3>
<div class="overflow-hidden bg-white rounded shadow">
    <table class="min-w-full text-sm text-left bg-red-200 hover:bg-red-300">
        <thead>
            <tr class="bg-red-100">
                <th class="px-4 py-2">Asunto</th>
                <th class="px-4 py-2">Contenido</th>
                <th class="px-4 py-2">Fecha</th>
                <th class="px-4 py-2">Estado</th>
                <th class="px-4 py-2">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse($boletines as $boletin)
                <tr class="bg-gray-200 hover:bg-gray-300 border-t-2">
                    <td class="px-4 py-2">{{ $boletin->asunto }}</td>
                    <td class="px-4 py-2">{{ Str::limit(strip_tags($boletin->contenido), 50) }}</td>
                    <td class="px-4 py-2 text-gray-600">{{ $boletin->updated_at->format('d/m/Y H:i') }}</td>
                    <td class="px-4 py-2">
                        <span
                            class="inline-block px-3 py-1 text-sm font-semibold text-white rounded
                                        {{ $boletin->estado === 'aprobado' ? 'bg-green-600' : ($boletin->estado === 'pendiente' ? 'bg-yellow-500' : 'bg-red-600') }}">
                            {{ ucfirst($boletin->estado) }}
                        </span>
                    </td>
                    <td class="px-4 py-2 space-x-2">
                        <a href="{{ route('operador.boletines.show', $boletin->id) }}"
                            class="text-blue-600 hover:underline">Ver</a>

                        <button type="button" onclick="mostrarModal('validar-boletin', '{{ $boletin->id }}')"
                            class="text-green-600 hover:underline">
                            Validar
                        </button>
                        <!-- Modal de validar -->
                        <div id="modal-validar-boletin-{{ $boletin->id }}" class="hidden">
                            <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
                                <div class="w-full max-w-md p-6 bg-white rounded-lg shadow-lg">
                                    <h3 class="mb-4 text-lg font-bold text-gray-800">Observaciones de la Validación</h3>
                                    <form action="{{ route('operador.boletines.validar', $boletin->id) }}"
                                        method="POST">
                                        @csrf
                                        <textarea name="observaciones" class="w-full p-2 border border-gray-300 rounded-md" rows="4" required></textarea>
                                        @error('observaciones')
                                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                        <div class="flex justify-end mt-4 space-x-2">
                                            <button type="button"
                                                onclick="ocultarModal('validar-boletin', '{{ $boletin->id }}')"
                                                class="px-4 py-2 text-gray-700 bg-gray-200 rounded hover:bg-gray-300">Cancelar</button>
                                            <x-button class="bg-green-600 hover:bg-green-700">Validar</x-button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- Botón que abre el modal -->
                        <button type="button" onclick="mostrarModal('rechazar-boletin', '{{ $boletin->id }}')"
                            class="text-red-600 hover:underline">
                            Rechazar
                        </button>
                        <!-- Modal de rechazo -->
                        <div id="modal-rechazar-boletin-{{ $boletin->id }}" class="hidden">
                            <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
                                <div class="w-full max-w-md p-6 bg-white rounded-lg shadow-lg">
                                    <h3 class="mb-4 text-lg font-bold text-gray-800">Observaciones del rechazo
                                    </h3>
                                    <form action="{{ route('operador.boletines.rechazar', $boletin->id) }}"
                                        method="POST">
                                        @csrf
                                        <textarea name="observaciones" class="w-full p-2 border border-gray-300 rounded-md" rows="4" required></textarea>
                                        @error('observaciones')
                                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                        <div class="flex justify-end mt-4 space-x-2">
                                            <button type="button"
                                                onclick="ocultarModal('rechazar-boletin', '{{ $boletin->id }}')"
                                                class="px-4 py-2 text-gray-700 bg-gray-200 rounded hover:bg-gray-300">Cancelar</button>
                                            <x-button class="bg-red-600 hover:bg-red-700">Rechazar</x-button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" class="py-4 text-center text-gray-500">No hay boletines pendientes.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
    <div class="mt-4">
        {{ $boletines->links() }}
    </div>
</div>
