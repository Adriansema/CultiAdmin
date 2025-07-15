// Importaciones base y generales
import './bootstrap';
// import 'laravel-livewire'; // Solo si lo necesitas
import './dashboard'; // Esto ya debe incluir tu grafica de ECharts
import './modal'; // Se encarga del contenedor del contacto.
import './UserSearch'; // Buscador para los usuarios y filtros
import './ProductSearch'; // Buscador para los productos/cultivos y filtros
import './BoletinSearch'; // Buscador para los boletines y filtros
import './NoticiaSearch'; // Buscador para las noticias y filtros
import './formulario'; // Modal paso a paso para los registros de los usuarios
import './Boletin-create'; // Modal con JavaScript para subir el boletin
import './notificacion-noticias'; // Se encarga de las notificaciones de las noticias que se ven reflejadas en el panel de administracion
import './notificacion-boletin'; // Se encarga de las notificaciones de los boletines que se ven reflejadas en el panel de administracion
import './securityForm'; // Se encarga de la validacion en el formulario de iniciar sesion

// 1. ModalesGenerales: Define la funcion global showGlobalMessage.
//    Debe cargarse primero porque TypeModals y Boletin-update la usan.
import './ModalesGenerales';

// 2. TypeModals: Define las funciones globales mostrarModal y cerrarModal.
//    Debe cargarse despues de ModalesGenerales porque usa showGlobalMessage.
//    Debe cargarse antes de Boletin-update porque Boletin-update usa sus funciones.
import './TypeModals';

// 3. Boletin-update: Contiene la logica especifica de actualizacion de boletines.
//    Debe cargarse al final porque usa funciones de ModalesGenerales y TypeModals.
import './Boletin-update';

