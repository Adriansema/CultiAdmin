<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800">Editar Producto</h2>
    </x-slot>

    <div class="max-w-4xl py-6 mx-auto">
        <form action="{{ route('productos.update', $producto) }}" method="POST" enctype="multipart/form-data">
            @method('PUT')
            @include('productos._form')
            <x-button class="mt-4">Actualizar</x-button>
        </form>
    </div>
</x-app-layout>
