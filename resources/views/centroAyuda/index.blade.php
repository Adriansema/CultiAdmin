@extends('layouts.app')

@section('content')
<div class="container py-8 mx-auto">
    <h1 class="mb-8 text-4xl font-extrabold text-gray-800">Centro de Ayuda</h1>

    @if(session('success'))
    <div class="flex items-center p-4 mb-6 text-green-800 bg-green-100 border-l-4 border-green-500 rounded-lg shadow">
        <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2l4-4" />
        </svg>
        <span class="font-semibold">{{ session('success') }}</span>
    </div>
    @endif

    <!-- Barra de búsqueda -->
    <div class="flex items-center w-full max-w-2xl px-4 py-2 mx-auto mb-8 bg-gray-100 rounded-full shadow-inner">
        <input id="search" type="text" placeholder="Busca aquí tus dudas..." oninput="filterQuestions()"
            class="w-full px-4 text-gray-700 placeholder-gray-400 bg-gray-100 rounded-full focus:outline-none focus:ring-2 focus:ring-blue-400"
            aria-label="Buscar preguntas frecuentes">
        <svg onclick="filterQuestions()" class="w-6 h-6 ml-2 text-gray-400 cursor-pointer" fill="none"
            stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round"
                d="M21 21l-4.35-4.35M16.65 16.65A7.5 7.5 0 1 0 5.64 5.64a7.5 7.5 0 0 0 10.61 10.61z" />
        </svg>
    </div>



    <!-- Pestañas -->
    <ul class="flex flex-wrap justify-center mb-8 text-sm font-medium text-gray-600 border-b border-gray-200"
        id="helpTabs" role="tablist">
        <li class="mr-2" role="presentation">
            <button id="faq-tab" data-bs-toggle="tab" data-bs-target="#faq" type="button" role="tab" aria-controls="faq"
                aria-selected="true"
                class="inline-block px-5 py-2 rounded-t-lg hover:text-blue-600 hover:bg-blue-50 focus:outline-none focus:ring-2 focus:ring-blue-300">
                Preguntas Frecuentes
            </button>
        </li>
        <li class="mr-2" role="presentation">
            <button id="guide-tab" data-bs-toggle="tab" data-bs-target="#guide" type="button" role="tab"
                aria-controls="guide" aria-selected="false"
                class="inline-block px-5 py-2 rounded-t-lg hover:text-blue-600 hover:bg-blue-50 focus:outline-none focus:ring-2 focus:ring-blue-300">
                Guía de Uso
            </button>
        </li>
    </ul>

    <!-- Contenido de pestañas -->
    <div class="pt-6 space-y-6 tab-content" id="helpTabsContent">
        <div class="tab-pane fade show active" id="faq" role="tabpanel" aria-labelledby="faq-tab">
            @include('centroAyuda.partials.faq')
        </div>
        <div class="tab-pane fade" id="guide" role="tabpanel" aria-labelledby="guide-tab">
            @include('centroAyuda.partials.guide')
        </div>
    </div>
</div>

<!-- Sección de footer -->
<div class="w-full">
    @include('centroAyuda.partials.footer')
</div>

@endsection

@section('scripts')
<script>
    // Debounce mejorado
let debounceTimeout;
function debounce(callback, delay) {
    clearTimeout(debounceTimeout);
    debounceTimeout = setTimeout(callback, delay);
}

function filterQuestions() {
    debounce(async () => {
        const term = encodeURIComponent(document.getElementById('search').value.trim());
        const list = document.getElementById('faq-list');
        if (!list) return;

        if (term === '') {
            list.innerHTML = '<p class="text-center text-gray-400">Empieza a escribir para buscar preguntas.</p>';
            return;
        }

        try {
            const response = await fetch(`/search-faq?query=${term}`);
            const faqs = await response.json();

            list.innerHTML = '';

            if (faqs.length === 0) {
                list.innerHTML = '<p class="text-center text-gray-500">No se encontraron resultados.</p>';
                return;
            }

            faqs.forEach(faq => {
                const div = document.createElement('div');
                div.className = 'p-4 bg-white rounded-lg shadow';
                div.innerHTML = `
                    <h3 class="mb-2 text-lg font-semibold text-gray-800">${faq.question}</h3>
                    <p class="text-gray-600">${faq.answer}</p>
                `;
                list.appendChild(div);
            });
        } catch (error) {
            console.error('Error al filtrar preguntas:', error);
            list.innerHTML = '<p class="text-center text-red-500">Error al buscar preguntas.</p>';
        }
    }, 300);

    document.getElementById('search').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        filterQuestions();
    }
});

}


    // Recordar pestaña activa
    document.addEventListener('DOMContentLoaded', () => {
        const tabs = document.querySelectorAll('button[data-bs-toggle="tab"]');
        tabs.forEach(tab => {
            tab.addEventListener('shown.bs.tab', e => {
                localStorage.setItem('activeHelpTab', e.target.getAttribute('data-bs-target'));
            });
        });

        const activeTab = localStorage.getItem('activeHelpTab');
        if (activeTab) {
            const trigger = document.querySelector(`button[data-bs-target="${activeTab}"]`);
            if (trigger) {
                new bootstrap.Tab(trigger).show();
            }
        }
    });
</script>
@endsection
