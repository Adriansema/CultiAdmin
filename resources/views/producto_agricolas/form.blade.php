@csrf
<div class="mb-3">
    <label>Nombre:</label>
    <input type="text" name="nombre" value="{{ old('nombre', $productoAgricola->nombre ?? '') }}" class="form-control">
</div>

<div class="mb-3">
    <label>Tipo:</label>
    <input type="text" name="tipo" value="{{ old('tipo', $productoAgricola->tipo ?? '') }}" class="form-control">
</div>

<div class="mb-3">
    <label>Suelo:</label>
    <input type="text" name="suelo" value="{{ old('suelo', $productoAgricola->suelo ?? '') }}" class="form-control">
</div>

<div class="mb-3">
    <label>Características:</label>
    <textarea name="caracteristicas" class="form-control">{{ old('caracteristicas', $productoAgricola->caracteristicas ?? '') }}</textarea>
</div>

<div class="mb-3">
    <label>Imagen:</label>
    <input type="file" name="imagen" class="form-control">
</div>

<button type="submit" class="btn btn-success">Guardar</button>
