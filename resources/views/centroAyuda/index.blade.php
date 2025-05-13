@extends('layouts.app')

@section('content')
<div class="container py-8 mx-auto">
    <h1 class="mb-8 text-4xl font-extrabold text-gray-800">Centro de Ayuda</h1>

    {{-- @if(session('success'))
    <div class="flex items-center p-4 mb-6 text-green-800 bg-green-100 border-l-4 border-green-500 rounded-lg shadow">
        <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2l4-4" />
        </svg>
        <span class="font-semibold">{{ session('success') }}</span>
    </div>
    @endif --}}

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
        {{-- <div class="flex justify-center tab-pane fade" id="contact" role="tabpanel" aria-labelledby="contact-tab">
            @include('centroAyuda.partials.contact')
        </div> --}}
        <div class="flex justify-center tab-pane fade" id="links" role="tabpanel" aria-labelledby="links-tab">
            @include('centroAyuda.partials.links')
        </div>
    </div>

    <!-- Botón para abrir el modal -->
    <button id="openModalBtn" class="px-6 py-2 text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:outline-none">
        Contáctenos
    </button>

    <!-- Modal (Oculto por defecto) -->
    <div id="contactModal" class="fixed inset-0 hidden bg-black z-[80] bg-opacity-60">
        <div class="flex items-center justify-between">
            <div class="w-full h-full mt-4">
                @include('centroAyuda.partials.contact')
            </div>
        </div>
    </div>

    <!-- Modal de éxito -->
    <div id="successModal" class="fixed inset-0 z-30 hidden bg-black bg-opacity-50">
        <div class="relative px-6 py-4 mx-auto my-40 text-center bg-white rounded-lg shadow-lg w-80">
            <button id="closeSuccessBtn" class="absolute text-gray-500 top-3 right-3 hover:text-gray-800">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
            <h2 class="mb-2 text-lg font-semibold text-green-700">¡Se ha enviado correctamente!</h2>
            <p class="text-sm text-gray-600">Gracias por contactarnos.</p>
        </div>
    </div>

</div>
@endsection

@section('scripts')
<script>
    function filterQuestions() {
        const input = document.getElementById('search').value.toLowerCase();
        const activeTab = document.querySelector('[role="tab"][aria-selected="true"]');
        const activeTargetId = activeTab?.getAttribute('data-bs-target')?.replace('#', '');
        const activePanel = document.getElementById(activeTargetId);

        if (!activePanel) return;

        const questions = activePanel.querySelectorAll('.question-item');

        questions.forEach(question => {
            const textContent = question.textContent.toLowerCase();
            const dataAttr = question.getAttribute('data-question')?.toLowerCase() || '';

            if (textContent.includes(input) || dataAttr.includes(input)) {
                question.style.display = '';
            } else {
                question.style.display = 'none';
            }
        });
    }
</script>

@endsection
