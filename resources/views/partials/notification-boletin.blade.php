<div class="boletines-scroll-container space-y-4">
    @forelse ($boletines as $boletin)
        <div id="boletin-{{ $boletin->id }}"
            class="bg-white p-4 rounded-2xl shadow-sm mb-4 hover:bg-[var(--color-hovercaja)]">
            <div class="flex items-center">
                <div class="flex-shrink-0 mr-2">
                    <img src="{{ asset('images/file-pdf.svg') }}" class="w-12 h-12" alt="Icono de PDF">
                </div>

                <div class="flex-grow min-w-0 flex flex-col"> 
                    <h3 class="text-base font-extrabold text-[var(--color-textmarca)] truncate">
                        {{ Str::limit($boletin->nombre ?? 'Sin nombre', 60, '...') }}
                    </h3>
                    <p class="text-sm text-gray-700 line-clamp-1"> 
                        {{ Str::limit($boletin->descripcion ?? 'No hay descripción disponible.', 50, '...') }}
                    </p>
                </div>

                @if ($boletin->archivo)
                    <a href="{{ route('boletines.download', $boletin->id) }}" class="ml-auto flex-shrink-0 group"> 
                         <img src="{{ asset('images/descargar.svg') }}"
                            class="w-10 h-10 relative inset-0 block group-hover:hidden" alt="Icono de Ver Todo">
                        <img src="{{ asset('images/hoverDes.svg') }}"
                            class="w-10 h-10 relative inset-0 hidden group-hover:block" alt="Icono de Ver Todo hover">
                    </a>
                @else
                    {{-- Puedes poner un placeholder si no hay archivo, o dejarlo vacío --}}
                @endif
            </div>
        </div>
    @empty
        <p class="text-gray-700 p-4 bg-white rounded-lg shadow-md no-boletines-message">No hay boletines recientes para
            mostrar.</p>
    @endforelse
</div>
