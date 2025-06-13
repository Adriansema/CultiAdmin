{{--@extends('layouts.app')

@section('title', 'Crear Producto')

@section('content')

    <div class="inline-block px-8 py-10">
        <div class="flex items-center space-x-2">
            <img src="{{ asset('images/reverse.svg') }}" class="w-4 h-4" alt="Icono Nuevo Usuario">
            <h1 class="text-3xl font-bold whitespace-nowrap">Crear Usuario</h1>
        </div>
        {!! Breadcrumbs::render('productos.create') !!}
    </div>

    @can('create', App\Models\Producto::class)
        <div class="container max-w-3xl py-8 mx-auto bg-gray-200 shadow-lg rounded-2xl">

            <div class="flex justify-between space-x-8">
                @can('import', App\Models\Producto::class)
                    <!-- Botón Importar CSV -->
                    <form action="{{ route('productos.importar.csv') }}" method="POST" enctype="multipart/form-data"
                        class="flex items-center justify-end mb-4 space-x-2 ">
                        @csrf
                        <button type="submit"
                            class="px-4 py-2 font-semibold text-white transition rounded-lg bg-cyan-600 hover:bg-cyan-700">
                            Importar CSV
                        </button>
                        <input type="file" name="archivo_csv" accept=".csv" required
                            class="text-sm text-white bg-slate-800 border border-cyan-600 rounded px-3 py-1.5 shadow-sm">
                    </form>
                @endcan
            </div>

            <form action="{{ route('productos.store') }}" method="POST" enctype="multipart/form-data" x-data="{ tab: 'cafe_infor', tipo: '{{ old('tipo', '') }}' }"
                x-init="console.log('Alpine init:', { tab: tab, tipo: tipo }); // Log al inicio
                $watch('tipo', value => {
                    console.log('Tipo changed to:', value); // Log cuando el tipo cambia
                    if (value === 'café') {
                        tab = 'caf_infor'; // Reinicia la pestaña a la primera de Café
                    } else if (value === 'mora') {
                        tab = 'mora_inf'; // Reinicia la pestaña a la primera de Mora
                    } else {
                        tab = ''; // Si no hay tipo seleccionado, no hay pestaña activa
                    }
                });
                // Si hay un tipo preseleccionado al cargar, asegúrate de que la pestaña inicial sea la correcta
                if (tipo === 'café') {
                    tab = 'caf_infor';
                } else if (tipo === 'mora') {
                    tab = 'mora_inf';
                }" class="bg-[var(--color-Gestion)] p-12 rounded-3xl space-y-2">
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
                    <select name="tipo" id="tipo" required x-model="tipo" 
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
                        {{-- Pestañas para Café --}
                        <div x-show="tipo === 'café'" x-cloak class="flex space-x-4"> 
                            <button type="button" class="px-4 py-2 font-semibold border-b-2"
                                :class="tab === 'caf_infor' ? 'border-indigo-600 text-indigo-600' : 'border-transparent'"
                                @click="tab = 'caf_infor'">Información Café</button>

                            <button type="button" class="px-5 py-2 font-semibold border-b-2"
                                :class="tab === 'caf_insumos' ? 'border-indigo-600 text-indigo-600' : 'border-transparent'"
                                @click="tab = 'caf_insumos'">Insumos Café</button>

                            <button type="button" class="px-5 py-2 font-semibold border-b-2"
                                :class="tab === 'caf_patoge' ? 'border-indigo-600 text-indigo-600' : 'border-transparent'"
                                @click="tab = 'caf_patoge'">Patógenos Café</button>
                        </div>

                        {{-- Pestañas para Mora --}
                        <div x-show="tipo === 'mora'" x-cloak class="flex space-x-4">
                            <button type="button" class="px-4 py-2 font-semibold border-b-2"
                                :class="tab === 'mora_inf' ? 'border-indigo-600 text-indigo-600' : 'border-transparent'"
                                @click="tab = 'mora_inf'">Información Mora</button>

                            <button type="button" class="px-5 py-2 font-semibold border-b-2"
                                :class="tab === 'mora_insu' ? 'border-indigo-600 text-indigo-600' : 'border-transparent'"
                                @click="tab = 'mora_insu'">Insumos Mora</button>

                            <button type="button" class="px-5 py-2 font-semibold border-b-2"
                                :class="tab === 'mora_patoge' ? 'border-indigo-600 text-indigo-600' : 'border-transparent'"
                                @click="tab = 'mora_patoge'">Patógenos Mora</button>
                        </div>
                    </nav>
                </div>

                <!-- Contenido de las Pestañas Dinámicas -->

                {{-- Pestaña: Información Café (caf_infor) --}
                <div x-show="tab === 'caf_infor' && tipo === 'café'" x-cloak class="space-y-4">
                    <div>
                        <label for="caf_infor_informacion" class="block text-sm font-medium text-gray-700">
                            Información General del Café <span class="text-red-600">*</span>
                        </label>
                        <textarea name="caf_infor[informacion]" id="caf_infor_informacion" class="w-full rounded">{{ old('caf_infor.informacion') }}</textarea>
                        @error('caf_infor.informacion')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Pestaña: Insumos Café (caf_insumos) --}
                <div x-show="tab === 'caf_insumos' && tipo === 'café'" x-cloak class="space-y-4">
                    <div>
                        <label for="caf_insumos_informacion" class="block text-sm font-medium text-gray-700">
                            Detalles de Insumos del Café <span class="text-red-600">*</span>
                        </label>
                        <textarea name="caf_insumos[informacion]" id="caf_insumos_informacion" class="w-full rounded">{{ old('caf_insumos.informacion') }}</textarea>
                        @error('caf_insumos.informacion')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Pestaña: Patógenos Café (caf_patoge) --}
                <div x-show="tab === 'caf_patoge' && tipo === 'café'" x-cloak class="space-y-4">
                    <div>
                        <label for="caf_patoge_patogeno" class="block text-sm font-medium text-gray-700">
                            Nombre del Patógeno del Café <span class="text-red-600">*</span>
                        </label>
                        <x-input type="text" name="caf_patoge[patogeno]" id="caf_patoge_patogeno" class="w-full rounded"
                            value="{{ old('caf_patoge.patogeno') }}" />
                        @error('caf_patoge.patogeno')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="caf_patoge_informacion" class="block text-sm font-medium text-gray-700">
                            Información de Patógenos del Café <span class="text-red-600">*</span>
                        </label>
                        <textarea name="caf_patoge[informacion]" id="caf_patoge_informacion" class="w-full rounded">{{ old('caf_patoge.informacion') }}</textarea>
                        @error('caf_patoge.informacion')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Pestaña: Información Mora (mora_inf) --}
                <div x-show="tab === 'mora_inf' && tipo === 'mora'" x-cloak class="space-y-4">
                    <div>
                        <label for="mora_inf_informacion" class="block text-sm font-medium text-gray-700">
                            Información General de la Mora <span class="text-red-600">*</span>
                        </label>
                        <textarea name="mora_inf[informacion]" id="mora_inf_informacion" class="w-full rounded">{{ old('mora_inf.informacion') }}</textarea>
                        @error('mora_inf.informacion')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Pestaña: Insumos Mora (mora_insu) --}
                <div x-show="tab === 'mora_insu' && tipo === 'mora'" x-cloak class="space-y-4">
                    <div>
                        <label for="mora_insu_informacion" class="block text-sm font-medium text-gray-700">
                            Detalles de Insumos de la Mora <span class="text-red-600">*</span>
                        </label>
                        <textarea name="mora_insu[informacion]" id="mora_insu_informacion" class="w-full rounded">{{ old('mora_insu.informacion') }}</textarea>
                        @error('mora_insu.informacion')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Pestaña: Patógenos Mora (mora_patoge) --}
                <div x-show="tab === 'mora_patoge' && tipo === 'mora'" x-cloak class="space-y-4">
                    <div>
                        <label for="mora_patoge_patogeno" class="block text-sm font-medium text-gray-700">
                            Nombre del Patógeno de la Mora <span class="text-red-600">*</span>
                        </label>
                        <x-input type="text" name="mora_patoge[patogeno]" id="mora_patoge_patogeno"
                            class="w-full rounded" value="{{ old('mora_patoge.patogeno') }}" />
                        @error('mora_patoge.patogeno')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="mora_patoge_informacion" class="block text-sm font-medium text-gray-700">
                            Información del Patógeno de la Mora <span class="text-red-600">*</span>
                        </label>
                        <textarea name="mora_patoge[informacion]" id="mora_patoge_informacion" class="w-full rounded">{{ old('mora_patoge.informacion') }}</textarea>
                        @error('mora_patoge.informacion')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>


                <!-- Botones -->
                <div class="flex justify-between pt-6">
                    <a href="{{ route('productos.index') }}"
                        class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white transition bg-black rounded hover:bg-gray-800">
                        <img src="{{ asset('images/vole.svg') }}" class="w-4 h-3" alt="Icono Volver">
                        <span class="font-bold whitespace-nowrap">Volver</span>
                    </a>
                    <x-button>
                        Guardar producto
                    </x-button>
                </div>
            </form>
        </div>
    @endcan
    @cannot('create', App\Models\Producto::class)
        <div class="max-w-xl mx-auto p-6 bg-red-100 text-red-700 rounded-lg shadow-md text-center">
            No tienes permiso para crear productos.
        </div>
    @endcannot
@endsection--}}

@extends('layouts.app') {{-- Asume que tienes un layout base --}}

@section('content')
<div class="container mx-auto p-4">
    <h1 class="text-2xl font-bold mb-4">Crear Nuevo Producto</h1>

    @if ($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <strong class="font-bold">¡Oops!</strong>
            <span class="block sm:inline">Hubo algunos problemas con tu entrada.</span>
            <ul class="mt-3 list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('productos.store') }}" method="POST" enctype="multipart/form-data" class="bg-white shadow-md rounded-lg p-6">
        @csrf {{-- Protección CSRF obligatoria en Laravel --}}
        <div class="mb-4">
            <label for="tipo" class="block text-gray-700 text-sm font-bold mb-2">Tipo de Producto:</label>
            <select name="tipo" id="tipo" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                <option value="">Seleccione un tipo</option>
                <option value="café" {{ old('tipo') == 'café' ? 'selected' : '' }}>Café</option>
                <option value="mora" {{ old('tipo') == 'mora' ? 'selected' : '' }}>Mora</option>
            </select>
            @error('tipo')
                <p class="text-red-500 text-xs italic">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label for="imagen" class="block text-gray-700 text-sm font-bold mb-2">Imagen:</label>
            <input type="file" name="imagen" id="imagen" class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none">
            @error('imagen')
                <p class="text-red-500 text-xs italic">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label for="observaciones" class="block text-gray-700 text-sm font-bold mb-2">Observaciones Generales:</label>
            <textarea name="observaciones" id="observaciones" rows="3" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">{{ old('observaciones') }}</textarea>
            @error('observaciones')
                <p class="text-red-500 text-xs italic">{{ $message }}</p>
            @enderror
        </div>

        {{-- Campos específicos para Café --}}
        <div id="campos_cafe" class="hidden"> {{-- Usa JS para mostrar/ocultar esto basado en la selección del tipo --}}
            <h2 class="text-xl font-semibold mb-3">Detalles de Café</h2>
            <div class="mb-4">
                <label for="cafe_data_numero_pagina" class="block text-gray-700 text-sm font-bold mb-2">Número de Página:</label>
                <input type="number" name="cafe_data[numero_pagina]" id="cafe_data_numero_pagina" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" value="{{ old('cafe_data.numero_pagina') }}">
                @error('cafe_data.numero_pagina')
                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                @enderror
            </div>
            <div class="mb-4">
                <label for="cafe_data_clase" class="block text-gray-700 text-sm font-bold mb-2">Clase:</label>
                <input type="text" name="cafe_data[clase]" id="cafe_data_clase" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" value="{{ old('cafe_data.clase') }}">
                @error('cafe_data.clase')
                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                @enderror
            </div>
            <div class="mb-4">
                <label for="cafe_data_informacion" class="block text-gray-700 text-sm font-bold mb-2">Información de Café:</label>
                <textarea name="cafe_data[informacion]" id="cafe_data_informacion" rows="5" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">{{ old('cafe_data.informacion') }}</textarea>
                @error('cafe_data.informacion')
                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                @enderror
            </div>
        </div>

        {{-- Campos específicos para Mora --}}
        <div id="campos_mora" class="hidden"> {{-- Usa JS para mostrar/ocultar esto basado en la selección del tipo --}}
            <h2 class="text-xl font-semibold mb-3">Detalles de Mora</h2>
            <div class="mb-4">
                <label for="mora_data_numero_pagina" class="block text-gray-700 text-sm font-bold mb-2">Número de Página:</label>
                <input type="number" name="mora_data[numero_pagina]" id="mora_data_numero_pagina" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" value="{{ old('mora_data.numero_pagina') }}">
                @error('mora_data.numero_pagina')
                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                @enderror
            </div>
            <div class="mb-4">
                <label for="mora_data_clase" class="block text-gray-700 text-sm font-bold mb-2">Clase:</label>
                <input type="text" name="mora_data[clase]" id="mora_data_clase" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" value="{{ old('mora_data.clase') }}">
                @error('mora_data.clase')
                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                @enderror
            </div>
            <div class="mb-4">
                <label for="mora_data_informacion" class="block text-gray-700 text-sm font-bold mb-2">Información de Mora:</label>
                <textarea name="mora_data[informacion]" id="mora_data_informacion" rows="5" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">{{ old('mora_data.informacion') }}</textarea>
                @error('mora_data.informacion')
                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="flex items-center justify-between">
            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                Guardar Producto
            </button>
            <a href="{{ route('productos.index') }}" class="inline-block align-baseline font-bold text-sm text-blue-500 hover:text-blue-800">
                Cancelar
            </a>
        </div>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const tipoSelect = document.getElementById('tipo');
        const camposCafe = document.getElementById('campos_cafe');
        const camposMora = document.getElementById('campos_mora');

        function toggleProductFields() {
            const selectedType = tipoSelect.value;
            camposCafe.classList.add('hidden');
            camposMora.classList.add('hidden');

            if (selectedType === 'café') {
                camposCafe.classList.remove('hidden');
            } else if (selectedType === 'mora') {
                camposMora.classList.remove('hidden');
            }
        }

        tipoSelect.addEventListener('change', toggleProductFields);

        // Llamar en la carga inicial para manejar old() values
        toggleProductFields();
    });
</script>
@endsection

