<div id="modal-validar-noticias-{{ $noticia->id_noticias }}"
    class="hidden fixed inset-0 z-50 items-center justify-center bg-black/50">
    <div class="w-full max-w-md p-6 bg-white rounded-lg shadow-lg">
        <h3 class="mb-4 text-lg font-bold text-gray-800">Observaciones de la validación</h3>
        <form action="{{ route('pendientes.noticias.validar', $noticia->id_noticias) }}" method="POST">
            @csrf
            <textarea name="observaciones" class="w-full p-2 border border-gray-300 rounded-md mb-6" rows="4"></textarea>
            @error('observaciones')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
            <div class="flex items-center justify-between">
                <button type="button" onclick="cerrarModal('validar-noticias', '{{ $noticia->id_noticias }}')"
                    class="bg-[var(--color-textmarca)] hover:bg-[var(--color-texthovermarca)] 
                        py-3 px-4 rounded-full text-md font-bold text-white focus:outline-none 
                        focus:shadow-outline inline-flex items-center transition duration-150 
                        ease-in-out transform hover:-translate-x-1">
                    <img src="{{ asset('images/regresar.svg') }}" alt="Regresar" class="w-5 h-6 mr-2">
                    <span class="whitespace-nowrap text-inherit">{{ __('Cancelar') }}</span>
                </button>

                <button type="submit"
                    class="bg-[var(--color-sgt)] hover:bg-[var(--color-hoversgt)] 
                    py-3 px-4 rounded-full text-md font-bold text-white focus:outline-none 
                    focus:shadow-outline inline-flex items-center transition duration-150 
                    ease-in-out transform hover:translate-x-1">
                    <span class="whitespace-nowrap text-inherit">{{ __('Validar') }}</span>
                    <img src="{{ asset('images/siguiente.svg') }}" alt="siguiente" class="w-5 h-6 ml-2">
                </button>
            </div>
        </form>
    </div>
</div>
