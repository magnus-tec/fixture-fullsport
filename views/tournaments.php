<?php
session_start();
if (!isset($_SESSION['email'])) {
    header("Location: ../views/login.php");
    exit();
}
if (!isset($_SESSION['name'])) {
    $_SESSION['name'] = 'Usuario'; // Asigna un valor predeterminado o maneja el error
}
require_once "../config/connection.php";
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Torneos - Full Sports</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
    <!-- Incluyendo Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="../public/css/style.css" rel="stylesheet">
    <!-- Enlace a Bootstrap JS con Popper.js -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        /* Basic Styles */
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #121826;
            color: #fff;
            margin: 0;
            padding: 0;
            transition: background-color 0.3s ease;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }


        /* Content Styles */
        .content {
            margin-left: 280px;
            padding: 20px;
            transition: margin-left 0.3s ease;
            min-height: calc(100vh - 60px);
            /* Adjust 60px to match your navbar height */
            display: flex;
            flex-direction: column;
        }

        .content.collapsed {
            margin-left: 80px;
        }

        /* Add this to ensure proper centering of content */
        .content>div {
            max-width: 1200px;
            width: 100%;
            margin: 0 auto;
            flex-grow: 1;
        }


        /* Responsive Styles */
        @media (max-width: 768px) {
            .sidebar {
                width: 100%;
                height: auto;
                position: relative;
            }

            .content,
            .content.collapsed {
                margin-left: 0;
                width: 100%;
            }

            .navbar,
            .navbar.collapsed {
                margin-left: 0;
                width: 100%;
            }
        }
    </style>
</head>

<body>
    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="brand">
            <img src="../public/img/logo.png" alt="Logo" class="brand-logo">
            <h4>Full Sports</h4>
        </div>
        <br>

        <a href="./" style="font-size: 12px;">
            <i class="bi bi-house-door" style="font-size: 14px; "></i><span>Inicio</span>
        </a>
        <a href="./tournaments.php" style="font-size: 12px; background: linear-gradient(90deg, #6b21a8, #7c3aed);
        color: white; box-shadow: 0 5px 15px rgba(109, 40, 217, 0.4); transform: scale(1.05);">
            <i class="bi bi-trophy" style="font-size: 14px;"></i><span>Mis Torneos</span>
        </a>
        <a href="./mis_equipos.php" style="font-size: 12px;">
            <i class="bi bi-person-lines-fill" style="font-size: 14px;"></i><span>Equipos</span>
        </a>
        <a href="#" style="font-size: 12px;">
            <i class="bi bi-calendar-check" style="font-size: 14px;"></i><span>Programar Partidos</span>
        </a>
        <a href="#" style="font-size: 12px;">
            <i class="bi bi-calendar" style="font-size: 14px;"></i><span>Calendario</span>
        </a>
        <a href="#" style="font-size: 12px;">
            <i class="bi bi-gear" style="font-size: 14px;"></i><span>Configuración</span>
        </a>

    </div>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg" id="navbar">
        <div class="container-fluid">
            <button class="btn btn-outline-light me-3" id="toggleSidebar">
                <i class="bi bi-list"></i>
            </button>
            <!-- Cuadro de búsqueda con colores más modernos -->
            <form class="d-flex ms-auto" style="width: 50%; max-width: 400px;">
                <input class="form-control me-2" type="search" placeholder="Buscar Torneos..." aria-label="Search"
                    style="background: linear-gradient(145deg, #f4f4f9, #d1d5db); color: #2d3748; border: none; border-radius: 30px; box-shadow: 2px 2px 8px rgba(0, 0, 0, 0.1);">
                <button class="btn btn-outline-light" type="submit"
                    style="border-radius: 30px; background-color: #6b21a8; color: white;">
                    <i class="bi bi-search"></i>
                </button>
            </form>
            <ul class="navbar-nav ms-auto">
                <!-- Create Tournament Button 
                <li class="nav-item">
                    <a id="navOpenTournamentModal"
                        class="nav-link p-2 d-flex align-items-center rounded border border-gray-400 hover:bg-transparent-100"
                        href="#">
                        <i class="bi bi-trophy-fill" style="font-size: 1rem;"></i>
                        <span style="font-size: 0.75rem;" class="ms-2">Crear Torneo</span>
                    </a>
                </li>-->


                <!-- Notification Dropdown -->
                <li class="nav-item dropdown">
                    <a class="nav-link" href="#" id="notificationDropdown" role="button" data-bs-toggle="dropdown"
                        aria-expanded="false">
                        <i class="bi bi-bell-fill" style="font-size: 1.2rem;"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="notificationDropdown"
                        style="min-width: 200px;">
                        <li>
                            <a class="dropdown-item text-center text-gray-500">No hay notificaciones</a>
                        </li>
                    </ul>
                </li>

                <!-- User Profile Dropdown -->
                <li class="nav-item dropdown">
                    <div class="d-flex align-items-center">
                        <!-- Icono de usuario -->
                        <i class="bi bi-person-circle" style="font-size: 1.5rem;"></i>

                        <!-- Correo de usuario -->
                        <span
                            class="text-xs text-gray-400 ms-2"><?php echo htmlspecialchars($_SESSION['email'], ENT_QUOTES, 'UTF-8'); ?></span>

                        <!-- Toggle dropdown -->
                        <a class="nav-link dropdown-toggle ms-2" href="#" id="userDropdown" role="button"
                            data-bs-toggle="dropdown" aria-expanded="false"></a>

                        <!-- Menú desplegable -->
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                            <li>
                                <a class="dropdown-item" href="../auth/profile.php">
                                    <i class="bi bi-person"></i> Mi Perfil
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="settings.php">
                                    <i class="bi bi-gear"></i> Configuración
                                </a>
                            </li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li>
                                <a class="dropdown-item text-danger" href="../auth/logout.php">
                                    <i class="bi bi-box-arrow-right"></i> Cerrar Sesión
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
            </ul>
        </div>
    </nav>
    <!-- Content -->
    <div class="content" id="content">

        <!-- Tournament Content -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Tabs -->
            <div class="border-b border-gray-800 mb-6">
                <nav class="flex gap-6">
                    <a href="../views/Siguiendo.php" class="text-gray-400 pb-4 hover:text-white">Siguiendo</a>
                    <a href="../views/Ranking.php" class="text-gray-400 pb-4 hover:text-white">Ranking</a>
                    <a href="../views/tournaments.php" class="text-white pb-4 border-b-2 border-[#7C3AED]">Mis
                        torneos</a>
                </nav>
            </div>

            <!-- Create Tournament Button -->
            <button id="openTournamentModal"
                class="w-full mb-6 p-4 border-2 border-dashed border-gray-700 rounded-lg text-gray-400 hover:text-white hover:border-[#7C3AED] transition-colors">
                <i class="ri-add-line text-xl"></i>
                <span>Crear Una Nueva Competición</span>
            </button>

            <!-- Tournaments List -->
            <div id="tournaments-list" class="space-y-4">
                <!-- Tournaments will be loaded here dynamically -->
            </div>
        </div>

    </div>




    <!-- Tournament Modal -->
    <div id="tournamentModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center">
    <div class="bg-[#1b2238] p-6 rounded-lg w-full max-w-xl">
        <h2 class="text-xl font-bold text-white mb-6 text-center">Crear Nueva Competición</h2>

        <form id="tournamentForm" class="space-y-6">
            <div class="relative">
                <label for="tournamentName" class="block text-sm font-medium text-white mb-2 flex items-center gap-2">
                    <i class="fas fa-trophy"></i> Nombre de tu Competición:
                </label>
                <input type="text" name="name" id="tournamentName"
                    class="w-full p-2 bg-[#292d3e] text-white rounded-lg border border-gray-600 focus:outline-none focus:border-blue-500"
                    placeholder="Nombre de competición" required>
            </div>

            <div class="relative">
                <label for="tournamentDescription" class="block text-sm font-medium text-white mb-2 flex items-center gap-2">
                    <i class="fas fa-align-left"></i> Descripción Corta:
                </label>
                <input name="description" id="tournamentDescription"
                    class="w-full p-2 bg-[#292d3e] text-white rounded-lg border border-gray-600 focus:outline-none focus:border-blue-500"
                    placeholder="Descripción" required></input>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div class="relative">
                    <label for="competitionType" class="block text-sm font-medium text-white mb-2 flex items-center gap-2">
                        <i class="fas fa-trophy-alt"></i> Tipo Competición:
                    </label>
                    <select name="competition_type" id="competitionType"
                        class="w-full p-2 bg-[#292d3e] text-white rounded-lg border border-gray-600 focus:outline-none focus:border-blue-500"
                        required>
                        <option value="" disabled selected>Seleccione</option>
                        <option value="Aficionado">Aficionado</option>
                        <option value="Profesional">Profesional</option>
                    </select>
                </div>

                <div class="relative">
                    <label for="sportType" class="block text-sm font-medium text-white mb-2 flex items-center gap-2">
                        <i class="fas fa-futbol"></i> Deporte:
                    </label>
                    <select name="sport_type" id="sportType"
                        class="w-full p-2 bg-[#292d3e] text-white rounded-lg border border-gray-600 focus:outline-none focus:border-blue-500"
                        required>
                        <option value="" disabled selected>Seleccione</option>
                        <option value="Futbol">Fútbol</option>
                        <option value="Futbol 7">Fútbol 7</option>
                        <option value="Futbol 8">Fútbol 8</option>
                        <option value="Fulbito">Fulbito</option>
                        <option value="Futsal">Futsal</option>
                    </select>
                </div>
            </div>

            <div class="relative">
                <label for="gender" class="block text-sm font-medium text-white mb-2 flex items-center gap-2">
                    <i class="fas fa-venus-mars"></i> Género:
                </label>
                <select name="gender" id="gender"
                    class="w-full p-2 bg-[#292d3e] text-white rounded-lg border border-gray-600 focus:outline-none focus:border-blue-500"
                    required>
                    <option value="" disabled selected>Seleccione</option>
                    <option value="General">General</option>
                    <option value="Varones">Varones</option>
                    <option value="Mujeres">Mujeres</option>
                    <option value="Menores">Menores</option>
                </select>
            </div>

            <div class="relative">
                <label for="urlSlug" class="block text-sm font-medium text-white mb-2 flex items-center gap-2">
                    <i class="fas fa-globe"></i> Nombre único - URL de torneo:
                </label>
                <div class="flex">
                    <input type="text" name="url_slug" id="urlSlug"
                        class="w-full p-2 bg-[#292d3e] text-white rounded-l-lg border border-gray-600 focus:outline-none focus:border-blue-500"
                        readonly>
                    <button type="button" id="verifyUrl"
                        class="px-4 py-2 bg-green-600 text-white rounded-r-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500">Verificar</button>
                </div>
                <p class="text-xs text-gray-400 mt-2">Si personalizas este campo, podrás tener una URL para tu campeonato única y elegante.</p>
            </div>

            <div class="flex justify-end gap-2">
                <button type="button" class="px-5 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500"
                    onclick="closeTournamentModal()">Cancelar</button>
                <button type="submit"
                    class="px-5 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500">Crear</button>
            </div>
        </form>
    </div>
</div>


    <script src="../js/modal.tournament.js"></script>

    <script>
        // Toggle sidebar and content collapse
        document.getElementById('toggleSidebar').addEventListener('click', function () {
            document.getElementById('sidebar').classList.toggle('collapsed');
            document.getElementById('navbar').classList.toggle('collapsed');
            document.getElementById('content').classList.toggle('collapsed');
        });
    </script>

    <script>
        // Toggle user menu
        const userMenuBtn = document.getElementById('userMenuBtn');
        const userMenu = document.getElementById('userMenu');
        userMenuBtn.addEventListener('click', () => {
            userMenu.classList.toggle('hidden');
        });

        // Open tournament modal
        document.getElementById('openTournamentModal').addEventListener('click', () => {
            document.getElementById('tournamentModal').classList.remove('hidden');
            document.getElementById('tournamentModal').classList.add('flex');
        });

        // Close tournament modal function
        function closeTournamentModal() {
            document.getElementById('tournamentModal').classList.remove('flex');
            document.getElementById('tournamentModal').classList.add('hidden');
        }
    </script>

<script>
    function fetchTournaments() {
        fetch('../controllers/get_user_tournaments.php')
            .then(response => response.json())
            .then(data => {
                const container = document.getElementById('tournaments-list');
                const createTournamentButton = document.getElementById('openTournamentModal');

                if (data.error) {
                    container.innerHTML = `<p class="text-red-500">${data.error}</p>`;
                    createTournamentButton.classList.add('hidden'); // Ocultar botón si hay error
                    return;
                }

                if (data.length === 0) {
                    // No hay torneos creados
                    container.innerHTML = `
                        <div class="text-center p-4 bg-[#1E293B] rounded-lg">
                            <p class="text-gray-400">No has creado ningún torneo aún.</p>
                            <button id="createFirstTournament" class="mt-4 px-4 py-2 bg-[#7C3AED] text-white rounded-md hover:bg-[#6D28D9] transition-colors">
                                Crear tu primer torneo
                            </button>
                        </div>
                    `;
                    createTournamentButton.classList.add('hidden'); // Ocultar el botón "Crear Una Nueva Competición"

                    document.getElementById('createFirstTournament').addEventListener('click', () => {
                        document.getElementById('tournamentModal').classList.remove('hidden');
                        document.getElementById('tournamentModal').classList.add('flex');
                    });
                } else {
                    // Hay torneos creados
                    container.innerHTML = data.map(tournament => `
                        <div class="flex items-center justify-between p-4 bg-[#1E293B] rounded-lg">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 bg-gray-700 rounded-full flex items-center justify-center">
                                    <i class="ri-trophy-line text-2xl"></i>
                                </div>
                                <div>
                                    <h3 class="font-semibold">${tournament.name}</h3>
                                    <p class="text-sm text-gray-400">${tournament.followers || 0} seguidores</p>
                                </div>
                            </div>
                            <a href="tournament-detail.php?id=${tournament.id}" class="px-4 py-2 bg-[#7C3AED] text-white rounded-md hover:bg-[#6D28D9] transition-colors">Administrar</a>
                        </div>
                    `).join('');

                    createTournamentButton.classList.remove('hidden'); // Mostrar el botón "Crear Una Nueva Competición"
                }
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById('tournaments-list').innerHTML = '<p class="text-red-500">Error al cargar los torneos.</p>';
                document.getElementById('openTournamentModal').classList.add('hidden'); // Ocultar botón en caso de error
            });
    }

    // Llamar a fetchTournaments al cargar la página
    document.addEventListener('DOMContentLoaded', fetchTournaments);
</script>

</body>

</html>