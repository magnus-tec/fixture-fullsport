<?php
session_start();
if (!isset($_SESSION['email'])) {
    header("Location: ../views/login.php");
    exit();
}

if (!isset($_SESSION['name'])) {
    $_SESSION['name'] = 'Usuario'; // Asigna un valor predeterminado o maneja el error
}
require_once "../config/connection.php";
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Torneos - Full Sports</title>
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

        <a href="../views/" style="font-size: 12px; background: linear-gradient(90deg, #6b21a8, #7c3aed);
        color: white; box-shadow: 0 5px 15px rgba(109, 40, 217, 0.4); transform: scale(1.05);">
            <i class="bi bi-house-door" style="font-size: 14px; "></i><span>Inicio</span>
        </a>
        <a href="../views/tournaments.php" style="font-size: 12px;">
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
    <!-- Content -->
    <div class="content" id="content"
        style="background-color: #1A1F2E; color: #ffffff; min-height: 100vh; padding: 20px;">
        <div style="max-width: 1200px; margin: 0 auto; font-family: Arial, sans-serif;">
            <!-- Tabs -->
            <div
                style="display: flex; gap: 20px; margin-bottom: 30px; border-bottom: 1px solid #2d3548; padding-bottom: 10px;">
                <div id="resumenTab" onclick="switchTab('resumen')"
                    style="color: #ffffff; cursor: pointer; font-weight: bold; position: relative;">
                    Resumen
                    <div id="resumenIndicator"
                        style="position: absolute; bottom: -11px; left: 0; right: 0; height: 2px; background-color: #22C55E;">
                    </div>
                </div>
                <div id="misDatosTab" onclick="switchTab('misDatos')"
                    style="color: #6b7280; cursor: pointer; position: relative;">
                    Mis Datos
                    <div id="misDatosIndicator"
                        style="position: absolute; bottom: -11px; left: 0; right: 0; height: 2px; background-color: #22C55E; display: none;">
                    </div>
                </div>
            </div>

            <!-- Resumen Content -->
            <div id="resumenContent" style="background-color: #1a1f2e; border-radius: 8px; padding: 24px;">
                <div style="display: flex; flex-direction: column; gap: 24px;">
                    <!-- Profile Header -->
                    <div style="display: flex; gap: 24px;">
                        <div style="flex-shrink: 0;">
                            <img src="https://via.placeholder.com/200x200" alt="Profile Picture"
                                style="width: 200px; height: 200px; object-fit: cover; background-color: #242937; border-radius: 4px;">
                            <div style="text-align: center; margin-top: 10px; color: #9ca3af;">@jefferson</div>
                        </div>

                        <!-- Stats -->
                        <div style="flex-grow: 1;">
                            <h2 style="text-align: center; color: #ffffff; margin-bottom: 24px; font-size: 1.5rem;">
                                Resumen Datos y Estadísticas
                            </h2>
                            <div style="display: grid; gap: 16px;">
                                <div style="display: flex; align-items: center; gap: 8px;">
                                    <span style="font-weight: 500; min-width: 200px; color: #9ca3af;">Nombre:</span>
                                    <span>Jefferson Calderon Burgos</span>
                                </div>
                                <div style="display: flex; align-items: center; gap: 8px;">
                                    <span style="font-weight: 500; min-width: 200px; color: #9ca3af;">Torneos Que
                                        Administra:</span>
                                    <span>2</span>
                                </div>
                                <div style="display: flex; align-items: center; gap: 8px;">
                                    <span style="font-weight: 500; min-width: 200px; color: #9ca3af;">Partidos
                                        Jugados:</span>
                                    <span>0</span>
                                </div>
                                <div style="display: flex; align-items: center; gap: 8px;">
                                    <span style="font-weight: 500; min-width: 200px; color: #9ca3af;">Goles
                                        Marcados:</span>
                                    <span>0 Goles</span>
                                </div>
                                <div style="display: flex; align-items: center; gap: 8px;">
                                    <span style="font-weight: 500; min-width: 200px; color: #9ca3af;">Participación
                                        Torneos:</span>
                                    <span>0 Torneos</span>
                                </div>
                                <div style="display: flex; align-items: center; gap: 8px;">
                                    <span style="font-weight: 500; min-width: 200px; color: #9ca3af;">Participación
                                        Equipos:</span>
                                    <span>0 Equipos</span>
                                </div>
                                <div style="display: flex; align-items: center; gap: 8px;">
                                    <span
                                        style="font-weight: 500; min-width: 200px; color: #9ca3af;">Campeonatos:</span>
                                    <span>0 Campeonatos</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- History Sections -->
                    <div style="margin-top: 24px;">
                        <h3 style="color: #9ca3af; font-size: 1.1rem; margin-bottom: 12px;">Historial de Equipos:</h3>
                        <p style="color: #6b7280;">No esta registrado en ningun equipo</p>

                        <h3 style="color: #9ca3af; font-size: 1.1rem; margin: 24px 0 12px;">Historial de Torneos:</h3>
                        <p style="color: #6b7280;">No esta registrado en ningun torneo</p>
                    </div>
                </div>
            </div>

            <!-- Mis Datos Section -->
            <div id="misDatosContent"
                style="display: none; background-color: #1a1f2e; border-radius: 8px; padding: 24px; margin-top: 24px;">
                <div style="display: flex; gap: 24px;">
                    <div style="flex-shrink: 0;">
                        <img src="https://via.placeholder.com/200x200" alt="Profile Picture"
                            style="width: 200px; height: 200px; object-fit: cover; background-color: #242937; border-radius: 4px;">
                        <button
                            style="display: block; width: 100%; margin-top: 12px; padding: 8px; background-color: #7834dd; color: #ffffff; border: none; border-radius: 4px; cursor: pointer;">Cambiar</button>
                    </div>
                    <div style="flex-grow: 1;">
                        <div style="margin-bottom: 16px;">
                            <label for="username"
                                style="display: block; margin-bottom: 8px; color: #9ca3af;">Username:</label>
                            <div style="display: flex; gap: 8px;">
                                <input type="text" id="username" value="@jefferson"
                                    style="flex-grow: 1; padding: 8px; background-color: #242937; border: 1px solid #374151; border-radius: 4px; color: #ffffff;">
                                <button
                                    style="padding: 8px 16px; background-color: #7834dd; color: #ffffff; border: none; border-radius: 4px; cursor: pointer;">Cambiar</button>
                            </div>
                        </div>
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                            <div>
                                <label for="nombres"
                                    style="display: block; margin-bottom: 8px; color: #9ca3af;">Nombres:</label>
                                <input type="text" id="nombres" value="Jefferson Calderon Burgos"
                                    style="width: 100%; padding: 8px; background-color: #242937; border: 1px solid #374151; border-radius: 4px; color: #ffffff;">
                            </div>
                            <div>
                                <label for="apellidos"
                                    style="display: block; margin-bottom: 8px; color: #9ca3af;">Apellidos:</label>
                                <input type="text" id="apellidos"
                                    style="width: 100%; padding: 8px; background-color: #242937; border: 1px solid #374151; border-radius: 4px; color: #ffffff;">
                            </div>
                            <div>
                                <label for="pais"
                                    style="display: block; margin-bottom: 8px; color: #9ca3af;">País:</label>
                                <input type="text" id="pais"
                                    style="width: 100%; padding: 8px; background-color: #242937; border: 1px solid #374151; border-radius: 4px; color: #ffffff;">
                            </div>
                            <div>
                                <label for="ciudad"
                                    style="display: block; margin-bottom: 8px; color: #9ca3af;">Ciudad:</label>
                                <input type="text" id="ciudad"
                                    style="width: 100%; padding: 8px; background-color: #242937; border: 1px solid #374151; border-radius: 4px; color: #ffffff;">
                            </div>
                            <div>
                                <label for="fnacimiento" style="display: block; margin-bottom: 8px; color: #9ca3af;">F.
                                    Nacimiento:</label>
                                <input type="text" id="fnacimiento" placeholder="dd/mm/aaaa"
                                    style="width: 100%; padding: 8px; background-color: #242937; border: 1px solid #374151; border-radius: 4px; color: #ffffff;">
                            </div>
                            <div>
                                <label for="telefono"
                                    style="display: block; margin-bottom: 8px; color: #9ca3af;">Teléfono:</label>
                                <input type="text" id="telefono" placeholder="+00 000000000"
                                    style="width: 100%; padding: 8px; background-color: #242937; border: 1px solid #374151; border-radius: 4px; color: #ffffff;">
                            </div>
                        </div>
                        <div style="margin-top: 24px;">
                            <div style="display: flex; gap: 10px;">
                                <button
                                    style="flex: 1; padding: 12px; background-color: #22C55E; color: #ffffff; border: none; border-radius: 4px; cursor: pointer;">
                                    Guardar
                                </button>
                                <button
                                    style="flex: 1; padding: 12px; background-color: #7834dd; color: #ffffff; border: 1px solid #374151; border-radius: 4px; cursor: pointer;">
                                    Cambiar Contraseña
                                </button>
                            </div>
                        </div>
                        <div style="margin-top: 24px;">
                            <button
                                style="width: 100%; padding: 12px; background-color: #DC2626; color: #ffffff; border: none; border-radius: 4px; cursor: pointer;">Eliminar
                                mi Cuenta</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function switchTab(tabName) {
            const resumenTab = document.getElementById('resumenTab');
            const misDatosTab = document.getElementById('misDatosTab');
            const resumenContent = document.getElementById('resumenContent');
            const misDatosContent = document.getElementById('misDatosContent');
            const resumenIndicator = document.getElementById('resumenIndicator');
            const misDatosIndicator = document.getElementById('misDatosIndicator');

            if (tabName === 'resumen') {
                resumenTab.style.color = '#ffffff';
                misDatosTab.style.color = '#6b7280';
                resumenContent.style.display = 'block';
                misDatosContent.style.display = 'none';
                resumenIndicator.style.display = 'block';
                misDatosIndicator.style.display = 'none';
            } else {
                resumenTab.style.color = '#ffffff';
                misDatosTab.style.color = '#ffffff';
                resumenContent.style.display = 'none';
                misDatosContent.style.display = 'block';
                resumenIndicator.style.display = 'none';
                misDatosIndicator.style.display = 'block';
            }
        }
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
        // Toggle user menu
        const userMenuBtn = document.getElementById('userMenuBtn');
        const userMenu = document.getElementById('userMenu');
        userMenuBtn.addEventListener('click', () => {
            userMenu.classList.toggle('hidden');
        });

        // Open tournament modal
        document.getElementById('openTournamentModal').addEventListener('click', () => {
            document.getElementById('tournamentModal').classList.remove('hidden');
            document.getElementById('tournamentModal').classList.add('flex');
        });

        // Close tournament modal function
        function closeTournamentModal() {
            document.getElementById('tournamentModal').classList.remove('flex');
            document.getElementById('tournamentModal').classList.add('hidden');
        }
    </script>
</body>

</html>