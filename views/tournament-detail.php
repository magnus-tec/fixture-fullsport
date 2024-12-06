<?php
session_start();
if (!isset($_SESSION['email'])) {
    header("Location: ../views/login.php");
    exit();
}

require_once "../config/connection.php";

// Get tournament details
$tournament_id = $_GET['id'] ?? null;
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

// Obtener versiones del torneo
$versions_sql = "SELECT tv.*, tc.name as category_name 
                 FROM tournament_versions tv
                 LEFT JOIN tournament_categories tc ON tv.id = tc.tournament_version_id
                 WHERE tv.tournament_id = ?";
$versions_stmt = $con->prepare($versions_sql);
$versions_stmt->bind_param("i", $tournament_id);
$versions_stmt->execute();
$versions_result = $versions_stmt->get_result();

// Fetch categories
$categories_sql = "SELECT tc.* FROM tournament_categories tc
                   JOIN tournament_versions tv ON tc.tournament_version_id = tv.id
                   WHERE tv.tournament_id = ?";
$categories_stmt = $con->prepare($categories_sql);
$categories_stmt->bind_param("i", $tournament_id);
$categories_stmt->execute();
$categories_result = $categories_stmt->get_result();

function fetchCategoriesAndVersions($con, $tournament_id) {
    $categories = [];
    $versions = [];

    // Fetch tournament versions
    $ver_sql = "SELECT id, name FROM tournament_versions WHERE tournament_id = ?";
    $ver_stmt = $con->prepare($ver_sql);
    $ver_stmt->bind_param("i", $tournament_id);
    $ver_stmt->execute();
    $ver_result = $ver_stmt->get_result();
    while ($row = $ver_result->fetch_assoc()) {
        $versions[] = $row;
    }

    // Fetch categories for all versions of this tournament
    $cat_sql = "SELECT tc.id, tc.name, tc.tournament_version_id 
                FROM tournament_categories tc
                JOIN tournament_versions tv ON tc.tournament_version_id = tv.id
                WHERE tv.tournament_id = ?";
    $cat_stmt = $con->prepare($cat_sql);
    $cat_stmt->bind_param("i", $tournament_id);
    $cat_stmt->execute();
    $cat_result = $cat_stmt->get_result();
    while ($row = $cat_result->fetch_assoc()) {
        $categories[] = $row;
    }

    return ['categories' => $categories, 'versions' => $versions];
}

// Call this function and assign the result to a variable
$categoriesAndVersions = fetchCategoriesAndVersions($con, $tournament_id);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($tournament['name']); ?> - Full Sports</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
                        <span class="text-xs text-gray-400 ms-2"><?php echo htmlspecialchars($_SESSION['email'], ENT_QUOTES, 'UTF-8'); ?></span>
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
            <!-- Tournament Header -->
            <div class="relative h-64 rounded-lg overflow-hidden mb-6">
                <img src="<?php echo htmlspecialchars($tournament['cover_image'] ? '../public/' . $tournament['cover_image'] : '../public/img/banner.jpg'); ?>"
                    alt="<?php echo htmlspecialchars($tournament['name']); ?> Cover" class="w-full h-full object-cover">
                <button onclick="openImageModal()"
                    class="absolute top-4 right-4 px-4 py-2 bg-green-500 text-white rounded-md hover:bg-green-600">
                    Cambiar Imagen
                </button>
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
            <div class="bg-[#1E293B] rounded-lg p-6 mb-6">
                <div class="flex justify-between items-center">
                    <div class="flex gap-3 ml-auto">
                        <a href="tournament_version.php?tournament_id=<?php echo htmlspecialchars($tournament['id']); ?>"
                            class="block w-full px-4 py-2 bg-green-500 text-white text-center rounded-md hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-green-400 flex items-center justify-center gap-3">
                            <i class="fas fa-clipboard-list w-5 h-5"></i>
                            Crear Version
                        </a>
                        <button onclick="openEditModal()"
                            class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-400 flex items-center gap-2">
                            <i class="fas fa-edit w-5 h-5"></i>
                            Editar
                        </button>
                        <button id="shareButton"
                            class="px-4 py-2 bg-orange-500 text-white rounded-md hover:bg-orange-600 focus:outline-none focus:ring-2 focus:ring-orange-400 flex items-center gap-2"
                            onclick="shareTournament()">
                            <i class="fas fa-share w-5 h-5"></i>
                            Compartir
                        </button>
                    </div>
                </div>

                <!-- Tournament URL -->
                <div class="mt-4">
                    <p class="text-sm text-gray-400 mb-2">URL del torneo:</p>
                    <div class="flex gap-2">
                        <input type="text"
                            value="https://fullsportplay.com/pages/<?php echo htmlspecialchars($tournament['url_slug']); ?>"
                            class="flex-1 bg-gray-700 rounded px-3 py-2 text-gray-200" readonly>
                        <button onclick="copyTournamentUrl()"
                            class="px-4 py-2 bg-gray-700 rounded-md hover:bg-gray-600">
                            Copiar
                        </button>
                    </div>
                </div>
            </div>

            <!-- Tournament Info -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Stats -->
                <div class="bg-[#1E293B] p-6 rounded-lg">
                    <h3 class="font-semibold mb-4">Crear y Asignar Categoria</h3>
                    <!-- Create Category Modal -->
                    <form id="createCategoryForm" class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium mb-1">Nombre de la Categoría:</label>
                            <input type="text" name="categoryName" id="categoryName"
                                class="w-full p-2 rounded bg-gray-700 border border-gray-600" required>
                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-1">Descripción de la Categoría:</label>
                            <textarea name="categoryDescription" id="categoryDescription"
                                class="w-full p-2 rounded bg-gray-700 border border-gray-600" required></textarea>
                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-1">Versión del Torneo:</label>
                            <select name="tournamentVersionId" id="tournamentVersionId"
                                class="w-full p-2 rounded bg-gray-700 border border-gray-600" required>
                                <option value="">Seleccione una versión</option>
                                <?php while ($version = $versions_result->fetch_assoc()): ?>
                                    <option value="<?php echo htmlspecialchars($version['id']); ?>">
                                        <?php echo htmlspecialchars($version['name']); ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>

                        <div class="flex justify-between items-center pt-4 border-t border-gray-700">
                            <div class="flex gap-2">
                                <button type="button" onclick="closeCreateCategoryModal()"
                                    class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600">
                                    Cancelar
                                </button>
                                <button type="submit"
                                    class="px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600">
                                    Crear
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Quick Actions -->
                <div class="bg-slate-800 p-6 rounded-lg shadow-lg">
                    <h3 class="font-semibold text-lg text-white mb-4">Lista de Categorias</h3>
                    <div class="space-y-3">
                        <!-- List of Categories -->
                        <div class="mb-4">
                            <?php if ($categories_result->num_rows > 0): ?>
                                <ul class="list-disc list-inside text-gray-300">
                                    <?php while ($category = $categories_result->fetch_assoc()): ?>
                                        <li><?php echo htmlspecialchars($category['name']); ?></li>
                                    <?php endwhile; ?>
                                </ul>
                            <?php else: ?>
                                <p class="text-gray-400 text-sm">No hay categorías creadas aún.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-6">
                <h2 class="text-lg font-medium mb-4">Versiones del Torneo</h2>
                <!-- Contenedor con diseño de grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <?php 
                    $versions_result->data_seek(0); // Reset the result pointer
                    while ($version = $versions_result->fetch_assoc()): 
                    ?>
                        <!-- Card personalizada -->
                        <div class="bg-gray-800 rounded-lg shadow-md overflow-hidden relative flex flex-col items-center text-center">
                            <!-- Tag superior izquierdo -->
                            <div class="absolute top-2 left-2 bg-indigo-500 text-white text-xs font-semibold py-1 px-3 rounded-md">
                                <?php echo htmlspecialchars($version['category_name'] ?? 'Fútbol'); ?>
                            </div>

                            <!-- Icono Superior Derecho -->
                            <div class="absolute top-2 right-2 text-indigo-400 text-xl">
                                <i class="ri-calendar-lock-line"></i>
                            </div>

                            <!-- Imagen principal -->
                            <img src="../public/img/banner.jpg" alt="Imagen de fondo" class="w-full h-32 object-cover">

                            <!-- Imagen de perfil -->
                            <img src="../public/img/usuario1.png" alt="Foto de perfil"
                                class="w-16 h-16 rounded-full border-4 border-indigo-500 -mt-8">

                            <!-- Título -->
                            <h3 class="mt-4 text-white font-semibold text-lg">
                                <?php echo htmlspecialchars($version['name']); ?>
                            </h3>

                            <!-- Seguidores -->
                            <p class="text-gray-400 text-sm mt-1">0 Seguidores</p>

                            <!-- Botón -->
                            <a href="tournament_overview.php?tournament_id=<?php echo $tournament_id; ?>&version_id=<?php echo $version['id']; ?>"
                                class="mt-4 mb-3 bg-indigo-500 text-white py-2 px-6 rounded-md text-sm font-medium transition hover:bg-indigo-600">
                                Administrar
                            </a>
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>

            <!-- Modal -->
            <div id="categoryModal"
                class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
                <div class="bg-[#1A1D24] p-6 rounded-lg w-full max-w-xl">
                    <h2 class="text-xl font-bold mb-4 text-white">Asignar Categoría</h2>
                    <button id="closeModal" class="text-red-500 float-right">&times;</button>
                    <div class="space-y-4 mt-4">
                        <div>
                            <label for="categorySelect" class="block text-sm font-medium mb-1 text-white">Selecciona una
                                Categoría:</label>
                            <select id="categorySelect"
                                class="w-full p-2 rounded bg-gray-800 border border-gray-700 text-white">
                                <option value="">-- Selecciona una categoría --</option>
                                <!-- Opciones de categorías se llenarán aquí -->
                            </select>
                        </div>
                        <div>
                            <label for="versionSelect" class="block text-sm font-medium mb-1 text-white">Selecciona una
                                Versión de Torneo:</label>
                            <select id="versionSelect"
                                class="w-full p-2 rounded bg-gray-800 border border-gray-700 text-white">
                                <option value="">-- Selecciona una versión --</option>
                                <!-- Opciones de versiones de torneo se llenarán aquí -->
                            </select>
                        </div>
                    </div>
                    <div class="flex justify-end gap-4 mt-6">
                        <button id="assignCategory"
                            class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">Asignar</button>
                        <button type="button" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700"
                            onclick="closeCategoryModal()">Cancelar</button>
                    </div>
                </div>
            </div>

            <!-- Image Upload Modal -->
            <div id="imageUploadModal"
                class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
                <div class="bg-[#1E293B] p-6 rounded-lg w-full max-w-md">
                    <h2 class="text-xl font-bold mb-4">Cambiar Imagen Portada de Torneo</h2>
                    <form id="imageUploadForm" class="space-y-4">
                        <div class="bg-gray-700 p-4 rounded-lg">
                            <input type="file" id="tournamentImage" name="tournamentImage" accept="image/*"
                                class="hidden" required>
                            <label for="tournamentImage"
                                class="inline-block px-4 py-2 bg-gray-600 text-white rounded cursor-pointer hover:bg-gray-500">
                                Seleccionar archivo
                            </label>
                            <span id="selectedFileName" class="ml-2 text-gray-300">
                                Ningún archivo seleccionado
                            </span>
                        </div>
                        <div class="flex justify-end gap-2">
                            <button type="button" onclick="closeImageModal()"
                                class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600">
                                Cancelar
                            </button>
                            <button type="submit" class="px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600">
                                Guardar
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Edit Tournament Modal -->
            <div id="editTournamentModal"
                class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
                <div class="bg-[#1E293B] p-6 rounded-lg w-full max-w-xl">
                    <h2 class="text-xl font-bold mb-4">Editar Información de Competición</h2>
                    <form id="editTournamentForm" class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium mb-1">Nombre de tu Competición:</label>
                            <input type="text" name="name" id="editTournamentName"
                                value="<?php echo htmlspecialchars($tournament['name']); ?>"
                                class="w-full p-2 rounded bg-gray-700 border border-gray-600" required>
                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-1">Descripción Corta:</label>
                            <textarea name="description" id="editTournamentDescription"
                                class="w-full p-2 rounded bg-gray-700 border border-gray-600"
                                required><?php echo htmlspecialchars($tournament['description']); ?></textarea>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium mb-1">Tipo Competición:</label>
                                <select name="competition_type" id="editCompetitionType"
                                    class="w-full p-2 rounded bg-gray-700 border border-gray-600" required>
                                    <option value="Aficionado" <?php echo $tournament['competition_type'] == 'Aficionado' ? 'selected' : ''; ?>>Aficionado</option>
                                    <option value="Profesional" <?php echo $tournament['competition_type'] == 'Profesional' ? 'selected' : ''; ?>>Profesional
                                    </option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium mb-1">Deporte:</label>
                                <select name="sport_type" id="editSportType"
                                    class="w-full p-2 rounded bg-gray-700 border border-gray-600" required>
                                    <option value="Futbol" <?php echo $tournament['sport_type'] == 'Futbol' ? 'selected' : ''; ?>>
                                        Fútbol</option>
                                    <option value="Futbol 7" <?php echo $tournament['sport_type'] == 'Futbol 7' ? 'selected' : ''; ?>>Fútbol 7</option>
                                    <option value="Futbol 8" <?php echo $tournament['sport_type'] == 'Futbol 8' ? 'selected' : ''; ?>>Fútbol 8</option>
                                    <option value="Fulbito" <?php echo $tournament['sport_type'] == 'Fulbito' ? 'selected' : ''; ?>>Fulbito</option>
                                    <option value="Futsal" <?php echo $tournament['sport_type'] == 'Futsal' ? 'selected' : ''; ?>>
                                        Futsal</option>
                                </select>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-1">Género:</label>
                            <select name="gender" id="editGender"
                                class="w-full p-2 rounded bg-gray-700 border border-gray-600" required>
                                <option value="General" <?php echo $tournament['gender'] == 'General' ? 'selected' : ''; ?>>
                                    General</option>
                                <option value="Varones" <?php echo $tournament['gender'] == 'Varones' ? 'selected' : ''; ?>>
                                    Varones</option>
                                <option value="Mujeres" <?php echo $tournament['gender'] == 'Mujeres' ? 'selected' : ''; ?>>
                                    Mujeres</option>
                                <option value="Menores" <?php echo $tournament['gender'] == 'Menores' ? 'selected' : ''; ?>>
                                    Menores</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-1">Nombre único - URL de torneo:</label>
                            <div class="flex gap-2">
                                <input type="text" name="url_slug" id="editUrlSlug"
                                    value="<?php echo htmlspecialchars($tournament['url_slug']); ?>"
                                    class="flex-1 p-2 rounded bg-gray-700 border border-gray-600">
                                <button type="button" id="verifyEditUrl"
                                    class="px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600">
                                    Verificar
                                </button>
                            </div>
                            <p class="text-xs text-gray-400 mt-1">
                                Si personalizas este campo, podrás tener una URL para tu campeonato como tú quieras y
                                que nadie más tendrá.
                            </p>
                        </div>

                        <div class="flex justify-between items-center pt-4 border-t border-gray-700">
                            <button type="button" onclick="deleteTournament()"
                                class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600">
                                Eliminar Competición
                            </button>
                            <div class="flex gap-2">
                                <button type="button" onclick="closeEditModal()"
                                    class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600">
                                    Cancelar
                                </button>
                                <button type="submit"
                                    class="px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600">
                                    Guardar
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Logo Upload Modal -->
            <div id="logoUploadModal"
                class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
                <div class="bg-[#1E293B] p-6 rounded-lg w-full max-w-md">
                    <h2 class="text-xl font-bold mb-4">Cambiar Logo del Torneo</h2>
                    <form id="logoUploadForm" class="space-y-4">
                        <div class="bg-gray-700 p-4 rounded-lg">
                            <input type="file" id="logo" name="logo" accept="image/*" class="hidden" required>
                            <label for="logo"
                                class="inline-block px-4 py-2 bg-gray-600 text-white rounded cursor-pointer hover:bg-gray-500">
                                Seleccionar archivo
                            </label>
                            <span id="selectedLogoName" class="ml-2 text-gray-300">Ningún archivo seleccionado</span>
                        </div>

                        <input type="hidden" id="tournamentId" name="tournament_id"
                            value="<?php echo $tournament['id']; ?>">

                        <div class="flex justify-end gap-2">
                            <button type="button" onclick="closeLogoModal()"
                                class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600">
                                Cancelar
                            </button>
                            <button type="submit" class="px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600">
                                Guardar
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <script>
                function openCreateCategoryModal() {
                    document.getElementById('createCategoryModal').classList.remove('hidden');
                }

                function closeCreateCategoryModal() {
                    document.getElementById('createCategoryModal').classList.add('hidden');
                }

                document.getElementById('createCategoryForm').addEventListener('submit', function (event) {
                    event.preventDefault();

                    const categoryName = document.getElementById('categoryName').value;
                    const categoryDescription = document.getElementById('categoryDescription').value;
                    const tournamentVersionId = document.getElementById('tournamentVersionId').value;

                    if (!categoryName || !categoryDescription || !tournamentVersionId) {
                        alert('Por favor, complete todos los campos');
                        return;
                    }

                    fetch('../controllers/create_category.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({
                            name: categoryName,
                            description: categoryDescription,
                            tournament_version_id: tournamentVersionId
                        })
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                alert('Categoría creada exitosamente');
                                closeCreateCategoryModal();
                                location.reload();
                            } else {
                                alert('Error: ' + data.message);
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alert('Hubo un problema al crear la categoría');
                        });
                });

                function populateSelect(selectId, options) {
                    const select = document.getElementById(selectId);
                    select.innerHTML = '<option value="">-- Select --</option>';
                    options.forEach(option => {
                        const optionElement = document.createElement('option');
                        optionElement.value = option.id;
                        optionElement.textContent = option.name;
                        select.appendChild(optionElement);
                    });
                }

                document.getElementById('openModal').addEventListener('click', function () {
                    const categoriesAndVersions = <?php echo json_encode($categoriesAndVersions); ?>;
                    populateSelect('categorySelect', categoriesAndVersions.categories);
                    populateSelect('versionSelect', categoriesAndVersions.versions);
                    document.getElementById('categoryModal').classList.remove('hidden');
                });

                document.getElementById('closeModal').addEventListener('click', function () {
                    document.getElementById('categoryModal').classList.add('hidden');
                });

                document.getElementById('assignCategory').addEventListener('click', async function () {
                    const categoryId = document.getElementById('categorySelect').value;
                    const versionId = document.getElementById('versionSelect').value;

                    if (!categoryId || !versionId) {
                        alert('Please select both a category and a version.');
                        return;
                    }

                    try {
                        const response = await fetch('../controllers/assign_category.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                            },
                            body: JSON.stringify({ categoryId, versionId })
                        });
                        const result = await response.json();
                        if (result.success) {
                            alert('Category assigned successfully!');
                            document.getElementById('categoryModal').classList.add('hidden');
                            location.reload();
                        } else {
                            alert('Error: ' + result.message);
                        }
                    } catch (error) {
                        console.error('Error:', error);
                        alert('An error occurred while assigning the category.');
                    }
                });

                function copyTournamentUrl() {
                    const urlInput = document.querySelector('input[type="text"]');
                    urlInput.select();
                    document.execCommand('copy');
                    alert("URL copiada: " + urlInput.value);
                }

                function shareTournament() {
                    const tournamentUrl = document.querySelector('input[type="text"]').value;

                    if (navigator.share) {
                        navigator.share({
                            title: 'Compartir Torneo',
                            text: '¡Mira este increíble torneo!',
                            url: tournamentUrl,
                        }).then(() => {
                            console.log('Compartido con éxito');
                        }).catch((error) => {
                            console.log('Error al Compartir', error);
                        });
                    } else {
                        copyTournamentUrl();
                        alert('La API de compartir no está disponible. URL copiada!');
                    }
                }

                document.getElementById('toggleSidebar').addEventListener('click', function () {
                    document.getElementById('sidebar').classList.toggle('collapsed');
                    document.getElementById('navbar').classList.toggle('collapsed');
                    document.getElementById('content').classList.toggle('collapsed');
                });

                const userMenuBtn = document.getElementById('userMenuBtn');
                const userMenu = document.getElementById('userMenu');

                userMenuBtn.addEventListener('click', () => {
                    userMenu.classList.toggle('hidden');
                });

                document.addEventListener('click', (e) => {
                    if (!userMenuBtn.contains(e.target) && !userMenu.contains(e.target)) {
                        userMenu.classList.add('hidden');
                    }
                });

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

                document.getElementById('verifyEditUrl').addEventListener('click', async function () {
                    const slug = document.getElementById('editUrlSlug').value;
                    if (!slug) {
                        alert('Por favor, ingresa una URL primero.');
                        return;
                    }

                    try {
                        const response = await fetch(`../controllers/verify_url.php?slug=${encodeURIComponent(slug)}&current_id=<?php echo $tournament_id; ?>`);
                        const data = await response.json();

                        if (data.exists) {
                            alert('La URL ya está en uso. Por favor, ingresa otra.');
                        } else {
                            alert('¡La URL está disponible!');
                        }
                    } catch (error) {
                        console.error('Error:', error);
                        alert('Error al verificar la URL');
                    }
                });

                document.getElementById('logo').addEventListener('change', function () {
                    const selectedFileName = this.files[0] ? this.files[0].name : 'Ningún archivo seleccionado';
                    document.getElementById('selectedLogoName').textContent = selectedFileName;
                });

                function openLogoModal() {
                    const modal = document.getElementById('logoUploadModal');
                    if (modal) {
                        modal.classList.remove('hidden');
                        modal.classList.add('flex');
                    } else {
                        console.error('Modal no encontrado');
                    }
                }

                function closeLogoModal() {
                    const modal = document.getElementById('logoUploadModal');
                    if (modal) {
                        modal.classList.add('hidden');
                        modal.classList.remove('flex');
                        document.getElementById('selectedLogoName').textContent = 'Ningún archivo seleccionado';
                        document.getElementById('logo').value = '';
                    } else {
                        console.error('Modal no encontrado');
                    }
                }

                document.getElementById('logoUploadForm').addEventListener('submit', async function (event) {
                    event.preventDefault();

                    const formData = new FormData(this);

                    try {
                        const response = await fetch('../controllers/upload_tournament_logo.php', {
                            method: 'POST',
                            body: formData,
                        });

                        const result = await response.json();

                        if (result.success) {
                            const logoElement = document.querySelector('#tournamentLogo img');
                            if (logoElement) {
                                logoElement.src = '../public/' + result.logo_url;
                            } else {
                                const newImg = document.createElement('img');
                                newImg.src = '../public/' + result.logo_url;
                                newImg.alt = 'Logo del torneo';
                                newImg.className = 'w-full h-full object-cover';

                                document.getElementById('tournamentLogo').innerHTML = '';
                                document.getElementById('tournamentLogo').appendChild(newImg);
                            }

                            closeLogoModal();
                        } else {
                            alert(result.message || 'Error al actualizar el logo.');
                        }
                    } catch (error) {
                        console.error('Error:', error);
                        alert('Error al subir el logo.');
                    }
                });

                function openImageModal() {
                    document.getElementById('imageUploadModal').classList.remove('hidden');
                    document.getElementById('imageUploadModal').classList.add('flex');
                }

                function closeImageModal() {
                    document.getElementById('imageUploadModal').classList.add('hidden');
                    document.getElementById('imageUploadModal').classList.remove('flex');
                }

                document.getElementById('tournamentImage').addEventListener('change', function (e) {
                    const fileName = e.target.files[0]?.name || 'Ningún archivo seleccionado';
                    document.getElementById('selectedFileName').textContent = fileName;
                });

                document.getElementById('imageUploadForm').addEventListener('submit', async function (e) {
                    e.preventDefault();
                    const formData = new FormData();
                    const imageFile = document.getElementById('tournamentImage').files[0];
                    formData.append('image', imageFile);
                    formData.append('tournament_id', '<?php echo $tournament_id; ?>');

                    try {
                        const response = await fetch('../controllers/upload_tournament_image.php', {
                            method: 'POST',
                            body: formData
                        });
                        const data = await response.json();

                        if (data.success) {
                            alert('Imagen actualizada exitosamente');
                            location.reload();
                        } else {
                            alert('Error al actualizar la imagen: ' + data.message);
                        }
                    } catch (error) {
                        console.error('Error:', error);
                        alert('Error al subir la imagen');
                    }
                });

                function openEditModal() {
                    document.getElementById('editTournamentModal').classList.remove('hidden');
                    document.getElementById('editTournamentModal').classList.add('flex');
                }

                function closeEditModal() {
                    document.getElementById('editTournamentModal').classList.add('hidden');
                    document.getElementById('editTournamentModal').classList.remove('flex');
                }

                document.getElementById('editTournamentForm').addEventListener('submit', async function (e) {
                    e.preventDefault();
                    const formData = new FormData(this);
                    formData.append('tournament_id', '<?php echo $tournament_id; ?>');

                    try {
                        const response = await fetch('../controllers/update_tournament.php', {
                            method: 'POST',
                            body: formData
                        });
                        const data = await response.json();

                        if (data.success) {
                            alert('Torneo actualizado exitosamente');
                            location.reload();
                        } else {
                            alert('Error al actualizar el torneo: ' + data.message);
                        }
                    } catch (error) {
                        console.error('Error:', error);
                        alert('Error al actualizar el torneo');
                    }
                });

                async function deleteTournament() {
                    if (!confirm('¿Estás seguro de que deseas eliminar este torneo? Esta acción no se puede deshacer.')) {
                        return;
                    }

                    try {
                        const response = await fetch('../controllers/delete_tournament.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                            },
                            body: JSON.stringify({
                                tournament_id: <?php echo $tournament_id; ?>
                            })
                        });
                        const data = await response.json();

                        if (data.success) {
                            alert('Torneo eliminado exitosamente');
                            window.location.href = 'tournaments.php';
                        } else {
                            alert('Error al eliminar el torneo: ' + data.message);
                        }
                    } catch (error) {
                        console.error('Error:', error);
                        alert('Error al eliminar el torneo');
                    }
                }
            </script>
        </div>
    </div>
</body>
</html>