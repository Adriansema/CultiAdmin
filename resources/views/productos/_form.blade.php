@csrf

<!-- actualizacion 09/04/2025 -->

<div class="mb-4">
    <x-label for="nombre" :value="'Nombre del producto'" />
    <x-input id="nombre" name="nombre" type="text" class="w-full" value="{{ old('nombre', $producto->nombre ?? '') }}" required />
</div>

<div class="mb-4">
    <x-label for="descripcion" :value="'Descripción'" />
    <textarea id="descripcion" name="descripcion" class="w-full border-gray-300 rounded shadow-sm" rows="4" required>{{ old('descripcion', $producto->descripcion ?? '') }}</textarea>
</div>

<div class="mb-4">
    <x-label for="imagen" :value="'Imagen (opcional)'" />
    <input id="imagen" name="imagen" type="file" class="w-full border-gray-300 rounded shadow-sm" />
</div>
