<x-app-layout>
    <x-slot name="header"><h2 class="text-xl font-semibold">Crear Boletín</h2></x-slot>

    <div class="max-w-4xl py-6 mx-auto">
        <form action="{{ route('boletines.store') }}" method="POST">
            @include('boletines._form')
            <x-button class="mt-4">Guardar</x-button>
        </form>
    </div>
</x-app-layout>
