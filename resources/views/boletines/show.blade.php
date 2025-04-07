<x-app-layout>
    <x-slot name="header"><h2 class="text-xl font-semibold">Ver Boletín</h2></x-slot>

    <div class="max-w-4xl py-6 mx-auto">
        <div class="p-6 bg-white rounded shadow">
            <h3 class="text-lg font-bold">{{ $boletin->asunto }}</h3>
            <p class="mt-4 text-gray-700 whitespace-pre-line">{{ $boletin->contenido }}</p>
        </div>
    </div>
</x-app-layout>
