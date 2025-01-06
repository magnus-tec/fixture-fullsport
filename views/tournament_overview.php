<?php
session_start();
require_once "../config/connection.php";

if (!isset($_SESSION['email'])) {
    header("Location: ../views/login.php");
    exit();
}

$tournament_id = $_GET['tournament_id'] ?? null;
$version_id = $_GET['version_id'] ?? null;
$category_id = $_GET['category_id'] ?? null;

if (!$tournament_id || !$version_id) {
    header("Location: tournaments.php");
    exit();
}

//Obtener el nombre de la categoria
$sql2 = "SELECT name from tournament_categories where id = ?";
$stmt2 = $con->prepare($sql2);
$stmt2->bind_param("i", $category_id);
$stmt2->execute();
$result2 = $stmt2->get_result();
$category_name = $result2->fetch_assoc();

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

        <!-- Tournament Details -->
        <div class="p-6">
            <!-- Header Image and Basic Info -->
            <div class="relative h-64 rounded-lg overflow-hidden mb-6">
                <img src="<?php echo htmlspecialchars('../public/' . ($tournament_data['cover_image'] ?? '../public/img/banner.jpg')); ?>"
                    alt="Tournament Cover" class="w-full h-full object-cover">
                <button onclick="openImageModal(<?php echo htmlspecialchars($version_id); ?>)"
                    class="absolute top-4 right-4 px-4 py-2 bg-green-500 text-white rounded-md hover:bg-green-600">
                    Cambiar Imagen
                </button>
                <div class="absolute bottom-0 left-0 right-0 p-6 bg-gradient-to-t from-black to-transparent">
                    <h1 class="text-3xl font-bold"><?php echo htmlspecialchars($tournament_data['name']); ?></h1>
                    <p class="text-xl font bold text-gray-400"><?php echo htmlspecialchars($category_name['name']); ?> </p>
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
                    <!-- Botón Equipos (Verde) -->
                    <button
                        class="px-4 py-2 bg-[#28A745] text-white rounded-lg hover:bg-[#218838] transition duration-300 ease-in-out transform hover:scale-105 flex items-center gap-2"
                        onclick="openTeamsModal()" aria-label="Abrir modal de equipos">
                        <i class="ri-folder-line text-lg"></i> Crear Equipo
                    </button>

                    <!-- Botón Mis Equipos (Morado) -->
                    <button
                        class="px-4 py-2 bg-[#6F42C1] text-white rounded-lg hover:bg-[#5a2d98] transition duration-300 ease-in-out transform hover:scale-105"
                        onclick="window.location.href='tournament_teams.php?tournament_id=<?php echo $tournament_id; ?>&version_id=<?php echo $version_id; ?>&category_id=<?php echo $category_id ?>'"
                        aria-label="Ver mis equipos">
                        <i class="ri-team-line text-lg"></i> Mis Equipos
                    </button>

                    <!-- Botón Ver (Azul) -->
                    <button
                        class="px-4 py-2 bg-[#04376e] text-white rounded-lg hover:bg-[#13357b] transition duration-300 ease-in-out transform hover:scale-105 flex items-center gap-2"
                        onclick="window.location.href = 'calendar.php?tournament_id=<?php echo $tournament_id; ?>&version_id=<?php echo $version_id; ?>&category_id=<?php echo $category_id ?>'"
                        aria-label="Ver calendario del torneo">
                        <i class="ri-calendar-line text-lg"></i> Calendario
                    </button>

                    <button
                        class="px-4 py-2 bg-[#007BFF] text-white rounded-lg hover:bg-[#0056b3] transition duration-300 ease-in-out transform hover:scale-105 flex items-center gap-2"
                        onclick="window.location.href = 'standings.php?tournament_id=<?php echo $tournament_id; ?>&version_id=<?php echo $version_id; ?>'"
                        aria-label="Ver tabla de posiciones">
                        <i class="ri-table-line text-lg"></i> Tabla
                    </button>

                    <!-- Botón Editar (Amarillo) -->
                    <button onclick="openEditVersionModal()"
                        class="px-4 py-2 bg-[#FFC107] text-white rounded-lg hover:bg-[#e0a800] transition duration-300 ease-in-out transform hover:scale-105 flex items-center gap-2"
                        aria-label="Editar versión del torneo">
                        <i class="ri-edit-line text-lg"></i> Editar
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
                    <div class="mt-4">
                        <button
                            class="bg-green-500 text-white px-4 py-2 rounded-md hover:bg-green-600 transition duration-300"
                            onclick="openAddEditPrizesModal(<?php echo $version_id; ?>)">
                            Agregar/Editar Premios
                        </button>
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
                    <div class="mt-4">
                        <button
                            class="bg-[#007BFF] text-white px-4 py-2 rounded-md hover:bg-[#0056b3] transition duration-300"
                            onclick="openAddTournamentBasesModal(<?php echo $version_id; ?>)">
                            Agregar Bases del Torneo
                        </button>
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
        </main>
    </div>

    <!-- Modal de carga de imágenes -->
    <div id="imageUploadModal"
        class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
        <div class="bg-white p-6 rounded-lg w-96">
            <h2 class="text-xl font-semibold mb-4 text-gray-800">Subir Nueva Imagen</h2>
            <form id="imageUploadForm" enctype="multipart/form-data">
                <input type="file" id="tournamentImage" accept="image/*" name="image" required class="mb-4">
                <input type="hidden" id="versionId" name="version_id" value="">
                <p id="selectedFileName" class="mt-2 text-gray-600">Ningún archivo seleccionado</p>
                <div class="flex justify-end mt-4">
                    <button type="button" class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600 mr-2"
                        onclick="closeImageModal()">Cancelar</button>
                    <button type="submit"
                        class="px-4 py-2 bg-green-500 text-white rounded-md hover:bg-green-600">Subir</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal de Edición de la Versión del Torneo -->
    <div id="editVersionModal"
        class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-[#1E293B] p-8 rounded-lg w-full max-w-4xl max-h-[90vh] overflow-y-auto shadow-lg">
            <!-- Título del Modal -->
            <h2 class="text-2xl font-semibold mb-6 text-white">Editar Detalles de la Versión del Torneo</h2>

            <form id="editVersionForm" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <input type="hidden" name="version_id" value="<?php echo htmlspecialchars($version_id); ?>">

                <!-- Nombre del Torneo -->
                <div>
                    <label class="block text-sm font-medium text-gray-400 mb-1">Nombre del Torneo</label>
                    <input type="text" name="version_name" placeholder="Nombre del Torneo"
                        value="<?php echo htmlspecialchars($tournament_data['name']); ?>" required
                        class="w-full p-3 rounded bg-gray-800 border border-gray-700 text-white">
                </div>

                <!-- Nombre de la Versión -->
                <div>
                    <label class="block text-sm font-medium text-gray-400 mb-1">Nombre de la Versión</label>
                    <input type="text" name="version_name" placeholder="Nombre de la Versión"
                        value="<?php echo htmlspecialchars($tournament_data['name']); ?>" required
                        class="w-full p-3 rounded bg-gray-800 border border-gray-700 text-white">
                </div>

                <!-- Fecha de Inicio -->
                <div>
                    <label class="block text-sm font-medium text-gray-400 mb-1">Fecha de Inicio</label>
                    <div class="relative">
                        <input id="startDate" type="text" name="start_date"
                            value="<?php echo htmlspecialchars($tournament_data['start_date']); ?>" required
                            class="w-full p-3 rounded bg-gray-800 border border-gray-700 text-white">
                        <button type="button" id="startDateIcon"
                            class="absolute inset-y-0 right-3 flex items-center text-gray-400 focus:outline-none">
                            <i class="far fa-calendar-alt"></i>
                        </button>
                    </div>
                </div>

                <!-- Fecha de Fin -->
                <div>
                    <label class="block text-sm font-medium text-gray-400 mb-1">Fecha de Fin</label>
                    <div class="relative">
                        <input id="endDate" type="text" name="end_date"
                            value="<?php echo htmlspecialchars($tournament_data['end_date']); ?>" required
                            class="w-full p-3 rounded bg-gray-800 border border-gray-700 text-white">
                        <button type="button" id="endDateIcon"
                            class="absolute inset-y-0 right-3 flex items-center text-gray-400 focus:outline-none">
                            <i class="far fa-calendar-alt"></i>
                        </button>
                    </div>
                </div>
                <!-- País -->
                <div>
                    <label class="block text-sm font-medium text-gray-400 mb-1">País</label>
                    <input type="text" name="country" placeholder="País"
                        value="<?php echo htmlspecialchars($tournament_data['country']); ?>" required
                        class="w-full p-3 rounded bg-gray-800 border border-gray-700 text-white">
                </div>

                <!-- Ciudad -->
                <div>
                    <label class="block text-sm font-medium text-gray-400 mb-1">Ciudad</label>
                    <input type="text" name="city" placeholder="Ciudad"
                        value="<?php echo htmlspecialchars($tournament_data['city']); ?>" required
                        class="w-full p-3 rounded bg-gray-800 border border-gray-700 text-white">
                </div>

                <!-- Dirección -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-400 mb-1">Dirección</label>
                    <textarea name="address" placeholder="Dirección" required
                        class="w-full p-3 rounded bg-gray-800 border border-gray-700 text-white"
                        rows="3"><?php echo htmlspecialchars($tournament_data['address']); ?></textarea>
                </div>

                <!-- Cuota de Inscripción -->
                <div>
                    <label class="block text-sm font-medium text-gray-400 mb-1">Cuota de Inscripción</label>
                    <input type="number" name="registration_fee" placeholder="Cuota de Inscripción" step="0.01"
                        value="<?php echo htmlspecialchars($tournament_data['registration_fee']); ?>" required
                        class="w-full p-3 rounded bg-gray-800 border border-gray-700 text-white">
                </div>

                <!-- Estado -->
                <div>
                    <label class="block text-sm font-medium text-gray-400 mb-1">Estado</label>
                    <select name="status" required
                        class="w-full p-3 rounded bg-gray-800 border border-gray-700 text-white">
                        <option value="Pendiente" <?php echo $tournament_data['status'] == 'Pendiente' ? 'selected' : ''; ?>>Pendiente</option>
                        <option value="En Progreso" <?php echo $tournament_data['status'] == 'En Progreso' ? 'selected' : ''; ?>>En Progreso</option>
                        <option value="Finalizado" <?php echo $tournament_data['status'] == 'Finalizado' ? 'selected' : ''; ?>>Finalizado</option>
                    </select>
                </div>

                <!-- URL de Google Maps -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-400 mb-1">URL de Google Maps</label>
                    <input type="url" name="google_maps_url" placeholder="URL de Google Maps"
                        value="<?php echo htmlspecialchars($tournament_data['google_maps_url']); ?>"
                        class="w-full p-3 rounded bg-gray-800 border border-gray-700 text-white">
                </div>

                <!-- Días de Juego -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-400 mb-1">Días de Juego</label>
                    <div class="flex flex-wrap gap-4">
                        <?php
                        $days = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'];
                        $selected_days = explode(',', $tournament_data['playing_days']);
                        foreach ($days as $day) {
                            $checked = in_array($day, $selected_days) ? 'checked' : '';
                            echo "<label class='inline-flex items-center'>
                            <input type='checkbox' name='playing_days[]' value='$day' $checked 
                                class='form-checkbox h-5 w-5 text-[#4F46E5] bg-gray-800 border-gray-600'>
                            <span class='ml-2 text-white'>$day</span>
                          </label>";
                        }
                        ?>
                    </div>
                </div>

                <!-- Rango de Horario de Partidos -->
                <div>
                    <label class="block text-sm font-medium text-gray-400 mb-1">Rango de Horario de Partidos</label>
                    <div class="grid grid-cols-2 gap-2">
                        <!-- Hora de Inicio -->
                        <div class="relative">
                            <input id="startTime" type="text" name="start_time" placeholder="Hora de Inicio"
                                value="<?php echo htmlspecialchars(explode(' - ', $tournament_data['match_time_range'])[0]); ?>"
                                required class="w-full p-3 rounded bg-gray-800 border border-gray-700 text-white">
                            <button type="button" id="startTimeIcon"
                                class="absolute inset-y-0 right-3 flex items-center text-gray-400 focus:outline-none">
                                <i class="far fa-clock"></i>
                            </button>
                        </div>
                        <!-- Hora de Fin -->
                        <div class="relative">
                            <input id="endTime" type="text" name="end_time" placeholder="Hora de Fin"
                                value="<?php echo htmlspecialchars(explode(' - ', $tournament_data['match_time_range'])[1]); ?>"
                                required class="w-full p-3 rounded bg-gray-800 border border-gray-700 text-white">
                            <button type="button" id="endTimeIcon"
                                class="absolute inset-y-0 right-3 flex items-center text-gray-400 focus:outline-none">
                                <i class="far fa-clock"></i>
                            </button>
                        </div>
                    </div>
                </div>

            </form>

            <!-- Botones -->
            <div class="flex justify-end gap-4 mt-6">
                <button type="button" class="px-5 py-2 bg-red-600 text-white rounded hover:bg-red-700"
                    onclick="closeEditVersionModal()">Cancelar</button>
                <button type="submit" form="editVersionForm"
                    class="px-5 py-2 bg-green-600 text-white rounded hover:bg-green-700">Guardar</button>
            </div>
        </div>
    </div>

    <!-- Modal para Agregar/Editar Premios -->
    <div id="addEditPrizesModal"
        class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" role="dialog"
        aria-labelledby="addEditPrizesTitle" aria-hidden="true">
        <div class="bg-white p-6 rounded-lg w-96 max-w-sm">
            <h2 id="addEditPrizesTitle" class="text-xl font-semibold mb-4 text-gray-800">Agregar/Editar Premio</h2>
            <form id="addEditPrizesForm">
                <input type="hidden" name="versionId">
                <textarea id="prizeDescription" name="prizeDescription" rows="4"
                    class="w-full border rounded p-2 mb-4 text-gray-800 focus:outline-none focus:ring-2 focus:ring-green-500"
                    placeholder="Descripción del premio" required></textarea>
                <div class="flex justify-end space-x-2">
                    <button type="button"
                        class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-300"
                        onclick="closeModal()">Cancelar</button>
                    <button type="submit"
                        class="px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-green-300">Guardar</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal para Agregar Bases del Torneo -->
    <div id="addTournamentBasesModal"
        class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" role="dialog"
        aria-labelledby="addTournamentBasesTitle" aria-hidden="true">
        <div class="bg-white p-6 rounded-lg w-96 max-w-sm">
            <h2 id="addTournamentBasesTitle" class="text-xl font-semibold mb-4 text-gray-800">Agregar Bases del Torneo
            </h2>
            <form id="addTournamentBasesForm" enctype="multipart/form-data">
                <input type="hidden" name="versionId">
                <label for="tournamentBasesFile" class="block text-sm text-gray-700 mb-2">Seleccionar archivo de
                    bases</label>
                <input type="file" id="tournamentBasesFile" name="tournamentBasesFile"
                    class="w-full mb-4 border rounded p-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    aria-describedby="selectedFileName" onchange="updateFileName()">
                <p id="selectedFileName" class="text-gray-600 mb-4">Ningún archivo seleccionado</p>
                <div class="flex justify-end space-x-2">
                    <button type="button"
                        class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-300"
                        onclick="closeModal()">Cancelar</button>
                    <button type="submit"
                        class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-300">Guardar</button>
                </div>
            </form>
        </div>
    </div>




    <div id="teamsModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-[#1A1D24] p-6 rounded-lg w-full max-w-xl">
            <h2 class="text-xl font-bold mb-4 text-white">Crear Nuevo Equipo</h2>
            <form id="teamsForm" class="space-y-4" enctype="multipart/form-data">
                <input type="hidden" name="tournament_id" value="<?php echo htmlspecialchars($tournament_id); ?>">
                <input type="hidden" name="tournament_version_id" value="<?php echo htmlspecialchars($version_id); ?>">
                <input type="hidden" name="category_id" value="<?php echo htmlspecialchars($category_id); ?>">

                <div>
                    <label for="teamName" class="block text-sm font-medium mb-1 text-white">Nombre de tu Equipo:</label>
                    <input type="text" name="team_name" id="teamName"
                        class="w-full p-2 rounded bg-gray-800 border border-gray-700 text-white" required>
                </div>

                <div>
                    <label for="teamCountry" class="block text-sm font-medium mb-1 text-white">Selecciona País:</label>
                    <input type="text" name="country" id="teamCountry"
                        class="w-full p-2 rounded bg-gray-800 border border-gray-700 text-white" required>
                </div>

                <div class="flex items-center">
                    <label class="block text-sm font-medium mb-1 text-white">Selecciona un color:</label>
                    <div id="colorPicker" class="flex items-center ml-2 cursor-pointer"
                        onclick="document.getElementById('teamColor').click();">
                        <input type="color" name="color" id="teamColor" class="hidden" required>
                        
                        <div class="ml-2">
                            <input type="color" name="color" id="colorPalette"
                                class="w-10 h-10 rounded-full cursor-pointer" oninput="updateSelectedColor(this.value)">
                        </div>
                    </div>
                </div>

                <!-- Campo para subir logo -->
                <div>
                    <label for="teamLogo" class="block text-sm font-medium mb-1 text-white">Sube el Logo (solo
                        imágenes):</label>
                    <div id="logoPreviewContainer"
                        class="w-24 h-24 border border-gray-700 rounded overflow-hidden flex items-center justify-center cursor-pointer"
                        onclick="document.getElementById('teamLogo').click();">
                        <input type="file" name="logo" id="teamLogo" accept="image/*" class="hidden" >
                        <img id="logoPreview" src="" alt="Vista previa del logo"
                            class="hidden w-full h-full object-cover">
                        <span class="text-gray-400">Seleccionar Logo</span>
                    </div>
                </div>

                <div class="flex justify-end gap-4 mt-6">
                    <button type="button" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700"
                        onclick="closeTeamsModal()">Cancelar</button>
                    <button type="submit"
                        class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">Crear</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Muestra la vista previa del logo
        document.getElementById('teamLogo').addEventListener('change', function (event) {
            const file = event.target.files[0];
            const preview = document.getElementById('logoPreview');
            const previewContainer = document.getElementById('logoPreviewContainer');

            if (file) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    preview.src = e.target.result;
                    preview.classList.remove('hidden'); // Muestra la imagen
                    previewContainer.classList.remove('hidden'); // Asegura que el contenedor esté visible
                    previewContainer.querySelector('span').classList.add('hidden'); // Oculta el texto de selección
                }
                reader.readAsDataURL(file);
            } else {
                preview.classList.add('hidden'); // Oculta la imagen si no hay archivo
                previewContainer.querySelector('span').classList.remove('hidden'); // Muestra el texto de selección
            }
        });

        // Muestra el color seleccionado
        function updateSelectedColor(color) {
            document.getElementById('selectedColor').style.backgroundColor = color;
        }
    </script>



    <script>
        // Función para actualizar el nombre del archivo seleccionado
        function updateFileName() {
            const fileInput = document.getElementById('tournamentBasesFile');
            const fileNameDisplay = document.getElementById('selectedFileName');

            if (fileInput.files && fileInput.files.length > 0) {
                fileNameDisplay.textContent = fileInput.files[0].name; // Mostrar el nombre del archivo
            } else {
                fileNameDisplay.textContent = "Ningún archivo seleccionado"; // Mensaje por defecto
            }
        }

        // Función para abrir el modal de Agregar/Editar Premios
        function openAddEditPrizesModal(versionId) {
            document.getElementById("addEditPrizesModal").classList.remove("hidden");
            document.querySelector('#addEditPrizesModal input[name="versionId"]').value = versionId; // Actualiza el versionId
        }

        // Función para abrir el modal de Agregar Bases del Torneo
        function openAddTournamentBasesModal(versionId) {
            document.getElementById("addTournamentBasesModal").classList.remove("hidden");
            document.querySelector('#addTournamentBasesModal input[name="versionId"]').value = versionId; // Actualiza el versionId
        }

        // Función para cerrar ambos modales
        function closeModal() {
            document.getElementById("addEditPrizesModal").classList.add("hidden");
            document.getElementById("addTournamentBasesModal").classList.add("hidden");
        }

        // Manejo del formulario de Agregar/Editar Premios
        document.getElementById("addEditPrizesForm").addEventListener("submit", function (event) {
            event.preventDefault(); // Prevenir el envío estándar del formulario

            const formData = new FormData(this);

            fetch("../controllers/save_tournament_prizes_bases.php", {
                method: "POST",
                body: formData,
            })
                .then((response) => response.json())
                .then((data) => {
                    if (data.status === "success") {
                        alert(data.message);

                        // Agregar el premio a la lista
                        const prizeList = document.getElementById("prizeList");
                        const newPrize = document.createElement("div");
                        newPrize.textContent = document.getElementById("prizeDescription").value; // Obtener el valor del premio
                        prizeList.appendChild(newPrize);

                        closeModal(); // Cerrar el modal
                    } else {
                        alert("Error al agregar el premio: " + data.message);
                    }
                })
                .catch((error) => console.error("Error:", error));
        });

        // Manejo del formulario de Agregar Bases del Torneo
        document.getElementById("addTournamentBasesForm").addEventListener("submit", function (event) {
            event.preventDefault(); // Prevenir el envío estándar del formulario

            const formData = new FormData(this);

            fetch("../controllers/save_tournament_prizes_bases.php", {
                method: "POST",
                body: formData,
            })
                .then((response) => response.json())
                .then((data) => {
                    if (data.status === "success") {
                        alert(data.message);

                        // Agregar la base a la lista
                        const baseList = document.getElementById("baseList");
                        const newBase = document.createElement("div");
                        newBase.innerHTML = `<a href="${data.file}" class="text-green-500" download>Descargar</a>`; // Enlace de descarga
                        baseList.appendChild(newBase);

                        closeModal(); // Cerrar el modal
                    } else {
                        alert("Error al agregar la base: " + data.message);
                    }
                })
                .catch((error) => console.error("Error:", error));
        });

        // Actualizar el nombre del archivo seleccionado en tiempo real
        document.getElementById("tournamentBasesFile").addEventListener("change", updateFileName);
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
        // Mostrar el color seleccionado y actualizar la vista
        const colorInput = document.getElementById('teamColor');
        const selectedColorDiv = document.getElementById('selectedColor');
        const colorAcceptBtn = document.getElementById('colorAcceptBtn');

        // Cuando el usuario selecciona un color
        colorInput.addEventListener('input', function () {
            selectedColorDiv.style.backgroundColor = colorInput.value;
        });

        // Acción de aceptar el color
        colorAcceptBtn.addEventListener('click', function () {
            // El color seleccionado ya está siendo mostrado en el div, pero podrías hacer otras acciones si lo necesitas
            alert("Color aceptado: " + colorInput.value);
        });

        // Función para cerrar el modal
        function closeTeamsModal() {
            document.getElementById('teamsModal').classList.add('hidden');
        }
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

        // Edit tournament functionality
        function openEditModal() {
            document.getElementById('editTournamentModal').classList.remove('hidden');
        }

        function closeEditModal() {
            document.getElementById('editTournamentModal').classList.add('hidden');
        }
    </script>
    <script>
        // Image upload functionality
        function openImageModal(versionId) {
            console.log('openImageModal called with versionId:', versionId);
            document.getElementById('imageUploadModal').classList.remove('hidden');
            document.getElementById('versionId').value = versionId;
        }

        function closeImageModal() {
            document.getElementById('imageUploadModal').classList.add('hidden');
            document.getElementById('imageUploadForm').reset();
            document.getElementById('selectedFileName').textContent = "Ningún archivo seleccionado";
        }

        document.getElementById('tournamentImage').addEventListener('change', function () {
            const fileName = this.files[0]?.name || "Ningún archivo seleccionado";
            document.getElementById('selectedFileName').textContent = fileName;
        });

        document.getElementById('imageUploadForm').addEventListener('submit', async function (e) {
            e.preventDefault();

            const formData = new FormData(this);

            try {
                const response = await fetch('../controllers/upload_image.php', {
                    method: 'POST',
                    body: formData,
                });

                const result = await response.json();

                if (result.success) {
                    alert(result.message);
                    const newImagePath = '../public/' + result.new_image_path;
                    document.querySelector('img[alt="Tournament Cover"]').src = newImagePath;
                    closeImageModal();
                } else {
                    alert(result.message || 'Error al subir la imagen.');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Ocurrió un error al procesar la solicitud.');
            }
        });
    </script>
    <script>
        function openEditVersionModal() {
            document.getElementById('editVersionModal').classList.remove('hidden');
        }

        function closeEditVersionModal() {
            document.getElementById('editVersionModal').classList.add('hidden');
        }
        document.getElementById('editVersionForm').addEventListener('submit', function (event) {
            event.preventDefault(); // Evitar el envío normal del formulario

            const formData = new FormData(this);

            fetch('../controllers/save_tournament_version_details.php', { // Cambia la ruta según tu estructura
                method: 'POST',
                body: formData
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert(data.message);
                        closeEditVersionModal(); // Cerrar el modal
                        location.reload(); // Recargar la página para ver los cambios
                    } else {
                        alert('Error al actualizar la versión: ' + data.message);
                    }
                })
                .catch(error => console.error('Error:', error));
        });
    </script>
    <script>
        // Funciones para abrir y cerrar el modal de equipos
        function openTeamsModal() {
            document.getElementById('teamsModal').classList.remove('hidden');
            document.getElementById('teamsModal').classList.add('flex');
        }

        function closeTeamsModal() {
            document.getElementById('teamsModal').classList.remove('flex');
            document.getElementById('teamsModal').classList.add('hidden');
        }

        // En el script de creación de equipos
        document.getElementById('teamsForm').addEventListener('submit', function (event) {
            event.preventDefault(); // Evitar el envío normal del formulario

            const formData = new FormData(this);

            fetch('../controllers/create_equipos.php', {
                method: 'POST',
                body: formData
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Éxito',
                            text: 'Equipo creado exitosamente.',
                            background: '#1a1d24',
                            color: '#ffffff'
                        });
                        closeTeamsModal(); // Cerrar el modal
                        // No redirigir a tournament_teams.php, solo cerrar el modal
                    } else {
                        alert('Error al crear el equipo: ' + data.message);
                    }
                })
                .catch(error => console.error('Error:', error));
        });
    </script>
    <script>
        // Configuración para Fecha de Inicio
        const startDatePicker = flatpickr("#startDate", { dateFormat: "Y-m-d", locale: "es" });

        // Configuración para Fecha de Fin
        const endDatePicker = flatpickr("#endDate", { dateFormat: "Y-m-d", locale: "es" });

        // Configuración para Hora de Inicio
        const startTimePicker = flatpickr("#startTime", { enableTime: true, noCalendar: true, dateFormat: "h:i K" });

        // Configuración para Hora de Fin
        const endTimePicker = flatpickr("#endTime", { enableTime: true, noCalendar: true, dateFormat: "h:i K" });

        // Lógica para abrir los calendarios/selector de hora al hacer clic en los íconos
        document.querySelector("#startDateIcon").addEventListener("click", () => {
            startDatePicker.open();
        });

        document.querySelector("#endDateIcon").addEventListener("click", () => {
            endDatePicker.open();
        });

        document.querySelector("#startTimeIcon").addEventListener("click", () => {
            startTimePicker.open();
        });

        document.querySelector("#endTimeIcon").addEventListener("click", () => {
            endTimePicker.open();
        });

    </script>
</body>

</html>