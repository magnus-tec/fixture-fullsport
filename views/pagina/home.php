



<section class="bg-dark-gradient text-white py-24 overflow-hidden relative">
    <div class="container mx-auto px-6 flex flex-col lg:flex-row items-center gap-10">
        <!-- Texto destacado -->
        <div class="lg:w-1/2">
            <h1 class="text-5xl lg:text-7xl font-extrabold mb-8 leading-tight animate__fadeInUp">
                <span class="text-gradient bg-clip-text bg-gradient-to-r from-blue-400 via-purple-500 to-blue-600">
                    Vive la pasión del deporte
                </span>
                <br>con Full Sports
            </h1>
            <p class="text-lg lg:text-2xl mb-10 animate__fadeInUp delay-200">
                Sigue a tus equipos favoritos, analiza estadísticas en tiempo real y conecta con otros fanáticos del deporte.
            </p>
            <div class="flex flex-col sm:flex-row items-center gap-4">
                <a href="#" class="btn-primary">
                    Crear Torneo
                </a>
                <a href="#" class="btn-secondary">
                    Empezar Ahora
                </a>
            </div>
        </div>

        <!-- Imagen con caja informativa -->
        <div class="lg:w-1/2 relative">
            <div class="relative w-full h-[300px] sm:h-[400px] md:h-[500px] lg:h-[600px]">
                <img src="./public/img/fondo1.png" alt="Jugador de fútbol en acción" 
                     class="w-full h-full object-cover rounded-lg shadow-lg hover:scale-105 transition-transform duration-500 ease-in-out">
            </div>

            <!-- Caja informativa -->
            <div class="absolute bottom-4 left-1/2 transform -translate-x-1/2 bg-white/10 backdrop-blur-md p-6 rounded-lg shadow-2xl text-center">
                <div class="flex items-center justify-center space-x-4">
                    <div class="bg-primary p-4 rounded-full">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                        </svg>
                    </div>
                    <div>
                        <p class="font-extrabold text-lg text-white">Full sport play</p>
                        <p class="text-sm text-gray-300">¡Fútbol para todos, en cualquier lugar!</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Estilos actualizados -->
<style>
    /* Estilos base */
    .bg-dark-gradient {
        background: #111827;
    }

    .text-gradient {
        background: linear-gradient(to right, #3B82F6, #7C3AED, #9333EA);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    .btn-primary, .btn-secondary {
        display: inline-block;
        padding: 0.75rem 1.5rem;
        font-size: 1rem;
        font-weight: bold;
        border-radius: 9999px;
        text-align: center;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .btn-primary {
        background: linear-gradient(to right, #2563EB, #1D4ED8);
        color: white;
        box-shadow: 0 4px 14px rgba(37, 99, 235, 0.5);
    }

    .btn-secondary {
        background: linear-gradient(to right, #EF4444, #DC2626);
        color: white;
        box-shadow: 0 4px 14px rgba(239, 68, 68, 0.5);
    }

    .btn-primary:hover, .btn-secondary:hover {
        transform: scale(1.1);
    }

    .animate__fadeInUp {
        animation: fadeInUp 1s ease-in-out forwards;
    }

    .delay-200 {
        animation-delay: 0.2s;
    }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
</style>
