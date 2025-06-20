@extends('layouts.app')

@section('content')

    <div class="max-w-lg mx-auto px-4 py-8">
        <div class="p-8 bg-white shadow-md rounded-3xl border border-gray-300">

            <div class="pt-2">
                {{-- Encabezado y navegación por pasos (como en la imagen) --}}
                <div class="flex justify-between items-center mb-6">
                    {{-- Título basado en la imagen "Nuevo usuario" --}}
                    <h1 class="text-2xl font-bold text-gray-800">Nuevo usuario</h1>
                    <button type="button" class="text-gray-500 hover:text-gray-700" onclick="window.history.back()">
                        {{-- <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg> --}}
                        <img src="{{ asset('images/X.svg') }}" alt="retroceder" class="w-3 h-5">
                    </button>
                </div>

                {{-- Indicador de pasos --}}
                <div class="flex items-center justify-center mb-8">
                    <div class="flex items-center text-gray-700">
                        <img src="{{ asset('images/1_Dpaso.svg') }}" alt="paso 1" class="w-7 h-10 mr-2">
                        <span class="font-semibold">Datos básicos</span>
                    </div>
                    <div class="mx-4 text-gray-400">
                        <img src="{{ asset('images/medio_1_2.svg') }}" alt="flecha" class="w-2 h-3 mr-2"> {{-- Alt text for accessibility --}}
                    </div>
                    <div class="flex items-center text-gray-400 font-semibold">
                        <img src="{{ asset('images/2_Dpaso.svg') }}" alt="paso 2" class="w-7 h-10 mr-2">
                        <span>Roles y permisos</span>
                    </div>
                </div>

                {{-- El formulario principal debe envolver todos los campos y el botón de submit --}}
                <form action="{{ route('usuarios.store') }}" method="POST" class="flex flex-col h-full"> {{-- Reduje space-y para la separación entre campos --}}
                    @csrf

                    {{-- Mensaje de éxito global (si lo usas aquí) --}}
                    @if (session('success'))
                        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => { show = false; }, 2000)" x-transition
                            class="fixed inset-0 flex items-center justify-center z-50 bg-gray-900 bg-opacity-50">
                            <div class="bg-white rounded-lg shadow-xl p-6 max-w-sm text-center relative">
                                <button @click="show = false"
                                    class="absolute top-3 right-3 text-gray-500 hover:text-gray-700 text-2xl font-bold leading-none focus:outline-none">
                                    &times;
                                </button>
                                <img src="{{ asset('images/check.svg') }}" alt="Icono de éxito"
                                    class="mx-auto h-24 w-24 mb-4">
                                <h2 class="text-2xl font-bold text-green-600 mb-4">¡Éxito!</h2>
                                <p class="text-gray-700 text-base">
                                    {{ session('success') }}
                                </p>
                            </div>
                        </div>
                    @endif

                    {{-- Errores de validación generales --}}
                    @if ($errors->any())
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                            <ul class="list-disc list-inside">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    {{-- Campo Nombre --}}
                    <div class="mb-4" x-data="{ name: '{{ old('name', $usuario->name ?? '') }}' }"> {{-- Inicializar x-data con old value --}}
                        <label for="name" class="block mb-1 text-sm font-bold text-gray-700">
                            <span class="inline-flex items-center">
                                <img src="{{ asset('images/user.svg') }}" alt="persona" class="w-4 h-4 mr-2"> Nombre:
                            </span>
                        </label>
                        <div class="relative">
                            <input id="name" type="text" name="name" placeholder="ingrese su nombre"
                                x-model="name" {{-- Bind x-model to input --}}
                                required
                                class="w-full px-3 py-2 text-sm border border-gray-300 rounded-2xl focus:outline-none
                                focus:ring-2 focus:ring-green-500 focus:border-transparent
                                @error('name') border-red-500 @enderror" />
                            @error('name')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Campo Correo --}}
                    <div class="mb-4" x-data="{ email: '{{ old('email') }}' }"> {{-- x-data para el email --}}
                        <label for="email" class="block mb-1 text-sm font-bold text-gray-700">
                            <span class="inline-flex items-center">
                                <img src="{{ asset('images/email.svg') }}" alt="email" class="w-4 h-4 mr-2"> Correo:
                            </span>
                        </label>
                        <div class="relative">
                            <input id="email" type="email" name="email" placeholder="ingrese su correo electronico"
                                x-model="email" {{-- Bind x-model to input --}}
                                required
                                class="w-full px-3 py-2 text-sm border border-gray-300 rounded-2xl focus:outline-none
                                focus:ring-2 focus:ring-green-500 focus:border-transparent
                                @error('email') border-red-500 @enderror" />
                            @error('email')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Campo Tipo de Documento --}}
                    <div class="mb-4">
                        <label for="type_document" class="block mb-1 text-sm font-bold text-gray-700">
                            <span class="inline-flex items-center">
                                <img src="{{ asset('images/tipo_docs.svg') }}" alt="tipo de documento" class="w-4 h-4 mr-2"> Tipo de
                                documento:
                            </span>
                        </label>
                        <div class="relative">
                            <select name="type_document" id="type_document" required
                                class="w-full px-3 py-2 text-sm border border-gray-300 rounded-2xl focus:outline-none
                                focus:ring-2 focus:ring-green-500 focus:border-transparent
                                {{ old('type_document') ? 'text-gray-900' : 'text-gray-500' }}" {{-- Aplica color basado en old value --}}
                                onchange="this.className = this.value ? 'w-full px-3 py-2 text-sm border border-gray-300 rounded-2xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent text-gray-900' : 'w-full px-3 py-2 text-sm border border-gray-300 rounded-2xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent text-gray-500'">
                                <option value="">Seleccione el tipo de documento</option> {{-- Añadido disabled selected para placeholder --}}
                                <option value="CC" {{ old('type_document') == 'CC' ? 'selected' : '' }}>Cédula de Ciudadanía</option>
                                <option value="TI" {{ old('type_document') == 'TI' ? 'selected' : '' }}>Tarjeta de Identidad</option>
                                <option value="CE" {{ old('type_document') == 'CE' ? 'selected' : '' }}>Cédula de Extranjería</option>
                                <option value="NIT" {{ old('type_document') == 'NIT' ? 'selected' : '' }}>NIT</option>
                            </select>
                            @error('type_document')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Campo Documento (solo si es la ruta create) --}}
                    @if (Route::currentRouteName() === 'usuarios.create')
                        <div class="mb-4" x-data="{ document: '{{ old('document') }}' }"> {{-- x-data para el documento --}}
                            <label for="document" class="block mb-1 text-sm font-bold text-gray-700">
                                <span class="inline-flex items-center">
                                    <img src="{{ asset('images/docs.svg') }}" alt="documento"
                                        class="w-4 h-4 mr-2"> Documento:
                                </span>
                            </label>
                            <div class="relative">
                                <input id="document" type="text" name="document"
                                    placeholder="ingrese su numero de documento"
                                    x-model="document" {{-- Bind x-model to input --}}
                                    required
                                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-2xl focus:outline-none focus:ring-2
                                 focus:ring-green-500 focus:border-transparent
                                 @error('document') border-red-500 @enderror" />
                            </div>
                            @error('document')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    @endif

                    {{-- Contenedor de los botones de acción --}}
                    {{-- Eliminamos space-y-16 de aquí y añadimos mt-8 o mt-10 para la separación superior --}}
                    <div class="flex justify-between items-center mt-auto pt-8"> 
                        <button type="button"
                            class="flex justify-start py-2 px-4 border border-gray-200 font-medium text-gray-700 rounded-full focus:outline-none focus:shadow-outline items-center text-md
                            hover:bg-gray-50 transition duration-150 ease-in-out">
                            <img src="{{ asset('images/Importar.svg') }}" alt="importar csv" class="w-5 h-5 mr-3">
                            Importar CSV
                        </button>
                        <button type="submit"
                            class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-3 rounded-full focus:outline-none focus:shadow-outline flex items-center text-md
                            transition duration-150 ease-in-out">
                            Siguiente
                            <img src="{{ asset('images/siguiente.svg') }}" alt="siguiente" class="w-5 h-6 ml-2">
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>
@endsection

