<div id="modal-noticia-{{ $noticia->id_noticias }}"
    class="hidden fixed inset-0 z-50 items-center justify-center bg-black/50">
    <div class="w-full max-w-md p-6 bg-white rounded-lg shadow-lg">
        <h3 class="mb-4 text-lg font-bold text-gray-800">
            ¿Estás seguro de eliminar esta noticia?
        </h3>
        <p class="mb-4 text-gray-600">
            Esta acción no se puede deshacer. La noticia será eliminada permanentemente
            del sistema.
        </p>
        <form action="{{ route('noticias.destroy', $noticia) }}" method="POST">
            @csrf
            @method('DELETE')
            <div class="flex items-center">
                <button type="button" onclick="cerrarModal('noticia', '{{ $noticia->id_noticias }}')"
                    class="bg-[var(--color-textmarca)] hover:bg-[var(--color-texthovermarca)] 
                        py-3 px-4 rounded-full text-md font-bold text-white focus:outline-none 
                        focus:shadow-outline inline-flex items-center transition duration-150 
                        ease-in-out transform hover:-translate-x-1">
                    <img src="{{ asset('images/regresar.svg') }}" alt="Regresar" class="w-5 h-6 mr-2">
                    <span class="whitespace-nowrap text-inherit">{{ __('Cancelar') }}</span>
                </button>

                <button type="submit"
                    class="ml-40 bg-[var(--color-rechazar)] hover:bg-[var(--color-rechazar-hover)] 
                    py-3 px-4 rounded-full text-md font-bold text-white focus:outline-none 
                    focus:shadow-outline inline-flex items-center transition duration-150 
                    ease-in-out transform hover:translate-x-1">
                    <span class="whitespace-nowrap text-inherit">{{ __('Eliminar') }}</span>
                    <img src="{{ asset('images/siguiente.svg') }}" alt="siguiente" class="w-5 h-6 ml-2">
                </button>
            </div>
        </form>
    </div>
</div>
