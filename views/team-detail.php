<?php
session_start();
if (!isset($_SESSION['email'])) {
    header("Location: ../views/login.php");
    exit();
}

require_once "../config/connection.php";

$tournament_id = $_GET['tournament_id'] ?? null;
$version_id = $_GET['version_id'] ?? null;
$category_id = $_GET['category_id'] ?? null;

// Obtener detalles del equipo
$team_id = $_GET['id'] ?? null;
if (!$team_id) {
    header("Location: mis_equipos.php");
    exit();
}

$sql = "SELECT * FROM teams WHERE id = ?";
$stmt = $con->prepare($sql);
$stmt->bind_param("i", $team_id);
$stmt->execute();
$result = $stmt->get_result();
$team = $result->fetch_assoc();

if (!$team) {
    header("Location: mis_equipos.php");
    exit();
}

// Obtener socios registrados
$sql_members = "SELECT * FROM members WHERE team_id = ?";
$stmt_members = $con->prepare($sql_members);
$stmt_members->bind_param("i", $team_id);
$stmt_members->execute();
$result_members = $stmt_members->get_result();
$members = $result_members->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($team['name']); ?> - Full Sports</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    <!-- Incluyendo Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="../public/css/style.css" rel="stylesheet">
    <!-- Enlace a Bootstrap JS con Popper.js -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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

        <!-- Team Content -->
        <div class="p-6">
            <!-- Tournament Header -->
            <div class="relative h-64 rounded-lg overflow-hidden mb-6">
                <img src="<?php echo htmlspecialchars($team['image'] ? $team['image'] : '../public/img/banner.jpg'); ?>"
                    alt="Portada" class="w-full h-full object-cover">
                <button onclick="openModal('changeImageModal')"
                    class="absolute top-4 right-4 px-4 py-2 bg-green-500 text-white rounded-md hover:bg-green-600">
                    Cambiar Imagen
                </button>
                <div class="absolute bottom-0 left-0 right-0 p-6 bg-gradient-to-t from-black to-transparent">
                    <div class="flex items-center gap-4">
                        <div class="w-20 h-20 bg-gray-700 rounded-full flex items-center justify-center overflow-hidden cursor-pointer"
                            onclick="openModal('changeLogoModal')">
                            <img src="<?php echo htmlspecialchars($team['logo'] ? $team['logo'] : '../public/img/default-logo.png'); ?>"
                                alt="Logo" class="w-full h-full object-cover rounded-full">
                        </div>
                        <div>
                            <h1 class="text-2xl font-bold"><?php echo htmlspecialchars($team['name']); ?></h1>
                            <p class="text-gray-300">País: <?php echo htmlspecialchars($team['country']); ?></p>
                        </div>
                    </div>
                </div>
            </div>
            <a href="tournament_teams.php?tournament_id=<?php echo $tournament_id; ?>&version_id=<?php echo $version_id; ?>&category_id=<?php echo $category_id ?>"
                    class="px-3 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 flex items-center space-x-2 w-12">
                    <i class="ri-arrow-left-line"></i>
                </a>
            <h1 class="text-4xl font-semibold text-white text-center mb-4">DETALLES DEL EQUIPO</h1>
            <!-- Contenedor General con grid para dividir en 3/4 y 1/4 -->
            <div class="max-w-7xl mx-auto p-6">
                <!-- Acciones del Equipo -->
                <div class="bg-[#162032] rounded-lg p-6 mb-6">
                    <div class="flex flex-wrap gap-4 justify-center items-center">
                        <button onclick="openModal('editTeamModal')"
                            class="px-6 py-3 bg-[#f97316] text-white rounded-md hover:bg-[#ff6600] text-sm font-semibold transition duration-200">
                            Agregar Informacion
                        </button>
                        <button onclick="openModal('addMemberModal')"
                            class="px-6 py-3 bg-green-500 text-white rounded-md hover:bg-green-600 text-sm font-semibold transition duration-200">
                            Agregar Socios
                        </button>
                        <button onclick="openModal('addPlayerModal')"
                            class="px-6 py-3 bg-blue-500 text-white rounded-md hover:bg-blue-600 text-sm font-semibold transition duration-200">
                            Agregar Jugadores
                        </button>
                        <button onclick="window.location.href='players.php?id=<?php echo $team['id']; ?>'"
                            class="px-6 py-3 bg-[#7C3AED] text-white rounded-md hover:bg-[#6D28D9] text-sm font-semibold transition duration-200">
                            Ver mis Jugadores
                        </button>
                        <button id="btn-delete-team"
                        class="px-6 py-3 bg-red-600 text-white rounded hover:bg-red-700">
                        Eliminar Equipo
                        </button>
                    </div>
                </div>

                <!-- Contenedor Principal usando grid -->
                <div class="grid grid-cols-4 gap-6">
                    <!-- Información del Equipo (3/4 de la fila) -->
                    <div class="col-span-3 bg-[#162032] rounded-lg p-6">
                        <h2 class="text-xl font-semibold mb-4 text-white">Información del Equipo</h2>
                        <div class="space-y-4 text-gray-300">
                            <div class="flex items-center">
                                <i class="ri-shield-line w-6 text-[#7C3AED]"></i>
                                <span class="ml-2">Apodo: <?php echo htmlspecialchars($team['nickname']); ?></span>
                            </div>
                            <div class="flex items-center">
                                <i class="ri-map-pin-line w-6 text-[#7C3AED]"></i>
                                <span class="ml-2">Ciudad: <?php echo htmlspecialchars($team['city']); ?></span>
                            </div>
                            <div class="flex items-center">
                                <i class="ri-flag-line w-6 text-[#7C3AED]"></i>
                                <span class="ml-2">Origen: <?php echo htmlspecialchars($team['origin']); ?></span>
                            </div>
                            <div class="flex items-center">
                                <i class="ri-user-line w-6 text-[#7C3AED]"></i>
                                <span class="ml-2">Entrenador:
                                    <?php echo htmlspecialchars($team['coach']); ?></span>
                            </div>
                            <div class="flex items-center">
                                <i class="ri-global-line w-6 text-[#7C3AED]"></i>
                                <span class="ml-2">Página Web: <a
                                        href="<?php echo htmlspecialchars($team['social_media']); ?>"
                                        class="text-blue-500"
                                        target="_blank"><?php echo htmlspecialchars($team['social_media']); ?></a></span>
                            </div>
                        </div>
                    </div>

                    <!-- Botones (1/4 de la fila) -->
                    <div class="col-span-1 flex flex-col justify-start space-y-4 mt-14">
                        <!-- Se ajustó mt-6 para un margen superior más pequeño -->
                        <button
                            class="flex items-center justify-center px-4 py-3 bg-[#f97316] text-white rounded-md hover:bg-[#ff6600] w-full text-sm font-semibold transition duration-200">
                            <i class="ri-share-line mr-2"></i> Compartir Equipo
                        </button>
                        <button
                            class="flex items-center justify-center px-4 py-3 bg-blue-500 text-white rounded-md hover:bg-blue-600 w-full text-sm font-semibold transition duration-200">
                            <i class="ri-calendar-line mr-2"></i> Próximos Partidos
                        </button>
                        <button
                            class="flex items-center justify-center px-4 py-3 bg-green-500 text-white rounded-md hover:bg-green-600 w-full text-sm font-semibold transition duration-200">
                            <i class="ri-megaphone-line mr-2"></i> Publicar Noticias
                        </button>
                    </div>
                </div> <!-- Fin del Contenedor Principal (grid) -->

                <!-- Socios Registrados -->
                <h2 class="text-lg font-semibold text-white mb-4 mt-8">Socios Registrados</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                    <?php if (!empty($members)): ?>
                        <?php foreach ($members as $member): ?>
                            <div class="bg-[#162032] rounded-lg p-6">
                                <h3 class="text-lg font-bold text-white"><?php echo htmlspecialchars($member['name']); ?>
                                </h3>
                                <p class="text-gray-400">Perfil Red Social: <a
                                        href="<?php echo htmlspecialchars($member['social_profile']); ?>" class="text-blue-500"
                                        target="_blank"><?php echo htmlspecialchars($member['social_profile']); ?></a></p>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="bg-[#162032] rounded-lg p-6">
                            <p class="text-gray-500">No hay socios registrados.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>


            <!-- Recent Players Section -->
            <h2 class="text-lg font-semibold mb-4">Últimos Jugadores Registrados</h2>
            <table class="w-full border-collapse bg-[#162032] shadow-md rounded-lg overflow-hidden">
                <thead class="bg-[#1e293b] text-white border-b border-[#7c3aed]">
                    <tr>
                        <th class="py-3 px-6 text-left font-semibold text-sm uppercase">Foto</th>
                        <th class="py-3 px-6 text-left font-semibold text-sm uppercase">Nombres</th>
                        <th class="py-3 px-6 text-left font-semibold text-sm uppercase">Último Equipo</th>
                        <th class="py-3 px-6 text-center font-semibold text-sm uppercase">Camiseta</th>
                        <th class="py-3 px-6 text-left font-semibold text-sm uppercase">Registrado</th>
                        <th class="py-3 px-6 text-center font-semibold text-sm uppercase">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#1e293b] text-gray-300">
                    <?php
                    $sql_players = "SELECT * FROM players WHERE team_id = ?";
                    $stmt_players = $con->prepare($sql_players);
                    $stmt_players->bind_param("i", $team_id);
                    $stmt_players->execute();
                    $result_players = $stmt_players->get_result();

                    while ($player = $result_players->fetch_assoc()) {
                        echo "<tr class='hover:bg-[#1e293b] transition duration-200'>
                <td class='py-4 px-6'>
                    <img src='../public/uploads3/photos/{$player['photo']}' alt='Foto' class='w-12 h-12 rounded-full border border-gray-700'>
                </td>
                <td class='py-4 px-6 font-medium'>{$player['full_name']}</td>
                <td class='py-4 px-6'>{$team['name']}</td>
                <td class='py-4 px-6 text-center font-semibold text-[#7c3aed]'>{$player['shirt_number']}</td>
                <td class='py-4 px-6 text-sm text-gray-400'>{$player['created_at']}</td>
                <td class='py-4 px-6 flex justify-center gap-2'>
                    <button class='px-3 py-2 text-sm font-medium text-white bg-[#7c3aed] rounded-lg hover:bg-[#9f47ff] transition edit-player' data-player='" . htmlspecialchars(json_encode($player), ENT_QUOTES, 'UTF-8') . "'>Editar</button>
                    <button class='px-3 py-2 text-sm font-medium text-white bg-red-500 rounded-lg hover:bg-red-600 transition delete-player' data-id='{$player['id']}'>Eliminar</button>
                </td>
            </tr>";
                    }
                    ?>
                </tbody>
            </table>

        </div>
        </main>
    </div>

    <!-- Modal para mddificar informacion -->
    <div id="editTeamModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center">
        <div class="bg-[#1A1D24] p-6 rounded-lg w-full max-w-xl">
            <h2 class="text-xl font-bold mb-4">Agregar Información de Equipo</h2>
            <form action="../controllers/teamController.php" method="POST">
                <input type="hidden" name="team_id" value="<?php echo $team['id']; ?>">

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium mb-1">Apodo del equipo:</label>
                        <input type="text" name="nickname" value="<?php echo htmlspecialchars($team['nickname']); ?>"
                            class="w-full p-2 rounded bg-gray-800 border border-gray-700" required>
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1">Ciudad:</label>
                        <input type="text" name="city" value="<?php echo htmlspecialchars($team['city']); ?>"
                            class="w-full p-2 rounded bg-gray-800 border border-gray-700" required>
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1">Origen o Procedencia:</label>
                        <input type="text" name="origin" value="<?php echo htmlspecialchars($team['origin']); ?>"
                            class="w-full p-2 rounded bg-gray-800 border border-gray-700" required>
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1">Entrenador:</label>
                        <input type="text" name="coach" value="<?php echo htmlspecialchars($team['coach']); ?>"
                            class="w-full p-2 rounded bg-gray-800 border border-gray-700" required>
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1">Página Web o Perfil Social:</label>
                        <input type="text" name="social_media"
                            value="<?php echo htmlspecialchars($team['social_media']); ?>"
                            class="w-full p-2 rounded bg-gray-800 border border-gray-700" required>
                    </div>
                </div>
                <div class="flex justify-between gap-4 mt-6">
                    <button type="submit" name="delete_team"
                        class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">
                        Eliminar Equipo
                    </button>
                    <div class="flex gap-2">
                        <button type="button" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700"
                            onclick="closeModal('editTeamModal')">
                            Cancelar
                        </button>
                        <button type="submit" name="update_team"
                            class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
                            Guardar
                        </button>
                    </div>
                </div>

            </form>
        </div>
    </div>

    <!-- Modal para agregar socios -->
    <div id="addMemberModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center">
        <div class="bg-[#1A1D24] p-6 rounded-lg w-full max-w-xl">
            <h2 class="text-xl font-bold mb-4">Agregar Socios</h2>
            <form action="../controllers/memberController.php" method="POST">
                <input type="hidden" name="team_id" value="<?php echo $team['id']; ?>">

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium mb-1">Nombre:</label>
                        <input type="text" name="member_name"
                            class="w-full p-2 rounded bg-gray-800 border border-gray-700" required>
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1">Perfil Res Social:</label>
                        <input type="text" name="social_profile"
                            class="w-full p-2 rounded bg-gray-800 border border-gray-700" required>
                    </div>
                </div>

                <div class="flex justify-end gap-2 mt-6">
                    <button type="button" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700"
                        onclick="closeModal('addMemberModal')">Cancelar</button>
                    <button type="submit"
                        class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">Agregar</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal para cambiar imagen -->
    <div id="changeImageModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center">
        <div class="bg-[#1A1D24] p-6 rounded-lg w-full max-w-xl">
            <h2 class="text-xl font-bold mb-4">Cambiar Imagen</h2>
            <form action="../controllers/imageController.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="team_id" value="<?php echo $team['id']; ?>">

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium mb-1">Subir nueva imagen:</label>
                        <input type="file" name="team_image"
                            class="w-full p-2 rounded bg-gray-800 border border-gray-700" required>
                    </div>
                </div>

                <div class="flex justify-end gap-2 mt-6">
                    <button type="button" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700"
                        onclick="closeModal('changeImageModal')">Cancelar</button>
                    <button type="submit"
                        class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">Guardar</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal para cambiar escudo -->
    <div id="changeLogoModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center">
        <div class="bg-[#1E293B] p-6 rounded-lg w-full max-w-md"
            style="background-color: #1E293B; padding: 1.5rem; border-radius: 0.5rem; max-width: 28rem; width: 100%;">
            <h2 class="text-xl font-bold mb-4"
                style="font-size: 1.25rem; font-weight: bold; margin-bottom: 1rem; color: white;">
                Cambiar Logo del Equipo
            </h2>
            <form action="../controllers/logoController.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="team_id" value="<?php echo $team['id']; ?>">

                <div class="space-y-4" style="gap: 1rem; display: flex; flex-direction: column;">
                    <div class="bg-gray-700 p-4 rounded-lg"
                        style="background-color: #374151; padding: 1rem; border-radius: 0.5rem;">
                        <input type="file" name="team_logo" id="teamLogo" accept="image/*" class="hidden" required>
                        <label for="teamLogo"
                            class="inline-block px-4 py-2 bg-gray-600 text-white rounded cursor-pointer hover:bg-gray-500"
                            style="display: inline-block; padding: 0.5rem 1rem; background-color: #4B5563; color: white; border-radius: 0.375rem; cursor: pointer; text-align: center;">
                            Seleccionar archivo
                        </label>
                        <span id="selectedTeamLogoName" class="ml-2 text-gray-300"
                            style="margin-left: 0.5rem; color: #D1D5DB;">
                            Ningún archivo seleccionado
                        </span>
                    </div>
                </div>

                <div class="flex justify-end gap-2 mt-6"
                    style="display: flex; justify-content: flex-end; gap: 0.5rem; margin-top: 1.5rem;">
                    <button type="button" class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600"
                        style="padding: 0.5rem 1rem; background-color: #EF4444; color: white; border-radius: 0.375rem; cursor: pointer;"
                        onclick="closeModal('changeLogoModal')">
                        Cancelar
                    </button>
                    <button type="submit" class="px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600"
                        style="padding: 0.5rem 1rem; background-color: #10B981; color: white; border-radius: 0.375rem; cursor: pointer;">
                        Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div id="addPlayerModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-[#1A1D24] p-6 rounded-lg w-full max-w-2xl max-h-[90vh] overflow-y-auto shadow-lg">
            <!-- Header del Modal -->
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold text-white">Agregar Deportista</h2>
                <button class="text-gray-400 hover:text-gray-200 focus:outline-none"
                    onclick="closeModal('addPlayerModal')">
                    <i class="fas fa-times text-2xl"></i>
                </button>
            </div>
            <!-- Formulario -->
            <form action="../controllers/playerController.php" method="POST" enctype="multipart/form-data"
                class="space-y-6">
                <input type="hidden" name="team_id" value="<?php echo $team['id']; ?>">

                <!-- Documento y Tipo -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="relative">
                        <label for="document_number" class="block text-sm font-medium text-gray-400 flex items-center">
                            <i class="fas fa-id-card mr-2"></i>N° Documento
                        </label>
                        <input type="text" id="document_number" name="document_number"
                            class="w-full p-3 rounded-md bg-gray-800 text-white border border-gray-700 focus:outline-none focus:ring-2 focus:ring-green-500"
                            required>
                    </div>
                    <div>
                        <label for="document_type" class="block text-sm font-medium text-gray-400">Tipo
                            Documento</label>
                        <select id="document_type" name="document_type"
                            class="w-full p-3 rounded-md bg-gray-800 text-white border border-gray-700 focus:outline-none focus:ring-2 focus:ring-green-500"
                            required>
                            <option value="DNI">DNI (Cédula de Identidad)</option>
                            <option value="Pasaporte">Pasaporte</option>
                            <option value="Cedula Extranjera">Cédula Extranjera</option>
                            <option value="Otros">Otros</option>
                        </select>
                    </div>
                </div>

                <!-- Nombre Completo -->
                <div class="relative">
                    <label for="full_name" class="block text-sm font-medium text-gray-400 flex items-center">
                        <i class="fas fa-user mr-2"></i>Nombre Completo
                    </label>
                    <input type="text" id="full_name" name="full_name"
                        class="w-full p-3 rounded-md bg-gray-800 text-white border border-gray-700 focus:outline-none focus:ring-2 focus:ring-green-500"
                        readonly required>
                </div>


                <!-- Dirección -->
                <div class="relative">
                    <label class="block text-sm font-medium text-gray-400 flex items-center mb-1">
                        <i class="fas fa-map-marker-alt mr-2"></i>Dirección, Procedencia o referencia:
                    </label>
                    <input type="text" name="address" class="w-full p-3 rounded-md bg-gray-800 border border-gray-700"
                        required>
                </div>

                <!-- Teléfono y Fecha de nacimiento -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="relative">
                        <label class="block text-sm font-medium text-gray-400 flex items-center mb-1">
                            <i class="fas fa-phone mr-2"></i>Teléfono o N° Contacto:
                        </label>
                        <input type="text" name="contact_number"
                            class="w-full p-3 rounded-md bg-gray-800 border border-gray-700" required>
                    </div>
                    <div class="relative">
                        <label class="block text-sm font-medium text-gray-400 flex items-center mb-1">
                            <i class="fas fa-calendar mr-2"></i>Fecha Nacimiento:
                        </label>
                        <input type="date" name="birth_date"
                            class="w-full p-3 rounded-md bg-gray-800 border border-gray-700" required>
                    </div>
                </div>

                <!-- Categoría Sexual y Posición -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="relative">
                        <label class="block text-sm font-medium text-gray-400 flex items-center mb-1">
                            <i class="fas fa-venus-mars mr-2"></i>Categoría Sexual:
                        </label>
                        <select name="gender" class="w-full p-3 rounded-md bg-gray-800 border border-gray-700" required>
                            <option value="Varon">Varón</option>
                            <option value="Mujer">Mujer</option>
                        </select>
                    </div>
                    <div class="relative">
                        <label class="block text-sm font-medium text-gray-400 flex items-center mb-1">
                            <i class="fas fa-running mr-2"></i>Posición en Cancha:
                        </label>
                        <select name="position" class="w-full p-3 rounded-md bg-gray-800 border border-gray-700"
                            required>
                            <option value="Portero">Portero</option>
                            <option value="Defensa Central">Defensa Central</option>
                            <option value="Defensa Izquierdo">Defensa Izquierdo</option>
                            <option value="Defensa Derecho">Defensa Derecho</option>
                            <option value="Medio Campo">Medio Campo</option>
                            <option value="Medio Campo Izquierdo">Medio Campo Izquierdo</option>
                            <option value="Medio Campo Derecho">Medio Campo Derecho</option>
                            <option value="Medio Ofensivo">Medio Ofensivo</option>
                            <option value="Delantero Derecho">Delantero Derecho</option>
                            <option value="Delantero Izquierdo">Delantero Izquierdo</option>
                            <option value="Delantero Central">Delantero Central</option>
                            <option value="Cierre - Futsal">Cierre - Futsal</option>
                            <option value="Alas - Futsal">Alas - Futsal</option>
                            <option value="Pivot - Futsal">Pivot - Futsal</option>
                        </select>
                    </div>
                </div>

                <!-- Número y Estado -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="relative">
                        <label class="block text-sm font-medium text-gray-400 flex items-center mb-1">
                            <i class="fas fa-tshirt mr-2"></i>N° Camiseta:
                        </label>
                        <input type="number" name="shirt_number"
                            class="w-full p-3 rounded-md bg-gray-800 border border-gray-700" required>
                    </div>
                    <div class="relative">
                        <label class="block text-sm font-medium text-gray-400 flex items-center mb-1">
                            <i class="fas fa-user-check mr-2"></i>Estado Deportista:
                        </label>
                        <select name="status" class="w-full p-3 rounded-md bg-gray-800 border border-gray-700" required>
                            <option value="Habilitado">Habilitado</option>
                            <option value="Suspendido">Suspendido</option>
                        </select>
                    </div>
                </div>

                <!-- Foto y Documento -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="relative">
                        <label class="block text-sm font-medium text-gray-400 mb-1">Foto:</label>
                        <div class="flex items-center">
                            <div class="relative w-20 h-20 rounded-md overflow-hidden mr-4 border-2 border-white"
                                onclick="openPhotoInput()">
                                <img id="photo-preview" src="#" alt="Foto" class="w-full h-full object-cover"
                                    style="display: none;">
                                <button type="button"
                                    class="absolute top-0 right-0 bg-gray-800 text-white p-2 rounded-full hover:bg-gray-700 focus:outline-none"
                                    style="display: none;">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z">
                                        </path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                </button>
                            </div>
                            <input type="file" name="photo" id="photo-input" class="hidden" accept="image/*"
                                onchange="previewPhoto(this)">
                        </div>
                    </div>

                    <div class="relative">
                        <label class="block text-sm font-medium text-gray-400 mb-1">Documento Identidad
                            (Archivo):</label>
                        <input type="file" name="identity_document"
                            class="w-full p-2 rounded-md bg-gray-800 border border-gray-700"
                            accept=".pdf,.jpg,.jpeg,.png" required>
                    </div>
                </div>

                <div class="flex justify-end gap-2 mt-6">
                    <button type="button" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700"
                        onclick="closeModal('addPlayerModal')">Cancelar</button>
                    <button type="submit"
                        class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">Guardar</button>
                </div>
            </form>
        </div>
    </div>


<!-- Modal para editar jugador -->
<div id="editPlayerModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-[#1A1D24] p-6 rounded-lg w-full max-w-2xl max-h-[90vh] overflow-y-auto shadow-lg">
        <!-- Header del Modal -->
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-white">Editar Jugador</h2>
            <button class="text-gray-400 hover:text-gray-200 focus:outline-none" onclick="closeModal('editPlayerModal')">
                <i class="fas fa-times text-2xl"></i>
            </button>
        </div>
        <!-- Formulario -->
        <form id="editPlayerForm" action="../controllers/playerController.php" method="POST" enctype="multipart/form-data" class="space-y-6">
            <input type="hidden" name="player_id" id="player_id">
            <input type="hidden" name="team_id" value="<?php echo $team['id']; ?>">

            <!-- Documento y Tipo -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="document_number" class="block text-sm font-medium text-gray-400 flex items-center gap-2">
                        <i class="fas fa-id-card"></i> N° Documento
                    </label>
                    <div class="relative">
                        <input type="text" id="document_number" name="document_number"
                            class="w-full p-3 rounded-md bg-gray-800 text-white border border-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500"
                            required>
                    </div>
                </div>
                <div>
                    <label for="document_type" class="block text-sm font-medium text-gray-400 flex items-center gap-2">
                        <i class="fas fa-file-alt"></i> Tipo Documento
                    </label>
                    <select id="document_type" name="document_type"
                        class="w-full p-3 rounded-md bg-gray-800 text-white border border-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        required>
                        <option value="DNI">DNI</option>
                        <option value="Pasaporte">Pasaporte</option>
                        <option value="Cedula Extranjera">Cédula Extranjera</option>
                        <option value="Otro">Otro</option>
                    </select>
                </div>
            </div>

            <!-- Nombre Completo y Dirección -->
            <div>
                <label for="full_name" class="block text-sm font-medium text-gray-400 flex items-center gap-2">
                    <i class="fas fa-user"></i> Nombre Completo
                </label>
                <input type="text" id="full_name" name="full_name"
                    class="w-full p-3 rounded-md bg-gray-800 text-white border border-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    required>
            </div>
            <div>
                <label for="address" class="block text-sm font-medium text-gray-400 flex items-center gap-2">
                    <i class="fas fa-map-marker-alt"></i> Dirección, Procedencia o Referencia
                </label>
                <input type="text" id="address" name="address"
                    class="w-full p-3 rounded-md bg-gray-800 text-white border border-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <!-- Teléfono y Fecha de Nacimiento -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="contact_number" class="block text-sm font-medium text-gray-400 flex items-center gap-2">
                        <i class="fas fa-phone"></i> Teléfono o N° Contacto
                    </label>
                    <input type="text" id="contact_number" name="contact_number"
                        class="w-full p-3 rounded-md bg-gray-800 text-white border border-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        required>
                </div>
                <div>
                    <label for="birth_date" class="block text-sm font-medium text-gray-400 flex items-center gap-2">
                        <i class="fas fa-calendar"></i> Fecha de Nacimiento
                    </label>
                    <input type="date" id="birth_date" name="birth_date"
                        class="w-full p-3 rounded-md bg-gray-800 text-white border border-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        required>
                </div>
            </div>

            <!-- Género y Posición -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="gender" class="block text-sm font-medium text-gray-400 flex items-center gap-2">
                        <i class="fas fa-venus-mars"></i> Categoría Sexual
                    </label>
                    <select id="gender" name="gender"
                        class="w-full p-3 rounded-md bg-gray-800 text-white border border-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        required>
                        <option value="Mujer">Mujer</option>
                        <option value="Varon">Varón</option>
                    </select>
                </div>
                <div>
                    <label for="position" class="block text-sm font-medium text-gray-400 flex items-center gap-2">
                        <i class="fas fa-running"></i> Posición en Cancha
                    </label>
                    <select id="position" name="position"
                        class="w-full p-3 rounded-md bg-gray-800 text-white border border-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        required>
                        <!-- Opciones de posición -->
                        <option value="Portero">Portero</option>
                        <!-- Resto de las posiciones -->
                    </select>
                </div>
            </div>

            <!-- Camiseta y Estado -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="shirt_number" class="block text-sm font-medium text-gray-400 flex items-center gap-2">
                        <i class="fas fa-tshirt"></i> N° Camiseta
                    </label>
                    <input type="number" id="shirt_number" name="shirt_number"
                        class="w-full p-3 rounded-md bg-gray-800 text-white border border-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        required>
                </div>
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-400 flex items-center gap-2">
                        <i class="fas fa-user-check"></i> Estado Deportista
                    </label>
                    <select id="status" name="status"
                        class="w-full p-3 rounded-md bg-gray-800 text-white border border-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        required>
                        <option value="Habilitado">Habilitado</option>
                        <option value="Suspendido">Suspendido</option>
                    </select>
                </div>
            </div>

            <!-- Botones -->
            <div class="flex justify-end gap-4">
                <button type="button"
                    class="px-6 py-2 rounded-md bg-red-600 text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500"
                    onclick="closeModal('editPlayerModal')">Cancelar</button>
                <button type="submit"
                    class="px-6 py-2 rounded-md bg-blue-600 text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    Actualizar
                </button>
            </div>
        </form>
    </div>
</div>




    <script>
        document.getElementById('document_number').addEventListener('input', function () {
            const documentNumber = this.value.trim(); // Obtiene el valor del input
            const documentType = document.getElementById('document_type').value;

            // Verifica que sea DNI y que el número de documento tenga 8 dígitos
            if (documentType === "DNI" && documentNumber.length === 8) {
                const token = "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJlbWFpbCI6ImplZmZlcnNvbmNhbGRlcm9uYnVyZ29zNTNAZ21haWwuY29tIn0.tIbA1UHevR5eJ8irNFCd5FJaLpUJSUSILW2CpHRVPdQ";
                const apiUrl = `https://dniruc.apisperu.com/api/v1/dni/${documentNumber}?token=${token}`;

                fetch(apiUrl)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error("No se encontraron datos para este DNI.");
                        }
                        return response.json();
                    })
                    .then(data => {
                        // Completa los campos con los datos obtenidos
                        document.getElementById('full_name').value = `${data.nombres} ${data.apellidoPaterno} ${data.apellidoMaterno}`;
                    })
                    .catch(error => {
                        console.error(error);
                        alert("Error al buscar datos del DNI: " + error.message);
                    });
            } else if (documentNumber.length > 8) {
                alert("El número de DNI debe tener 8 dígitos.");
            }
        });

        // Limpia los campos si el tipo de documento cambia
        document.getElementById('document_type').addEventListener('change', function () {
            if (this.value !== "DNI") {
                document.getElementById('document_number').value = "";
                document.getElementById('full_name').value = "";
            }
        });
    </script>



    <script>
        // Toggle sidebar and content collapse
        document.getElementById('toggleSidebar').addEventListener('click', function () {
            document.getElementById('sidebar').classList.toggle('collapsed');
            document.getElementById('navbar').classList.toggle('collapsed');
            document.getElementById('content').classList.toggle('collapsed');
        });
    </script>

    <!-- Script para editar -->
    <script>
        function openModal(modalId) {
            const modal = document.getElementById(modalId);
            modal.classList.remove('hidden');
            modal.classList.add('flex'); // Cambia a 'flex' para centrar
        }

        function closeModal(modalId) {
            const modal = document.getElementById(modalId);
            modal.classList.add('hidden');
            modal.classList.remove('flex'); // Cambia a 'hidden' para ocultar
        }
    </script>
    <!-- Script para agregar socios  -->
    <script>
        function openModal(addMemberModal) {
            const modal = document.getElementById(addMemberModal);
            modal.classList.remove('hidden');
            modal.classList.add('flex'); // Cambia a 'flex' para centrar
        }

        function closeModal(addMemberModal) {
            const modal = document.getElementById(addMemberModal);
            modal.classList.add('hidden');
            modal.classList.remove('flex'); // Cambia a 'hidden' para ocultar
        }
    </script>
    <!-- Script para cambiar imagen  -->
    <script>
        function openModal(changeImageModal) {
            const modal = document.getElementById(changeImageModal);
            modal.classList.remove('hidden');
            modal.classList.add('flex'); // Cambia a 'flex' para centrar
        }

        function closeModal(changeImageModal) {
            const modal = document.getElementById(changeImageModal);
            modal.classList.add('hidden');
            modal.classList.remove('flex'); // Cambia a 'hidden' para ocultar
        }


        // Mostrar el nombre del archivo seleccionado
        document.getElementById('teamLogo').addEventListener('change', function () {
            const selectedFileName = this.files[0] ? this.files[0].name : 'Ningún archivo seleccionado';
            document.getElementById('selectedTeamLogoName').textContent = selectedFileName;
        });

        // Función para cerrar el modal
        function closeModal(modalId) {
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
                // Restablecer el texto del nombre del archivo
                document.getElementById('selectedTeamLogoName').textContent = 'Ningún archivo seleccionado';
                // Limpiar el valor del input
                document.getElementById('teamLogo').value = '';
            }
        }
    </script>

    <script>
        // Toggle user menu
        const userMenuBtn = document.getElementById('userMenuBtn');
        const userMenu = document.getElementById('userMenu');
        userMenuBtn.addEventListener('click', () => {
            userMenu.classList.toggle('hidden');
        });
    </script>

    <script>
        function openModal(addPlayerModal) {
            const modal = document.getElementById(addPlayerModal);
            modal.classList.remove('hidden');
            modal.classList.add('flex'); // Cambia a 'flex' para centrar
        }

        function closeModal(addPlayerModal) {
            const modal = document.getElementById(addPlayerModal);
            modal.classList.add('hidden');
            modal.classList.remove('flex'); // Cambia a 'hidden' para ocultar
        }
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Función para abrir el modal de edición
            function openEditModal(playerData) {
                document.getElementById('player_id').value = playerData.id;
                document.getElementById('document_number').value = playerData.document_number;
                document.getElementById('document_type').value = playerData.document_type;
                document.getElementById('full_name').value = playerData.full_name;
                document.getElementById('address').value = playerData.address;
                document.getElementById('contact_number').value = playerData.contact_number;
                document.getElementById('birth_date').value = playerData.birth_date;
                document.getElementById('gender').value = playerData.gender;
                document.getElementById('position').value = playerData.position;
                document.getElementById('shirt_number').value = playerData.shirt_number;
                document.getElementById('status').value = playerData.status;

                openModal('editPlayerModal');
            }

            // Event listener para los botones de editar
            document.querySelectorAll('.edit-player').forEach(function (button) {
                button.addEventListener('click', function () {
                    var playerData = JSON.parse(this.getAttribute('data-player'));
                    openEditModal(playerData);
                });
            });

            // Event listener para los botones de eliminar
            document.querySelectorAll('.delete-player').forEach(function (button) {
                button.addEventListener('click', function () {
                    var playerId = this.getAttribute('data-id');
                    if (confirm("¿Estás seguro de que deseas eliminar este jugador?")) {
                        window.location.href = '../controllers/playerController.php?action=delete&player_id=' + playerId;
                    }
                });
            });
        });
    </script>

    <script>
        function openPhotoInput() {
            document.getElementById('photo-input').click();
        }

        function previewPhoto(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function (e) {
                    document.getElementById('photo-preview').src = e.target.result;
                    document.getElementById('photo-preview').style.display = 'block';
                    document.querySelector('#photo-preview + button').style.display = 'none';
                }
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>

    <script>
        document.getElementById("btn-delete-team").addEventListener("click", () =>{
            Swal.fire({
            title: '¿Estás seguro que quieres eliminar este equipo?',
            text: "Esta acción no se puede deshacer",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                try {
                    fetch('../controllers/delete_team.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({
                            team_id: <?php echo $team_id; ?>
                        })
                    }).then((res) => res.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire(
                                'Eliminado',
                                'El equipo ha sido eliminado.',
                                'success'
                            );
                            window.location.href = "tournaments.php";
                        } else {
                            alert('Error al eliminar el equipo: ' + data.message);
                        }
                    });

                } catch (error) {
                    console.error('Error:', error);
                    alert('Error al eliminar el equipo');
                }
            }
        });
            
        });
    </script>
</body>

</html>