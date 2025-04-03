@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">Agregar Boletín</h1>

    <form action="{{ route('boletines.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="asunto" class="form-label">Asunto</label>
            <input type="text" name="asunto" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="contenido" class="form-label">Contenido</label>
            <textarea name="contenido" class="form-control" rows="3" required></textarea>
        </div>
        <button type="submit" class="btn btn-success">Guardar</button>
        <a href="{{ route('boletines.index') }}" class="btn btn-secondary">Volver</a>
    </form>
</div>
@endsection
