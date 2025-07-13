@extends('layouts.app')

@section('title', 'Centro de ayuda de la aplicación') {{-- Añadido el título aquí --}}

@section('content')
    {{-- Contenedor del título y breadcrumbs: ajustado para responsividad --}}
    <div class="w-full px-4 py-6 md:px-8 lg:px-12"> {{-- Ajuste de padding para pantallas pequeñas y grandes --}}
        <div class="flex items-center space-x-4"> {{-- Aumentado space-x para consistencia --}}
            <img src="{{ asset('images/reverse.svg') }}" class="w-4 h-4" alt="Icono Nuevo Usuario">
            {{-- Título responsivo: cambia de tamaño según la pantalla --}}
            <h1 class="text-xl font-bold sm:text-2xl lg:text-3xl whitespace-nowrap">Centro de ayuda de la aplicación</h1>
        </div>
        {{-- Breadcrumbs: texto más pequeño en móvil --}}
        <div class="py-2 text-sm text-gray-600">
            {!! Breadcrumbs::render('centroAyuda.index') !!}
        </div>
    </div>

    {{-- Contenedor principal del contenido, centrado y con fondo/sombra --}}
    <div class="w-full p-4 mx-auto mb-8 bg-white shadow-sm max-w-screen-2xl rounded-2xl"> {{-- Fondo blanco, redondeado, padding y sombra --}}

        {{-- Contenido de pestanas --}}
        <div class="pt-6 space-y-6 tab-content" id="helpTabsContent">
            <div class="tab-pane fade show active" id="faq" role="tabpanel" aria-labelledby="faq-tab">
                {{-- Los partials internos (faq, guide, links) también deberán ser responsivos si contienen tablas o elementos que lo requieran --}}
                @include('centroAyuda.partials.faq')
            </div>
            <div class="tab-pane fade" id="guide" role="tabpanel" aria-labelledby="guide-tab">
                @include('centroAyuda.partials.guide')
            </div>
            <div class="flex justify-center tab-pane fade" id="links" role="tabpanel" aria-labelledby="links-tab">
                @include('centroAyuda.partials.links')
            </div>
        </div>

        {{-- Asegúrate de que este modal sea gestionado por JS para mostrar/ocultar --}}
        <div id="contactModal" class="fixed inset-0 hidden bg-black z-[80] bg-opacity-60 overflow-y-auto p-4 flex items-center justify-center"> {{-- Añadido p-4, flexbox para centrar contenido, overflow-y-auto para scroll si el contenido es largo --}}
            <div class="w-full h-full max-w-2xl mx-auto my-auto bg-white rounded-lg shadow-lg"> {{-- max-w-2xl para el ancho del modal --}}
                @include('centroAyuda.partials.contact')
            </div>
        </div>

        {{-- Asegúrate de que este modal sea gestionado por JS para mostrar/ocultar --}}
        <div id="successModal" class="fixed inset-0 z-30 flex items-center justify-center hidden p-4 bg-black bg-opacity-50"> {{-- Añadido flexbox para centrar contenido, p-4 --}}
            <div class="relative w-full max-w-sm px-6 py-4 mx-auto my-auto text-center bg-white rounded-lg shadow-lg"> {{-- w-full para móvil, max-w-sm para escritorio --}}
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
