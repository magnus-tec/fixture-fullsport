<?php
session_start();
require_once "../config/connection.php";

if (!isset($_SESSION['email'])) {
    header("Location: ../views/login.php");
    exit();
}

$tournament_id = $_GET['tournament_id'] ?? null;
$version_id = $_GET['version_id'] ?? null;

if (!$tournament_id || !$version_id) {
    header("Location: tournaments.php");
    exit();
}

// Fetch tournament and version details
$sql = "SELECT t.name AS tournament_name, tv.name AS version_name, tv.format_type, tvd.* 
        FROM tournaments t
        JOIN tournament_versions tv ON t.id = tv.tournament_id
        JOIN tournament_version_details tvd ON tv.id = tvd.version_id
        WHERE t.id = ? AND tv.id = ?";
$stmt = $con->prepare($sql);
$stmt->bind_param("ii", $tournament_id, $version_id);
$stmt->execute();
$result = $stmt->get_result();
$tournament_version = $result->fetch_assoc();

if (!$tournament_version) {
    header("Location: tournaments.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalles de la Versión del Torneo - <?php echo htmlspecialchars($tournament_version['version_name']); ?>
    </title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
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
        /* Custom styles to match the original design */
        body {
            background-color: #0F172A;
            color: white;
        }

        .sidebar {
            background-color: #1A1D24;
            border-right-color: #1f2937;
        }

        .custom-input {
            background-color: #374151;
            border-color: #4B5563;
            color: white;
        }

        .custom-input:focus {
            border-color: #7C3AED;
            outline: none;
            box-shadow: 0 0 0 2px rgba(124, 58, 237, 0.2);
        }

        .custom-select {
            background-color: #374151;
            border-color: #4B5563;
            color: white;
        }

        .custom-checkbox:checked {
            background-color: #7C3AED;
            border-color: #7C3AED;
        }

        .custom-submit {
            background-color: #7C3AED;
        }

        .custom-submit:hover {
            background-color: #6D28D9;
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
            <div class="max-w-4xl mx-auto">
                <div class="flex items-center mb-8">
                    <h1 class="text-3xl font-bold text-green-500">Detalles de la Versión del Torneo</h1>
                </div>

                <form id="versionDetailsForm" class="bg-[#1E293B] p-6 rounded-lg shadow-lg">
                    <input type="hidden" name="version_id" value="<?php echo $version_id; ?>">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Nombre del Torneo -->
                        <div>
                            <label for="tournament_name" class="block text-sm font-medium text-gray-400 mb-1">Nombre del
                                Torneo</label>
                            <input type="text" id="tournament_name" name="tournament_name"
                                value="<?php echo htmlspecialchars($tournament_version['tournament_name']); ?>"
                                class="custom-input w-full p-2 rounded-md" >
                        </div>

                        <!-- Nombre de la Versión -->
                        <div>
                            <label for="version_name" class="block text-sm font-medium text-gray-400 mb-1">Nombre de la
                                Versión</label>
                            <input type="text" id="version_name" name="version_name"
                                value="<?php echo htmlspecialchars($tournament_version['version_name']); ?>"
                                class="custom-input w-full p-2 rounded-md">
                        </div>

                        <!-- Fecha de Inicio -->
                        <div>
                            <label for="start_date" class="block text-sm font-medium text-gray-400 mb-1">Fecha de
                                Inicio</label>
                            <div class="relative">
                                <input id="start_date" type="text" name="start_date"
                                    value="<?php echo htmlspecialchars($tournament_version['start_date']); ?>" required
                                    class="custom-input w-full p-2 rounded-md">
                                <button type="button" id="start_date_icon"
                                    class="absolute inset-y-0 right-3 flex items-center text-gray-400 focus:outline-none">
                                    <i class="far fa-calendar-alt"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Fecha de Fin -->
                        <div>
                            <label for="end_date" class="block text-sm font-medium text-gray-400 mb-1">Fecha de
                                Fin</label>
                            <div class="relative">
                                <input id="end_date" type="text" name="end_date"
                                    value="<?php echo htmlspecialchars($tournament_version['end_date']); ?>" required
                                    class="custom-input w-full p-2 rounded-md">
                                <button type="button" id="end_date_icon"
                                    class="absolute inset-y-0 right-3 flex items-center text-gray-400 focus:outline-none">
                                    <i class="far fa-calendar-alt"></i>
                                </button>
                            </div>
                        </div>

                        <!-- País -->
                        <div>
                            <label for="country" class="block text-sm font-medium text-gray-400 mb-1">País</label>
                            <input type="text" id="country" name="country"
                                value="<?php echo htmlspecialchars($tournament_version['country']); ?>"
                                class="custom-input w-full p-2 rounded-md">
                        </div>

                        <!-- Ciudad -->
                        <div>
                            <label for="city" class="block text-sm font-medium text-gray-400 mb-1">Ciudad</label>
                            <input type="text" id="city" name="city"
                                value="<?php echo htmlspecialchars($tournament_version['city']); ?>"
                                class="custom-input w-full p-2 rounded-md">
                        </div>

                        <!-- Dirección -->
                        <div class="md:col-span-2">
                            <label for="address" class="block text-sm font-medium text-gray-400 mb-1">Dirección</label>
                            <textarea id="address" name="address" rows="3"
                                class="custom-input w-full p-2 rounded-md"><?php echo htmlspecialchars($tournament_version['address']); ?></textarea>
                        </div>

                        <!-- Cuota de Inscripción -->
                        <div>
                            <label for="registration_fee" class="block text-sm font-medium text-gray-400 mb-1">Cuota de
                                Inscripción</label>
                            <input type="number" id="registration_fee" name="registration_fee"
                                value="<?php echo $tournament_version['registration_fee']; ?>" step="0.01"
                                class="custom-input w-full p-2 rounded-md">
                        </div>

                        <!-- Estado -->
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-400 mb-1">Estado</label>
                            <select id="status" name="status" class="custom-select w-full p-2 rounded-md">
                                <option value="Pendiente" <?php echo $tournament_version['status'] == 'Pendiente' ? 'selected' : ''; ?>>Pendiente</option>
                                <option value="En Progreso" <?php echo $tournament_version['status'] == 'En Progreso' ? 'selected' : ''; ?>>En Progreso</option>
                                <option value="Finalizado" <?php echo $tournament_version['status'] == 'Finalizado' ? 'selected' : ''; ?>>Finalizado</option>
                            </select>
                        </div>

                        <!-- URL de Google Maps -->
                        <div class="md:col-span-2">
                            <label for="google_maps_url" class="block text-sm font-medium text-gray-400 mb-1">URL de
                                Google Maps</label>
                            <input type="url" id="google_maps_url" name="google_maps_url"
                                value="<?php echo htmlspecialchars($tournament_version['google_maps_url'] ?? ''); ?>"
                                class="custom-input w-full p-2 rounded-md">
                        </div>

                        <!-- Días de Juego -->
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-400 mb-2">Días de Juego</label>
                            <div class="flex flex-wrap gap-4">
                                <?php
                                $days = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'];
                                $selected_days = explode(',', $tournament_version['playing_days']);
                                foreach ($days as $day) {
                                    $checked = in_array($day, $selected_days) ? 'checked' : '';
                                    echo "<label class='inline-flex items-center'>
                            <input type='checkbox' name='playing_days[]' value='$day' $checked class='custom-checkbox form-checkbox h-5 w-5 rounded border-gray-600 text-[#7C3AED] focus:ring-[#7C3AED]'>
                            <span class='ml-2 text-white'>$day</span>
                          </label>";
                                }
                                ?>
                            </div>
                        </div>

                        <!-- Rango de Horario de Partidos -->
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-400 mb-1">Rango de Horario de
                                Partidos</label>
                            <div class="grid grid-cols-2 gap-2">
                                <div class="relative">
                                    <input id="start_time" type="text" name="start_time" placeholder="Hora de Inicio"
                                        required class="custom-input w-full p-2 rounded-md">
                                    <button type="button" id="start_time_icon"
                                        class="absolute inset-y-0 right-3 flex items-center text-gray-400 focus:outline-none">
                                        <i class="far fa-clock"></i>
                                    </button>
                                </div>
                                <div class="relative">
                                    <input id="end_time" type="text" name="end_time" placeholder="Hora de Fin" required
                                        class="custom-input w-full p-2 rounded-md">
                                    <button type="button" id="end_time_icon"
                                        class="absolute inset-y-0 right-3 flex items-center text-gray-400 focus:outline-none">
                                        <i class="far fa-clock"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end">
                        <button type="submit"
                            class="custom-submit px-4 py-2 text-white rounded-md hover:bg-[#6D28D9] focus:outline-none focus:ring-2 focus:ring-[#7C3AED] focus:ring-offset-2 focus:ring-offset-[#1E293B]">
                            Guardar Cambios
                        </button>
                    </div>
                </form>


            </div>
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

    <script>
        // Toggle sidebar and content collapse
        document.getElementById('toggleSidebar').addEventListener('click', function () {
            document.getElementById('sidebar').classList.toggle('collapsed');
            document.getElementById('navbar').classList.toggle('collapsed');
            document.getElementById('content').classList.toggle('collapsed');
        });
    </script>

    <script>
        document.getElementById('versionDetailsForm').addEventListener('submit', function (e) {
            e.preventDefault();

            const formData = new FormData(this);
            const submitButton = this.querySelector('button[type="submit"]');
            submitButton.disabled = true;
            submitButton.textContent = 'Guardando...';

            axios.post('../controllers/save_tournament_version_details.php', formData)
                .then(response => {
                    if (response.data.success) {
                        alert('Detalles de la versión del torneo guardados exitosamente');
                        window.location.href = `../views/tournament_overview.php?tournament_id=${response.data.tournament_id}&version_id=${response.data.version_id}`;
                    } else {
                        throw new Error(response.data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error al procesar la solicitud: ' + error.message);
                })
                .finally(() => {
                    submitButton.disabled = false;
                    submitButton.textContent = 'Guardar Cambios';
                });
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/es.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            // Calendarios
            flatpickr("#start_date", { dateFormat: "Y-m-d", locale: "es" });
            flatpickr("#end_date", { dateFormat: "Y-m-d", locale: "es" });

            // Horarios
            flatpickr("#start_time", { enableTime: true, noCalendar: true, dateFormat: "h:i K" });
            flatpickr("#end_time", { enableTime: true, noCalendar: true, dateFormat: "h:i K" });

            // Eventos para íconos
            document.getElementById("start_date_icon").addEventListener("click", () => document.getElementById("start_date")._flatpickr.open());
            document.getElementById("end_date_icon").addEventListener("click", () => document.getElementById("end_date")._flatpickr.open());
            document.getElementById("start_time_icon").addEventListener("click", () => document.getElementById("start_time")._flatpickr.open());
            document.getElementById("end_time_icon").addEventListener("click", () => document.getElementById("end_time")._flatpickr.open());
        });
    </script>
</body>
</html>