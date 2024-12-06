<?php
session_start();
require_once "../config/connection.php";

if (!isset($_SESSION['email'])) {
    header("Location: ../auth/login.php");
    exit();
}

$tournament_id = $_GET['tournament_id'] ?? null;
$version_id = $_GET['version_id'] ?? null;

if (!$tournament_id || !$version_id) {
    header("Location: tournaments.php");
    exit();
}

// Obtener detalles del torneo, versión y administrador
$sql = "SELECT 
            t.*, 
            tv.*, 
            tvd.*, 
            IFNULL(u.name, 'N/A') AS admin_name, 
            IFNULL(u.email, 'N/A') AS admin_email,
            u.role AS admin_role
        FROM tournaments t
        JOIN tournament_versions tv ON t.id = tv.tournament_id
        JOIN tournament_version_details tvd ON tv.id = tvd.version_id
        LEFT JOIN usertable u ON t.user_id = u.id
        WHERE t.id = ? AND tv.id = ?";
$stmt = $con->prepare($sql);
$stmt->bind_param("ii", $tournament_id, $version_id);
$stmt->execute();
$result = $stmt->get_result();
$tournament_data = $result->fetch_assoc();

$prizes = $tournament_data['prizes'] ? explode(',', $tournament_data['prizes']) : [];
$bases = $tournament_data['tournament_bases'] ? explode(',', $tournament_data['tournament_bases']) : [];

if (!$tournament_data) {
    header("Location: tournaments.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($tournament_data['name']); ?> - Detalles</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
    <!-- CSS de Flatpickr -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

    <!-- Iconos (Font Awesome) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <!-- JS de Flatpickr -->
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    <!-- Idioma español -->
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/es.js"></script>
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

        <a href="../views/" style="font-size: 12px;">
            <i class="bi bi-house-door" style="font-size: 14px; "></i><span>Inicio</span>
        </a>
        <a href="../views/tournaments.php" style="font-size: 12px; background: linear-gradient(90deg, #6b21a8, #7c3aed);
        color: white; box-shadow: 0 5px 15px rgba(109, 40, 217, 0.4); transform: scale(1.05);">
            <i class="bi bi-trophy" style="font-size: 14px;"></i><span>Mis Torneos</span>
        </a>
        <a href="../views/mis_equipos.php" style="font-size: 12px;">
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
    <!-- Content -->
    <div class="content" id="content">
            <!-- Tournament Details -->
            <div class="p-6">
                <!-- Header Image and Basic Info -->
                <div class="relative h-64 rounded-lg overflow-hidden mb-6">
                    <img src="<?php echo htmlspecialchars('../public/' . ($tournament_data['cover_image'] ?? '../public/img/banner.jpg')); ?>"
                        alt="Tournament Cover" class="w-full h-full object-cover">
                    <div class="absolute bottom-0 left-0 right-0 p-6 bg-gradient-to-t from-black to-transparent">
                        <h1 class="text-3xl font-bold"><?php echo htmlspecialchars($tournament_data['name']); ?></h1>
                        <p class="text-gray-300"><?php echo htmlspecialchars($tournament_data['description']); ?></p>
                    </div>
                </div>

                <div class="flex justify-between mb-4">
                    <a href="tournament-detail.php?id=<?php echo $tournament_id; ?>"
                        class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 flex items-center space-x-2">
                        <i class="ri-arrow-left-line"></i>
                    </a>
                </div>

                <h1 class="text-4xl font-semibold text-white text-center mb-4">DETALLES DE LA VERSIÓN DEL TORNEO</h1>

                <div class="flex justify-center items-center mb-4 p-4 bg-[#0f172a] rounded-lg shadow-md">
                    <div class="flex space-x-4">
                        <!-- Botón Ver Equipos (Morado) -->
                        <button
                            class="px-4 py-2 bg-[#6F42C1] text-white rounded-lg hover:bg-[#5a2d98] transition duration-300 ease-in-out transform hover:scale-105"
                            onclick="window.location.href='tournament_teams.php?tournament_id=<?php echo $tournament_id; ?>'"
                            aria-label="Ver equipos">
                            <i class="ri-team-line text-lg"></i> Ver Equipos
                        </button>

                        <!-- Botón Compartir (Naranja) -->
                        <button id="shareButton"
                            class="px-4 py-2 bg-[#FD7E14] text-white rounded-lg hover:bg-[#e06e00] transition duration-300 ease-in-out transform hover:scale-105 flex items-center gap-2"
                            aria-label="Compartir torneo">
                            <i class="ri-share-line text-lg"></i> Compartir
                        </button>
                    </div>
                </div>

                <!-- Tournament Information -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8 p-4">
                    <!-- Información del Torneo -->
                    <div class="bg-[#1E293B] rounded-lg p-6 shadow-lg">
                        <h2 class="text-2xl font-semibold text-white mb-4">Información del Torneo</h2>
                        <div class="space-y-4 text-gray-300">
                            <div class="flex items-center">
                                <i class="ri-football-line w-6 text-[#4CAF50]"></i>
                                <span
                                    class="ml-3"><?php echo htmlspecialchars($tournament_data['sport_type'] . ', ' . $tournament_data['gender']); ?></span>
                            </div>
                            <div class="flex items-center">
                                <i class="ri-organization-chart w-6 text-[#FF9800]"></i>
                                <span class="ml-3">Modalidad:
                                    <?php echo htmlspecialchars($tournament_data['format_type']); ?></span>
                            </div>
                            <div class="flex items-center">
                                <i class="ri-money-dollar-circle-line w-6 text-[#FF5722]"></i>
                                <span class="ml-3">Inscripción:
                                    S/<?php echo number_format($tournament_data['registration_fee'], 2); ?></span>
                            </div>
                            <div class="flex items-center">
                                <i class="ri-time-line w-6 text-[#03A9F4]"></i>
                                <span class="ml-3">Horario:
                                    <?php echo htmlspecialchars($tournament_data['match_time_range']); ?></span>
                            </div>
                        </div>
                    </div>

                    <!-- Estado y Programación -->
                    <div class="bg-[#1E293B] rounded-lg p-6 shadow-lg">
                        <h2 class="text-2xl font-semibold text-white mb-4">Estado y Programación</h2>
                        <div class="space-y-4">
                            <!-- Estado -->
                            <div class="flex items-center">
                                <span class="px-3 py-1 rounded-full text-sm
                    <?php echo $tournament_data['status'] == 'Pendiente' ? 'bg-yellow-500/20 text-yellow-500' :
                        ($tournament_data['status'] == 'En Progreso' ? 'bg-green-500/20 text-green-500' :
                            'bg-blue-500/20 text-blue-500'); ?>">
                                    <?php echo htmlspecialchars($tournament_data['status']); ?>
                                </span>
                            </div>
                            <!-- Fechas -->
                            <div class="flex items-center">
                                <i class="ri-calendar-line w-6 text-[#9C27B0]"></i>
                                <span class="ml-3">Inicio:
                                    <?php echo date('d/m/Y', strtotime($tournament_data['start_date'])); ?></span>
                            </div>
                            <div class="flex items-center">
                                <i class="ri-calendar-check-line w-6 text-[#9C27B0]"></i>
                                <span class="ml-3">Fin:
                                    <?php echo date('d/m/Y', strtotime($tournament_data['end_date'])); ?></span>
                            </div>
                            <!-- Días de Juego -->
                            <div>
                                <h3 class="text-sm font-medium text-gray-400 mb-2">Días de Juego:</h3>
                                <div class="flex flex-wrap gap-2">
                                    <?php
                                    $playing_days = explode(',', $tournament_data['playing_days']);
                                    foreach ($playing_days as $day) {
                                        echo "<span class='px-2 py-1 bg-gray-700 rounded-md text-sm'>" . htmlspecialchars($day) . "</span>";
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Premios para Ganadores -->
                    <div class="bg-[#1E293B] rounded-lg p-6 shadow-lg">
                        <h2 class="text-2xl font-semibold text-white mb-4">Premios para Ganadores</h2>
                        <div id="prizeList" class="mt-2 text-gray-300">
                            <?php if (!empty($prizes)): ?>
                                <?php foreach ($prizes as $prize): ?>
                                    <div><?php echo htmlspecialchars(trim($prize)); ?></div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="text-gray-500">No hay premios agregados.</div>
                            <?php endif; ?>
                        </div>

                        <h3 class="text-2xl font-semibold mt-6 text-white">Bases del Torneo</h3>
                        <div id="baseList" class="mt-2 text-gray-300">
                            <?php if (!empty($bases)): ?>
                                <?php foreach ($bases as $base): ?>
                                    <div>
                                        <a href="<?php echo htmlspecialchars(trim($base)); ?>"
                                            class="text-blue-500 hover:text-blue-400"
                                            download><?php echo htmlspecialchars(basename(trim($base))); ?></a>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="text-gray-500">No hay bases agregadas.</div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Ubicación -->
                    <div class="bg-[#1E293B] rounded-lg p-6 mb-8 shadow-lg">
                        <h2 class="text-2xl font-semibold text-white mb-4">Ubicación</h2>
                        <div class="flex items-center mb-4">
                            <i class="ri-map-pin-line w-6 text-[#2196F3]"></i>
                            <span
                                class="ml-3"><?php echo htmlspecialchars($tournament_data['address'] . ', ' . $tournament_data['city'] . ', ' . $tournament_data['country']); ?></span>
                        </div>
                        <?php if (!empty($tournament_data['google_maps_url'])): ?>
                            <a href="<?php echo htmlspecialchars($tournament_data['google_maps_url']); ?>" target="_blank"
                                class="inline-flex items-center text-blue-500 hover:text-blue-400">
                                <i class="ri-map-2-line mr-2"></i>
                                Ver en Google Maps
                            </a>
                        <?php endif; ?>
                    </div>

                    <!-- Administrador del Torneo -->
                    <div class="bg-[#1E293B] rounded-lg p-6 shadow-lg">
                        <h2 class="text-2xl font-semibold text-white mb-4">Administrador del Torneo</h2>
                        <div class="flex items-center">
                            <img src="<?php echo htmlspecialchars($tournament_data['profile_image'] ?? '../public/img/usuario1.png'); ?>"
                                alt="Admin Profile" class="w-16 h-16 rounded-full border-2 border-[#4CAF50]">
                            <div class="ml-4">
                                <h3 class="font-semibold text-white">
                                    <?php echo htmlspecialchars($tournament_data['admin_name']); ?>
                                </h3>
                                <p class="text-gray-400">
                                    <?php echo htmlspecialchars($tournament_data['admin_email']); ?>
                                </p>
                                <button
                                    class="mt-2 px-4 py-2 bg-[#7C3AED] text-white rounded-md hover:bg-[#6D28D9] transition duration-300">
                                    Perfil
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
      
    </div>

    <script>
        // Mostrar el GIF de carga durante 4 segundos
        setTimeout(function () {
            document.getElementById('loading').style.display = 'none';
        }, 1500); 
    </script>

<script>
        // Toggle sidebar and content collapse
        document.getElementById('toggleSidebar').addEventListener('click', function () {
            document.getElementById('sidebar').classList.toggle('collapsed');
            document.getElementById('navbar').classList.toggle('collapsed');
            document.getElementById('content').classList.toggle('collapsed');
        });
    </script>

    <script>
        // User menu toggle
        const userMenuBtn = document.getElementById('userMenuBtn');
        const userMenu = document.getElementById('userMenu');

        userMenuBtn.addEventListener('click', () => {
            userMenu.classList.toggle('hidden');
        });

        document.addEventListener('click', (e) => {
            if (!userMenuBtn.contains(e.target) && !userMenu.contains(e.target)) {
                userMenu.classList.add('hidden');
            }
        });
    </script>
</body>

</html>