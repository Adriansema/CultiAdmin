document.addEventListener('DOMContentLoaded', function () {
    const boletinesContainer = document.querySelector('.boletines-scroll-container');
    
    if (boletinesContainer) {

        // Lógica para el mensaje "No hay boletines" (similar a noticias)
        function toggleNoBoletinesMessage() {
            const boletinesScrollContainer = document.querySelector('.boletines-scroll-container');

            // ¡IMPORTANTE! Verificar si el contenedor existe
            if (!boletinesScrollContainer) {
                console.error('ERROR: .boletines-scroll-container no encontrado. Asegúrate de que este elemento exista en tu HTML.');
                return; // Salir de la función si el contenedor no se encuentra
            }

            const remainingBoletines = boletinesScrollContainer.querySelectorAll('[id^="boletin-"]').length;
            const existingNoBoletinesMessage = boletinesScrollContainer.querySelector('.no-boletines-message');

            if (remainingBoletines === 0) {
                if (!existingNoBoletinesMessage) {
                    boletinesScrollContainer.insertAdjacentHTML('beforeend', `<p class="text-gray-700 p-4 bg-white rounded-lg shadow-md no-boletines-message">No hay boletines recientes para mostrar.</p>`);
                }
            } else {
                if (existingNoBoletinesMessage) {
                    existingNoBoletinesMessage.remove();
                }
            }
        }
        // Ejecutar al cargar para el estado inicial
        toggleNoBoletinesMessage();
    }

});