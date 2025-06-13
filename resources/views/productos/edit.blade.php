{{-- @extends('layouts.app')

@section('title', 'Editar Producto')

@section('content')
    <div class="inline-block px-8 py-10">
        <div class="flex items-center space-x-2">
            <img src="{{ asset('images/reverse.svg') }}" class="w-4 h-4" alt="Icono Volver">
            <h1 class="text-3xl whitespace-nowrap font-bold">Editar Producto</h1>
        </div>
        {{-- {!! Breadcrumbs::render('productos.edit', $producto) !!} 
    </div>

    {{-- Protección principal: El formulario de edición solo se mostrará si el usuario puede 'actualizar' este producto --}
    @can('update', $producto)
        <div class="container max-w-4xl py-6 mx-auto">
            <form action="{{ route('productos.update', $producto) }}" method="POST" enctype="multipart/form-data"
                x-data="{
                    tipo: '{{ old('tipo', $producto->tipo) }}',
                
                    // Datos precargados para Café
                    caf_infor_informacion: '{{ old('caf_infor.informacion', $producto->cafe->cafInfor->informacion ?? '') }}',
                    caf_insumos_informacion: '{{ old('caf_insumos.informacion', $producto->cafe->cafInsumos->informacion ?? '') }}',
                    caf_patoge_patogeno: '{{ old('caf_patoge.patogeno', $producto->cafe->cafPatoge->patogeno ?? '') }}',
                    caf_patoge_informacion: '{{ old('caf_patoge.informacion', $producto->cafe->cafPatoge->informacion ?? '') }}',
                
                    // Datos precargados para Mora
                    mora_inf_informacion: '{{ old('mora_inf.informacion', $producto->mora->moraInf->informacion ?? '') }}',
                    mora_insu_informacion: '{{ old('mora_insu.informacion', $producto->mora->moraInsu->informacion ?? '') }}',
                    mora_patoge_patogeno: '{{ old('mora_patoge.patogeno', $producto->mora->moraPatoge->patogeno ?? '') }}',
                    mora_patoge_informacion: '{{ old('mora_patoge.informacion', $producto->mora->moraPatoge->informacion ?? '') }}',
                }" class="bg-[var(--color-Gestion)] p-12 rounded-3xl shadow-2xl space-y-2">
                @csrf
                @method('PUT') {{-- Importante para el método PUT en Laravel --}

                <!-- Campo de Imagen -->
                <div class="mb-4">
                    <label for="imagen" class="block text-sm font-medium">Imagen (opcional)</label>
                    <input type="file" name="imagen" id="imagen" class="w-full">
                    @if ($producto->imagen)
                        <p class="mt-1 text-sm text-gray-600">Imagen actual: <a
                                href="{{ asset('storage/' . $producto->imagen) }}" target="_blank"
                                class="underline text-blue-500">Ver</a></p>
                        <div class="mt-2">
                            <input type="checkbox" name="eliminar_imagen" id="eliminar_imagen" class="mr-1">
                            <label for="eliminar_imagen" class="text-sm text-red-600">Eliminar imagen actual</label>
                        </div>
                    @endif
                    @error('imagen')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Tipo de producto (deshabilitado para edición) -->
                <div class="mb-4">
                    <label for="tipo" class="block text-sm font-medium">Tipo de producto *</label>
                    <select name="tipo" id="tipo" class="w-full border-gray-300 rounded shadow-sm" required disabled>
                        <option value="">-- Selecciona --</option>
                        <option value="café" {{ old('tipo', $producto->tipo) == 'café' ? 'selected' : '' }}>Café</option>
                        <option value="mora" {{ old('tipo', $producto->tipo) == 'mora' ? 'selected' : '' }}>Mora</option>
                    </select>
                    {{-- Campo oculto para enviar el tipo, ya que el select está deshabilitado --}
                    <input type="hidden" name="tipo" value="{{ $producto->tipo }}">
                    @error('tipo')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Observaciones (común para ambos tipos) -->
                <div class="mb-4">
                    <label for="observaciones" class="block text-sm font-medium">Observaciones (Opcional)</label>
                    <textarea name="observaciones" id="observaciones" rows="3" class="w-full border-gray-300 rounded shadow-sm">{{ old('observaciones', $producto->observaciones) }}</textarea>
                    @error('observaciones')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Campos para Café --}
                <div x-show="tipo === 'café'" x-cloak class="space-y-4">
                    <h2 class="text-xl font-bold mt-6 mb-4">Detalles de Café</h2>
                    <div>
                        <label for="caf_infor_informacion" class="block text-sm font-medium text-gray-700">
                            Información General del Café <span class="text-red-600">*</span>
                        </label>
                        <textarea name="caf_infor[informacion]" id="caf_infor_informacion" class="w-full rounded"
                            x-model="caf_infor_informacion"></textarea>
                        @error('caf_infor.informacion')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="caf_insumos_informacion" class="block text-sm font-medium text-gray-700">
                            Detalles de Insumos del Café <span class="text-red-600">*</span>
                        </label>
                        <textarea name="caf_insumos[informacion]" id="caf_insumos_informacion" class="w-full rounded"
                            x-model="caf_insumos_informacion"></textarea>
                        @error('caf_insumos.informacion')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="caf_patoge_patogeno" class="block text-sm font-medium text-gray-700">
                            Nombre del Patógeno de la Café <span class="text-red-600">*</span>
                        </label>
                        <input type="text" name="caf_patoge[patogeno]" id="caf_patoge_patogeno" class="w-full rounded"
                            x-model="caf_patoge_patogeno" />
                        @error('caf_patoge.patogeno')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="caf_patoge_informacion" class="block text-sm font-medium text-gray-700">
                            Información de Patógenos del Café <span class="text-red-600">*</span>
                        </label>
                        <textarea name="caf_patoge[informacion]" id="caf_patoge_informacion" class="w-full rounded"
                            x-model="caf_patoge_informacion"></textarea>
                        @error('caf_patoge.informacion')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Campos para Mora --}
                <div x-show="tipo === 'mora'" x-cloak class="space-y-4">
                    <h2 class="text-xl font-bold mt-6 mb-4">Detalles de Mora</h2>
                    <div>
                        <label for="mora_inf_informacion" class="block text-sm font-medium text-gray-700">
                            Información General de la Mora <span class="text-red-600">*</span>
                        </label>
                        <textarea name="mora_inf[informacion]" id="mora_inf_informacion" class="w-full rounded" x-model="mora_inf_informacion"></textarea>
                        @error('mora_inf.informacion')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="mora_insu_informacion" class="block text-sm font-medium text-gray-700">
                            Detalles de Insumos de la Mora <span class="text-red-600">*</span>
                        </label>
                        <textarea name="mora_insu[informacion]" id="mora_insu_informacion" class="w-full rounded"
                            x-model="mora_insu_informacion"></textarea>
                        @error('mora_insu.informacion')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="mora_patoge_patogeno" class="block text-sm font-medium text-gray-700">
                            Nombre del Patógeno de la Mora <span class="text-red-600">*</span>
                        </label>
                        <input type="text" name="mora_patoge[patogeno]" id="mora_patoge_patogeno" class="w-full rounded"
                            x-model="mora_patoge_patogeno" />
                        @error('mora_patoge.patogeno')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="mora_patoge_informacion" class="block text-sm font-medium text-gray-700">
                            Información del Patógeno de la Mora <span class="text-red-600">*</span>
                        </label>
                        <textarea name="mora_patoge[informacion]" id="mora_patoge_informacion" class="w-full rounded"
                            x-model="mora_patoge_informacion"></textarea>
                        @error('mora_patoge.informacion')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Botones de acción -->
                <div class="flex justify-between items-center mt-6">
                    <a href="{{ route('productos.index', $producto->id) }}"
                        class="inline-block px-4 py-2 text-white bg-gray-600 rounded hover:bg-gray-700">
                        Volver
                    </a>
                    <button type="submit" class="px-4 py-2 text-white bg-green-600 rounded hover:bg-green-700">
                        Actualizar
                    </button>
                </div>
            </form>
        </div>
    @endcan
    {{-- Mensaje opcional si el usuario no tiene permiso --}
    @cannot('update', $producto)
        <div class="max-w-xl mx-auto p-6 bg-red-100 text-red-700 rounded-lg shadow-md text-center">
            No tienes permiso para editar este producto.
        </div>
    @endcannot
@endsection --}}

@extends('layouts.app') {{-- Asume que tienes un layout base --}}

@section('content')
    <div class="container mx-auto p-4">
        <h1 class="text-2xl font-bold mb-4">Editar Producto: {{ $producto->tipo }} - ID: {{ $producto->id }}</h1>

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

        <form action="{{ route('productos.update', $producto) }}" method="POST" enctype="multipart/form-data"
            class="bg-white shadow-md rounded-lg p-6">
            @csrf
            @method('PUT') {{-- Método PUT para actualizaciones en Laravel --}}

            <div class="mb-4">
                <label for="tipo" class="block text-gray-700 text-sm font-bold mb-2">Tipo de Producto:</label>
                {{-- El tipo no se permite cambiar en la edición según tu controlador, así que lo mostramos deshabilitado --}}
                <input type="text" name="tipo" id="tipo"
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 bg-gray-100 leading-tight focus:outline-none focus:shadow-outline"
                    value="{{ $producto->tipo }}" readonly>
                @error('tipo')
                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="imagen" class="block text-gray-700 text-sm font-bold mb-2">Imagen Actual:</label>
                @if ($producto->imagen)
                    <img src="{{ asset('storage/' . $producto->imagen) }}" alt="Imagen actual del producto"
                        class="w-32 h-32 object-cover rounded-lg mb-2">
                @else
                    <p class="text-gray-600 text-sm mb-2">No hay imagen actual.</p>
                @endif
                <label for="nueva_imagen" class="block text-gray-700 text-sm font-bold mb-2">Subir Nueva Imagen
                    (Opcional):</label>
                <input type="file" name="imagen" id="nueva_imagen"
                    class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none">
                @error('imagen')
                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="observaciones" class="block text-gray-700 text-sm font-bold mb-2">Observaciones
                    Generales:</label>
                <textarea name="observaciones" id="observaciones" rows="3"
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">{{ old('observaciones', $producto->observaciones) }}</textarea>
                @error('observaciones')
                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                @enderror
            </div>

            {{-- Campos específicos para Café --}}
            @if ($producto->tipo === 'café' && $producto->cafe)
                <div id="campos_cafe">
                    <h2 class="text-xl font-semibold mb-3">Detalles de Café</h2>
                    <div class="mb-4">
                        <label for="cafe_data_numero_pagina" class="block text-gray-700 text-sm font-bold mb-2">Número de
                            Página:</label>
                        <input type="number" name="cafe_data[numero_pagina]" id="cafe_data_numero_pagina"
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                            value="{{ old('cafe_data.numero_pagina', $producto->cafe->numero_pagina) }}">
                        @error('cafe_data.numero_pagina')
                            <p class="text-red-500 text-xs italic">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="mb-4">
                        <label for="cafe_data_clase" class="block text-gray-700 text-sm font-bold mb-2">Clase:</label>
                        <input type="text" name="cafe_data[clase]" id="cafe_data_clase"
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                            value="{{ old('cafe_data.clase', $producto->cafe->clase) }}">
                        @error('cafe_data.clase')
                            <p class="text-red-500 text-xs italic">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="mb-4">
                        <label for="cafe_data_informacion" class="block text-gray-700 text-sm font-bold mb-2">Información de
                            Café:</label>
                        <textarea name="cafe_data[informacion]" id="cafe_data_informacion" rows="5"
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">{{ old('cafe_data.informacion', $producto->cafe->informacion) }}</textarea>
                        @error('cafe_data.informacion')
                            <p class="text-red-500 text-xs italic">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            @endif

            {{-- Campos específicos para Mora --}}
            @if ($producto->tipo === 'mora' && $producto->mora)
                <div id="campos_mora">
                    <h2 class="text-xl font-semibold mb-3">Detalles de Mora</h2>
                    <div class="mb-4">
                        <label for="mora_data_numero_pagina" class="block text-gray-700 text-sm font-bold mb-2">Número de
                            Página:</label>
                        <input type="number" name="mora_data[numero_pagina]" id="mora_data_numero_pagina"
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                            value="{{ old('mora_data.numero_pagina', $producto->mora->numero_pagina) }}">
                        @error('mora_data.numero_pagina')
                            <p class="text-red-500 text-xs italic">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="mb-4">
                        <label for="mora_data_clase" class="block text-gray-700 text-sm font-bold mb-2">Clase:</label>
                        <input type="text" name="mora_data[clase]" id="mora_data_clase"
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                            value="{{ old('mora_data.clase', $producto->mora->clase) }}">
                        @error('mora_data.clase')
                            <p class="text-red-500 text-xs italic">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="mb-4">
                        <label for="mora_data_informacion" class="block text-gray-700 text-sm font-bold mb-2">Información de
                            Mora:</label>
                        <textarea name="mora_data[informacion]" id="mora_data_informacion" rows="5"
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">{{ old('mora_data.informacion', $producto->mora->informacion) }}</textarea>
                        @error('mora_data.informacion')
                            <p class="text-red-500 text-xs italic">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            @endif

            <div class="flex items-center justify-between">
                <button type="submit"
                    class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                    Actualizar Producto
                </button>
                <a href="{{ route('productos.index') }}"
                    class="inline-block align-baseline font-bold text-sm text-blue-500 hover:text-blue-800">
                    Cancelar
                </a>
            </div>
        </form>
    </div>
@endsection
