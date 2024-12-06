<footer class="bg-gradient-to-b from-[#0F172A] to-[#1E293B] text-white py-16">
    <div class="container mx-auto px-6">
        <div class="grid sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            <!-- Descripción -->
            <div>
                <h3 class="text-2xl font-bold mb-4">Full Sport</h3>
                <p class="text-gray-400 mb-6 leading-relaxed">
                    La plataforma definitiva para seguir tus deportes favoritos, explorar torneos y vivir el deporte
                    como nunca.
                </p>
                <div class="flex gap-4">
                    <!-- Redes sociales -->
                    <a href="#" class="text-gray-400 hover:text-white transition-colors">
                        <i class="fab fa-facebook-f text-xl"></i>
                    </a>
                    <a href="#" class="text-gray-400 hover:text-white transition-colors">
                        <i class="fab fa-twitter text-xl"></i>
                    </a>
                    <a href="#" class="text-gray-400 hover:text-white transition-colors">
                        <i class="fab fa-instagram text-xl"></i>
                    </a>
                </div>
            </div>

            <!-- Enlaces rápidos -->
            <div>
                <h3 class="font-semibold text-xl mb-4">Enlaces Rápidos</h3>
                <ul class="space-y-3">
                    <li class="flex items-center gap-3">
                        <i class="fas fa-futbol text-lg text-gray-400"></i>
                        <a href="#" class="text-gray-400 hover:text-[#4d47f5] transition-colors">Fútbol</a>
                    </li>
                    <li class="flex items-center gap-3">
                        <i class="fas fa-list-alt text-lg text-gray-400"></i>
                        <a href="#" class="text-gray-400 hover:text-[#4d47f5] transition-colors">Ligas</a>
                    </li>
                    <li class="flex items-center gap-3">
                        <i class="fas fa-users text-lg text-gray-400"></i>
                        <a href="#" class="text-gray-400 hover:text-[#4d47f5] transition-colors">Equipos</a>
                    </li>
                    <li class="flex items-center gap-3">
                        <i class="fas fa-user text-lg text-gray-400"></i>
                        <a href="#" class="text-gray-400 hover:text-[#4d47f5] transition-colors">Jugadores</a>
                    </li>
                </ul>
            </div>


            <!-- Soporte -->
            <div>
                <h3 class="font-semibold text-xl mb-4">Soporte</h3>
                <ul class="space-y-3">
                    <li class="flex items-center gap-3">
                        <i class="fas fa-headset text-lg text-gray-400"></i>
                        <a href="#" class="text-gray-400 hover:text-[#4d47f5] transition-colors">Centro de ayuda</a>
                    </li>
                    <li class="flex items-center gap-3">
                        <i class="fas fa-phone text-lg text-gray-400"></i>
                        <a href="#" class="text-gray-400 hover:text-[#4d47f5] transition-colors">Contacto</a>
                    </li>
                    <li class="flex items-center gap-3">
                        <i class="fas fa-user-secret text-lg text-gray-400"></i>
                        <a href="#" class="text-gray-400 hover:text-[#4d47f5] transition-colors">Política de
                            privacidad</a>
                    </li>
                    <li class="flex items-center gap-3">
                        <i class="fas fa-file-contract text-lg text-gray-400"></i>
                        <a href="#" class="text-gray-400 hover:text-[#4d47f5] transition-colors">Términos de uso</a>
                    </li>
                </ul>
            </div>

            <!-- Imagen de soporte -->
            <div class="flex justify-center items-center">
                <img src="../fixturepro/public/img/logo.png" alt="Soporte" class="w-40 md:w-48 lg:w-56 object-contain">
            </div>
        </div>

        <!-- Footer inferior -->
        <div class="border-t border-gray-800 mt-12 pt-8 text-center text-gray-400">
            <p>&copy; <span id="current-year"></span> <a href="#" class="hover:text-white transition-colors">Magus
                    Technologies</a>. Todos los derechos reservados.</p>
        </div>
    </div>
</footer>

<script>
    document.getElementById("current-year").textContent = new Date().getFullYear();
</script>