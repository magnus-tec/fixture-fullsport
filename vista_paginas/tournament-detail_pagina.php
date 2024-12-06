<?php
session_start();
require_once "../config/connection.php";

// Get tournament details
$tournament_id = $_GET['id'] ?? null;
if (!$tournament_id) {
    header("Location: ../views/tournaments.php");
    exit();
}

$sql = "SELECT * FROM tournaments WHERE id = ?";
$stmt = $con->prepare($sql);
$stmt->bind_param("i", $tournament_id);
$stmt->execute();
$result = $stmt->get_result();
$tournament = $result->fetch_assoc();

if (!$tournament) {
    header("Location: ../views/tournaments.php");
    exit();
}

// Obtener versiones del torneo
$versions_sql = "SELECT * FROM tournament_versions WHERE tournament_id = ?";
$versions_stmt = $con->prepare($versions_sql);
$versions_stmt->bind_param("i", $tournament_id);
$versions_stmt->execute();
$versions_result = $versions_stmt->get_result();
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($tournament['name']); ?> - Full Sports</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/remixicon/fonts/remixicon.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="../public/css/style.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

    <style>
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

        .content {
            margin-left: 280px;
            padding: 20px;
            transition: margin-left 0.3s ease;
            min-height: calc(100vh - 60px);
            display: flex;
            flex-direction: column;
        }

        .content.collapsed {
            margin-left: 80px;
        }

        .content > div {
            max-width: 1200px;
            width: 100%;
            margin: 0 auto;
            flex-grow: 1;
        }

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
            <form class="d-flex ms-auto" style="width: 50%; max-width: 400px;">
                <input class="form-control me-2" type="search" placeholder="Buscar Torneos..." aria-label="Search"
                    style="background: linear-gradient(145deg, #f4f4f9, #d1d5db); color: #2d3748; border: none; border-radius: 30px; box-shadow: 2px 2px 8px rgba(0, 0, 0, 0.1);">
                <button class="btn btn-outline-light" type="submit"
                    style="border-radius: 30px; background-color: #6b21a8; color: white;">
                    <i class="bi bi-search"></i>
                </button>
            </form>
            <ul class="navbar-nav ms-auto">
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

                <!-- Login Button -->
                <li class="nav-item">
                    <a href="../auth/login.php" class="nav-link btn btn-outline-light">
                        <i class="bi bi-box-arrow-in-right me-2"></i>Iniciar Sesión
                    </a>
                </li>
            </ul>
        </div>
    </nav>

    <!-- Content -->
    <div class="content" id="content">
        <!-- Tournament Content -->
        <div class="p-6">
            <!-- Tournament Header -->
            <div class="relative h-64 rounded-lg overflow-hidden mb-6">
                <img src="<?php echo htmlspecialchars($tournament['cover_image'] ? '../public/' . $tournament['cover_image'] : '../public/img/banner.jpg'); ?>"
                    alt="<?php echo htmlspecialchars($tournament['name']); ?> Cover" class="w-full h-full object-cover">
                <div class="absolute bottom-0 left-0 right-0 p-6 bg-gradient-to-t from-black to-transparent">
                    <div class="flex items-center gap-4">
                        <div id="tournamentLogo"
                            class="w-20 h-20 bg-gray-700 rounded-full flex items-center justify-center overflow-hidden cursor-pointer"
                            onclick="openLogoModal()">
                            <?php if ($tournament['logo_image']): ?>
                                <img src="../public/<?php echo htmlspecialchars($tournament['logo_image']); ?>"
                                    alt="Logo del torneo" class="w-full h-full object-cover">
                            <?php else: ?>
                                <img src="../public/img/default-logo.png" alt="Logo predeterminado"
                                    class="w-full h-full object-cover">
                            <?php endif; ?>
                        </div>
                        <div>
                            <h1 class="text-2xl font-bold"><?php echo htmlspecialchars($tournament['name']); ?></h1>
                            <p class="text-gray-300"><?php echo htmlspecialchars($tournament['description']); ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tournament Actions -->
            <section class="bg-gray-800 rounded-lg p-6 mb-6">
                <div class="flex justify-between items-center">
                    <!-- Stats Section -->
                    <div class="grid grid-cols-2 gap-6">
                        <div class="text-center">
                            <p class="text-gray-400 text-sm">Seguidores</p>
                            <p id="followersCount" class="text-2xl font-bold text-white">0</p>
                        </div>
                        <div class="text-center">
                            <p class="text-gray-400 text-sm">Visitas</p>
                            <p class="text-2xl font-bold text-white">0</p>
                        </div>
                    </div>

                    <!-- Buttons Section -->
                    <div class="flex gap-4">
                        <!-- Follow Button -->
                        <button id="followButton"
                            class="px-5 py-3 bg-blue-500 text-white font-semibold rounded-md shadow-md hover:bg-blue-600 focus:ring-2 focus:ring-blue-400 focus:ring-offset-2 focus:outline-none transition-all">
                            Seguir
                        </button>
                        <!-- Share Button -->
                        <button id="shareButton"
                            class="px-5 py-3 bg-orange-500 text-white font-semibold rounded-md shadow-md hover:bg-orange-600 focus:ring-2 focus:ring-orange-400 focus:ring-offset-2 focus:outline-none transition-all">
                            Compartir Este Torneo
                        </button>
                    </div>
                </div>
            </section>

            <!-- Tournament URL -->
            <div class="mt-6">
                <label for="tournamentUrl" class="text-sm text-gray-400 block mb-2">URL del torneo:</label>
                <div class="flex items-center gap-3">
                    <input id="tournamentUrl" type="text"
                        value="https://fullsportplay.com/pages/<?php echo htmlspecialchars($tournament['url_slug']); ?>"
                        class="flex-1 bg-gray-800 rounded-lg px-4 py-2 text-gray-200 border border-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:ring-offset-2 transition-all"
                        readonly>
                    <button onclick="copyTournamentUrl()"
                        class="px-5 py-2 bg-blue-500 text-white font-semibold rounded-lg hover:bg-blue-600 focus:ring-2 focus:ring-blue-400 focus:ring-offset-2 focus:outline-none transition-all">
                        Copiar
                    </button>
                </div>
            </div>

            <!-- Tournament Versions -->
            <div class="mt-6">
                <h2 class="text-lg font-medium text-gray-200 mb-4">Versiones del Torneo</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    <?php while ($version = $versions_result->fetch_assoc()): ?>
                        <div class="relative bg-[#1e293b] rounded-lg shadow-lg overflow-hidden text-center w-64">
                            <span class="absolute top-3 left-3 bg-purple-600 text-white text-xs font-bold py-1 px-3 rounded-md">
                                Fútbol
                            </span>
                            <div class="absolute top-3 right-3 text-white-500 text-xl">
                                <i class="ri-calendar-line"></i>
                            </div>
                            <div>
                                <img src="../public/img/banner.jpg" alt="Imagen de fondo"
                                    class="w-full h-32 object-cover rounded-t-lg">
                            </div>
                            <div class="-mt-8 flex justify-center">
                                <img src="../public/img/usuario1.png" alt="Foto de perfil"
                                    class="w-16 h-16 rounded-full border-4 border-purple-600">
                            </div>
                            <h3 class="mt-4 text-base font-semibold text-gray-200">
                                <?php echo htmlspecialchars($version['name']); ?>
                            </h3>
                            <p class="text-sm text-gray-400 mb-4">0 Seguidores</p>
                            <a href="tournament_overview.php?tournament_id=<?php echo $tournament_id; ?>&version_id=<?php echo $version['id']; ?>"
                                class="inline-block bg-purple-600 hover:bg-purple-700 text-white py-2 px-4 rounded-md text-sm font-medium transition">
                                Ver Version
                            </a>
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Toggle sidebar and content collapse
        document.getElementById('toggleSidebar').addEventListener('click', function () {
            document.getElementById('sidebar').classList.toggle('collapsed');
            document.getElementById('navbar').classList.toggle('collapsed');
            document.getElementById('content').classList.toggle('collapsed');
        });

        // Copy tournament URL
        function copyTournamentUrl() {
            const urlInput = document.querySelector('input[readonly]');
            urlInput.select();
            document.execCommand('copy');
            alert('URL copiada al portapapeles');
        }

        // Share tournament
        document.getElementById('shareButton').addEventListener('click', function () {
            if (navigator.share) {
                navigator.share({
                    title: '<?php echo htmlspecialchars($tournament['name']); ?>',
                    text: '¡Mira este torneo en Full Sports!',
                    url: 'https://fullsportplay.com/t/<?php echo htmlspecialchars($tournament['url_slug']); ?>'
                });
            } else {
                copyTournamentUrl();
            }
        });

        // Follow button functionality
        const followButton = document.getElementById('followButton');
        const followersCount = document.getElementById('followersCount');
        let isFollowing = false;
        let followerCount = 0;

        followButton.addEventListener('click', () => {
            if (isFollowing) {
                isFollowing = false;
                followerCount = Math.max(0, followerCount - 1);
                followButton.textContent = 'Seguir';
                followButton.classList.replace('bg-red-500', 'bg-blue-500');
                followButton.classList.replace('hover:bg-red-600', 'hover:bg-blue-600');
            } else {
                isFollowing = true;
                followerCount++;
                followButton.textContent = 'Dejar de Seguir';
                followButton.classList.replace('bg-blue-500', 'bg-red-500');
                followButton.classList.replace('hover:bg-blue-600', 'hover:bg-red-600');
            }
            followersCount.textContent = followerCount;
        });
    </script>
</body>
</html>
