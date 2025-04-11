@if ($boletines->count())
    <div class="space-y-4">
        @foreach ($boletines as $boletin)
            <div class="p-4 bg-white rounded-lg shadow">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-800">{{ $boletin->asunto }}</h3>
                    <span class="text-sm px-2 py-1 rounded
                        {{
                            $boletin->estado === 'aprobado' ? 'bg-green-100 text-green-800' :
                            ($boletin->estado === 'rechazado' ? 'bg-red-100 text-red-800' :
                            'bg-yellow-100 text-yellow-800')
                        }}">
                        {{ ucfirst($boletin->estado) }}
                    </span>
                </div>

                <p class="mt-1 text-sm text-gray-600">{{ Str::limit($boletin->contenido, 100) }}</p>
                <p class="mt-2 text-xs text-gray-400">Publicado el {{ $boletin->created_at->format('d/m/Y H:i') }}</p>

                {{-- Mostrar observaciones si existen --}}
                @if ($boletin->observaciones)
                    <p class="mt-2 text-sm text-red-600">Observaciones: {{ $boletin->observaciones }}</p>
                @endif

                {{-- Enlace a detalles --}}
                <a href="{{ route('operador.boletines.show', $boletin->id) }}"
                   class="inline-block mt-2 text-sm text-blue-600 hover:underline">
                    Ver detalles →
                </a>
            </div>
        @endforeach
    </div>

    <div class="mt-4">
        {{ $boletines->links() }}
    </div>
@else
    <p class="text-sm text-gray-500">No se encontraron boletines con los filtros seleccionados.</p>
@endif
