<div class="relative flex items-center w-full">
    {{-- ! Un campo de texto (<input>) para escribir la búsqueda, con estilos que cambian al pasar el ratón o al enfocarlo. --}}
    <input type="text" id="SearchLive" name="q" placeholder="Buscar Producto"
        class="form-control border border-[var(--color-ajustes)] hover:border-[var(--color-hover)] rounded-2xl pr-10 py-2 w-full
        focus:border-[var(--color-hover)] focus:outline-none focus:ring-0" value="{{ request('q') }}"/>

    {{-- ! Un icono de lupa (search.svg) visible por defecto a la derecha del campo. --}}
    <img src="{{ asset('images/search.svg') }}" id="searchIcon"
        class="w-4 h-5 absolute right-3 top-1/2 -translate-y-1/2 cursor-pointer" alt="icono de búsqueda">

    <div id="clearIconContainer" class="w-4 h-5 absolute right-3 top-1/2 -translate-y-1/2 cursor-pointer hidden group">
        {{-- ! Un icono de "X" (Equis.svg y Equis-Hover.svg) oculto que aparece cuando el usuario empieza a escribir. Este icono, al hacer clic, permite borrar el texto del campo de búsqueda y desaparece. --}}
        <img src="{{ asset('images/Equis.svg') }}" class="w-3 h-4 group-hover:hidden" alt="icono de borrado">
        <img src="{{ asset('images/Equis-Hover.svg') }}" class="w-4 h-4 hidden group-hover:block"
            alt="icono de borrado en hover">
    </div>
    {{-- ? La funcionalidad es la de un componente de UI para búsqueda que ofrece una experiencia visual intuitiva para buscar y borrar texto. --}}
</div>