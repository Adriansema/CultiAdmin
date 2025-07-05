@extends('layouts.guest')

@section('content')

    <div class="w-full max-w-xl p-8 bg-white shadow-xl rounded-2xl">

        {{-- Modal de Éxito --}}
        @if (session('success'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => { show = false; }, 2000)" {{-- Se cierra automáticamente después de 2 segundos --}} x-transition
                class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900 bg-opacity-50">
                <div class="relative max-w-sm p-6 text-center bg-white rounded-lg shadow-xl">
                    {{-- Botón de cerrar (la "X") --}}
                    <button @click="show = false"
                        class="absolute text-2xl font-bold leading-none text-gray-500 top-3 right-3 hover:text-gray-700 focus:outline-none">
                        &times;
                    </button>

                    {{-- Icono de Éxito --}}
                    <img src="{{ asset('images/check.svg') }}" alt="Icono de éxito" class="w-24 h-24 mx-auto mb-4">
                    <h2 class="mb-4 text-2xl font-bold text-green-600">¡Éxito!</h2>
                    <p class="text-base text-gray-700">
                        {{ session('success') }}
                    </p>
                </div>
            </div>
        @endif

        <h1 class="mb-6 text-3xl font-bold text-center text-gray-800">Envía tu PQRS</h1>
        <p class="mb-8 text-center text-gray-600">
            Por favor, completa este formulario para enviar tu pregunta, queja, reclamo o sugerencia.
        </p>

        {{-- Mensajes de error de validación --}}
        @if ($errors->any())
            <div class="p-4 mb-6 text-red-700 bg-red-100 border-l-4 border-red-500 rounded-md" role="alert">
                <p class="font-bold">¡Ups! Hubo algunos problemas con tu envío:</p>
                <ul class="mt-2 list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('pqrs.store') }}" method="POST">
            @csrf {{-- Protección CSRF --}}

            <div class="mb-4">
                <label for="tipo" class="block mb-2 text-sm font-bold text-gray-700">
                    Tipo de solicitud:
                </label>
                <select name="tipo" id="tipo" required
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline
                               @error('tipo') border-red-500 @enderror">
                    <option value="">Selecciona un tipo</option>
                    <option value="pregunta" {{ old('tipo') == 'pregunta' ? 'selected' : '' }}>Pregunta</option>
                    <option value="queja" {{ old('tipo') == 'queja' ? 'selected' : '' }}>Queja</option>
                    <option value="reclamo" {{ old('tipo') == 'reclamo' ? 'selected' : '' }}>Reclamo</option>
                    <option value="sugerencia" {{ old('tipo') == 'sugerencia' ? 'selected' : '' }}>Sugerencia</option>
                </select>
                @error('tipo')
                    <p class="mt-2 text-xs italic text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="nombre" class="block mb-2 text-sm font-bold text-gray-700">
                    Tu nombre (Opcional):
                </label>
                <input type="text" name="nombre" id="nombre" value="{{ old('nombre') }}"
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline
                                 @error('nombre') border-red-500 @enderror"
                    placeholder="Ej: Juan Pérez">
                @error('nombre')
                    <p class="mt-2 text-xs italic text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="email" class="block mb-2 text-sm font-bold text-gray-700">
                    Tu Correo Electrónico: <span class="text-red-500">*</span>
                </label>
                <input type="email" name="email" id="email" value="{{ old('email') }}" required
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline
                                 @error('email') border-red-500 @enderror"
                    placeholder="Ej: tu_correo@ejemplo.com">
                @error('email')
                    <p class="mt-2 text-xs italic text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="telefono" class="block mb-2 text-sm font-bold text-gray-700">
                    Tu Teléfono (Opcional):
                </label>
                <input type="text" name="telefono" id="telefono" value="{{ old('telefono') }}"
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline
                                 @error('telefono') border-red-500 @enderror"
                    placeholder="Ej: 3001234567">
                @error('telefono')
                    <p class="mt-2 text-xs italic text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="asunto" class="block mb-2 text-sm font-bold text-gray-700">
                    Asunto: <span class="text-red-500">*</span>
                </label>
                <input type="text" name="asunto" id="asunto" value="{{ old('asunto') }}" required
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline
                                 @error('asunto') border-red-500 @enderror"
                    placeholder="Breve descripción del problema">
                @error('asunto')
                    <p class="mt-2 text-xs italic text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label for="mensaje" class="block mb-2 text-sm font-bold text-gray-700">
                    Mensaje: <span class="text-red-500">*</span>
                </label>
                <textarea name="mensaje" id="mensaje" rows="6" required
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline
                                 @error('mensaje') border-red-500 @enderror"
                    placeholder="Describe tu pregunta, queja o reclamo en detalle.">{{ old('mensaje') }}</textarea>
                @error('mensaje')
                    <p class="mt-2 text-xs italic text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center justify-between">
                {{-- Botón para volver al inicio de sesión (primero para que aparezca a la izquierda) --}}
                <a href="{{ route('login') }}"
                    class="px-4 py-2 font-bold text-center text-gray-800 bg-gray-300 rounded hover:bg-gray-400 focus:outline-none focus:shadow-outline">
                    Volver al inicio de sesión
                </a>

                {{-- Botón Enviar PQR (segundo para que aparezca a la derecha) --}}
                <button type="submit"
                    class="px-4 py-2 font-bold text-white bg-blue-500 rounded hover:bg-blue-700 focus:outline-none focus:shadow-outline">
                    Enviar PQR
                </button>
            </div>
        </form>
    </div>

@endsection
