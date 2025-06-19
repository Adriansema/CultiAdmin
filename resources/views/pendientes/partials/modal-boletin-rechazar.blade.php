<!-- Modal de rechazo -->
<div id="modal-rechazar-boletin-{{ $boletin->id }}" class="hidden">
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
        <div class="w-full max-w-md p-6 bg-white rounded-lg shadow-lg">
            <h3 class="mb-4 text-lg font-bold text-gray-800">Observaciones del rechazo
            </h3>
            <form action="{{ route('pendientes.boletines.rechazar', $boletin->id) }}" method="POST">
                @csrf
                <textarea name="observaciones" class="w-full p-2 border border-gray-300 rounded-md" rows="4" required></textarea>
                @error('observaciones')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
                <div class="flex justify-end mt-4 space-x-2">
                    <button type="button" onclick="ocultarModal('rechazar-boletin', '{{ $boletin->id }}')"
                        class="px-4 py-2 text-gray-700 bg-gray-200 rounded hover:bg-gray-300">Cancelar</button>
                    <x-button class="bg-red-600 hover:bg-red-700">Rechazar</x-button>
                </div>
            </form>
        </div>
    </div>
</div>
