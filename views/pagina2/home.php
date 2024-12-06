<section class="bg-custom-dark text-white py-24 overflow-hidden relative">
    <div class="container mx-auto px-6 flex flex-col lg:flex-row items-center space-y-10 lg:space-y-0">
        <!-- Texto destacado -->
        <div class="lg:w-1/2">
            <h1 class="text-5xl lg:text-7xl font-extrabold mb-8 leading-tight animate__animated animate__fadeInUp">
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-400 to-blue-600">
                    Vive la pasión del deporte
                </span>
                <br>con Full Sports
            </h1>
            <p class="text-lg lg:text-2xl mb-10 animate__animated animate__fadeInUp" style="animation-delay: 0.2s;">
                Sigue a tus equipos favoritos, analiza estadísticas en tiempo real y conecta con otros fanáticos del deporte.
            </p>
            <div class="flex flex-col sm:flex-row items-center space-y-4 sm:space-y-0 sm:space-x-4">
                <a href="#" class="bg-gradient-to-r from-blue-500 to-blue-700 text-white px-8 py-4 rounded-full font-bold shadow-lg hover:shadow-xl transition duration-300 transform hover:scale-110">
                    Comienza ahora
                </a>
                <a href="#" class="bg-gradient-to-r from-red-500 to-red-700 text-white px-8 py-4 rounded-full font-bold shadow-lg hover:shadow-xl transition duration-300 transform hover:scale-110">
                    Explora deportes
                </a>
            </div>
        </div>

<!-- Imagen con caja informativa centrada debajo -->
<div class="lg:w-1/2 relative w-full h-[300px] sm:h-[400px] md:h-[500px] lg:h-[600px] animate__animated animate__fadeInUp">
    <div class="absolute inset-0 bg-gradient-to-r from-custom-blue-20 to-transparent rounded-lg"></div>
    <div class="relative w-full h-full overflow-hidden">
        <img src="./public/img/fondo1.png" alt="Jugador de fútbol en acción" 
             class="object-contain w-full h-full transition-transform duration-500 ease-in-out transform hover:scale-105"
             style="filter: drop-shadow(0 0 30px rgba(59, 130, 246, 0.4));">
    </div>

    <!-- Caja informativa centrada -->
    <div class="absolute bottom-2 left-1/2 transform -translate-x-1/2 translate-y-1/2 bg-white/10 backdrop-blur-lg p-8 rounded-xl shadow-2xl">
        <div class="flex items-center space-x-6">
            <div class="bg-custom-blue p-4 rounded-full">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                </svg>
            </div>
            <div>
                <p class="font-extrabold text-xl text-white">Super League 2024</p>
                <p class="text-gray-300">¡Únete ahora!</p>
            </div>
        </div>
    </div>
</div>

    </div>
</section>

<style>
    /* Estilo actualizado */
    .bg-custom-dark { background-color: #111827; } /* Gris oscuro */
    .bg-custom-blue { background-color: #3B82F6; } /* Azul */
    .from-custom-blue-20 { --tw-gradient-from: rgba(56, 189, 248, 0.2); }

    .animate__animated {
        animation-duration: 1s;
        animation-fill-mode: both;
    }
    .animate__fadeInUp {
        animation-name: fadeInUp;
    }
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translate3d(0, 100%, 0);
        }
        to {
            opacity: 1;
            transform: translate3d(0, 0, 0);
        }
    }
</style>
