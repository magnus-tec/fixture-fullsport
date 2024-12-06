<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Moderno</title>
    <!-- Incluyendo Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="../public/css/style.css" rel="stylesheet">
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
        <a href="./tournaments.php" style="font-size: 12px;">
            <i class="bi bi-trophy" style="font-size: 14px;"></i><span>Mis Torneos</span>
        </a>
        <a href="./mis_equipos.php" style="font-size: 12px;">
            <i class="bi bi-person-lines-fill" style="font-size: 14px;"></i><span>Equipos</span>
        </a>
        <a href="#" style="font-size: 12px;">
            <i class="bi bi-calendar-check" style="font-size: 14px;"></i><span>Programar Partidos</span>
        </a>
        <a href="#" style="font-size: 12px; background: linear-gradient(90deg, #6b21a8, #7c3aed);
        color: white; box-shadow: 0 5px 15px rgba(109, 40, 217, 0.4); transform: scale(1.05);">
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
                <!-- Create Tournament Button -->
                <li class="nav-item">
                    <a id="navOpenTournamentModal"
                        class="nav-link p-2 d-flex align-items-center rounded border border-gray-400 hover:bg-transparent-100"
                        href="#">
                        <i class="bi bi-trophy-fill" style="font-size: 1rem;"></i>
                        <span style="font-size: 0.75rem;" class="ms-2">Crear Torneo</span>
                    </a>
                </li>


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

    <!-- Quick Actions Modal -->
    <div id="Modaltournament" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 hidden">
        <!-- Contenido del modal con fondo sólido -->
        <div class="bg-gray-900 rounded-lg p-6 max-w-md w-full shadow-lg">
            <h2 class="text-lg font-bold text-white-800 mb-4">Acciones Rápidas</h2>
            <div class="space-y-4">
                <button id="openTournamentModal"
                    class="w-full bg-purple-600 text-white py-2 rounded-md hover:bg-purple-700 transition duration-200">
                    Crear Nuevo Torneo
                </button>
                <button
                    class="w-full bg-purple-600 text-white py-2 rounded-md hover:bg-purple-700 transition duration-200">
                    Programar Partido
                </button>
                <button
                    class="w-full bg-purple-600 text-white py-2 rounded-md hover:bg-purple-700 transition duration-200">
                    Registrar Equipo
                </button>
            </div>
            <button id="closeTournamentModal"
                class="mt-4 w-full bg-gray-600 text-white py-2 rounded-md hover:bg-gray-700 transition duration-200">
                Cerrar
            </button>
        </div>
    </div>

    <!-- Tournament Modal -->
    <div id="tournamentModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center">
        <div class="bg-[#1A1D24] p-6 rounded-lg w-full max-w-xl">
            <h2 class="text-xl font-bold mb-4">Crear Nueva Competición</h2>

            <form id="tournamentForm" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium mb-1">Nombre de tu Competición:</label>
                    <input type="text" name="name" id="tournamentName"
                        class="w-full p-2 rounded bg-gray-800 border border-gray-700" required>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Descripción Corta:</label>
                    <textarea name="description" id="tournamentDescription"
                        class="w-full p-2 rounded bg-gray-800 border border-gray-700" required></textarea>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium mb-1">Tipo Competición:</label>
                        <select name="competition_type" id="competitionType"
                            class="w-full p-2 rounded bg-gray-800 border border-gray-700" required>
                            <option value="Aficionado">Aficionado</option>
                            <option value="Profesional">Profesional</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1">Deporte:</label>
                        <select name="sport_type" id="sportType"
                            class="w-full p-2 rounded bg-gray-800 border border-gray-700" required>
                            <option value="Futbol">Fútbol</option>
                            <option value="Futbol 7">Fútbol 7</option>
                            <option value="Futbol 8">Fútbol 8</option>
                            <option value="Fulbito">Fulbito</option>
                            <option value="Futsal">Futsal</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Género:</label>
                    <select name="gender" id="gender" class="w-full p-2 rounded bg-gray-800 border border-gray-700"
                        required>
                        <option value="General">General</option>
                        <option value="Varones">Varones</option>
                        <option value="Mujeres">Mujeres</option>
                        <option value="Menores">Menores</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Nombre único - URL de torneo:</label>
                    <div class="flex gap-2">
                        <input type="text" name="url_slug" id="urlSlug"
                            class="flex-1 p-2 rounded bg-gray-800 border border-gray-700" readonly>
                        <button type="button" id="verifyUrl"
                            class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">Verificar</button>
                    </div>
                    <p class="text-xs text-gray-400 mt-1">Si personalizas este campo, podrás tener una URL para tu
                        campeonato como tú quieras y que nadie más tendrá.</p>
                </div>

                <div class="flex justify-end gap-2 mt-6">
                    <button type="button" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700"
                        onclick="closeTournamentModal()">Cancelar</button>
                    <button type="submit"
                        class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">Crear</button>
                </div>
            </form>
        </div>
    </div>


    <!-- JS and Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Toggle sidebar and content collapse
        document.getElementById('toggleSidebar').addEventListener('click', function () {
            document.getElementById('sidebar').classList.toggle('collapsed');
            document.getElementById('navbar').classList.toggle('collapsed');
            document.getElementById('content').classList.toggle('collapsed');
        });
    </script>

    <script>
        // Botones de abrir y cerrar el modal
        const navOpenTournamentModal = document.getElementById('navOpenTournamentModal');
        const tournamentModal = document.getElementById('tournamentModal');
        const closeTournamentModal = document.getElementById('closeTournamentModal');

        // Abrir modal al hacer clic en el botón del menú
        navOpenTournamentModal.addEventListener('click', (e) => {
            e.preventDefault(); // Evitar comportamiento predeterminado del enlace
            tournamentModal.classList.remove('hidden');
        });

        // Cerrar modal al hacer clic en el botón "Cerrar"
        closeTournamentModal.addEventListener('click', () => {
            tournamentModal.classList.add('hidden');
        });

        // Cerrar modal al hacer clic fuera del contenido
        window.addEventListener('click', (e) => {
            if (e.target === tournamentModal) {
                tournamentModal.classList.add('hidden');
            }
        });
    </script>

</body>

</html>