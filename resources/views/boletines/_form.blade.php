@csrf

<div class="mb-4">
    <x-label for="asunto" :value="'Asunto'" />
    <x-input id="asunto" name="asunto" type="text" class="w-full"
             value="{{ old('asunto', $boletin->asunto ?? '') }}" required />
    @error('asunto')
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>

<div class="mb-4">
    <x-label for="contenido" :value="'Contenido'" />
    <textarea id="contenido" name="contenido" rows="6"
              class="w-full border-gray-300 rounded shadow-sm" required>{{ old('contenido', $boletin->contenido ?? '') }}</textarea>
    @error('contenido')
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>
