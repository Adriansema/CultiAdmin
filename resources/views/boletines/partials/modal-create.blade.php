<!-- Modal -->
<div x-show="open" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50"
    style="display: none;">
    <div class="w-full max-w-2xl p-6 bg-white rounded shadow-lg">
        <h3 class="mb-4 text-xl font-semibold">Crear Boletín o Importar desde PDF</h3>

        <!-- Crear boletín e importar PDF -->
        <form action="{{ route('boletines.importarPdf') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="mb-4">
                <label class="block font-semibold">Contenido del boletín</label>
                <textarea name="contenido" required class="w-full px-3 py-2 border rounded"
                    placeholder="Ej: Boletín de mora - semana 3"></textarea>
            </div>
            <div class="mb-4">
                <label class="block font-semibold">Importar archivo PDF</label>
                <input type="file" name="archivo" accept=".pdf" required class="w-full px-3 py-2 border rounded" />
            </div>
            <button type="submit" class="px-4 py-2 text-white bg-green-600 rounded hover:bg-green-700">
                Guardar Boletín
            </button>
        </form>

        <hr class="my-4" />


        <button @click="open = false" class="px-4 py-2 mt-4 text-gray-700 bg-gray-200 rounded hover:bg-gray-300">
            Cerrar
        </button>
    </div>
</div>
