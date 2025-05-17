@extends('layouts.app')

@section('title', 'Editar Producto')

@section('content')
    <div class="inline-block px-8 py-10">
        <div class="flex items-center space-x-2">
            <img src="{{ asset('images/reverse.svg') }}" class="w-4 h-4" alt="Icono Nuevo Usuario">
            <h1 class="text-3xl whitespace-nowrap font-bold">Editar Producto</h1>
        </div>
        {!! Breadcrumbs::render('productos.edit', $producto) !!}
    </div>

    <div class="container max-w-4xl py-6 mx-auto">
        <form action="{{ route('productos.update', $producto) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            @php
                $detalles = json_decode($producto->detalles_json, true) ?? [];
            @endphp

            <div class="mb-4">
                <label for="imagen" class="block text-sm font-medium">Imagen (opcional)</label>
                <input type="file" name="imagen" id="imagen" class="w-full">
                @if ($producto->imagen)
                    <p class="mt-1 text-sm text-gray-600">Imagen actual: <a
                            href="{{ asset('storage/' . $producto->imagen) }}" target="_blank"
                            class="underline text-blue-500">Ver</a></p>
                @endif
                @error('imagen')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="tipo" class="block text-sm font-medium">Tipo de producto *</label>
                <select name="tipo" id="tipo" class="w-full border-gray-300 rounded shadow-sm" required>
                    <option value="">-- Selecciona --</option>
                    <option value="cafe" {{ old('tipo', $producto->tipo) == 'cafe' ? 'selected' : '' }}>Café</option>
                    <option value="mora" {{ old('tipo', $producto->tipo) == 'mora' ? 'selected' : '' }}>Mora</option>
                </select>
                @error('tipo')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Campos personalizados del array detalles -->
            @foreach ([
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
        ] as $key => $label)
                <div class="mb-4">
                    <label for="detalles_{{ $key }}" class="block text-sm font-medium">{{ $label }}</label>
                    <textarea name="detalles[{{ $key }}]" id="detalles_{{ $key }}" rows="2"
                        class="w-full border-gray-300 rounded shadow-sm">{{ old("detalles.$key", $detalles[$key] ?? '') }}</textarea>
                </div>
            @endforeach

            <div class="mb-4">
                <label for="observaciones" class="block text-sm font-medium">Observaciones (opcional)</label>
                <textarea name="observaciones" id="observaciones" rows="3" class="w-full border-gray-300 rounded shadow-sm">{{ old('observaciones') }}</textarea>
                @error('observaciones')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit" class="px-4 py-2 text-white bg-green-600 rounded hover:bg-green-700">Actualizar</button>
            <!-- Botón volver -->
            <a href="{{ route('productos.index') }}"
                class="inline-block px-4 py-2 text-white bg-gray-600 rounded hover:bg-gray-700">
                Volver al listado
            </a>
        </form>
    </div>
@endsection
