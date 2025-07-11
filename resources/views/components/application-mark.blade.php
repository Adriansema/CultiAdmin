<div class="flex items-center justify-center w-56 px-1 py-3">
    <img x-show="sidebarOpen"
         src="{{ asset('images/Loogoo.svg') }}"
         alt="Logo Completo"
         class="w-64 h-10 ml-12 transition-all duration-300" />

    <img x-show="!sidebarOpen"
         src="{{ asset('images/C.svg') }}"
         alt="Logo C"
         class="w-auto h-10 transition-all duration-300 **mr-auto**" />
</div>