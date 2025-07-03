document.addEventListener('DOMContentLoaded', function () {
    // Obtener el token CSRF de la meta etiqueta
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    const markAsReadButtons = document.querySelectorAll('.mark-as-read-btn');

    // HTML del mensaje cuando no hay noticias
    const noNewsMessageHtml = `<p class="text-gray-700 p-4 bg-white rounded-lg shadow-md no-noticias-message">No hay noticias recientes para mostrar.</p>`;

    // Función para actualizar el contador de noticias en el encabezado
    function updateUnreadNewsCount(change) {
        const unreadNewsCountElement = document.getElementById('unread-news-count');
        if (unreadNewsCountElement) {
            let currentCountText = unreadNewsCountElement.textContent.trim();
            let currentCount = 0;

            // Parsear el conteo actual
            if (currentCountText === '+9') {
                currentCount = 10; // Asumimos que si es "+9", hay 10 o más
            } else {
                currentCount = parseInt(currentCountText) || 0;
            }

            // Aplicar el cambio
            currentCount += change;

            // Asegurarse de que el conteo no sea negativo
            if (currentCount < 0) {
                currentCount = 0;
            }

            // Actualizar el texto del contador
            if (currentCount >= 10) {
                unreadNewsCountElement.textContent = '+9';
            } else {
                unreadNewsCountElement.textContent = currentCount;
            }
        }
    }

    // Función para manejar la visibilidad del mensaje "No hay noticias"
    function toggleNoNewsMessage() {
        const noticiasScrollContainer = document.querySelector('.noticias-scroll-container');
        // Contamos cuántos elementos de noticia REALES quedan
        const remainingNoticias = noticiasScrollContainer.querySelectorAll('[id^="noticia-"]').length;
        const existingNoNewsMessage = noticiasScrollContainer.querySelector('.no-noticias-message');

        if (remainingNoticias === 0) {
            // Si no quedan noticias y el mensaje NO está ya presente, lo insertamos
            if (!existingNoNewsMessage) {
                noticiasScrollContainer.insertAdjacentHTML('beforeend', noNewsMessageHtml);
            }
        } else {
            // Si quedan noticias y el mensaje SÍ está presente, lo eliminamos
            if (existingNoNewsMessage) {
                existingNoNewsMessage.remove();
            }
        }
    }


    markAsReadButtons.forEach(button => {
        button.addEventListener('click', function () {
            const noticiaId = this.dataset.noticiaId;
            const noticiaElement = document.getElementById(`noticia-${noticiaId}`);

            // --- Depuración ---
            console.log('Botón clickeado para Noticia ID:', noticiaId);
            console.log('Elemento de Noticia encontrado:', noticiaElement);
            // --- Fin de depuración ---

            if (noticiaElement) {
                // Añadir clase para la animación de desvanecimiento
                noticiaElement.classList.add('fade-out');

                // Enviar petición al servidor para marcarla como leída
                fetch(`/noticia/${noticiaId}/mark-as-read`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({})
                })
                    .then(response => {
                        if (!response.ok) {
                            console.error('Network response was not ok:', response.status, response.statusText);
                            return response.json().then(err => { throw new Error(err.message || 'Error del servidor'); });
                        }
                        return response.json();
                    })
                    .then(data => {
                        console.log('Noticia marcada como leída en el servidor:', data);
                        // Eliminar el elemento del DOM después de la animación y la confirmación del servidor
                        noticiaElement.addEventListener('transitionend', function () {
                            noticiaElement.remove();
                            updateUnreadNewsCount(-1); // Decrementar el contador
                            toggleNoNewsMessage(); // Comprobar y mostrar/ocultar el mensaje
                        }, { once: true });
                    })
                    .catch(error => {
                        console.error('Error al marcar como leída:', error);
                        // Si hay un error, revertir la animación y mostrar un mensaje
                        noticiaElement.classList.remove('fade-out');
                        alert('Hubo un error al marcar la noticia como leída. Inténtalo de nuevo: ' + error.message);
                    });
            }
        });
    });

    // Ejecutar al cargar la página para asegurar que el mensaje se muestra/oculta correctamente si no hay noticias al inicio
    toggleNoNewsMessage();
});
