<div class="container flex flex-col py-8">

    <!-- Verifica si hay un mensaje de éxito -->
    @if (session('success'))
    <div class="flex items-center p-4 mb-4 text-green-700 bg-green-100 border-l-4 border-green-500">
        <!-- Icono de éxito en SVG -->
        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-2" fill="none" stroke="currentColor"
            viewBox="0 0 24 24" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2l4-4m0 0l6-6M5 13l2 2l4-4m0 0l6-6"></path>
        </svg>
        <p class="font-semibold">{{ session('success') }}</p>
    </div>
    @endif

    <div id="closeModal" class="relative w-full max-w-md p-6 transition duration-300 bg-white rounded-3xl group shadow-sombra hover:bg-black">
        <!-- Botón de cierre "X" -->
        <button id="closeModalBtn" class="absolute text-black transition duration-300 top-4 right-4 group-hover:text-white">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>

        <!-- Título -->
        <h1 class="mb-6 text-3xl font-bold text-black transition duration-300 group-hover:text-white">Contacto</h1>

        <!-- Descripción -->
        <p class="mb-4 text-lg text-black transition duration-300 group-hover:text-white">
            Si necesitas asistencia personalizada o tienes alguna duda que no hayas podido resolver
            a través del Centro de Ayuda, no dudes en contactarnos. Nuestro equipo de soporte está aquí para ayudarte.
        </p>

        <form id="contactForm" action="{{ route('centroAyuda.contact.submit') }}" method="POST" class="w-full h-full space-y-3">
            <!-- CSRF Token -->
            @csrf

            <div class="mb-4">
                <input type="text" id="name" name="name" placeholder="nombre"
                    class="block w-full px-4 py-2 mt-2 border-gray-200 rounded-lg focus:border-blue-500 focus:ring focus:ring-blue-200"
                    required>
            </div>

            <div class="mb-4">
                <input type="email" id="email" name="email" placeholder="Correo Electrónico"
                    class="block w-full px-4 py-2 mt-2 border-gray-200 rounded-lg focus:border-blue-500 focus:ring focus:ring-blue-200"
                    required>
            </div>

            <div class="mb-4">
                <textarea id="message" name="message" rows="4" placeholder="Mensaje"
                    class="block w-full px-4 py-2 mt-2 border-gray-200 rounded-lg focus:border-blue-500 focus:ring focus:ring-blue-200"
                    required></textarea>
            </div>

            <div class="mb-4">
                <button type="submit"
                    class="w-full py-3 font-semibold text-white transition duration-300 bg-blue-600 rounded-lg hover:bg-blue-700">
                    Enviar Mensaje
                </button>
            </div>
        </form>

        <div class="mt-6 text-center">
            <p class="mb-4 text-lg text-black transition duration-300 group-hover:text-white">O contáctanos directamente a:</p>
            <p class="text-blue-600 transition duration-300 ease-in-out hover:text-green-400 hover:underline hover:font-semibold">
                <a href="mailto:andresconde45678@gmail.com">soporteayuda2025@gmail.com</a>
            </p>
        </div>
    </div>
</div>
