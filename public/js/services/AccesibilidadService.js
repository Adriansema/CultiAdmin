export default class AccesibilidadService {
    //public/js/services/AccesibilidadService.js
    static toggleContrast() {
        document.body.classList.toggle('contrast-high');
        localStorage.setItem('contrastHigh', document.body.classList.contains('contrast-high'));
    }

    static toggleDarkMode() {
        document.body.classList.toggle('dark-mode');
        localStorage.setItem('darkMode', document.body.classList.contains('dark-mode'));
    }

    static increaseFont(currentFontSize) {
        currentFontSize += 2;
        AccesibilidadService.applyFontSize(currentFontSize);
        return currentFontSize;
    }

    static decreaseFont(currentFontSize) {
        if (currentFontSize > 10) {
            currentFontSize -= 2;
            AccesibilidadService.applyFontSize(currentFontSize);
        }
        return currentFontSize;
    }

    static applyFontSize(size) {
        document.documentElement.style.fontSize = size + 'px';
        localStorage.setItem('fontSize', size);
    }

    static loadPreferences() {
        if (localStorage.getItem('contrastHigh') === 'true') {
            document.body.classList.add('contrast-high');
        }
        if (localStorage.getItem('darkMode') === 'true') {
            document.body.classList.add('dark-mode');
        }
        if (localStorage.getItem('fontSize')) {
            AccesibilidadService.applyFontSize(parseFloat(localStorage.getItem('fontSize')));
        }
    }
}

