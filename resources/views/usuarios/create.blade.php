@extends('layouts.app')

@section('content')
    <div class="max-w-xl py-6 mx-auto">
        <div class="p-6 space-y-6 bg-[var(--color-Gestion)] shadow-md rounded-2xl">

            <div class="pt-6">
                <h2 class="mb-4 text-2xl font-semibold text-gray-700">Crear Nuevo Usuario</h2>
                <form action="{{ route('usuarios.store') }}" method="POST" class="space-y-6">
                    @csrf
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

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="mb-6" x-data="{ name: '' }">
                        <label for="name" class="block mb-1 text-sm font-bold text-gray-700">
                            <span class="inline-flex items-center">
                                <img src="{{ asset('images/user.svg') }}" alt="persona" class="w-4 h-4 mr-2"> Nombre:
                            </span>
                        </label>

                        <div class="relative">
                            <input id="name" type="name" name="name" placeholder="ingrese su nombre"
                                value="{{ old('name', $usuario->name ?? '') }}" required
                                class="w-full px-3 py-2 text-sm border border-gray-300 rounded-2xl focus:outline-none 
                                focus:ring-2 focus:ring-green-500 focus:border-transparent" />
                            @error('name')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-6" x-data="{ email: '' }">
                        <label for="email" class="block mb-1 text-sm font-bold text-gray-700">
                            <span class="inline-flex items-center">
                                <img src="{{ asset('images/email.svg') }}" alt="persona" class="w-4 h-4 mr-2">Correo:
                            </span>
                        </label>
                        <div class="relative">
                            <input id="email" type="email" name="email" placeholder="ingrese su correo electronico"
                                required
                                class="w-full px-3 py-2 text-sm border border-gray-300 rounded-2xl focus:outline-none 
                                focus:ring-2 focus:ring-green-500 focus:border-transparent" />
                            @error('email')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-6" x-data="{ type_document: '' }">
                        <label for="type_document" class="block mb-1 text-sm font-bold text-gray-700">
                            <span class="inline-flex items-center">
                                <img src="{{ asset('images/tipo_docs.svg') }}" alt="persona" class="w-4 h-4 mr-2">Tipo de
                                documento:
                            </span>
                        </label>

                        <div class="relative">
                            <select name="type_document" id="type_document" required
                                class="w-full px-3 py-2 text-sm border border-gray-300 rounded-2xl focus:outline-none
                                focus:ring-2 focus:ring-green-500 focus:border-transparent text-gray-500"
                                onchange="this.className = this.value ? 'w-full px-1 py-2 pl-10 pr-10 text-sm border border-gray-300 rounded-2xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent text-gray-900' : 'w-full px-1 py-2 pl-10 pr-10 text-sm border border-gray-300 rounded-2xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent text-gray-500'">
                                <option value="">Seleccione el tipo de documento</option>
                                <option value="CC">Cédula de Ciudadanía</option>
                                <option value="TI">Tarjeta de Identidad</option>
                                <option value="CE">Cédula de Extranjería</option>
                                <option value="NIT">NIT</option>
                            </select>

                            @error('type_document')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    @if (Route::currentRouteName() === 'usuarios.create')
                        <div class="mb-6"> <label for="password" class="block mb-1 text-sm font-bold text-gray-700">
                                <span class="inline-flex items-center">
                                    <img src="{{ asset('images/docs.svg') }}" alt="documento"
                                        class="w-4 h-4 mr-2">Documento:
                                </span>
                            </label>

                            <div class="relative">
                                <input id="password" type="text" name="password"
                                    placeholder="ingrese su numero de documento" required
                                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-2xl focus:outline-none focus:ring-2
                                 focus:ring-green-500 focus:border-transparent" />
                            </div>

                            @error('document')
                                <span class="text-red-500 text-xs block mt-1">{{ $message }}</span>
                            @enderror
                        </div>
                    @endif

                    <div class="flex flex-col gap-4 sm:flex-row sm:justify-between sm:items-center">
                        <a href="{{ route('usuarios.index') }}"
                            class="px-4 py-2 text-center text-white rounded bg-[var(--color-iconos)] hover:bg-[var(--color-iconos6)]">
                            Volver a la lista
                        </a>

                        <x-button class="text-center bg-green-600 hover:bg-green-700">
                            Guardar Usuario
                        </x-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
