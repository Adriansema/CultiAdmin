document.addEventListener('DOMContentLoaded', function () {
    const markAsReadButtons = document.querySelectorAll('.mark-as-read-btn');

    markAsReadButtons.forEach(button => {
        button.addEventListener('click', function () {
            const noticiaId = this.dataset.noticiaId;
            const noticiaElement = document.getElementById(`noticia-${noticiaId}`);

            // --- Depuración para el error "siempre se va la primera" ---
            console.log('Botón clickeado para Noticia ID:', noticiaId);
            console.log('Elemento de Noticia encontrado:', noticiaElement);
            // --- Fin de depuración ---

            if (noticiaElement) {
                // Añadir clase para la animación de desvanecimiento
                noticiaElement.classList.add('fade-out');

                // Enviar petición al servidor para marcarla como leída
                fetch(`/noticia/${noticiaId}/mark-as-read`, { // Usa la ruta definida en web.php
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}', // Asegúrate de tener el token CSRF
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({}) // Cuerpo vacío para POST simple
                })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.json();
                    })
                    .then(data => {
                        console.log('Noticia marcada como leída en el servidor:', data);
                        // Eliminar el elemento del DOM después de la animación y la confirmación del servidor
                        noticiaElement.addEventListener('transitionend', function () {
                            noticiaElement.remove();
                        }, { once: true }); // Ejecutar el listener solo una vez
                    })
                    .catch(error => {
                        console.error('Error al marcar como leída:', error);
                        // Si hay un error, puedes revertir la animación o mostrar un mensaje al usuario
                        noticiaElement.classList.remove('fade-out'); // Quita la animación para que vuelva a ser visible
                        // IMPORTANTE: En producción, usa un modal personalizado en lugar de alert()
                        alert('Hubo un error al marcar la noticia como leída. Inténtalo de nuevo.');
                    });
            }
        });
    });
});