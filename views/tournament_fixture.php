<?php
session_start();
if (!isset($_SESSION['email'])) {
  header("Location: ../views/login.php");
  exit();
}

require_once "../config/connection.php";

// Get tournament details
$tournament_id = $_GET['tournament_id'] ?? null;
$version_id = $_GET['version_id'] ?? null;
if (!$tournament_id || !$version_id) {
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
// $versions_sql = "SELECT tv.*, tc.name as category_name, tc.id 
//                  FROM tournament_versions tv
//                  LEFT JOIN tournament_categories tc ON tv.id = tc.tournament_version_id
//                  WHERE tv.tournament_id = ?";

/*CONSULTA MODIFICADA*/
$categories_sql = "SELECT * FROM tournament_categories where tournament_version_id = ?";
$categories_sql = $con->prepare($categories_sql);
$categories_sql->bind_param("i", $version_id);
$categories_sql->execute();
$categories_result = $categories_sql->get_result();
while ($row = $categories_result->fetch_assoc()) {
  $categories[] = $row;
}

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

    .content>div {
      max-width: 1200px;
      width: 100%;
      margin: 0 auto;
    }

    .menu-item {
      display: flex;
      flex-direction: column;
      position: relative;
    }

    .submenu {
      overflow: hidden;
      max-height: 0;
      transition: max-height 0.3s ease;
    }

    .menu-item:hover .submenu {
      max-height: 200px;
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
    <div class="flex align-items-start mb-3">
      <a href="tournament-detail.php?id=<?php echo $tournament_id; ?>"
        class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 flex items-center space-x-2">
        <i class="ri-arrow-left-line"></i>
      </a>
    </div>

    <div>
      <h1 class="text-2xl font-medium mb-4">Generador de Partidos</h1>
      <div>
        <label class="block text-sm font-medium mb-1">Buscar por Categoria:</label>
        <select name="searchCategoria" id="searchCategoria"
          class=" p-2 rounded bg-gray-700 border border-gray-600" required>
          <option value="">Seleccione una categoria</option>
          <?php
          foreach ($categories as $category) {
          ?>
            <option value="<?php echo htmlspecialchars($category['id']); ?>">
              <?php echo htmlspecialchars($category['name']); ?>
            </option>
          <?php
          }
          ?>
        </select>
      </div>
      <div id="divprueba" class="mt-3">
      </div>
      <div id="divpartidos" class="mt-3">
      </div>

      <script>
        document.getElementById('searchCategoria').addEventListener("change", function() {
          let selectedValue = this.value;
          let tournament_id = <?php echo $tournament_id; ?>;
          let version_id = <?php echo $version_id; ?>;
          console.log(selectedValue);
          if (selectedValue) {
            const data = {
              version_id: version_id,
              tournament_id: tournament_id,
              category_id: selectedValue
            }
            fetch('../controllers/get_teams_category.php', {
                method: "POST",
                headers: {
                  'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: new URLSearchParams(data).toString()
              }).then(res => res.json())
              .then(data => {
                let divEquipos = '<h2 class="text-xl font-bold">Equipos</h2>';
                divEquipos += '<div class="grid grid-cols-[repeat(auto-fit,minmax(200px,300px))] gap-6 mt-2">';
                data.map((item, i) => {
                  divEquipos += `<div class="flex items-center gap-3 bg-blue-100 p-3 rounded">
                                  <span class="flex items-center justify-center w-8 h-8 rounded-full bg-blue-500 text-white font-bold text-sm">
                                    ${i + 1}
                                  </span>
                                  <span class="text-gray-700 font-medium text-lg">
                                    ${item.name}
                                  </span>
                                </div>`
                });
                divEquipos += '</div>';
                divEquipos += `<button id="generatePartidos" class="rounded bg-violet-700 hover:bg-violet-500 p-3 mt-5"><i class="bi bi-calendar" style="font-size: 14px;"></i> Generar partidos</button>`;
                document.getElementById('divprueba').innerHTML = divEquipos;
                document.getElementById('divpartidos').innerHTML = "";

                document.getElementById("generatePartidos").addEventListener('click', function() {
                  const tournamentId = <?php echo json_encode($tournament_id); ?>;
                  const versionId = <?php echo json_encode($version_id); ?>;
                  const categoryId = selectedValue;
                  console.log(tournamentId, versionId, categoryId);
                  fetch('../controllers/generate_partidos.php', {
                      method: 'POST',
                      headers: {
                        'Content-Type': 'application/json',
                      },
                      body: JSON.stringify({
                        tournament_id: tournamentId,
                        version_id: versionId,
                        category_id: categoryId
                      })
                    })
                    .then(response => response.json())
                    .then(data => {
                      if (data.success) {
                        Swal.fire({
                          icon: 'success',
                          title: 'Éxito',
                          text: 'Partidos generados exitosamente.',
                          background: '#1a1d24',
                          color: '#ffffff'
                        }).then(() => {
                          const equipos = data.teams;
                          const fixture = data.fixture;
                          const partidos = fixture.map(fix => ({
                            home_team_id: equipos.find(equipo => equipo.id === fix.home_team_id)?.name || "desconocido",
                            away_team_id: equipos.find(equipo => equipo.id === fix.away_team_id)?.name || "desconocido",
                          }));
                          let divPartidos = '<h2 class="text-xl font-bold">Partidos</h2>';
                          divPartidos += '<div class="grid grid-cols-[repeat(auto-fit,minmax(200px,300px))] gap-6 mt-2">';
                          partidos.map((item) => {
                            divPartidos += `<div class="flex justify-between items-center p-4 bg-blue-100 rounded-lg shadow-md">
                            <span class="text-lg font-medium text-gray-800">${item.home_team_id}</span> 
                            <span class="text-sm text-gray-500">vs</span>
                            <span class="text-lg font-medium text-gray-800">${item.away_team_id}</span>
                          </div>`
                          });
                          divPartidos += '</div>';
                          divPartidos += `<button id="generateFixture" class="rounded bg-green-700 hover:bg-green-500 p-3 mt-5"><i class="bi bi-calendar" style="font-size: 14px;"></i> Guardar Partidos</button>`;
                          document.getElementById('divpartidos').innerHTML = divPartidos;
                          
                          document.getElementById('generateFixture').addEventListener('click', function() {
                            const tournamentId = <?php echo json_encode($tournament_id); ?>;
                            const versionId = <?php echo json_encode($version_id); ?>;
                            const categoryId = selectedValue;
                            fetch('../controllers/generate_fixture.php', {
                                method: 'POST',
                                headers: {
                                  'Content-Type': 'application/json',
                                },
                                body: JSON.stringify({
                                  tournament_id: tournamentId,
                                  version_id: versionId,
                                  category_id: categoryId
                                })
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
                                    window.location.href = `calendar.php?tournament_id=${tournamentId}&version_id=${versionId}&category_id=${categoryId}`;
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
              });
          }
        });
      </script>
      <script>
      </script>
      <script>
        function openCreateCategoryModal() {
          document.getElementById('createCategoryModal').classList.remove('hidden');
        }

        function closeCreateCategoryModal() {
          document.getElementById('createCategoryModal').classList.add('hidden');
        }



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



        document.getElementById('assignCategory').addEventListener('click', async function() {
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
              body: JSON.stringify({
                categoryId,
                versionId
              })
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



        document.getElementById('toggleSidebar').addEventListener('click', function() {
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
      </script>
    </div>
  </div>
  </div>
</body>

</html>