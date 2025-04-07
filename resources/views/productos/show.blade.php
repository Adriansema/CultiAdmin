<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800">Detalle del Producto</h2>
    </x-slot>

    <div class="max-w-3xl py-6 mx-auto">
        <div class="p-6 bg-white rounded shadow">
            <h3 class="text-lg font-bold">{{ $producto->nombre }}</h3>
            <p class="mt-2 text-gray-700">{{ $producto->descripcion }}</p>

            @if ($producto->imagen)
                <img src="{{ asset('storage/' . $producto->imagen) }}" class="mt-4 rounded-md max-h-64">
            @endif

            <div class="mt-4 text-sm text-gray-600">
                Estado: <strong class="capitalize">{{ $producto->estado }}</strong><br>
                @if ($producto->observaciones)
                    Observaciones: <em>{{ $producto->observaciones }}</em>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
