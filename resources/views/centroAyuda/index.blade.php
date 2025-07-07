@extends('layouts.app')

@section('content')
    <div class="inline-block px-8 py-10">
        <div class="flex items-center space-x-2">
            <img src="{{ asset('images/reverse.svg') }}" class="w-4 h-4" alt="Icono Nuevo Usuario">
            <h1 class="text-3xl whitespace-nowrap font-bold">Centro de ayuda de la aplicación</h1>
        </div>
        {!! Breadcrumbs::render('centroAyuda.index') !!}
    </div>

    <div class="container py-8 mx-auto">
        <!-- Contenido de pestañas -->
        <div class="pt-6 space-y-6 tab-content" id="helpTabsContent">
            <div class="tab-pane fade show active" id="faq" role="tabpanel" aria-labelledby="faq-tab">
                @include('centroAyuda.partials.faq')
            </div>
            <div class="tab-pane fade" id="guide" role="tabpanel" aria-labelledby="guide-tab">
                @include('centroAyuda.partials.guide')
            </div>
            <div class="flex justify-center tab-pane fade" id="links" role="tabpanel" aria-labelledby="links-tab">
                @include('centroAyuda.partials.links')
            </div>
        </div>
        
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
