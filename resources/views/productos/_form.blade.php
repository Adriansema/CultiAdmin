@csrf

<div class="mb-4">
    <x-label for="nombre" :value="'Nombre del Producto'" />
    <x-input id="nombre" name="nombre" type="text" class="w-full" value="{{ old('nombre', $producto->nombre ?? '') }}" required />
</div>

<div class="mb-4">
    <x-label for="descripcion" :value="'Descripción'" />
    <textarea id="descripcion" name="descripcion" class="w-full border-gray-300 rounded-md shadow-sm" required>{{ old('descripcion', $producto->descripcion ?? '') }}</textarea>
</div>

<div class="mb-4">
    <x-label for="imagen" :value="'Imagen'" />
    <input type="file" name="imagen" class="block w-full text-sm text-gray-500">
    @isset($producto->imagen)
        <img src="{{ asset('storage/' . $producto->imagen) }}" class="h-24 mt-2">
    @endisset
</div>
