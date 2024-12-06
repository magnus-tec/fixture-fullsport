<?php
session_start();

require_once "../config/connection.php";

$tournament_id = $_GET['tournament_id'] ?? null;

if (!$tournament_id) {

    exit();
}

// Obtener equipos del torneo
$sql = "SELECT * FROM teams WHERE tournament_id = ?";
$stmt = $con->prepare($sql);
$stmt->bind_param("i", $tournament_id);
$stmt->execute();
$result = $stmt->get_result();
$teams = $result->fetch_all(MYSQLI_ASSOC);

// Obtener el nombre del torneo
$tournament_sql = "SELECT name FROM tournaments WHERE id = ?";
$tournament_stmt = $con->prepare($tournament_sql);
$tournament_stmt->bind_param("i", $tournament_id);
$tournament_stmt->execute();
$tournament_result = $tournament_stmt->get_result();
$tournament_name = $tournament_result->fetch_assoc()['name'];
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Equipos del Torneo - Full Sports</title>
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
        <a href="../vista_paginas/tournament_overview_pagina.php?tournament_id=<?php echo htmlspecialchars($tournament['id']); ?>&version_id=<?php echo htmlspecialchars($tournament['version_id']); ?>"
            style="font-size: 12px;">
            <i class="bi bi-info-circle" style="font-size: 14px;"></i><span>Información</span>
        </a>
        <a href="../vista_paginas/calendar.php?tournament_id=<?php echo $tournament_id; ?>&version_id=<?php echo $version_id; ?>"
            style="font-size: 12px;">
            <i class="bi bi-calendar-event" style="font-size: 14px;"></i><span>Calendario</span>
        </a>
        <a href="../vista_paginas/tournament_teams.php" style="font-size: 12px; background: linear-gradient(90deg, #6b21a8, #7c3aed);
        color: white; box-shadow: 0 5px 15px rgba(109, 40, 217, 0.4); transform: scale(1.05);">
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

        <!-- Teams Content -->
        <div class="p-6">
            <!-- Teams List -->
            <div id="teams-list" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                <?php foreach ($teams as $team): ?>
                    <div class="bg-[#1E293B] rounded-lg p-4 flex flex-col items-center">
                        <div class="w-16 h-16 bg-gray-700 rounded-full flex items-center justify-center mb-2">
                            <img src="<?php echo htmlspecialchars($team['logo']); ?>" alt="Team Logo"
                                class="w-full h-full object-cover rounded-full"
                                onerror="this.src='../public/img/default-logo.png';" />
                        </div>

                        <h3 class="font-semibold text-center"><?php echo htmlspecialchars($team['name']); ?></h3>
                        <p class="text-sm text-gray-400 text-center">@<?php echo htmlspecialchars($team['country']); ?>
                        </p> <!-- Asegúrate de que 'username' esté en la base de datos -->
                        <a href="../auth/login.php"
                            class="mt-2 px-4 py-2 bg-[#7C3AED] text-white rounded-md hover:bg-[#6D28D9] transition-colors">Ver Plantel</a>
                    </div>
                <?php endforeach; ?>
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
    </script>

    <script>
        // Toggle user menu
        const userMenuBtn = document.getElementById('userMenuBtn');
        const userMenu = document.getElementById('userMenu');
        userMenuBtn.addEventListener('click', () => {
            userMenu.classList.toggle('hidden');
        });

        // Function to fetch and display teams
        function fetchTeams() {
            const tournamentId = <?php echo json_encode($tournament_id); ?>;
            fetch(../controllers/refrescar_teams.php ? tournament_id = ${ tournamentId })
                .then(response => response.json())
                .then(data => {
                    const teamsList = document.getElementById('teams-list');
                    teamsList.innerHTML = ''; // Limpiar la lista actual
                    data.teams.forEach(team => {
                        const teamDiv = document.createElement('div');
                        teamDiv.className = 'bg-[#1E293B] rounded-lg p-4 flex flex-col items-center';
                        teamDiv.innerHTML = `
                   <div class="w-16 h-16 bg-gray-700 rounded-full flex items-center justify-center mb-2">
                        ${team.logo ?
                                <img src="${team.logo}" alt="Logo de ${team.name}" class="w-full h-full object-cover rounded-full" /> :
                                <i class="ri-shield-line text-3xl"></i>
                            }
                    </div>
                    <h3 class="font-semibold text-center">${team.name}</h3>
                    <p class="text-sm text-gray-400 text-center">@${team.country}</p>
                    <a href="team-detail.php?id=${team.id}" class="mt-2 px-4 py-2 bg-[#7C3AED] text-white rounded-md hover:bg-[#6D28D9] transition-colors">Ver Plantel</a>
                `;
                        teamsList.appendChild(teamDiv);
                    });
                })
                .catch(error => console.error('Error al cargar equipos:', error));
        }

        // Call fetchTeams when the page loads
        document.addEventListener('DOMContentLoaded', fetchTeams);
    </script>
</body>

</html>