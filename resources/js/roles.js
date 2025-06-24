/* document.addEventListener('DOMContentLoaded', function () {
    const roleLabels = document.querySelectorAll('.role-label');

    roleLabels.forEach(label => {
        label.addEventListener('click', function (e) {
            // Prevenir el comportamiento por defecto del click en la label
            // que a veces puede interactuar directamente con el input
            e.preventDefault(); 

            const checkbox = this.querySelector('.role-checkbox');
            const icon = this.querySelector('.role-icon');

            // Toggle el estado 'checked' del checkbox oculto
            checkbox.checked = !checkbox.checked;

            // Actualizar la apariencia visual (clases de Tailwind e imagen)
            if (checkbox.checked) {
                this.classList.remove('bg-gray-100', 'text-gray-700', 'hover:bg-gray-200');
                this.classList.add('bg-indigo-200', 'text-indigo-800');
                icon.src = icon.src.replace('sin_marca.svg', 'con_marca.svg');
            } else {
                this.classList.remove('bg-indigo-200', 'text-indigo-800');
                this.classList.add('bg-gray-100', 'text-gray-700', 'hover:bg-gray-200');
                icon.src = icon.src.replace('con_marca.svg', 'sin_marca.svg');
            }
        });
    });
}); */