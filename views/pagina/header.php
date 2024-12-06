<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Full Sports</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Estilos personalizados */
        .text-primary {
            color: #3B82F6;
        }
        .btn-primary {
            background: linear-gradient(to right, #2563EB, #1D4ED8);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 9999px;
            font-weight: bold;
            transition: all 0.3s ease;
        }
        .btn-primary:hover {
            transform: scale(1.05);
            box-shadow: 0 4px 14px rgba(37, 99, 235, 0.5);
        }
        .btn-secondary {
            background: linear-gradient(to right, #EF4444, #DC2626);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 9999px;
            font-weight: bold;
            transition: all 0.3s ease;
        }
        .btn-secondary:hover {
            transform: scale(1.05);
            box-shadow: 0 4px 14px rgba(239, 68, 68, 0.5);
        }
    </style>
</head>
<body>
    <header class="bg-dark-gradient text-white shadow-lg">
        <div class="container mx-auto px-6 py-4 flex justify-between items-center">
            <!-- Logo -->
            <a href="/" class="flex items-center space-x-3">
                <img src="./public/img/logo.png" alt="Full Sports Logo" class="w-10 h-10">
                <span class="text-2xl font-bold">Full Sports</span>
            </a>

            <!-- Menú de navegación (escritorio) -->
            <nav class="hidden lg:flex space-x-6">
                <a href="#" class="hover:text-primary transition-colors">Inicio</a>
                <a href="#" class="hover:text-primary transition-colors">Deportes</a>
                <a href="#" class="hover:text-primary transition-colors">Ligas</a>
                <a href="#" class="hover:text-primary transition-colors">Equipos</a>
                <a href="#" class="hover:text-primary transition-colors">Jugadores</a>
            </nav>

            <!-- Botones de acción (escritorio) -->
            <div class="hidden lg:flex items-center space-x-4">
                <a href="./auth/login.php" class="btn-secondary">Iniciar Sesión</a>
                <a href="./auth/register.php" class="btn-primary">Registrarse</a>
            </div>

            <!-- Botón del menú móvil -->
            <button class="lg:hidden text-2xl focus:outline-none" onclick="toggleMenu()">
                <span id="menuIcon">&#9776;</span>
            </button>
        </div>

        <!-- Menú móvil -->
        <div class="lg:hidden bg-dark-gradient" id="mobileMenu" style="display: none;">
            <nav class="flex flex-col space-y-4 p-4">
                <a href="#" class="hover:text-primary transition-colors">Inicio</a>
                <a href="#" class="hover:text-primary transition-colors">Deportes</a>
                <a href="#" class="hover:text-primary transition-colors">Ligas</a>
                <a href="#" class="hover:text-primary transition-colors">Equipos</a>
                <a href="#" class="hover:text-primary transition-colors">Jugadores</a>
                <a href="./auth/login.php" class="btn-secondary w-full text-center">Iniciar Sesión</a>
                <a href="./auth/register.php" class="btn-primary w-full text-center">Registrarse</a>
            </nav>
        </div>
    </header>

    <!-- Script para el menú móvil -->
    <script>
        function toggleMenu() {
            const mobileMenu = document.getElementById('mobileMenu');
            const menuIcon = document.getElementById('menuIcon');
            const isOpen = mobileMenu.style.display === 'block';
            mobileMenu.style.display = isOpen ? 'none' : 'block';
            menuIcon.textContent = isOpen ? '☰' : '✖';
        }
    </script>
</body>
</html>
