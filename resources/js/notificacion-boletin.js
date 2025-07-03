document.addEventListener('DOMContentLoaded', function () {
    // Lógica para el mensaje "No hay boletines" (similar a noticias)
    function toggleNoBoletinesMessage() {
        const boletinesScrollContainer = document.querySelector('.boletines-scroll-container');
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

    // Si quieres que el boletín desaparezca al descargar (menos común, pero posible)
    // const downloadButtons = document.querySelectorAll('.download-boletin-btn');
    // downloadButtons.forEach(button => {
    //     button.addEventListener('click', function(event) {
    //         // event.preventDefault(); // Evita la descarga directa si quieres AJAX
    //         const boletinId = this.dataset.boletinId; // Si usas data-boletin-id
    //         const boletinElement = document.getElementById(`boletin-${boletinId}`);
    //         if (boletinElement) {
    //             boletinElement.classList.add('fade-out-boletin');
    //             boletinElement.addEventListener('transitionend', function() {
    //                 boletinElement.remove();
    //                 toggleNoBoletinesMessage(); // Actualiza el mensaje
    //             }, { once: true });
    //         }
    //         // Aquí iría la lógica AJAX para marcar como descargado si fuera necesario
    //     });
    // });
});