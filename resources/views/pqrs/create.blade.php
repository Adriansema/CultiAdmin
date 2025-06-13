@extends('layouts.guest')

@section('content')

    <div class="bg-white p-8 rounded-2xl shadow-xl w-full max-w-xl">

        {{-- Modal de Éxito --}}
        @if (session('success'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => { show = false; }, 2000)" {{-- Se cierra automáticamente después de 2 segundos --}} x-transition
                class="fixed inset-0 flex items-center justify-center z-50 bg-gray-900 bg-opacity-50">
                <div class="bg-white rounded-lg shadow-xl p-6 max-w-sm text-center relative">
                    {{-- Botón de cerrar (la "X") --}}
                    <button @click="show = false"
                        class="absolute top-3 right-3 text-gray-500 hover:text-gray-700 text-2xl font-bold leading-none focus:outline-none">
                        &times;
                    </button>

                    {{-- Icono de Éxito --}}
                    <img src="{{ asset('images/check.svg') }}" alt="Icono de éxito" class="mx-auto h-24 w-24 mb-4">
                    <h2 class="text-2xl font-bold text-green-600 mb-4">¡Éxito!</h2>
                    <p class="text-gray-700 text-base">
                        {{ session('success') }}
                    </p>
                </div>
            </div>
        @endif

        <h1 class="text-3xl font-bold text-gray-800 text-center mb-6">Envía tu PQRS</h1>
        <p class="text-gray-600 text-center mb-8">
            Por favor, completa este formulario para enviar tu pregunta, queja, reclamo o sugerencia.
        </p>

        {{-- Mensajes de error de validación --}}
        @if ($errors->any())
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-md" role="alert">
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
                <label for="tipo" class="block text-gray-700 text-sm font-bold mb-2">
                    Tipo de Solicitud:
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
                    <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="nombre" class="block text-gray-700 text-sm font-bold mb-2">
                    Tu Nombre (Opcional):
                </label>
                <input type="text" name="nombre" id="nombre" value="{{ old('nombre') }}"
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline
                                 @error('nombre') border-red-500 @enderror"
                    placeholder="Ej: Juan Pérez">
                @error('nombre')
                    <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="email" class="block text-gray-700 text-sm font-bold mb-2">
                    Tu Correo Electrónico: <span class="text-red-500">*</span>
                </label>
                <input type="email" name="email" id="email" value="{{ old('email') }}" required
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline
                                 @error('email') border-red-500 @enderror"
                    placeholder="Ej: tu_correo@ejemplo.com">
                @error('email')
                    <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="telefono" class="block text-gray-700 text-sm font-bold mb-2">
                    Tu Teléfono (Opcional):
                </label>
                <input type="text" name="telefono" id="telefono" value="{{ old('telefono') }}"
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline
                                 @error('telefono') border-red-500 @enderror"
                    placeholder="Ej: 3001234567">
                @error('telefono')
                    <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="asunto" class="block text-gray-700 text-sm font-bold mb-2">
                    Asunto: <span class="text-red-500">*</span>
                </label>
                <input type="text" name="asunto" id="asunto" value="{{ old('asunto') }}" required
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline
                                 @error('asunto') border-red-500 @enderror"
                    placeholder="Breve descripción del problema">
                @error('asunto')
                    <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label for="mensaje" class="block text-gray-700 text-sm font-bold mb-2">
                    Mensaje: <span class="text-red-500">*</span>
                </label>
                <textarea name="mensaje" id="mensaje" rows="6" required
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline
                                 @error('mensaje') border-red-500 @enderror"
                    placeholder="Describe tu pregunta, queja o reclamo en detalle.">{{ old('mensaje') }}</textarea>
                @error('mensaje')
                    <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center justify-between">
                {{-- Botón para volver al inicio de sesión (primero para que aparezca a la izquierda) --}}
                <a href="{{ route('login') }}"
                    class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded text-center
                             focus:outline-none focus:shadow-outline">
                    Volver al Inicio de Sesión
                </a>

                {{-- Botón Enviar PQR (segundo para que aparezca a la derecha) --}}
                <button type="submit"
                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded
                           focus:outline-none focus:shadow-outline">
                    Enviar PQR
                </button>
            </div>
        </form>
    </div>

@endsection 
