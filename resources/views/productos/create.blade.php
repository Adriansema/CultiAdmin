@extends('layouts.app')

@section('title', 'Crear Producto')

@section('content')

    <div class="inline-block px-8 py-10">
        <div class="flex items-center space-x-2">
            <img src="{{ asset('images/reverse.svg') }}" class="w-4 h-4" alt="Icono Nuevo Usuario">
            <h1 class="text-3xl whitespace-nowrap font-bold">Crear Usuario</h1>
        </div>
        {!! Breadcrumbs::render('productos.create') !!}
    </div>

    <div class="container max-w-2xl py-2 mx-auto">

        <div class="flex justify-between w-40 space-x-8">
            <!-- Botón Importar CSV -->
            <form action="{{ route('productos.importar.csv') }}" method="POST" enctype="multipart/form-data"
                class="flex items-center justify-end mb-4 space-x-2 ">
                @csrf
                <input type="file" name="archivo_csv" accept=".csv" required
                    class="text-sm text-white bg-slate-800 border border-cyan-600 rounded px-3 py-1.5 shadow-sm">
                <button type="submit"
                    class="bg-cyan-600 hover:bg-cyan-700 text-white font-semibold px-4 py-2 rounded-lg transition">
                    Importar CSV
                </button>
            </form>

            <!-- boton de generar CSV-->
            <form action="{{ route('productos.generarCSV') }}" method="GET" style="float: right;">
                <select name="tipo"
                    class="w-40 bg-gray-600 bg-opacity-60 border border-cyan-500 text-cyan-100 text-sm rounded-xl shadow-xl focus:ring-2 focus:ring-cyan-400 focus:border-transparent transition-all duration-300 px-4 py-3 pr-10 appearance-none hover:cursor-pointer"
                    required>
                    <option value="" class="bg-slate-900 text-slate-400">Selecciona tipo</option>
                    <option value="café">Café</option>
                    <option value="mora">Mora</option>
                </select>
                <button type="submit"
                    class="bg-cyan-600 hover:bg-cyan-700 text-white font-semibold px-4 py-2 rounded-lg transition">
                    Generar CSV
                </button>
            </form>
        </div>

        <!-- Alpine.js debe estar disponible (Jetstream ya lo incluye) -->
        <form action="{{ route('productos.store') }}" method="POST" enctype="multipart/form-data" x-data="{ tab: 'historia' }"
            class="bg-red-200 p-8 rounded-2xl shadow-xl space-y-2">
            @csrf

            <!-- Imagen -->
            <div>
                <x-label for="imagen" :value="'Imagen del producto (opcional)'" />
                <x-input id="imagen" type="file" name="imagen" class="w-full mt-1" />
                @error('imagen')
                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Tipo -->
            <div>
                <x-label for="tipo" :value="'Tipo de producto *'" />
                <select name="tipo" id="tipo" required
                    class="w-full mt-1 border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">-- Selecciona un tipo --</option>
                    <option value="café" {{ old('tipo') == 'café' ? 'selected' : '' }}>Café</option>
                    <option value="mora" {{ old('tipo') == 'mora' ? 'selected' : '' }}>Mora</option>
                </select>
                @error('tipo')
                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Tabs -->
            <div class="border-b mb-4">
                <nav class="flex space-x-4">
                    <button type="button" class="px-4 py-2 font-semibold border-b-2"
                        :class="tab === 'historia' ? 'border-indigo-600 text-indigo-600' : 'border-transparent'"
                        @click="tab = 'historia'">Historia</button>

                    <button type="button" class="px-4 py-2 font-semibold border-b-2"
                        :class="tab === 'productos' ? 'border-indigo-600 text-indigo-600' : 'border-transparent'"
                        @click="tab = 'productos'">Productos</button>

                    <button type="button" class="px-4 py-2 font-semibold border-b-2"
                        :class="tab === 'variantes' ? 'border-indigo-600 text-indigo-600' : 'border-transparent'"
                        @click="tab = 'variantes'">Variantes</button>

                    <button type="button" class="px-4 py-2 font-semibold border-b-2"
                        :class="tab === 'enfermedades' ? 'border-indigo-600 text-indigo-600' : 'border-transparent'"
                        @click="tab = 'enfermedades'">Enfermedades</button>

                    <button type="button" class="px-4 py-2 font-semibold border-b-2"
                        :class="tab === 'agroinsumos' ? 'border-indigo-600 text-indigo-600' : 'border-transparent'"
                        @click="tab = 'agroinsumos'">Agroinsumos</button>
                </nav>
            </div>

            <!-- Tab: historia -->
            <div x-show="tab === 'historia'" class="space-y-4">
                <label for="historia" class="block text-sm font-medium text-gray-700">
                    Historia del productos <span class="text-red-600">*</span>
                </label>
                <textarea name="detalles[historia]" id="historia" required class="w-full rounded">{{ old('detalles.historia') }}</textarea>
            </div>

            <!-- Tab: productos -->
            <div x-show="tab === 'productos'" x-cloak class="space-y-4">
                @foreach ([
            'productos y sus características' => 'Producto y sus Características',
        ] as $key => $label)
                    <div>
                        <label for="{{ $key }}" class="block text-sm font-medium text-gray-700">
                            {{ $label }} <span class="text-red-600">*</span>
                        </label>
                        <textarea name="detalles[{{ $key }}]" id="{{ $key }}" required class="w-full rounded">{{ old("detalles.$key") }}</textarea>
                    </div>
                @endforeach
            </div>

            <!-- Tab: Variantes -->
            <div x-show="tab === 'variantes'" x-cloak class="space-y-4">
                @foreach ([
            'variantes' => 'Variantes',
        ] as $key => $label)
                    <div>
                        <label for="{{ $key }}" class="block text-sm font-medium text-gray-700">
                            {{ $label }} <span class="text-red-600">*</span>
                        </label>
                        <textarea name="detalles[{{ $key }}]" id="{{ $key }}" required class="w-full rounded">{{ old("detalles.$key") }}</textarea>
                    </div>
                @endforeach
            </div>

            <!-- Tab: Enfermedades -->
            <div x-show="tab === 'enfermedades'" x-cloak class="space-y-4">
                @foreach ([
            'enfermedades' => 'Enfermedades',
        ] as $key => $label)
                    <div>
                        <label for="{{ $key }}" class="block text-sm font-medium text-gray-700">
                            {{ $label }} <span class="text-red-600">*</span>
                        </label>
                        <textarea name="detalles[{{ $key }}]" id="{{ $key }}" required class="w-full rounded">{{ old("detalles.$key") }}</textarea>
                    </div>
                @endforeach
            </div>

            <!-- Tab: agroinsumos -->
            <div x-show="tab === 'agroinsumos'" x-cloak class="space-y-4">
                <div>
                    <label for="insumos" class="block text-sm font-medium text-gray-700">
                        Insumos <span class="text-red-600">*</span>
                    </label>
                    <textarea name="detalles[insumos]" id="insumos" required class="w-full rounded">{{ old('detalles.insumos') }}</textarea>
                </div>
            </div>

            <!-- Botones -->
            <div class="flex justify-between pt-6">
                <a href="{{ route('productos.index') }}"
                    class="px-4 py-2 text-sm font-medium bg-black text-white rounded hover:bg-gray-800 transition">
                    ← Volver
                </a>
                <x-button>
                    Guardar producto
                </x-button>
            </div>
        </form>
    </div>
@endsection
