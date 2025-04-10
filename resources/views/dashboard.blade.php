@extends('layouts.app')

@section('content')
<main>

    {{-- 🟩 Banner principal con logo y campesino --}}
    <section id="banner" class="w-full bg-white shadow rounded-lg overflow-hidden">
    <!-- Aquí va la imagen de fondo, título, logo y campesino -->
    <div class="relative w-full h-56 md:h-64 bg-banner"> <!-- Usa la clase personalizada aquí -->
        <!-- Fondo oscuro que se superpone a la imagen -->
        <div class="absolute inset-0 bg-black bg-opacity-30"></div>

        <div class="relative z-10 flex flex-col md:flex-row items-center justify-between h-full px-6 md:px-12 py-6 md:py-10">
            {{-- Texto y logo --}}
            <div class="text-white space-y-2">
                <h1 class="text-2xl md:text-4xl font-bold">Panel <br class="hidden md:block"> de Administración</h1>
                <img src="{{ asset('images/logo-cultiva.png') }}" alt="Logo Cultiva Sena" class="h-10 md:h-14 mt-2">
            </div>

            {{-- Imagen del campesino --}}
            <div class="mt-4 md:mt-0">
                <img src="{{ asset('images/campesino.png') }}" alt="Campesino" class="h-40 md:h-52 object-contain">
            </div>
        </div>
    </div>
   </section>



    {{--  Sección de gráfica de usuarios conectados --}}
    <section id="usuarios-conectados" class="bg-white shadow rounded-lg p-6">
        <!-- Título + filtros + gráfica -->
    </section>

    {{--  Métricas generales (Usuarios, Registrados, etc) --}}
    <section id="metricas" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- 4 tarjetas métricas -->
    </section>

    {{--  Novedades y  Boletines --}}
    <section id="novedades-boletines" class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Novedades -->
        <div id="novedades" class="bg-white shadow rounded-lg p-6 lg:col-span-2">
            <!-- Aquí van las novedades -->
        </div>

        <!-- Boletines -->
        <div id="boletines" class="bg-white shadow rounded-lg p-6">
            <!-- Aquí van los boletines -->
        </div>

    </section>

</main>
@endsection
