@extends('layouts.app')

@section('content')
    <div class="max-w-2xl px-8 pt-6 pb-8 mx-auto mt-10 bg-white rounded shadow-md">
        <h2 class="mb-6 text-2xl font-bold">Importar Boletines desde PDF</h2>
        @if ($errors->any())
            <div class="px-4 py-3 mb-4 text-red-700 bg-red-100 border border-red-400 rounded">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>• {{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('boletines.importarPdf') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="mb-4">
                <label class="block mb-2 text-sm font-bold text-gray-700">Archivo PDF:</label>
                <input type="file" name="archivo" accept=".pdf"
                    class="w-full px-3 py-2 leading-tight text-gray-700 border rounded shadow appearance-none focus:outline-none focus:shadow-outline"
                    required>
            </div>

            <div class="flex items-center justify-between">
                <button type="submit"
                    class="px-4 py-2 font-bold text-white bg-blue-500 rounded hover:bg-blue-700 focus:outline-none focus:shadow-outline">
                    Importar
                </button>
            </div>
        </form>
    </div>
@endsection
