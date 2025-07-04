<div id="appNotificationModal"
    class="fixed inset-0 z-[10000] items-center justify-center bg-gray-900 bg-opacity-50 hidden">
    <div class="relative bg-white rounded-lg shadow-xl p-6 w-full max-w-sm mx-auto my-8">
        <div class="flex items-center justify-center flex-col">
            {{-- Icono (se actualizará con JS) --}}
            <div id="appNotificationIconContainer" class="mb-4">
                {{-- SVG de éxito (por defecto, se puede cambiar a error con JS) --}}
                <svg id="appNotificationSuccessIcon" class="w-16 h-16 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                {{-- SVG de error (inicialmente oculto) --}}
                <svg id="appNotificationErrorIcon" class="w-16 h-16 text-red-500 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            {{-- Mensaje --}}
            <p id="appNotificationText" class="text-lg font-semibold text-gray-800 text-center"></p>
            {{-- Botón OK para cerrar --}}
            <button type="button" id="appNotificationCloseButton"
                class="mt-6 px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                OK
            </button>
        </div>
    </div>
</div>
