<footer class="w-full py-6 text-white transition-all duration-300 ease-in-out bg-black">
    <div class="px-4 mx-auto max-w-7xl">
        <div class="flex flex-col items-start justify-between md:flex-row md:items-center">
            <div class="flex flex-col items-start mb-4 md:mb-0">
                <img src="{{ asset('images/Loogoo.svg') }}" alt="Ícono Cultiva" class="w-auto h-10">
                <p class="text-sm text-gray-400"> © SENA </p>
            </div>

            <div class="grid grid-cols-2 gap-4 md:grid-cols-4">
                <div>
                    <p class="mb-3 text-lg font-semibold">Soporte</p>
                    @include('centroAyuda.partials.links')
                </div>

                <div class="relative">
                    <!-- Botón -->
                    <button id="openContact" class="flex px-4 py-2 space-y-2 text-white transition bg-blue-700 rounded-lg ring-2 ring-blue-700 hover:bg-blue-800">
                        Contáctanos
                    </button>

                    <!-- Fondo borroso (inicialmente oculto) -->
                    <div id="blurBackground" class="fixed inset-0 z-40 hidden bg-black bg-opacity-40 backdrop-blur-sm"></div>

                    <!-- Sidebar de contacto -->
                    <div id="contactSidebar" class="fixed top-0 right-0 z-50 h-full transition-transform duration-300 ease-in-out transform translate-x-full bg-black shadow-lg w-80">
                        @include('centroAyuda.partials.contact')
                    </div>
                </div>

                <div>
                    <h2 class="mb-3 text-xl font-bold uppercase">Síguenos</h2>
                    <div class="flex space-x-4">
                        <a href="#" class="text-gray-400 hover:text-blue-500">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M22 12c0-5.522-4.477-10-10-10S2 6.478 2 12c0 5 3.657 9.128 8.438 9.878v-6.988h-2.54v-2.89h2.54V9.797c0-2.506 1.493-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562v1.875h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 17 22 12z" />
                            </svg>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-sky-400">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M23 3a10.9 10.9 0 01-3.14 1.53A4.48 4.48 0 0022.4 1s-1.88.89-3.28 1.17A4.48 4.48 0 0012 6v1A10.66 10.66 0 013 4s-4 9 5 13a11.64 11.64 0 01-7 2c9 5 20 0 20-11.5a4.5 4.5 0 00-.08-.83A7.72 7.72 0 0023 3z" />
                            </svg>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-[#E1306C]">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M7.75 2A5.75 5.75 0 002 7.75v8.5A5.75 5.75 0 007.75 22h8.5A5.75 5.75 0 0022 16.25v-8.5A5.75 5.75 0 0016.25 2h-8.5zM12 7a5 5 0 110 10 5 5 0 010-10zm0 1.5a3.5 3.5 0 100 7 3.5 3.5 0 000-7zm5-2a1 1 0 110 2 1 1 0 010-2z" />
                            </svg>
                        </a>
                        <!-- YouTube -->
                        <a href="#" class="text-gray-400 hover:text-red-600">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M23.498 6.186a2.986 2.986 0 0 0-2.1-2.109C19.505 3.5 12 3.5 12 3.5s-7.505 0-9.398.577a2.986 2.986 0 0 0-2.1 2.109C0 8.09 0 12 0 12s0 3.91.502 5.814a2.986 2.986 0 0 0 2.1 2.109C4.495 20.5 12 20.5 12 20.5s7.505 0 9.398-.577a2.986 2.986 0 0 0 2.1-2.109C24 15.91 24 12 24 12s0-3.91-.502-5.814zM9.75 15.568V8.432L15.818 12 9.75 15.568z" />
                            </svg>
                        </a>
                        <!-- Discord -->
                        <a href="#" class="text-gray-400 hover:text-indigo-500">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M20.317 4.369a19.791 19.791 0 0 0-4.885-1.515.07.07 0 0 0-.074.035c-.21.371-.444.859-.608 1.24-1.844-.276-3.68-.276-5.486 0-.163-.393-.405-.87-.617-1.24a.07.07 0 0 0-.073-.035A19.736 19.736 0 0 0 3.677 4.37a.064.064 0 0 0-.03.027C.533 9.045-.32 13.579.099 18.057a.077.077 0 0 0 .028.051 19.978 19.978 0 0 0 5.995 3.033.07.07 0 0 0 .078-.027c.462-.63.873-1.295 1.226-1.994a.07.07 0 0 0-.038-.095 13.266 13.266 0 0 1-1.915-.92.07.07 0 0 1-.007-.116c.128-.096.256-.195.382-.291a.07.07 0 0 1 .074-.01c4.028 1.84 8.388 1.84 12.38 0a.07.07 0 0 1 .075.009c.126.096.254.195.382.291a.07.07 0 0 1-.006.116c-.612.35-1.249.652-1.916.921a.07.07 0 0 0-.037.094c.36.7.771 1.366 1.225 1.996a.07.07 0 0 0 .079.028 19.934 19.934 0 0 0 6-3.033.077.077 0 0 0 .028-.05c.5-5.177-.838-9.671-3.548-13.661a.062.062 0 0 0-.031-.028zM8.02 15.331c-1.183 0-2.156-1.085-2.156-2.419 0-1.334.955-2.419 2.156-2.419 1.2 0 2.155 1.085 2.155 2.419 0 1.334-.955 2.419-2.155 2.419z" />
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <hr class="my-8 border-gray-700">

        <div class="mt-8 text-sm text-center text-gray-200">
            © {{ date('Y') }} Cultiva SENA. Todos los derechos reservados.
        </div>
    </div>
</footer>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const openBtn = document.getElementById('openContact');
        const sidebar = document.getElementById('contactSidebar');
        const blurBg = document.getElementById('blurBackground');

        openBtn.addEventListener('click', () => {
            sidebar.classList.remove('translate-x-full');
            blurBg.classList.remove('hidden');
        });

        blurBg.addEventListener('click', () => {
            sidebar.classList.add('translate-x-full');
            blurBg.classList.add('hidden');
        });
    });
    </script>

