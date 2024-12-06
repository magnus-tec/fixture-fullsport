<?php
session_start();
if (!isset($_SESSION['email'])) {
    header("Location: ../views/login.php");
    exit();
}
require_once "../config/connection.php";

$tournament_id = $_GET['tournament_id'] ?? null;
$version_id = $_GET['version_id'] ?? null;

if (!$tournament_id || !$version_id) {
    header("Location: tournaments.php");
    exit();
}

// Obtener equipos de la versión específica del torneo
$sql = "SELECT * FROM teams WHERE tournament_id = ? AND tournament_version_id = ?";
$stmt = $con->prepare($sql);
$stmt->bind_param("ii", $tournament_id, $version_id);
$stmt->execute();
$result = $stmt->get_result();
$teams = $result->fetch_all(MYSQLI_ASSOC);
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

        <!-- Teams Content -->
        <div class="p-6">
            <div class="mt-6 text-center">
                <button id="generateFixture"
                    class="w-full mb-6 p-4 border-2 border-dashed border-gray-700 rounded-lg text-gray-400 hover:text-white hover:border-[#7C3AED] transition-colors">
                    <i class="ri-add-line text-xl"></i>
                    <span>Generar Fixture</span>
                </button>
            </div>

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
                        <a href="team-detail.php?id=<?php echo $team['id']; ?>"
                            class="mt-2 px-4 py-2 bg-[#7C3AED] text-white rounded-md hover:bg-[#6D28D9] transition-colors">Administrar</a>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        </main>
    </div>

    <div id="teamsModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center">
        <div class="bg-[#1A1D24] p-6 rounded-lg w-full max-w-xl">
            <h2 class="text-xl font-bold mb-4">Crear Nuevo Equipo</h2>
            <form id="teamsForm" class="space-y-4">
                <input type="hidden" name="tournament_id" value="<?php echo htmlspecialchars($tournament_id); ?>">
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
                        onclick="closeTeamsModal()">Cancelar</button>
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
    </script>
    <script>
        // Open team modal
        document.getElementById('openTeamModal').addEventListener('click', () => {
            document.getElementById('teamsModal').classList.remove('hidden');
            document.getElementById('teamsModal').classList.add('flex');
        });

        // Close team modal function
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
                        fetchTeams(); // Obtener la lista actualizada de equipos
                    } else {
                        alert('Error al crear el equipo: ' + data.message);
                    }
                })
                .catch(error => console.error('Error:', error));
        });

        // Function to fetch and display teams
        function fetchTeams() {
            const tournamentId = document.querySelector('input[name="tournament_id"]').value; // Obtener el tournament_id del input oculto
            fetch(`../controllers/refrescar_teams.php?tournament_id=${tournamentId}`) // Pasar el tournament_id en la solicitud
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
                                `<img src="${team.logo}" alt="Logo de ${team.name}" class="w-full h-full object-cover rounded-full" />` :
                                `<i class="ri-shield-line text-3xl"></i>`
                            }
                    </div>
                    <h3 class="font-semibold text-center">${team.name}</h3>
                    <p class="text-sm text-gray-400 text-center">@${team.country}</p>
                    <a href="team-detail.php?id=${team.id}" class="mt-2 px-4 py-2 bg-[#7C3AED] text-white rounded-md hover:bg-[#6D28D9] transition-colors">Administrar</a>
                `;
                        teamsList.appendChild(teamDiv);
                    });
                })
                .catch(error => console.error('Error al cargar equipos:', error));
        }
    </script>

<script>
    document.getElementById('generateFixture').addEventListener('click', function () {
        const tournamentId = <?php echo json_encode($tournament_id); ?>;
        const versionId = <?php echo json_encode($version_id); ?>;
        fetch('../controllers/generate_fixture.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ tournament_id: tournamentId, version_id: versionId })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Éxito',
                    text: 'Fixture generado exitosamente.',
                    background: '#1a1d24',
                    color: '#ffffff'
                }).then(() => {
                    window.location.href = `calendar.php?tournament_id=${tournamentId}&version_id=${versionId}`;
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'No se pudo generar el fixture: ' + data.message,
                    background: '#1a1d24',
                    color: '#ffffff'
                });
            }
        })
        .catch(error => console.error('Error:', error));
    });
</script>
</body>

</html>