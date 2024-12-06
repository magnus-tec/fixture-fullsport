<?php
session_start();
if (!isset($_SESSION['email'])) {
    header("Location: ../views/login.php");
    exit();
}
if (!isset($_SESSION['name'])) {
    $_SESSION['name'] = 'Usuario';
}
require_once "../config/connection.php";
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Equipos - Full Sports</title>
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
        <a href="./tournaments.php" style="font-size: 12px;">
            <i class="bi bi-trophy" style="font-size: 14px;"></i><span>Mis Torneos</span>
        </a>
        <a href="./mis_equipos.php" style="font-size: 12px; background: linear-gradient(90deg, #6b21a8, #7c3aed);
        color: white; box-shadow: 0 5px 15px rgba(109, 40, 217, 0.4); transform: scale(1.05);">
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
                                <a class="dropdown-item text-danger" href="../views/logout.php">
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


        <!-- Header -->
        <header class="flex h-14 items-center gap-4 border-b border-gray-800 bg-[#1A1D24] px-6 hidden">
            <h1 class="text-lg font-semibold flex-1">Equipos</h1>
            <button class="text-gray-400 p-2"><i class="ri-notification-3-line"></i></button>
            <div class="relative">
                <button id="userMenuBtn" class="flex items-center gap-2 text-gray-400">
                    <img src="../public/img/usuario1.png" style="width: 32px; height: 32px" alt="Avatar"
                        class="rounded-full">
                    <div class="text-left">
                        <div class="text-sm font-medium text-white"><?php echo $_SESSION['name']; ?></div>
                        <div class="text-xs text-gray-400"><?php echo $_SESSION['email']; ?></div>
                    </div>
                    <i class="ri-arrow-down-s-line"></i>
                </button>
            </div>
        </header>

        <!-- Teams Content -->
        <div class="p-6">
            <!-- Create Team Button -->
            <button id="openTeamModal"
                class="w-full mb-6 p-4 border-2 border-dashed border-gray-700 rounded-lg text-gray-400 hover:text-white hover:border-[#7C3AED] transition-colors">
                <i class="ri-add-line text-xl"></i>
                <span>Crear Un Nuevo Equipo</span>
            </button>

            <!-- Teams List -->
            <div id="teams-list" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                <?php foreach ($teams as $team): ?>
                    <div class="bg-[#1E293B] rounded-lg p-4 flex flex-col items-center">
                        <div class="w-16 h-16 bg-gray-700 rounded-full flex items-center justify-center mb-2">
                            <?php if (!empty($team['logo'])): ?>
                                <img src="<?php echo htmlspecialchars($team['logo']); ?>"
                                    alt="Logo de <?php echo htmlspecialchars($team['name']); ?>"
                                    class="w-full h-full object-cover rounded-full" />
                            <?php else: ?>
                                <i class="ri-shield-line text-3xl text-gray-400"></i>
                            <?php endif; ?>
                        </div>
                        <h3 class="font-semibold text-center"><?php echo htmlspecialchars($team['name']); ?></h3>
                        <p class="text-sm text-gray-400 text-center">@<?php echo htmlspecialchars($team['country']); ?>
                        </p>
                        <?php if ($team['user_id'] == $_SESSION['user_id']): ?>
                            <a href="team-detail.php?id=<?php echo $team['id']; ?>"
                                class="mt-2 px-4 py-2 bg-[#7C3AED] text-white rounded-md hover:bg-[#6D28D9] transition-colors inline-flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                    stroke="currentColor" class="w-5 h-5 mr-2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M15.75 9V5.25m0 0L12 9m3.75-3.75L17.25 9m-5.25 6v3.75m0-3.75L9 12.75m3 3l3.75-3.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Administrar
                            </a>
                        <?php else: ?>
                            <a href="../pages/team-detail.php?id=<?php echo $team['id']; ?>"
                                class="mt-2 px-4 py-2 bg-[#6d28d9] text-white rounded-md hover:bg-[#5b21b6] transition-colors inline-flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                    stroke="currentColor" class="w-5 h-5 mr-2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M15.75 9V5.25m0 0L12 9m3.75-3.75L17.25 9m-5.25 6v3.75m0-3.75L9 12.75m3 3l3.75-3.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Ver
                            </a>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        </main>
    </div>

    <!-- Team Modal -->
    <div id="teamModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center">
        <div class="bg-[#1A1D24] p-6 rounded-lg w-full max-w-xl">
            <h2 class="text-xl font-bold mb-4">Crear Nuevo Equipo</h2>
            <form id="teamForm" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium mb-1">Nombre de tu Equipo:</label>
                    <input type="text" name="team_name" id="teamName"
                        class="w-full p-2 rounded bg-gray-800 border border-gray-700" required>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Selecciona País:</label>
                    <input type="text" name="country" id="teamCountry"
                        class="w-full p-2 rounded bg-gray-800 border border-gray-700" required>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Selecciona un color:</label>
                    <input type="color" name="color" id="teamColor"
                        class="w-full p-2 rounded bg-gray-800 border border-gray-700" required>
                </div>
                <div class="flex justify-end gap-2 mt-6">
                    <button type="button" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700"
                        onclick="closeTeamModal()">Cancelar</button>
                    <button type="submit"
                        class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">Crear</button>
                </div>
            </form>
        </div>
    </div>

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

        // Open team modal
        document.getElementById('openTeamModal').addEventListener('click', function () {
            document.getElementById('teamModal').classList.remove('hidden');
            document.getElementById('teamModal').classList.add('flex');
        });

        // Close team modal
        function closeTeamModal() {
            document.getElementById('teamModal').classList.add('hidden');
            document.getElementById('teamModal').classList.remove('flex');
        }

        // Submit team form
        document.getElementById('teamForm').addEventListener('submit', async function (e) {
            e.preventDefault();
            const formData = new FormData(this);

            try {
                const response = await fetch('../controllers/create_team.php', {
                    method: 'POST',
                    body: formData
                });

                const data = await response.json();

                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Éxito',
                        text: 'Equipo creado exitosamente.',
                        background: '#1a1d24',
                        color: '#ffffff'
                    });
                    closeTeamModal();
                    this.reset();
                    fetchTeams();
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Error al crear el equipo: ' + data.message,
                        background: '#1a1d24',
                        color: '#ffffff'
                    });
                }
            } catch (error) {
                console.error('Error al enviar el formulario:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Ocurrió un error al crear el equipo.',
                    background: '#1a1d24',
                    color: '#ffffff'
                });
            }
        });

        // Fetch and display teams
        function fetchTeams() {
            fetch('../controllers/get_teams.php')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const teams = data.teams;
                        const container = document.getElementById('teams-list');
                        container.innerHTML = teams.map(team => `
                            <div class="bg-[#1E293B] rounded-lg p-4 flex flex-col items-center">
                                <div class="w-16 h-16 bg-gray-700 rounded-full flex items-center justify-center mb-2">
                                    ${team.logo ?
                                `<img src="${team.logo}" alt="Logo de ${team.name}" class="w-full h-full object-cover rounded-full" />` :
                                `<i class="ri-shield-line text-3xl"></i>`
                            }
                                </div>
                                <h3 class="font-semibold text-center">${team.name}</h3>
                                <p class="text-sm text-gray-400 text-center">@${team.country}</p>
                                <p class="text-xs text-gray-500">Creado por: ${team.creator_name || 'Desconocido'}</p>
                                ${team.user_id == <?php echo $_SESSION['user_id']; ?> ?
                                `<a href="team-detail.php?id=${team.id}" 
                                        class="mt-2 px-4 py-2 bg-[#7C3AED] text-white rounded-md hover:bg-[#6D28D9] transition-colors inline-flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                             stroke-width="1.5" stroke="currentColor" class="w-5 h-5 mr-2">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                  d="M15.75 9V5.25m0 0L12 9m3.75-3.75L17.25 9m-5.25 6v3.75m0-3.75L9 12.75m3 3l3.75-3.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        Administrar
                                    </a>` :
                                `<a href="../pages/team-detail.php?id=${team.id}" 
                                        class="mt-2 px-4 py-2 bg-[#6d28d9] text-white rounded-md hover:bg-[#5b21b6] transition-colors inline-flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                             stroke-width="1.5" stroke="currentColor" class="w-5 h-5 mr-2">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                  d="M15.75 9V5.25m0 0L12 9m3.75-3.75L17.25 9m-5.25 6v3.75m0-3.75L9 12.75m3 3l3.75-3.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        Visualizar
                                    </a>`
                            }
                            </div>
                        `).join('');
                    } else {
                        console.error('Error fetching teams:', data.error);
                        document.getElementById('teams-list').innerHTML = '<p class="text-red-500">Error al cargar los equipos.</p>';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    document.getElementById('teams-list').innerHTML = '<p class="text-red-500">Error al cargar los equipos.</p>';
                });
        }

        // Call fetchTeams when the page loads
        document.addEventListener('DOMContentLoaded', fetchTeams);
    </script>
</body>

</html>