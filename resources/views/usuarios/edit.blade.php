<x-app-layout>
    <x-slot name="header"><h2 class="text-xl font-semibold">Editar Usuario</h2></x-slot>

    <div class="max-w-4xl py-6 mx-auto">
        <form action="{{ route('usuarios.update', $usuario) }}" method="POST">
            @method('PUT')
            @include('usuarios._form')
            <x-button class="mt-4">Actualizar</x-button>
        </form>
    </div>
</x-app-layout>
