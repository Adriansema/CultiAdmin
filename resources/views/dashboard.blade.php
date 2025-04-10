@extends('layouts.app')

@section('content')

<section id="banner" class="w-full bg-white shadow rounded-lg overflow-hidden">
        <!-- Aquí va la imagen de fondo, título, logo y campesino -->
        <div class="relative w-full h-80 md:h-100 bg-banner"> <!-- Usa la clase personalizada aquí -->
            
        </div>
    </section>

<main>
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
