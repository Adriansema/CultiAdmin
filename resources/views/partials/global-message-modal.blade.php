{{-- resources/views/partials/global-message-modal.blade.php --}}
<div id="globalMessageModalVanilla"
    class="fixed inset-0 z-[10000] items-center justify-center bg-gray-900 bg-opacity-50 hidden">
    <div class="relative w-full max-w-sm p-6 mx-auto my-8 bg-white rounded-lg shadow-xl">
        <div class="flex flex-col items-center justify-center">
            {{-- Icono (se actualizará con JS) --}}
            <div id="globalMessageIconContainer" class="mb-4">
                {{-- SVG de éxito (por defecto, se puede cambiar a error con JS) --}}
                <svg id="globalMessageSuccessIcon" class="w-16 h-16 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                {{-- SVG de error (inicialmente oculto) --}}
                <svg id="globalMessageErrorIcon" class="hidden w-16 h-16 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            {{-- Mensaje --}}
            <p id="globalMessageText" class="text-lg font-semibold text-center text-gray-800"></p>
            {{-- Botón OK para cerrar --}}
            <button type="button" id="globalMessageCloseButton"
                class="px-4 py-2 mt-6 text-white bg-blue-600 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                Aceptar
            </button>
        </div>
    </div>
</div>
