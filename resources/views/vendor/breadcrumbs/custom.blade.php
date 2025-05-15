{{-- CON ENLACE --}}
 <div class="breadcrumb flex items-center">
    @foreach ($breadcrumbs as $key => $breadcrumb)
        @if ($key < count($breadcrumbs) - 1)
            <a href="{{ $breadcrumb->url }}" class="opacity-75 hover:opacity-100 transition font-medium">{{ $breadcrumb->title }}</a>
            <img src="{{ asset('images/miga.svg') }}" class="w-4 h-3" alt="Flechita"> 
        @else
            <div class="text-lg font-bold">{{ $breadcrumb->title }}</div> 
        @endif
    @endforeach
</div>