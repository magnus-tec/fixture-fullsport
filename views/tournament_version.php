<?php
session_start();
if (!isset($_SESSION['email'])) {
    header("Location: ../views/login.php");
    exit();
}

require_once "../config/connection.php";

// Get tournament details
$tournament_id = $_GET['tournament_id'] ?? null;
if (!$tournament_id) {
    header("Location: tournaments.php");
    exit();
}

$sql = "SELECT * FROM tournaments WHERE id = ?";
$stmt = $con->prepare($sql);
$stmt->bind_param("i", $tournament_id);
$stmt->execute();
$result = $stmt->get_result();
$tournament = $result->fetch_assoc();

if (!$tournament) {
    header("Location: tournaments.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Versiones de Torneo - <?php echo htmlspecialchars($tournament['name']); ?></title>
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
    min-height: calc(100vh - 60px); /* Adjust 60px to match your navbar height */
    display: flex;
    flex-direction: column;
}

.content.collapsed {
    margin-left: 80px;
}

/* Add this to ensure proper centering of content */
.content > div {
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

            <!-- Tournament Versions Content -->
            <div class="p-6">
                <div class="flex items-center mb-8">
                    <a href="tournament-detail.php?id=<?php echo $tournament_id; ?>"
                        class="mr-4 text-blue-500 hover:text-blue-600">
                        <i class="ri-arrow-left-line mr-2"></i>Volver
                    </a>
                    <h1 class="text-2xl font-bold text-green-500">Nueva Versión de Torneo para
                        <?php echo htmlspecialchars($tournament['name']); ?>
                    </h1>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div class="bg-[#1E293B] rounded-lg p-6 text-center">
                        <i class="ri-table-line text-5xl text-cyan-500 mb-4"></i>
                        <h3 class="text-xl font-semibold mb-4">Liga</h3>
                        <button onclick="openPointsModal('Liga')"
                            class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                            Comenzar
                        </button>
                    </div>

                    <div class="bg-[#1E293B] rounded-lg p-6 text-center">
                        <i class="ri-git-branch-line text-5xl text-orange-500 mb-4"></i>
                        <h3 class="text-xl font-semibold mb-4">Eliminatoria</h3>
                        <button onclick="openPointsModal('Eliminatoria')"
                            class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                            Comenzar
                        </button>
                    </div>

                    <div class="bg-[#1E293B] rounded-lg p-6 text-center">
                        <i class="ri-git-merge-line text-5xl text-gray-500 mb-4"></i>
                        <h3 class="text-xl font-semibold mb-4">Play Off</h3>
                        <button onclick="openPointsModal('PlayOff')"
                            class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                            Comenzar
                        </button>
                    </div>

                    <div class="bg-[#1E293B] rounded-lg p-6 text-center">
                        <i class="ri-trophy-line text-5xl text-green-500 mb-4"></i>
                        <h3 class="text-xl font-semibold mb-4">Otro</h3>
                        <button onclick="openPointsModal('Otro')"
                            class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                            Comenzar
                        </button>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Points Configuration Modal -->
    <div id="pointsModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center">
        <div class="bg-[#1E293B] p-6 rounded-lg w-full max-w-md">
            <h2 class="text-xl font-bold mb-4">Personalice su Modalidad <span id="modalFormatType"></span></h2>
            <form id="pointsForm" action="create_tournament_version.php" method="POST" class="space-y-4">
                <input type="hidden" name="tournament_id" value="<?php echo $tournament_id; ?>">
                <input type="hidden" name="format_type" id="formatType">

                <div>
                    <label class="block text-sm font-medium mb-1">Puntos Ganador:</label>
                    <input type="number" name="points_winner" value="3" class="w-full p-2 bg-gray-700 rounded">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Puntos Empate:</label>
                    <input type="number" name="points_draw" value="1" class="w-full p-2 bg-gray-700 rounded">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Puntos Derrota:</label>
                    <input type="number" name="points_loss" value="0" class="w-full p-2 bg-gray-700 rounded">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Puntos Walk Over:</label>
                    <input type="number" name="points_walkover" value="0" class="w-full p-2 bg-gray-700 rounded">
                </div>
                <div class="flex justify-end gap-2 mt-6">
                    <button type="button" onclick="closePointsModal()"
                        class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">
                        Cancelar
                    </button>
                    <button type="button" onclick="openConfirmModal()"
                        class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
                        Continuar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal de Confirmación -->
    <div id="confirmModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center">
        <div class="bg-[#1E293B] p-6 rounded-lg w-full max-w-md">
            <h2 class="text-xl font-bold mb-4">Confirmar Creación de Versión</h2>
            <p>¿Está seguro de que desea crear la versión con los puntos ingresados?</p>
            <div class="flex justify-end gap-2 mt-6">
                <button type="button" onclick="closeConfirmModal()"
                    class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">
                    Cancelar
                </button>
                <button id="confirmContinue" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
                    Continuar
                </button>
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
        // User menu toggle
        const userMenuBtn = document.getElementById('userMenuBtn');
        const userMenu = document.getElementById('userMenu');

        userMenuBtn.addEventListener('click', () => {
            userMenu.classList.toggle('hidden');
        });

        // Close menu when clicking outside
        document.addEventListener('click', (e) => {
            if (!userMenuBtn.contains(e.target) && !userMenu.contains(e.target)) {
                userMenu.classList.add('hidden');
            }
        });

        // Points modal functions
        function openPointsModal(format) {
            document.getElementById('modalFormatType').textContent = format;
            document.getElementById('formatType').value = format;
            document.getElementById('pointsModal').classList.remove('hidden');
            document.getElementById('pointsModal').classList.add('flex');
        }

        function closePointsModal() {
            document.getElementById('pointsModal').classList.add('hidden');
            document.getElementById('pointsModal').classList.remove('flex');
        }
    </script>
    <script>
        // Confirm modal functions
        function openConfirmModal() {
            document.getElementById('confirmModal').classList.remove('hidden');
            document.getElementById('confirmModal').classList.add('flex');
        }

        function closeConfirmModal() {
            document.getElementById('confirmModal').classList.add('hidden');
            document.getElementById('confirmModal').classList.remove('flex');
        }

        // Handle confirmation
        document.getElementById('confirmContinue').addEventListener('click', function () {
            const formData = new FormData(document.getElementById('pointsForm'));

            fetch('../controllers/create_tournament_version.php', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                // Manejo de la respuesta...
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    // Redirigir a la página de detalles de la versión
                    window.location.href = `tournament_version_form.php?tournament_id=${formData.get('tournament_id')}&version_id=${data.data.version_id}`;
                } else {
                    throw new Error(data.message || 'Error desconocido');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al procesar la solicitud: ' + error.message);
            })
            .finally(() => {
                closeConfirmModal(); // Cerrar el modal de confirmación
            });
        });
    </script>
</>

</html>