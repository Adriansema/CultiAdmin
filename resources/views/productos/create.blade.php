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

    <div class="container max-w-4xl py-4 mx-auto">
        <form action="{{ route('productos.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <!-- Tipo -->
            <div class="mb-4">
                <label for="tipo" class="block text-sm font-medium">Tipo de producto *</label>
                <select name="tipo" id="tipo" class="w-full border-gray-300 rounded shadow-sm" required>
                    <option value="">-- Selecciona --</option>
                    <option value="café" {{ old('tipo') == 'café' ? 'selected' : '' }}>Café</option>
                    <option value="mora" {{ old('tipo') == 'mora' ? 'selected' : '' }}>Mora</option>
                    <!-- Más tipos si necesitas -->
                </select>
                @error('tipo')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Detalles -->
            @php
                $campos = [
                    'que_es' => '¿Qué es?',
                    'historia' => 'Historia del cultivo',
                    'variedad' => 'Variedad',
                    'especies' => 'Especies',
                    'caracteristicas' => 'Características',
                    'clima' => 'Condiciones del clima',
                    'suelo' => 'Tipo de suelo ideal',
                    'riego' => 'Requerimientos de riego',
                    'cosecha' => 'Época de cosecha',
                    'postcosecha' => 'Proceso postcosecha',
                    'plagas' => 'Plagas y enfermedades comunes',
                    'usos' => 'Usos y aplicaciones',
                    'valor_nutricional' => 'Valor nutricional',
                    'impacto_economico' => 'Impacto económico',
                    'tecnicas_cultivo' => 'Técnicas de cultivo',
                    'certificaciones' => 'Certificaciones disponibles',
                    'ubicacion_geografica' => 'Ubicación geográfica óptima',
                    'nombre_cientifico' => 'Nombre científico',
                ];
            @endphp

            @foreach ($campos as $key => $label)
                <div class="mb-4">
                    <label class="block text-sm font-medium">{{ $label }}</label>
                    <textarea name="detalles[{{ $key }}]" rows="2" class="w-full border-gray-300 rounded shadow-sm" required>{{ old("detalles.$key") }}</textarea>
                    @error("detalles.$key")
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            @endforeach

            <!-- Observaciones -->
            <div class="mb-4">
                <label for="observaciones" class="block text-sm font-medium">Observaciones (opcional)</label>
                <textarea name="observaciones" id="observaciones" rows="3" class="w-full border-gray-300 rounded shadow-sm">{{ old('observaciones') }}</textarea>
                @error('observaciones')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Imagen -->
            <div class="mb-4">
                <label for="imagen" class="block text-sm font-medium">Imagen del producto (opcional)</label>
                <input type="file" name="imagen" id="imagen" class="w-full">
                @error('imagen')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit" class="px-4 py-2 text-white bg-blue-600 rounded hover:bg-blue-700">Guardar</button>
            <!-- Botón volver -->
            <a href="{{ route('productos.index') }}"
                class="inline-block px-4 py-2 text-white bg-gray-400 rounded hover:bg-gray-700">
                Volver al listado
            </a>
        </form>
    </div>
@endsection
