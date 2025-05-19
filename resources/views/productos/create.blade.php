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
        <form action="{{ route('productos.store') }}" method="POST" enctype="multipart/form-data" x-data="{ tab: 'general' }"
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
                        :class="tab === 'general' ? 'border-indigo-600 text-indigo-600' : 'border-transparent'"
                        @click="tab = 'general'">General</button>

                    <button type="button" class="px-4 py-2 font-semibold border-b-2"
                        :class="tab === 'cultivo' ? 'border-indigo-600 text-indigo-600' : 'border-transparent'"
                        @click="tab = 'cultivo'">Cultivo</button>

                    <button type="button" class="px-4 py-2 font-semibold border-b-2"
                        :class="tab === 'produccion' ? 'border-indigo-600 text-indigo-600' : 'border-transparent'"
                        @click="tab = 'produccion'">Producción</button>

                    <button type="button" class="px-4 py-2 font-semibold border-b-2"
                        :class="tab === 'salud' ? 'border-indigo-600 text-indigo-600' : 'border-transparent'"
                        @click="tab = 'salud'">Salud & Uso</button>

                    <button type="button" class="px-4 py-2 font-semibold border-b-2"
                        :class="tab === 'otros' ? 'border-indigo-600 text-indigo-600' : 'border-transparent'"
                        @click="tab = 'otros'">Otros</button>
                </nav>
            </div>

            <!-- Tab: General -->
            <div x-show="tab === 'general'" class="space-y-4">
                <label for="que_es" class="block text-sm font-medium text-gray-700">
                    ¿Qué es? <span class="text-red-600">*</span>
                </label>
                <textarea name="detalles[que_es]" id="que_es" required class="w-full rounded">{{ old('detalles.que_es') }}</textarea>

                <label for="historia" class="block text-sm font-medium text-gray-700">
                    Historia del cultivo <span class="text-red-600">*</span>
                </label>
                <textarea name="detalles[historia]" id="historia" required class="w-full rounded">{{ old('detalles.historia') }}</textarea>

                <label for="nombre_cientifico" class="block text-sm font-medium text-gray-700">
                    Nombre científico <span class="text-red-600">*</span>
                </label>
                <textarea name="detalles[nombre_cientifico]" id="nombre_cientifico" required class="w-full rounded">{{ old('detalles.nombre_cientifico') }}</textarea>
            </div>

            <!-- Tab: Cultivo -->
            <div x-show="tab === 'cultivo'" x-cloak class="space-y-4">
                @foreach ([
            'variedad' => 'Variedad',
            'especies' => 'Especies',
            'caracteristicas' => 'Características',
            'clima' => 'Clima',
            'suelo' => 'Suelo',
            'riego' => 'Riego',
        ] as $key => $label)
                    <div>
                        <label for="{{ $key }}" class="block text-sm font-medium text-gray-700">
                            {{ $label }} <span class="text-red-600">*</span>
                        </label>
                        <textarea name="detalles[{{ $key }}]" id="{{ $key }}" required class="w-full rounded">{{ old("detalles.$key") }}</textarea>
                    </div>
                @endforeach
            </div>

            <!-- Tab: Producción -->
            <div x-show="tab === 'produccion'" x-cloak class="space-y-4">
                @foreach ([
            'cosecha' => 'Cosecha',
            'postcosecha' => 'Postcosecha',
            'tecnicas_cultivo' => 'Técnicas de cultivo',
            'certificaciones' => 'Certificaciones',
        ] as $key => $label)
                    <div>
                        <label for="{{ $key }}" class="block text-sm font-medium text-gray-700">
                            {{ $label }} <span class="text-red-600">*</span>
                        </label>
                        <textarea name="detalles[{{ $key }}]" id="{{ $key }}" required class="w-full rounded">{{ old("detalles.$key") }}</textarea>
                    </div>
                @endforeach
            </div>

            <!-- Tab: Salud & Uso -->
            <div x-show="tab === 'salud'" x-cloak class="space-y-4">
                @foreach ([
            'usos' => 'Usos y aplicaciones',
            'valor_nutricional' => 'Valor nutricional',
            'impacto_economico' => 'Impacto económico',
        ] as $key => $label)
                    <div>
                        <label for="{{ $key }}" class="block text-sm font-medium text-gray-700">
                            {{ $label }} <span class="text-red-600">*</span>
                        </label>
                        <textarea name="detalles[{{ $key }}]" id="{{ $key }}" required class="w-full rounded">{{ old("detalles.$key") }}</textarea>
                    </div>
                @endforeach
            </div>

            <!-- Tab: Otros -->
            <div x-show="tab === 'otros'" x-cloak class="space-y-4">
                <div>
                    <label for="ubicacion_geografica" class="block text-sm font-medium text-gray-700">
                        Ubicación geográfica óptima <span class="text-red-600">*</span>
                    </label>
                    <textarea name="detalles[ubicacion_geografica]" id="ubicacion_geografica" required class="w-full rounded">{{ old('detalles.ubicacion_geografica') }}</textarea>
                </div>

                <div>
                    <label for="plagas" class="block text-sm font-medium text-gray-700">
                        Plagas y enfermedades comunes <span class="text-red-600">*</span>
                    </label>
                    <textarea name="detalles[plagas]" id="plagas" required class="w-full rounded">{{ old('detalles.plagas') }}</textarea>
                </div>

                <div>
                    <label for="observaciones" class="block text-sm font-medium text-gray-700">
                        Observaciones (opcional)
                    </label>
                    <textarea name="observaciones" id="observaciones" class="w-full rounded">{{ old('observaciones') }}</textarea>
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
