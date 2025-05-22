
{{-- Maneja tres estado diferentes: el input con el icono de lupa, cuando se presiona el input cambia de icono por la x(red) y a medidas que escribes y deseas eliminar lo que escrbiste pasas
el cursor por la x(red) y automaticamente cambia de icono como si fuera un hover:bg... y se elimina lo que escribiste, pero claro funciona si solo le das en ese icono de resto no --}}
<div class="relative flex items-center w-full max-w-xl">
    <input type="text" id="searchInput" placeholder="Buscar Usuario"
        class="form-control border border-[var(--color-ajustes)] hover:border-[var(--color-hover)] rounded-2xl pr-10 py-2 w-full
                  focus:border-[var(--color-hover)] focus:outline-none focus:ring-0" />

    <img src="{{ asset('images/search.svg') }}" id="searchIcon"
        class="w-4 h-5 absolute right-3 top-1/2 -translate-y-1/2 cursor-pointer" alt="icono de búsqueda">

    <div id="clearIconContainer" class="w-4 h-5 absolute right-3 top-1/2 -translate-y-1/2 cursor-pointer hidden group">
        <img src="{{ asset('images/Equis.svg') }}" class="w-3 h-4 group-hover:hidden" alt="icono de borrado">
        <img src="{{ asset('images/Equis-Hover.svg') }}" class="w-4 h-4 hidden group-hover:block"
            alt="icono de borrado en hover">
    </div>
</div>

<ul id="userList" class="mt-4 border border-gray-300 rounded-lg divide-y divide-gray-200">
    @foreach ($usuarios as $usuario)
        {{-- <li class="course-item p-2">{{ $usuario->fullname }}</li> --}}
        {{-- CONJUGA MUY BIEN Y DA LAS RESPUESTA ESPERADAS, PERO LO UNICO ES QUE SE SALE DEL DISEÑO Y ADEMAS COMO ES UN UL LISTA DESORDENADA NO ES LO MISMO COMO UNA TABLA QUE 
        TE MUESTRA CON DETALLES Y ORGANIZADO --}} 
        {{-- <li class="course-item p-2"> 
            <strong>Nombre:</strong> {{ $usuario->name }} <br>
            <strong>Email:</strong> {{ $usuario->email }} <br>
            <strong>Estado:</strong> {{ $usuario->estado }} <br>
            <strong>Rol:</strong>
            @if ($usuario->roles->isNotEmpty())
                {{ $usuario->roles->first()->name }}
            @else
                Sin Rol
            @endif
        </li> --}}
    @endforeach
</ul>

{{-- Para que la paginación funcione, la respuesta JSON de Laravel incluye links.
     Si quieres que la paginación se actualice también, necesitarás renderizarla dinámicamente con JS.
     Por ahora, la paginación de Laravel Blade es solo para la carga inicial.
     Si quieres paginación AJAX, necesitas un contenedor para ella y lógica JS.
--}}
