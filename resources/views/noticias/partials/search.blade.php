<div class="relative flex items-center w-full max-w-xl">
    <input type="text"
           id="buscar-noticia-input"
           name="q"
           placeholder="Buscar noticia"
           class="form-control border border-[var(--color-ajustes)] hover:border-[var(--color-hover)] rounded-2xl pr-10 py-3 w-full
                  focus:border-[var(--color-hover)] focus:outline-none focus:ring-0"
           value="{{ request('q') }}">

    {{-- icono de Lupa (search.svg) --}}
    <img src="{{ asset('images/search.svg') }}"
         id="searchIcon"
         class="w-5 h-5 absolute right-3 top-1/2 -translate-y-1/2 {{ request('q') ? 'hidden' : '' }}"
         alt="icono de busqueda">

    {{-- Contenedor del icono de Borrar (Equis.svg / Equis-Hover.svg) --}}
    <div id="clearIconContainer"
         class="w-4 h-5 absolute right-3 top-1/2 -translate-y-1/2 cursor-pointer group {{ request('q') ? '' : 'hidden' }}">
        <img src="{{ asset('images/Equis.svg') }}"
             class="w-3 h-5 group-hover:hidden"
             alt="icono de borrado">
        <img src="{{ asset('images/Equis-Hover.svg') }}"
             class="w-4 h-5 hidden group-hover:block"
             alt="icono de borrado en hover">
    </div>
</div>