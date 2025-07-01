document.addEventListener('DOMContentLoaded', function() {
    // Referencias a los elementos del DOM del select y el botón de generación de CSV
    const csvTypeSelect = document.getElementById('csvTypeSelect');
    const generateCsvButton = document.getElementById('generateCsvButton');

    // Verifica que ambos elementos existan antes de añadir el event listener
    if (generateCsvButton && csvTypeSelect) {
        // Log para confirmar que los elementos fueron encontrados y el listener se adjuntará
        console.log("generarArchivo.js: Elementos 'csvTypeSelect' y 'generateCsvButton' encontrados. Inicializando listener."); 

        // Añade un event listener al botón para manejar el clic
        generateCsvButton.addEventListener('click', function() {
            // Obtiene el valor seleccionado del dropdown (tipo de CSV a generar)
            const selectedType = csvTypeSelect.value;
            
            // Construye la URL de descarga, incluyendo el tipo de CSV como parámetro de consulta
            const downloadUrl = `/exportar-csv?type=${selectedType}`;
            
            // Log para confirmar la URL que se va a usar para la descarga
            console.log(`generarArchivo.js: Intentando descargar CSV de tipo: '${selectedType}' desde la URL: '${downloadUrl}'`);
            
            // Redirige la ventana del navegador a la URL de descarga.
            // Esto le indica al servidor que inicie el proceso de descarga del archivo.
            window.location.href = downloadUrl; 

            // Nota: window.location.href no lanza errores si la descarga falla en el servidor.
            // La depuración de la respuesta del servidor debe hacerse en la pestaña 'Network'.
        });
    } else {
        // Mensaje de error si los elementos no se encuentran, útil para depuración.
        console.error("generarArchivo.js: Elementos 'csvTypeSelect' o 'generateCsvButton' no encontrados. La funcionalidad de descarga de CSV no se inicializará.");
    }
});
