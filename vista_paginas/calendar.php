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

// Fetch fixtures
$stmt = $con->prepare("SELECT f.*, ht.name as home_team, at.name as away_team 
                       FROM fixtures f 
                       JOIN teams ht ON f.home_team_id = ht.id 
                       JOIN teams at ON f.away_team_id = at.id 
                       WHERE f.tournament_version_id = ? 
                       ORDER BY f.match_date, f.match_time");
$stmt->bind_param("i", $version_id);
$stmt->execute();
$result = $stmt->get_result();
$fixtures = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calendario - Full Sports</title>
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

        .content > div {
            max-width: 1200px;
            width: 100%;
            margin: 0 auto;
            flex-grow: 1;
        }


                /* Estilos personalizados */
                .text-primary {
            color: #3B82F6;
        }

        .btn-primary {
            background: linear-gradient(to right, #7834dc, #7834dc);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 9999px;
            font-weight: bold;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            transform: scale(1.05);
            box-shadow: 0 4px 14px #7834dc;
        }

        .btn-secondary {
            background: linear-gradient(to right, #7834dc, #7834dc);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 9999px;
            font-weight: bold;
            transition: all 0.3s ease;
        }

        .btn-secondary:hover {
            transform: scale(1.05);
            box-shadow: 0 4px 14px #7834dc;
        }

        @media (max-width: 768px) {
            .sidebar {
                width: 100%;
                height: auto;
                position: relative;
            }

            .content, .content.collapsed {
                margin-left: 0;
                width: 100%;
            }

            .navbar, .navbar.collapsed {
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

        <a href="../vista_paginas/tournament_overview_pagina.php?tournament_id=<?php echo htmlspecialchars($tournament['id']); ?>&version_id=<?php echo htmlspecialchars($tournament['version_id']); ?>" style="font-size: 12px;">
            <i class="bi bi-info-circle" style="font-size: 14px;"></i><span>Información</span>
        </a>
        <a href="../vista_paginas/calendar.php?tournament_id=<?php echo $tournament_id; ?>&version_id=<?php echo $version_id; ?>"
            style="font-size: 12px; background: linear-gradient(90deg, #6b21a8, #7c3aed);
        color: white; box-shadow: 0 5px 15px rgba(109, 40, 217, 0.4); transform: scale(1.05);">
            <i class="bi bi-calendar-event" style="font-size: 14px;"></i><span>Calendario</span>
        </a>
        <a href="../vista_paginas/mis_equipos.php" style="font-size: 12px;">
            <i class="bi bi-people" style="font-size: 14px;"></i><span>Equipos</span>
        </a>
        <a href="" style="font-size: 12px;">
            <i class="bi bi-check-circle" style="font-size: 14px;"></i><span>Resultados</span>
        </a>
        <a href="" style="font-size: 12px;">
            <i class="bi bi-bar-chart-line" style="font-size: 14px;"></i><span>Tablas</span>
        </a>
        <a href="" style="font-size: 12px;">
            <i class="bi bi-award" style="font-size: 14px;"></i><span>Goles/Tarjetas</span>
        </a>
        <a href="#" style="font-size: 12px;">
            <i class="bi bi-newspaper" style="font-size: 14px;"></i><span>Noticias</span>
        </a>
    </div>

    <!-- Navbar -->
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg" id="navbar">
        <div class="container-fluid">
            <ul class="navbar-nav ms-auto">
                <!-- Botones de acción (escritorio) -->
                <div class="hidden lg:flex items-center space-x-4">
                    <a href="./auth/login.php" class="btn-secondary">Iniciar Sesión</a>
                    <a href="./auth/register.php" class="btn-primary">Registrarse</a>
                </div>
            </ul>
        </div>
    </nav>

    <!-- Content -->
    <div class="content" id="content">
        <div class="p-6">
            <h1 class="text-2xl font-bold mb-6">Calendario de Partidos</h1>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <?php foreach ($fixtures as $fixture): ?>
                    <div class="bg-[#1E293B] rounded-lg p-4 shadow-md">
                        <div class="text-lg font-semibold mb-2 text-center">
                            <?php echo htmlspecialchars($fixture['home_team']); ?> vs <?php echo htmlspecialchars($fixture['away_team']); ?>
                        </div>
                        <div class="text-sm text-gray-400 text-center">
                            Fecha: <?php echo date('d/m/Y', strtotime($fixture['match_date'])); ?>
                        </div>
                        <div class="text-sm text-gray-400 text-center">
                            Hora: <?php echo date('H:i', strtotime($fixture['match_time'])); ?>
                        </div>
                        <div class="mt-3 text-center">
                            <span class="px-3 py-1 bg-[#7C3AED] text-white text-xs rounded-full">
                                <?php echo htmlspecialchars($fixture['status']); ?>
                            </span>
                        </div>
                    </div>
                <?php endforeach; ?>
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