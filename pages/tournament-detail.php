<?php
session_start();
if (!isset($_SESSION['email'])) {
    header("Location: ../auth/login.php");
    exit();
}

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
                    <!-- Input Field -->
                    <input id="tournamentUrl" type="text"
                        value="https://fullsportplay.com/pages/<?php echo htmlspecialchars($tournament['url_slug']); ?>"
                        class="flex-1 bg-gray-800 rounded-lg px-4 py-2 text-gray-200 border border-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:ring-offset-2 transition-all"
                        readonly>
                    <!-- Copy Button -->
                    <button onclick="copyTournamentUrl()"
                        class="px-5 py-2 bg-blue-500 text-white font-semibold rounded-lg hover:bg-blue-600 focus:ring-2 focus:ring-blue-400 focus:ring-offset-2 focus:outline-none transition-all">
                        Copiar
                    </button>
                </div>
            </div>
            <div class="mt-6">
                <h2 class="text-lg font-medium text-gray-200 mb-4">Versiones del Torneo</h2>
                <!-- Contenedor con diseño de grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    <?php while ($version = $versions_result->fetch_assoc()): ?>
                        <!-- Card personalizada -->
                        <div class="relative bg-[#1e293b] rounded-lg shadow-lg overflow-hidden text-center w-64">
                            <!-- Tag superior izquierdo -->
                            <span
                                class="absolute top-3 left-3 bg-purple-600 text-white text-xs font-bold py-1 px-3 rounded-md">
                                Fútbol
                            </span>

                            <!-- Icono Superior Derecho -->
                            <div class="absolute top-3 right-3 text-white-500 text-xl">
                                <i class="ri-calendar-line"></i>
                            </div>

                            <!-- Imagen principal -->
                            <div>
                                <img src="../public/img/banner.jpg" alt="Imagen de fondo"
                                    class="w-full h-32 object-cover rounded-t-lg">
                            </div>

                            <!-- Imagen de perfil -->
                            <div class="-mt-8 flex justify-center">
                                <img src="../public/img/usuario1.png" alt="Foto de perfil"
                                    class="w-16 h-16 rounded-full border-4 border-purple-600">
                            </div>

                            <!-- Título -->
                            <h3 class="mt-4 text-base font-semibold text-gray-200">
                                <?php echo htmlspecialchars($version['name']); ?>
                            </h3>

                            <!-- Seguidores -->
                            <p class="text-sm text-gray-400 mb-4">0 Seguidores</p>

                            <!-- Botón -->
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
    </script>

    <script>
        // Toggle user menu
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

        // Image Upload Modal Functions
        function openImageModal() {
            document.getElementById('imageUploadModal').classList.remove('hidden');
            document.getElementById('imageUploadModal').classList.add('flex');
        }

        function closeImageModal() {
            document.getElementById('imageUploadModal').classList.add('hidden');
            document.getElementById('imageUploadModal').classList.remove('flex');
        }
        // Edit Tournament Modal Functions
        function openEditModal() {
            document.getElementById('editTournamentModal').classList.remove('hidden');
            document.getElementById('editTournamentModal').classList.add('flex');
        }

        function closeEditModal() {
            document.getElementById('editTournamentModal').classList.add('hidden');
            document.getElementById('editTournamentModal').classList.remove('flex');
        }

        // File Input Handler
        document.getElementById('tournamentImage').addEventListener('change', function (e) {
            const fileName = e.target.files[0]?.name || 'Ningún archivo seleccionado';
            document.getElementById('selectedFileName').textContent = fileName;
        });

        // Image Upload Form Handler
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

        // Edit Tournament Form Handler
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

        // Delete Tournament Function
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

        // URL Verification
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
    </script>
  
    <script>

        // Mostrar el nombre del archivo seleccionado
        document.getElementById('logo').addEventListener('change', function () {
            const selectedFileName = this.files[0] ? this.files[0].name : 'Ningún archivo seleccionado';
            document.getElementById('selectedLogoName').textContent = selectedFileName;
        });

        // Función para abrir el modal
        function openLogoModal() {
            const modal = document.getElementById('logoUploadModal');
            if (modal) {
                modal.classList.remove('hidden');
                modal.classList.add('flex');
            } else {
                console.error('Modal no encontrado');
            }
        }

        // Función para cerrar el modal
        function closeLogoModal() {
            const modal = document.getElementById('logoUploadModal');
            if (modal) {
                modal.classList.add('hidden');
                modal.classList.remove('flex');

                // Restablecer el texto del nombre del archivo
                document.getElementById('selectedLogoName').textContent = 'Ningún archivo seleccionado';

                // Restablecer el input del archivo
                document.getElementById('logo').value = '';
            } else {
                console.error('Modal no encontrado');
            }
        }


        // Escucha el evento submit del formulario
        document.getElementById('logoUploadForm').addEventListener('submit', async function (event) {
            event.preventDefault();

            const formData = new FormData(this); // Captura los datos del formulario

            try {
                const response = await fetch('../controllers/upload_tournament_logo.php', {
                    method: 'POST',
                    body: formData,
                });

                const result = await response.json();

                if (result.success) {
                    // Actualiza la imagen mostrada
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

                    // Cierra el modal
                    closeLogoModal();
                } else {
                    alert(result.message || 'Error al actualizar el logo.');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Error al subir el logo.');
            }
        });
    </script>
    <script>
        // Mostrar el GIF de carga durante 4 segundos
        setTimeout(function () {
            document.getElementById('loading').style.display = 'none';
        }, 1500); 
    </script>

    <script>
        // JavaScript to handle follow button toggle
        const followButton = document.getElementById('followButton');
        const followersCount = document.getElementById('followersCount');
        let isFollowing = false; // Track follow state
        let followerCount = 0;   // Initialize followers count

        followButton.addEventListener('click', () => {
            if (isFollowing) {
                isFollowing = false;
                followerCount = Math.max(0, followerCount - 1); // Prevent negative count
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
            followersCount.textContent = followerCount; // Update followers count display
        });
    </script>
</body>

</html>