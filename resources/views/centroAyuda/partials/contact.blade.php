<div class="container py-8">

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

    <div class="p-6 text-gray-700 bg-white rounded-lg shadow-md">
        <h1 class="mb-6 text-3xl font-bold">Contacto</h1>
        <p class="mb-4 text-lg ">Si necesitas asistencia personalizada o tienes alguna duda que no hayas podido resolver
            a través del Centro de Ayuda, no dudes en contactarnos. Nuestro equipo de soporte está aquí para ayudarte.
        </p>

        <h2 class="mb-4 text-2xl font-semibold">Formulario de Contacto</h2>

        <form action="{{ route('contact.submit') }}" method="POST">
            @csrf

            <div class="mb-4">
                <label for="name" class="block text-sm font-medium text-gray-500">Nombre</label>
                <input type="text" id="name" name="name"
                    class="block w-full px-4 py-2 mt-2 border-gray-200 rounded-lg focus:border-blue-500 focus:ring focus:ring-blue-200"
                    required>
            </div>

            <div class="mb-4">
                <label for="email" class="block text-sm font-medium text-gray-500">Correo Electrónico</label>
                <input type="email" id="email" name="email"
                    class="block w-full px-4 py-2 mt-2 border-gray-200 rounded-lg focus:border-blue-500 focus:ring focus:ring-blue-200"
                    required>
            </div>

            <div class="mb-4">
                <label for="message" class="block text-sm font-medium text-gray-500">Mensaje</label>
                <textarea id="message" name="message" rows="4"
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
            <p class="text-gray-600">O contáctanos directamente a:</p>
            <p class="text-blue-600">
                <a href="mailto:andresconde45678@gmail.com">andresconde45678@gmail.com</a>
            </p>
        </div>
    </div>
</div>
