<?php
session_start();
if (!isset($_SESSION['email'])) {
    header("Location: ../auth/login.php");
    exit();
}

require_once "../config/connection.php";

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


            <!-- Team Content -->
            <div class="p-6">
                <!-- Tournament Header -->
                <div class="relative h-64 rounded-lg overflow-hidden mb-6">
                    <img src="<?php echo htmlspecialchars($team['image'] ? $team['image'] : '../public/img/banner.jpg'); ?>"
                        alt="Portada" class="w-full h-full object-cover">
                    <div class="absolute bottom-0 left-0 right-0 p-6 bg-gradient-to-t from-black to-transparent">
                        <div class="flex items-center gap-4">
                            <div class="w-20 h-20 bg-gray-700 rounded-full flex items-center justify-center overflow-hidden">
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




                <h1 class="text-4xl font-semibold text-white text-center mb-4">DETALLES DEL EQUIPO</h1>
                <!-- Contenedor General con grid para dividir en 3/4 y 1/4 -->
                <div class="max-w-7xl mx-auto p-6">
                    <!-- Acciones del Equipo -->
                    <div class="bg-[#162032] rounded-lg p-6 mb-6">
                        <div class="flex flex-wrap gap-4 justify-center items-center">
                            <button 
                                class="px-6 py-3 bg-[#f97316] text-white rounded-md hover:bg-[#ff6600] text-sm font-semibold transition duration-200">
                                <i class="ri-share-line mr-2"></i> Compartir Equipo
                            </button>
                            <button 
                                class="px-6 py-3 bg-green-500 text-white rounded-md hover:bg-green-600 text-sm font-semibold transition duration-200">
                                <i class="ri-megaphone-line mr-2"></i> Publicar Noticias
                            </button>
                            <button
                                class="px-6 py-3 bg-blue-500 text-white rounded-md hover:bg-blue-600 text-sm font-semibold transition duration-200">
                                <i class="ri-calendar-line mr-2"></i> Próximos Partidos
                            </button>
                            <button onclick="window.location.href='players.php?id=<?php echo $team['id']; ?>'"
                                class="px-6 py-3 bg-[#7C3AED] text-white rounded-md hover:bg-[#6D28D9] text-sm font-semibold transition duration-200">
                                <i class="ri-eye-line text-lg"></i> Ver Jugadores
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

                        <!-- Sección: Socios Registrados -->
                        <div class="col-span-1 flex flex-col space-y-4 mt-6">
                            <h2 class="text-lg font-semibold text-white mb-4 mt-6">Socios Registrados</h2>

                            <?php if (!empty($members)): ?>
                                <?php foreach ($members as $member): ?>
                                    <div class="bg-[#162032] rounded-lg p-6">
                                        <h3 class="text-lg font-bold text-white">
                                            <?php echo htmlspecialchars($member['name'], ENT_QUOTES, 'UTF-8'); ?>
                                        </h3>
                                        <p class="text-gray-400">
                                            Perfil Red Social:
                                            <a href="<?php echo htmlspecialchars($member['social_profile'], ENT_QUOTES, 'UTF-8'); ?>"
                                                class="text-blue-500 underline hover:text-blue-400 focus:outline-none focus:ring-2 focus:ring-blue-300"
                                                target="_blank" rel="noopener noreferrer">
                                                <?php echo htmlspecialchars($member['social_profile'], ENT_QUOTES, 'UTF-8'); ?>
                                            </a>
                                        </p>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="bg-[#162032] rounded-lg p-6">
                                    <p class="text-gray-500">No hay socios registrados.</p>
                                </div>
                            <?php endif; ?>
                        </div>

                    </div> <!-- Fin del Contenedor Principal (grid) -->
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
                </td>
            </tr>";
                        }
                        ?>
                    </tbody>
                </table>

            </div>
        
    </div>

    <script>
        // Toggle user menu
        const userMenuBtn = document.getElementById('userMenuBtn');
        const userMenu = document.getElementById('userMenu');
        userMenuBtn.addEventListener('click', () => {
            userMenu.classList.toggle('hidden');
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
        // Mostrar el GIF de carga durante 4 segundos
        setTimeout(function () {
            document.getElementById('loading').style.display = 'none';
        }, 1500); 
    </script>
</body>

</html>