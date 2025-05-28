@extends('layouts.app')

@section('title', 'Crear Producto')

@section('content')

    <div class="inline-block px-8 py-10">
        <div class="flex items-center space-x-2">
            <img src="{{ asset('images/reverse.svg') }}" class="w-4 h-4" alt="Icono Nuevo Usuario">
            <h1 class="text-3xl font-bold whitespace-nowrap">Crear Usuario</h1>
        </div>
        {!! Breadcrumbs::render('productos.create') !!}
    </div>

    <div class="container max-w-3xl py-2 mx-auto">

        <div class="flex justify-between space-x-8">
            <!-- Botón Importar CSV -->
            <form action="{{ route('productos.importar.csv') }}" method="POST" enctype="multipart/form-data"
                class="flex items-center justify-end mb-4 space-x-2 ">
                @csrf
                <input type="file" name="archivo_csv" accept=".csv" required
                    class="text-sm text-white bg-slate-800 border border-cyan-600 rounded px-3 py-1.5 shadow-sm">
                <button type="submit"
                    class="px-4 py-2 font-semibold text-white transition rounded-lg bg-cyan-600 hover:bg-cyan-700">
                    Importar CSV
                </button>
            </form>

            <!-- boton de generar CSV-->
            <form action="{{ route('productos.generarCSV') }}" method="GET" style="float: right;">
                <select name="tipo"
                    class="px-4 py-3 pr-10 text-sm transition-all duration-300 bg-gray-600 border shadow-xl appearance-none  bg-opacity-60 border-cyan-500 text-cyan-100 rounded-xl focus:ring-2 focus:ring-cyan-400 focus:border-transparent hover:cursor-pointer"
                    required>
                    <option value="" class="bg-slate-900 text-slate-400">Selecciona tipo</option>
                    <option value="café">Café</option>
                    <option value="mora">Mora</option>
                </select>
                <button type="submit"
                    class="px-4 py-2 font-semibold text-white transition rounded-lg bg-cyan-600 hover:bg-cyan-700">
                    Generar CSV
                </button>
            </form>
        </div>

        <!-- Alpine.js debe estar disponible (Jetstream ya lo incluye) -->
        <form action="{{ route('productos.store') }}" method="POST" enctype="multipart/form-data" x-data="{ tab: 'historia' }"
            class="bg-[var(--color-formulario)] p-12 rounded-3xl shadow-2xl space-y-2">
            @csrf

            <!-- Imagen -->
            <div>
                <x-label for="imagen" :value="'Imagen del producto (opcional)'" />
                <x-input id="imagen" type="file" name="imagen" class="mt-0" />
                @error('imagen')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Tipo -->
            <div>
                <label class="block text-sm font-medium text-gray-700">
                    Tipo Seleccionado <span class="text-red-600">*</span>
                </label>
                <select name="tipo" id="tipo" required
                    class="w-full mt-1 border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">-- Selecciona un tipo --</option>
                    <option value="café" {{ old('tipo') == 'café' ? 'selected' : '' }}>Café</option>
                    <option value="mora" {{ old('tipo') == 'mora' ? 'selected' : '' }}>Mora</option>
                </select>
                @error('tipo')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Tabs -->
            <div class="mb-2 border-b">
                <nav class="flex space-x-4">
                    <button type="button" class="px-4 py-2 font-semibold border-b-2"
                        :class="tab === 'historia' ? 'border-indigo-600 text-indigo-600' : 'border-transparent'"
                        @click="tab = 'historia'">Historia</button>

                    <button type="button" class="px-5 py-2 font-semibold border-b-2"
                        :class="tab === 'productos' ? 'border-indigo-600 text-indigo-600' : 'border-transparent'"
                        @click="tab = 'productos'">Productos</button>

                    <button type="button" class="px-5 py-2 font-semibold border-b-2"
                        :class="tab === 'variantes' ? 'border-indigo-600 text-indigo-600' : 'border-transparent'"
                        @click="tab = 'variantes'">Variantes</button>

                    <button type="button" class="px-5 py-2 font-semibold border-b-2"
                        :class="tab === 'enfermedades' ? 'border-indigo-600 text-indigo-600' : 'border-transparent'"
                        @click="tab = 'enfermedades'">Enfermedades</button>

                    <button type="button" class="px-4 py-2 font-semibold border-b-2"
                        :class="tab === 'agroinsumos' ? 'border-indigo-600 text-indigo-600' : 'border-transparent'"
                        @click="tab = 'agroinsumos'">Agroinsumos</button>
                </nav>
            </div>

            <!-- Tab: historia -->
            <div x-show="tab === 'historia'" x-cloak class="space-y-4">
                @foreach ([
            'historia' => 'Historia del producto',
        ] as $key => $label)
                    <div>
                        <label for="{{ $key }}" class="block text-sm font-medium text-gray-700">
                            {{ $label }} <span class="text-red-600">*</span>
                        </label>
                        <textarea name="detalles[{{ $key }}]" id="{{ $key }}" required class="w-full rounded">{{ old("detalles.$key") }}</textarea>
                    </div>
                @endforeach
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

            <!-- Tab: Agroinsumos -->
            <div x-show="tab === 'agroinsumos'" x-cloak class="space-y-4">
                @foreach ([
            'insumos' => 'Insumos',
        ] as $key => $label)
                    <div>
                        <label for="{{ $key }}" class="block text-sm font-medium text-gray-700">
                            {{ $label }} <span class="text-red-600">*</span>
                        </label>
                        <textarea name="detalles[{{ $key }}]" id="{{ $key }}" required class="w-full rounded">{{ old("detalles.$key") }}</textarea>
                    </div>
                @endforeach
            </div>


            <!-- Botones -->
            <div class="flex justify-between pt-6">
                <a href="{{ route('productos.index') }}"
                    class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white transition bg-black rounded hover:bg-gray-800">
                    <img src="{{ asset('images/vole.svg') }}" class="w-4 h-3" alt="Icono Nuevo Usuario">
                    <span class="font-bold whitespace-nowrap">Volver</span>
                </a>
                <x-button>
                    Guardar producto
                </x-button>
            </div>
        </form>
    </div>
@endsection
