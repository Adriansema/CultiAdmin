<div id="modal-success-" class="hidden fixed inset-0 z-50 items-center justify-center bg-black bg-opacity-50 transition-opacity duration-300">
    <div class="bg-white rounded-lg shadow-xl p-8 max-w-sm w-full mx-4 transform transition-transform duration-300 scale-95 opacity-0"
         id="modal-content-success-">
        <div class="text-center">
            <svg class="mx-auto h-12 w-12 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <h3 class="text-xl font-bold text-gray-800 mt-4 mb-2" id="success-modal-title">
                ¡Éxito!
            </h3>
            <p class="text-gray-600 mb-6" id="success-modal-message">
                {{-- El mensaje se insertará aquí con JavaScript --}}
            </p>
            <button type="button" onclick="ocultarModal('success', '')"
                class="px-5 py-2 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-opacity-75">
                Cerrar
            </button>
        </div>
    </div>
</div>
