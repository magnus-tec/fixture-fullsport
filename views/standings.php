<?php
session_start();
if (!isset($_SESSION['email'])) {
    header("Location: ../views/login.php");
    exit();
}
require_once "../config/connection.php";

$tournament_id = $_GET['tournament_id'] ?? null;
$version_id = $_GET['version_id'] ?? null;

if (!$tournament_id) {
    header("Location: tournaments.php");
    exit();
}

// Fetch standings
$stmt = $con->prepare("SELECT s.*, t.name as team_name 
                       FROM standings s 
                       JOIN teams t ON s.team_id = t.id 
                       WHERE s.tournament_version_id = ?
                       ORDER BY s.points DESC, s.goals_for - s.goals_against DESC");
$stmt->bind_param("i", $version_id);
$stmt->execute();
$result = $stmt->get_result();
$standings = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tabla de Posiciones - Full Sports</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
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

        .content>div {
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

        <a href="./" style="font-size: 12px;">
            <i class="bi bi-house-door" style="font-size: 14px; "></i><span>Inicio</span>
        </a>
        <a href="./tournaments.php" style="font-size: 12px;">
            <i class="bi bi-trophy" style="font-size: 14px;"></i><span>Mis Torneos</span>
        </a>
        <a href="./mis_equipos.php" style="font-size: 12px;">
            <i class="bi bi-person-lines-fill" style="font-size: 14px;"></i><span>Mis Equipos</span>
        </a>
        <a href="#" style="font-size: 12px;">
            <i class="bi bi-calendar-check" style="font-size: 14px;"></i><span>Programar Partidos</span>
        </a>
        <a href="calendar.php?tournament_id=<?php echo $tournament_id; ?>&version_id=<?php echo $version_id; ?>"
            style="font-size: 12px;">
            <i class="bi bi-calendar" style="font-size: 14px;"></i><span>Calendario</span>
        </a>

        <a href="standings.php?tournament_id=<?php echo $tournament_id; ?>&version_id=<?php echo $version_id; ?>" style="font-size: 12px; background: linear-gradient(90deg, #6b21a8, #7c3aed); color: white;
             box-shadow: 0 5px 15px rgba(109, 40, 217, 0.4); transform: scale(1.05);">
            <i class="bi bi-table" style="font-size: 14px;"></i><span>Tabla de Posiciones</span>
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
                <li class="nav-item dropdown">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-person-circle" style="font-size: 1.5rem;"></i>
                        <span
                            class="text-xs text-gray-400 ms-2"><?php echo htmlspecialchars($_SESSION['email'], ENT_QUOTES, 'UTF-8'); ?></span>
                        <a class="nav-link dropdown-toggle ms-2" href="#" id="userDropdown" role="button"
                            data-bs-toggle="dropdown" aria-expanded="false"></a>
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
        <div class="p-6">
            <h1 class="text-2xl font-bold mb-6">Tabla de Posiciones</h1>

            <div class="overflow-x-auto">
                <table class="w-full bg-[#1E293B] rounded-lg overflow-hidden">
                    <thead>
                        <tr class="bg-[#2D3748] text-white">
                            <th class="p-3 text-left">Posición</th>
                            <th class="p-3 text-left">Equipo</th>
                            <th class="p-3 text-center">PJ</th>
                            <th class="p-3 text-center">G</th>
                            <th class="p-3 text-center">E</th>
                            <th class="p-3 text-center">P</th>
                            <th class="p-3 text-center">GF</th>
                            <th class="p-3 text-center">GC</th>
                            <th class="p-3 text-center">DG</th>
                            <th class="p-3 text-center">Pts</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($standings as $index => $team): ?>
                            <tr class="text-gray-300 border-b border-gray-700">
                                <td class="p-3"><?php echo $index + 1; ?></td>
                                <td class="p-3"><?php echo htmlspecialchars($team['team_name']); ?></td>
                                <td class="p-3 text-center"><?php echo $team['played']; ?></td>
                                <td class="p-3 text-center"><?php echo $team['won']; ?></td>
                                <td class="p-3 text-center"><?php echo $team['drawn']; ?></td>
                                <td class="p-3 text-center"><?php echo $team['lost']; ?></td>
                                <td class="p-3 text-center"><?php echo $team['goals_for']; ?></td>
                                <td class="p-3 text-center"><?php echo $team['goals_against']; ?></td>
                                <td class="p-3 text-center"><?php echo $team['goals_for'] - $team['goals_against']; ?></td>
                                <td class="p-3 text-center font-bold"><?php echo $team['points']; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('toggleSidebar').addEventListener('click', function () {
            document.getElementById('sidebar').classList.toggle('collapsed');
            document.getElementById('navbar').classList.toggle('collapsed');
            document.getElementById('content').classList.toggle('collapsed');
        });
    </script>
</body>

</html>