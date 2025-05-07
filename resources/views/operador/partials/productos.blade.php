@if ($productos->count())
    <div class="space-y-4">
        @foreach ($productos as $producto)
            <div class="p-4 bg-white rounded-lg shadow">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-800">{{ $producto->titulo }}</h3>
                    <span class="text-sm px-2 py-1 rounded
                        {{
                            $producto->estado === 'aprobado' ? 'bg-green-100 text-green-800' :
                            ($producto->estado === 'rechazado' ? 'bg-red-100 text-red-800' :
                            'bg-yellow-100 text-red-800')
                        }}">
                        {{ ucfirst($producto->estado) }}
                    </span>
                </div>

                <p class="mt-1 text-sm text-gray-600">{{ $producto->descripcion }}</p>
                <p class="mt-2 text-xs text-gray-400">Publicado el {{ $producto->created_at->format('d/m/Y H:i') }}</p>

                {{-- Mostrar observaciones si existen --}}
                @if ($producto->observaciones)
                    <p class="mt-2 text-sm text-red-600">Observaciones: {{ $producto->observaciones }}</p>
                @endif

                {{-- Enlace a detalles --}}
                <a href="{{ route('operador.productos.show', $producto->id) }}"
                   class="inline-block mt-2 text-sm text-blue-600 hover:underline">
                    Ver detalles →
                </a>
            </div>
        @endforeach
    </div>

    <div class="mt-4">
        {{ $productos->links() }}
    </div>
@else
    <p class="text-sm text-gray-500">No se encontraron productos con los filtros seleccionados.</p>
@endif
