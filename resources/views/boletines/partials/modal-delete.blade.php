<div id="modal-boletin-{{ $boletin->id }}" class="hidden">
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
        <div class="w-full max-w-md p-6 bg-white rounded-lg shadow-lg">
            <h3 class="mb-4 text-lg font-bold text-gray-800">
                ¿Estás seguro de eliminar este boletin?
            </h3>
            <p class="mb-4 text-gray-600">
                Esta acción no se puede deshacer. El boletin será eliminado permanentemente
                del sistema.
            </p>
            <form action="{{ route('boletines.destroy', $boletin) }}" method="POST" class="inline-block">
                @csrf
                @method('DELETE')
                <div class="flex justify-end mt-4 space-x-2">
                    <button type="button" onclick="cerrarModal('boletin', '{{ $boletin->id }}')"
                        class="px-4 py-2 text-gray-700 bg-gray-200 rounded hover:bg-gray-300">
                        Cancelar
                    </button>
                    <x-button class="bg-red-600 hover:bg-red-700">
                        Eliminar
                    </x-button>
                </div>
            </form>
        </div>
    </div>
</div>
