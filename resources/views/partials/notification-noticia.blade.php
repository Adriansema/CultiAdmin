<div class="noticias-scroll-container space-y-4">
    @forelse ($noticias as $noticia)
        {{-- Cada noticia tendrá un ID único para el JavaScript --}}
        <div id="noticia-{{ $noticia->id_noticias }}" class="p-4 rounded-lg">
            <div class="flex"> {{-- Contenedor principal flex para el icono y el bloque de contenido --}}
                {{-- Columna del Icono (con ancho fijo para la indentación) --}}
                <div class="flex-shrink-0 mr-2"> {{-- mr-2 para un pequeño espacio entre el icono y el texto --}}
                    <img src="{{ asset('images/infor_noti.svg') }}" class="w-7 h-6" alt="informacion de la noticia">
                </div>

                {{-- Columna del Contenido (Título, Información, Botones) --}}
                <div class="flex-grow min-w-0"> {{-- flex-grow para ocupar el espacio restante --}}
                    {{-- Fila del Título y Hora --}}
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="text-base font-bold text-[var(--color-textmarca)] truncate">
                            {{ Str::limit($noticia->titulo ?? 'Sin título', 60, '...') }}
                        </h3>
                        <span class="text-base font-normal text-gray-500 ml-4 flex-shrink-0 flex items-center">
                            <img src="{{ asset('images/hora.svg') }}" class="w-8 h-5 mr-1"
                                alt="informacion de la noticia">
                            {{ $noticia->created_at->diffForHumans() }}
                        </span>
                    </div>

                    {{-- Contenido de la noticia --}}
                    <p class="text-sm text-gray-700 mb-3 line-clamp-3">
                        {{ $noticia->informacion ?? 'No hay información disponible.' }}</p>

                    {{-- Botones de acción --}}
                    <div class="flex space-x-2 text-md font-semibold"> {{-- CAMBIO: Usamos space-x-2 para separación mínima --}}
                        <button data-noticia-id="{{ $noticia->id_noticias }}"
                            class="mark-as-read-btn text-[var(--color-textmarca)] hover:bg-[var(--color-hovermarca)] hover:text-white rounded-lg px-2 focus:outline-none">
                            Marcar como leído
                        </button>
                        @if ($noticia && $noticia->id_noticias)
                            <a href="{{ route('noticias.show', $noticia->id_noticias) }}"
                                class="text-[var(--color-textver)] hover:bg-[var(--color-hover)] hover:text-white px-2 rounded-lg">
                                Ver más
                            </a>
                        @else
                            <span class="text-gray-400">No disponible</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @empty
        <p class="text-gray-600 p-4">No hay noticias recientes para mostrar.</p>
    @endforelse
</div>
