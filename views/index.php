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

// Obtener torneos de la base de datos
$sql = "SELECT * FROM tournaments"; // Asegúrate de que la tabla se llame 'tournaments'
$stmt = $con->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();
$tournaments = $result->fetch_all(MYSQLI_ASSOC);
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

        <a href="./" style="font-size: 12px; background: linear-gradient(90deg, #6b21a8, #7c3aed);
        color: white; box-shadow: 0 5px 15px rgba(109, 40, 217, 0.4); transform: scale(1.05);">
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
                <!-- Create Tournament Button -->
                <li class="nav-item">
                    <a id="openTournamentModal"
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
        <h2 cla ss="text-lg font-bold mb-4"></h2>
        <div id="tournaments-cards" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            <!-- Tarjetas dinámicas generadas con PHP -->
            <?php foreach ($tournaments as $tournament): ?>
                <div class="bg-[#1b2238] rounded-lg p-4 shadow-md hover:scale-105 transition-transform">
                    <div class="text-center mb-4">
                        <h3 class="text-lg font-bold"><?php echo htmlspecialchars($tournament['name']); ?></h3>
                        <p class="text-gray-400"><?php echo htmlspecialchars($tournament['description']); ?></p>
                    </div>
                    <div class="flex flex-wrap gap-2 mb-4 justify-center">
                        <span
                            class="bg-[#3A3D44] text-white px-2 py-1 rounded-md text-xs"><?php echo htmlspecialchars($tournament['competition_type']); ?></span>
                        <span
                            class="bg-[#3A3D44] text-white px-2 py-1 rounded-md text-xs"><?php echo htmlspecialchars($tournament['sport_type']); ?></span>
                        <span
                            class="bg-[#3A3D44] text-white px-2 py-1 rounded-md text-xs"><?php echo htmlspecialchars($tournament['gender']); ?></span>
                    </div>
                    <div class="flex justify-center">
                        <?php if ($tournament['created_by'] == $_SESSION['user_id']): ?>
                            <a href="./tournament-detail.php?id=<?php echo $tournament['id']; ?>"
                                class="inline-flex items-center justify-center bg-[#6d28d9] text-white px-4 py-2 rounded-md hover:bg-[#5b21b6] transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                    stroke="currentColor" class="w-5 h-5 mr-2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M15.75 9V5.25m0 0L12 9m3.75-3.75L17.25 9m-5.25 6v3.75m0-3.75L9 12.75m3 3l3.75-3.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Administrar
                            </a>
                        <?php else: ?>
                            <a href="../pages/tournament-detail.php?id=<?php echo $tournament['id']; ?>"
                                class="inline-flex items-center justify-center bg-[#6d28d9] text-white px-4 py-2 rounded-md hover:bg-[#5b21b6] transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                    stroke="currentColor" class="w-5 h-5 mr-2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M15.75 9V5.25m0 0L12 9m3.75-3.75L17.25 9m-5.25 6v3.75m0-3.75L9 12.75m3 3l3.75-3.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Visualizar
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>



    <!-- Tournament Modal -->
    <div id="tournamentModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-[#1b2238] p-6 rounded-lg w-full max-w-xl">
            <h2 class="text-xl font-bold text-white mb-6 text-center">Crear Nueva Competición</h2>

            <form id="tournamentForm" class="space-y-6">
                <div class="relative">
                    <label for="tournamentName"
                        class="block text-sm font-medium text-white mb-2 flex items-center gap-2">
                        <i class="fas fa-trophy"></i> Nombre de tu Competición:
                    </label>
                    <input type="text" name="name" id="tournamentName"
                        class="w-full p-2 bg-[#292d3e] text-white rounded-lg border border-gray-600 focus:outline-none focus:border-blue-500"
                        placeholder="Nombre de competición" required>
                </div>

                <div class="relative">
                    <label for="tournamentDescription"
                        class="block text-sm font-medium text-white mb-2 flex items-center gap-2">
                        <i class="fas fa-align-left"></i> Descripción Corta:
                    </label>
                    <input name="description" id="tournamentDescription"
                        class="w-full p-2 bg-[#292d3e] text-white rounded-lg border border-gray-600 focus:outline-none focus:border-blue-500"
                        placeholder="Descripción" required></input>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="relative">
                        <label for="competitionType"
                            class="block text-sm font-medium text-white mb-2 flex items-center gap-2">
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
                        <label for="sportType"
                            class="block text-sm font-medium text-white mb-2 flex items-center gap-2">
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
                    <p class="text-xs text-gray-400 mt-2">Si personalizas este campo, podrás tener una URL para tu
                        campeonato única y elegante.</p>
                </div>

                <div class="flex justify-end gap-2">
                    <button type="button"
                        class="px-5 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500"
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
            // Código existente para cargar los torneos
        }

        // Llamar a la función para cargar los torneos al cargar la página
        document.addEventListener('DOMContentLoaded', fetchTournaments);

        // Llamar a fetchTournaments después de crear un torneo
        function onTournamentCreated() {
            fetchTournaments();
        }
    </script>
    <script>
        const fetchTournaments = async () => {
            const container = document.getElementById('tournaments-cards');

            try {
                const response = await fetch('../controllers/get_tournaments.php');
                if (!response.ok) {
                    throw new Error(HTTP error! status: ${ response.status });
                }
                const tournaments = await response.json();
                container.innerHTML = tournaments.map(createTournamentCard).join('');

                // Add animations after cards are created
                addCardAnimations();
            } catch (error) {
                console.error('Error fetching tournaments:', error);
                container.innerHTML = '<p class="text-red-500">Error al cargar los torneos.</p>';
            }
        };

        const createTournamentCard = (tournament) => {
            const { id, name, competition_type, description, sport_type, gender } = tournament;
            return `
      <div class="tournament-card bg-[#2A2D34] rounded-2xl p-6 shadow-2xl relative overflow-hidden transition-transform duration-300 hover:scale-105">
        <div class="absolute inset-0 bg-gradient-to-br from-[#6d28d9] to-transparent opacity-10 rounded-2xl"></div>
        <div class="relative z-10">
          <div class="flex items-center justify-between mb-4">
            <h3 class="tournament-name text-2xl font-bold text-white">${name}</h3>
            <span class="bg-[#6d28d9] text-white rounded-full px-3 py-1 text-xs font-semibold">${competition_type}</span>
          </div>
          <p class="text-gray-300 mb-4 line-clamp-2">${description}</p>
          <div class="flex flex-wrap gap-2 mb-4">
            <span class="bg-[#3A3D44] text-white px-3 py-1 rounded-md text-xs font-medium">${sport_type}</span>
            <span class="bg-[#3A3D44] text-white px-3 py-1 rounded-md text-xs font-medium">${gender}</span>
          </div>
          <div class="flex justify-end">
            <a href="views/tournament-detail.php?id=${id}" class="results-button inline-flex items-center justify-center bg-[#6d28d9] text-white px-5 py-2 rounded-lg shadow-lg hover:bg-[#5b21b6] transition-colors duration-300">
              <span class="mr-2">Últimos Resultados</span>
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-5 h-5">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
              </svg>
            </a>
          </div>
        </div>
        <div class="absolute bottom-0 left-0 right-0 h-1 bg-gradient-to-r from-[#6d28d9] via-[#8b5cf6] to-[#6d28d9]"></div>
      </div>
    `;
        };

        const addCardAnimations = () => {
            const cards = document.querySelectorAll('.tournament-card');
            cards.forEach((card, index) => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(20px)';
                setTimeout(() => {
                    card.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                }, index * 100);
            });

            const buttons = document.querySelectorAll('.results-button');
            buttons.forEach(button => {
                button.addEventListener('mouseenter', () => {
                    button.style.transform = 'scale(1.05)';
                });
                button.addEventListener('mouseleave', () => {
                    button.style.transform = 'scale(1)';
                });
                button.addEventListener('mousedown', () => {
                    button.style.transform = 'scale(0.95)';
                });
                button.addEventListener('mouseup', () => {
                    button.style.transform = 'scale(1.05)';
                });
            });

            // Add hover effect for tournament names
            const tournamentNames = document.querySelectorAll('.tournament-name');
            tournamentNames.forEach(name => {
                name.addEventListener('mouseenter', () => {
                    name.style.textShadow = '0 0 10px rgba(109, 40, 217, 0.7)';
                });
                name.addEventListener('mouseleave', () => {
                    name.style.textShadow = 'none';
                });
            });
        };

        // Call the function when the DOM is fully loaded
        document.addEventListener('DOMContentLoaded', fetchTournaments);
    </script>


</body>

</html>