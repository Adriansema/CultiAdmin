@csrf

<div class="mb-4">
    <label for="nombre" class="block text-sm font-medium">Nombre del producto *</label>
    <input type="text" name="nombre" id="nombre" class="w-full border-gray-300 rounded shadow-sm"
           value="{{ old('nombre', $producto->nombre ?? '') }}" required>
    @error('nombre')
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>

<div class="mb-4">
    <label for="descripcion" class="block text-sm font-medium">Descripción *</label>
    <textarea name="descripcion" id="descripcion" rows="4" class="w-full border-gray-300 rounded shadow-sm" required>{{ old('descripcion', $producto->descripcion ?? '') }}</textarea>
    @error('descripcion')
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>

<div class="mb-4">
    <label for="imagen" class="block text-sm font-medium">Imagen (opcional)</label>
    <input type="file" name="imagen" id="imagen" class="w-full">
    @error('imagen')
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
    @enderror

    @if(!empty($producto?->imagen))
        <p class="mt-2 text-sm text-gray-600">Imagen actual:</p>
        <img src="{{ asset('storage/' . $producto->imagen) }}" alt="Imagen actual" class="w-32 h-auto mt-2 rounded shadow">
    @endif
</div>
