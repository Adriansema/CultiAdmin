<x-app-layout>
    <x-slot name="header"><h2 class="text-xl font-semibold">Crear Producto</h2></x-slot>

<!-- actualizacion 08/04/2025-->

    <div class="max-w-4xl py-6 mx-auto">
        <form action="{{ route('productos.store') }}" method="POST" enctype="multipart/form-data">
            @include('productos._form')
            <x-button class="mt-4">Guardar</x-button>
        </form>
    </div>
</x-app-layout>
