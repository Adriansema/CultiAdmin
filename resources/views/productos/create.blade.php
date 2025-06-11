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

    {{-- Protección del contenido principal de la página de creación --}}
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
                {{-- Inicializa 'tab' a una de las nuevas pestañas --}} x-init="console.log('Alpine init:', { tab: tab, tipo: tipo }); // Log al inicio
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
                    <select name="tipo" id="tipo" required x-model="tipo" {{-- x-model para vincular con Alpine.js --}}
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
                        {{-- Pestañas para Café --}}
                        <div x-show="tipo === 'café'" x-cloak class="flex space-x-4"> {{-- Usamos div con x-show --}}
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

                        {{-- Pestañas para Mora --}}
                        <div x-show="tipo === 'mora'" x-cloak class="flex space-x-4"> {{-- Usamos div con x-show --}}
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

                {{-- Pestaña: Información Café (caf_infor) --}}
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

                {{-- Pestaña: Insumos Café (caf_insumos) --}}
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

                {{-- Pestaña: Patógenos Café (caf_patoge) --}}
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

                {{-- Pestaña: Información Mora (mora_inf) --}}
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

                {{-- Pestaña: Insumos Mora (mora_insu) --}}
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

                {{-- Pestaña: Patógenos Mora (mora_patoge) --}}
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
    {{-- Si el usuario no tiene permiso para crear productos, puede que quieras mostrar un mensaje o redirigir --}}
    @cannot('create', App\Models\Producto::class)
        <div class="max-w-xl mx-auto p-6 bg-red-100 text-red-700 rounded-lg shadow-md text-center">
            No tienes permiso para crear productos.
        </div>
    @endcannot
@endsection
