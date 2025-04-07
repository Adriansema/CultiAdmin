@csrf

<div class="mb-4">
    <x-label for="asunto" :value="'Asunto'" />
    <x-input id="asunto" name="asunto" type="text" class="w-full" value="{{ old('asunto', $boletin->asunto ?? '') }}" required />
</div>

<div class="mb-4">
    <x-label for="contenido" :value="'Contenido'" />
    <textarea id="contenido" name="contenido" class="w-full border-gray-300 rounded shadow-sm" rows="6" required>{{ old('contenido', $boletin->contenido ?? '') }}</textarea>
</div>
