@extends('layouts.app')
@section('content')
<div class="container">
    <h2>Agregar nuevo producto agrícola</h2>
    <form action="{{ route('productos-agricolas.store') }}" method="POST" enctype="multipart/form-data">
        @include('productos_agricolas._form')
    </form>
</div>
@endsection
