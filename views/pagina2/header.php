<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Full Sports</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Custom styles to replicate Tailwind classes */
        .bg-custom-dark { background-color: #0F172A; }
        .text-custom-blue { color: #4d47f5; }
        .bg-custom-red { background-color: #f34739; }
        .hover-bg-custom-red:hover { background-color: #d63c30; }
        .bg-custom-blue { background-color: #4d47f5; }
        .hover-bg-custom-blue:hover { background-color: #0EA5E9; }
        .bg-custom-darker { background-color: #1E293B; }
    </style>
</head>
<body>
    <header class="bg-custom-dark text-white">
        <div class="container mx-auto px-4 py-4 flex justify-between items-center">
            <a href="/" class="flex items-center space-x-2">
                <img src="./public/img/logo.png" alt="Deporstar Logo" class="w-10 h-10">
                <span class="text-2xl font-bold">Full Sports</span>
            </a>
            <nav class="hidden lg:flex space-x-8">
                <a href="#" class="hover:text-custom-blue transition-colors">Inicio</a>
                <a href="#" class="hover:text-custom-blue transition-colors">Deportes</a>
                <a href="#" class="hover:text-custom-blue transition-colors">Ligas</a>
                <a href="#" class="hover:text-custom-blue transition-colors">Equipos</a>
                <a href="#" class="hover:text-custom-blue transition-colors">Jugadores</a>
            </nav>
            <div class="hidden lg:flex items-center space-x-4">
                <a href="./auth/login.php" class="bg-custom-red text-white px-4 py-2 rounded hover:bg-custom-red transition">Iniciar Sesión</a>
                <a href="./auth/register.php" class="bg-custom-blue text-custom-dark px-4 py-2 rounded hover:bg-custom-blue transition">Registrarse</a>
            </div>
            <button class="lg:hidden" onclick="toggleMenu()">
                <span id="menuIcon" class="text-2xl">&#9776;</span>
            </button>
        </div>
        <div class="lg:hidden bg-custom-darker" id="mobileMenu" style="display: none;">
            <nav class="flex flex-col space-y-4 p-4">
                <a href="#" class="hover:text-custom-blue transition-colors">Inicio</a>
                <a href="#" class="hover:text-custom-blue transition-colors">Deportes</a>
                <a href="#" class="hover:text-custom-blue transition-colors">Ligas</a>
                <a href="#" class="hover:text-custom-blue transition-colors">Equipos</a>
                <a href="#" class="hover:text-custom-blue transition-colors">Jugadores</a>
                <a href="./auth/seccion/login.php" class="bg-custom-red text-white px-4 py-2 rounded hover:bg-custom-red transition">Iniciar Sesión</a>
                <a href="./auth/seccion/register.php" class="bg-custom-blue text-custom-dark px-4 py-2 rounded w-full hover:bg-custom-blue transition">Registrarse</a>
            </nav>
        </div>
    </header>

    <script>
        function toggleMenu() {
            const mobileMenu = document.getElementById('mobileMenu');
            const menuIcon = document.getElementById('menuIcon');
            mobileMenu.style.display = mobileMenu.style.display === 'none' ? 'block' : 'none';
            menuIcon.textContent = menuIcon.textContent === '✖' ? '☰' : '✖';
        }
    </script>
</body>
</html>
