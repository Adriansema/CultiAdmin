<div class="relative flex items-center w-full max-w-xl group"> <input type="text" id="searchInput" placeholder="Buscar Usuario" class="form-control border border-[var(--color-ajustes)] hover:border-[var(--color-hover)] rounded-2xl pr-10 py-2 w-full" />
    <img src="{{ asset('images/search.svg') }}" class="w-4 h-5 absolute right-3" alt="icono de búsqueda">
    <img src="{{ asset('images/Equis.svg') }}" class="w-4 h-5 absolute right-3" alt="icono de búsqueda">
    <img src="{{ asset('images/Equis-Hover.svg') }}" class="w-4 h-5 absolute right-3" alt="icono de búsqueda">
</div>
<ul id="courseList">
    @foreach ($usuarios as $usuario)
        <li class="course-item">{{ $usuario->fullname }}</li>
    @endforeach 
</ul>

