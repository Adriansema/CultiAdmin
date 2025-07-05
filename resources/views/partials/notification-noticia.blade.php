<div class="space-y-4 noticias-scroll-container">
    @forelse ($noticias as $noticia)
        <div id="noticia-{{ $noticia->id_noticias }}"
            class="bg-white p-4 rounded-2xl shadow-sm mb-4 hover:bg-[var(--color-hovercaja)]">
            <div class="flex">
                <div class="flex-shrink-0 mr-2">
                    <img src="{{ asset('images/infor_noti.svg') }}" class="h-6 w-7" alt="Información de la noticia">
                </div>

                <div class="flex-grow min-w-0">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="text-base font-bold text-[var(--color-textmarca)] truncate">
                            {{ Str::limit($noticia->titulo ?? 'Sin título', 60, '...') }}
                        </h3>
                        <span class="flex items-center flex-shrink-0 ml-4 text-base font-normal text-gray-500">
                            <img src="{{ asset('images/hora.svg') }}" class="w-8 h-5 mr-1"
                                alt="Información de la noticia">
                            {{ $noticia->created_at->diffForHumans() }}
                        </span>
                    </div>

                    <p class="mb-3 text-sm text-gray-700 line-clamp-3">
                        {{ $noticia->informacion ?? 'No hay información disponible.' }}</p>

                    <div class="flex space-x-2 font-semibold text-md">
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
    @endforelse
</div>
