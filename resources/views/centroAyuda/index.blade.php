@extends('layouts.app')


@section('content')
<div class="container py-8">
    <h1 class="mb-6 text-3xl font-bold">Ayuda de la Aplicación</h1>

    <!-- Verifica si hay un mensaje de éxito -->
    @if (session('success'))
        <div class="flex items-center p-4 mb-4 text-green-700 bg-green-100 border-l-4 border-green-500">
            <!-- Icono de éxito en SVG -->
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2l4-4m0 0l6-6M5 13l2 2l4-4m0 0l6-6"></path>
            </svg>
            <p class="font-semibold">{{ session('success') }}</p>
        </div>
    @endif

    <div class="mb-4">
        <input type="text" id="search" class="w-full p-3 border border-gray-300 rounded-lg"
            placeholder="Busca aquí tus dudas..." oninput="filterQuestions()" />
    </div>
</div>

<!-- Contenido de las pestañas -->
<div class="tab-content" id="helpTabsContent">

    <!-- FAQs -->
    <div class="tab-pane fade show active" id="faq" role="tabpanel">
        <div id="faqContent">
            @include('centroAyuda.partials.faq')
        </div>
    </div>

    <!-- Guía de Usuario -->
    <div class="tab-pane fade" id="guide" role="tabpanel">
        <div id="guideContent">
            @include('centroAyuda.partials.guide')
        </div>
    </div>

    <!-- Contacto -->
    <div class="tab-pane fade" id="contact" role="tabpanel">
        <div id="contactContent">
            @include('centroAyuda.partials.contact')
        </div>
    </div>

    <div class="tab-pane fade" id="contact" role="tabpanel">
        <div id="linksContent">
            @include('centroAyuda.partials.links')
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Función para filtrar las preguntas mediante AJAX
    let debounceTimeout;
function debounce(func, delay) {
    clearTimeout(debounceTimeout);
    debounceTimeout = setTimeout(func, delay);
}

function filterQuestions() {
    debounce(() => {
        const searchTerm = document.getElementById('search').value;

        fetch(`/search-faq?query=${searchTerm}`)
            .then(response => response.json())
            .then(data => {
                const faqList = document.getElementById('faq-list');
                faqList.innerHTML = '';

                data.forEach(faq => {
                    const faqItem = document.createElement('div');
                    faqItem.classList.add('faq-item', 'transition-all', 'opacity-0', 'duration-500');

                    faqItem.innerHTML = `
                        <div class="p-4 bg-gray-100 rounded-lg">
                            <h3 class="text-xl font-semibold">${faq.question}</h3>
                            <p class="text-gray-600">${faq.answer}</p>
                        </div>
                    `;

                    faqList.appendChild(faqItem);

                    setTimeout(() => {
                        faqItem.classList.add('opacity-100');
                    }, 100);
                });
            });
    }, 300);  // 300ms de retraso
}

</script>
@endpush
@endsection
