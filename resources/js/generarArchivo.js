document.addEventListener('DOMContentLoaded', function() {
    // Referencias a los elementos del DOM del select y el botón de generación de CSV
    const csvTypeSelect = document.getElementById('csvTypeSelect');
    const generateCsvButton = document.getElementById('generateCsvButton');

    // Verifica que ambos elementos existan antes de añadir el event listener
    if (generateCsvButton && csvTypeSelect) {

        // Añade un event listener al botón para manejar el clic
        generateCsvButton.addEventListener('click', function() {
            // Obtiene el valor seleccionado del dropdown (tipo de CSV a generar)
            const selectedType = csvTypeSelect.value;
            
            // Construye la URL de descarga, incluyendo el tipo de CSV como parámetro de consulta
            const downloadUrl = `./exportar-csv?type=${selectedType}`;
            
            
            // Redirige la ventana del navegador a la URL de descarga.
            // Esto le indica al servidor que inicie el proceso de descarga del archivo.
            window.location.href = downloadUrl; 

            // Nota: window.location.href no lanza errores si la descarga falla en el servidor.
            // La depuración de la respuesta del servidor debe hacerse en la pestaña 'Network'.
        });
    }
});
