import './bootstrap';
// import 'laravel-livewire'; // solo si lo necesitas
import './dashboard'; //  Esto ya debe incluir tu gráfica de ECharts
// Tu nuevo archivo con la gráfica ECharts
import './modal';
import './Inputsearch'; // Primero, el que maneja la carga y el DOM

//importando Flatpickr
import flatpickr from "flatpickr";
import "flatpickr/dist/flatpickr.min.css"; // Importa también los estilos CSS de flatpickr
// --- Importar y configurar el idioma español para Flatpickr ---
import { Spanish } from "flatpickr/dist/l10n/es.js"; // Importa el locale español
flatpickr.localize(Spanish); // Establece el idioma español como global por defecto para todas las instancias
