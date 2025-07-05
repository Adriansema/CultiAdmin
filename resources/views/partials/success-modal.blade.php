<div id="noticia-modal-exito" class="fixed inset-0 z-50 items-center justify-center hidden bg-black bg-opacity-50">
    <div id="noticia-modal-contenido" class="w-full max-w-sm p-8 mx-4 bg-white rounded-lg shadow-xl opacity-0">
        <div class="text-center">
            <svg class="w-12 h-12 mx-auto text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <h3 class="mt-4 mb-2 text-xl font-bold text-gray-800" id="success-modal-title">
                ¡Éxito!
            </h3>
            <p class="mb-6 text-gray-600" id="success-modal-message">
                {{-- El mensaje se insertará aquí con JavaScript --}}
            </p>
            <button type="button" id="noticia-modal-cerrar-btn"
                class="px-5 py-2 font-semibold text-white bg-green-600 rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-opacity-75">
                Cerrar
            </button>
        </div>
    </div>
</div>
