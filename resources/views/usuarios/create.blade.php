<x-app-layout>
    <x-slot name="header"><h2 class="text-xl font-semibold">Crear Usuario</h2></x-slot>

    <div class="max-w-4xl py-6 mx-auto">
        <form action="{{ route('usuarios.store') }}" method="POST">
            @include('usuarios._form')
            <x-button class="mt-4">Guardar</x-button>
        </form>
    </div>
</x-app-layout>
