document.addEventListener('DOMContentLoaded', function () {
    const noticiasContainer = document.querySelector('.noticias-scroll-container');

    // Verifica que el contenedor de noticias exista
    if (noticiasContainer) {

        // Obtener el token CSRF de la meta etiqueta
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        const markAsReadButtons = document.querySelectorAll('.mark-as-read-btn');

        // HTML del mensaje cuando no hay noticias (ya es ASCII)
        const noNewsMessageHtml = `<p class="text-gray-700 p-4 bg-white rounded-lg shadow-md no-noticias-message">No hay noticias recientes para mostrar.</p>`;

        // Funcion para actualizar el contador de noticias en el encabezado
        function updateUnreadNewsCount(change) {
            const unreadNewsCountElement = document.getElementById('unread-news-count');
            if (unreadNewsCountElement) {
                let currentCountText = unreadNewsCountElement.textContent.trim();
                let currentCount = 0;

                // Parsear el conteo actual
                if (currentCountText === '+9') {
                    currentCount = 10; // Asumimos que si es "+9", hay 10 o mas
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

        // Funcion para manejar la visibilidad del mensaje "No hay noticias"
        function toggleNoNewsMessage() {
            const noticiasScrollContainer = document.querySelector('.noticias-scroll-container');

            // ¡IMPORTANTE! Verificar si el contenedor existe
            if (!noticiasScrollContainer) {
                // CAMBIO: Mensaje console.error sin caracteres no ASCII
                console.error('ERROR: .noticias-scroll-container no encontrado. Asegurate de que este elemento exista en tu HTML.');
                return; // Salir de la funcion si el contenedor no se encuentra
            }

            // Contamos cuantos elementos de noticia REALES quedan
            const remainingNoticias = noticiasScrollContainer.querySelectorAll('[id^="noticia-"]').length;
            const existingNoNewsMessage = noticiasScrollContainer.querySelector('.no-noticias-message');

            if (remainingNoticias === 0) {
                // Si no quedan noticias y el mensaje NO esta ya presente, lo insertamos
                if (!existingNoNewsMessage) {
                    noticiasScrollContainer.insertAdjacentHTML('beforeend', noNewsMessageHtml);
                }
            } else {
                // Si quedan noticias y el mensaje SI esta presente, lo eliminamos
                if (existingNoNewsMessage) {
                    existingNoNewsMessage.remove();
                }
            }
        }


        markAsReadButtons.forEach(button => {
            button.addEventListener('click', function () {
                const noticiaId = this.dataset.noticiaId;
                const noticiaElement = document.getElementById(`noticia-${noticiaId}`);

                if (noticiaElement) {
                    // Anadir clase para la animacion de desvanecimiento
                    noticiaElement.classList.add('fade-out');

                    // Enviar peticion al servidor para marcarla como leida
                    fetch(`./noticia/${noticiaId}/mark-as-read`, {
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
                                // Mensaje de error del servidor (ya es ASCII)
                                return response.json().then(err => { throw new Error(err.message || 'Error del servidor'); });
                            }
                            return response.json();
                        })
                        .then(data => {
                            // Eliminar el elemento del DOM despues de la animacion y la confirmacion del servidor
                            noticiaElement.addEventListener('transitionend', function () {
                                noticiaElement.remove();
                                updateUnreadNewsCount(-1); // Decrementar el contador
                                toggleNoNewsMessage(); // Comprobar y mostrar/ocultar el mensaje
                            }, { once: true });
                        })
                        .catch(error => {
                            // Si hay un error, revertir la animacion y mostrar un mensaje
                            noticiaElement.classList.remove('fade-out');
                            // CAMBIO: Mensaje de alerta sin caracteres no ASCII
                            alert('Hubo un error al marcar la noticia como leida. Intentalo de nuevo: ' + error.message);
                        });
                }
            });
        });

        // Ejecutar al cargar la pagina para asegurar que el mensaje se muestra/oculta correctamente si no hay noticias al inicio
        toggleNoNewsMessage();
    }
});