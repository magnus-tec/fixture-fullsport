<?php
session_start();
require_once "../config/connection.php";
require_once "../controllers/FixtureController.php";

if (!isset($_SESSION['email'])) {
    header("Location: ../auth/login.php");
    exit();
}

$tournament_id = $_GET['tournament_id'] ?? null;
$version_id = $_GET['version_id'] ?? null;
$category_id = $_GET['category_id'] ?? null;

if (!$tournament_id || !$version_id || !$category_id) {
    header("Location: tournaments.php");
    exit();
}

// Fetch category details
$stmt = $con->prepare("SELECT * FROM tournament_categories WHERE id = ?");
$stmt->bind_param("i", $category_id);
$stmt->execute();
$category = $stmt->get_result()->fetch_assoc();

// Fetch teams in this category
$stmt = $con->prepare("SELECT * FROM teams WHERE category_id = ?");
$stmt->bind_param("i", $category_id);
$stmt->execute();
$teams = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Fetch fixtures for this category
$fixtureController = new FixtureController($con);
$fixtures = $fixtureController->getFixtures($version_id, $category_id);

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($category['name']); ?> - Detalles</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
</head>
<body class="bg-gray-900 text-white">
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold mb-6"><?php echo htmlspecialchars($category['name']); ?></h1>
        <p class="text-gray-400 mb-8"><?php echo htmlspecialchars($category['description']); ?></p>

        <!-- Teams Section -->
        <section class="mb-12">
            <h2 class="text-2xl font-semibold mb-4">Equipos</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <?php foreach ($teams as $team): ?>
                    <div class="bg-gray-800 p-4 rounded-lg shadow-md">
                        <h3 class="text-xl font-semibold"><?php echo htmlspecialchars($team['name']); ?></h3>
                        <p class="text-gray-400">País: <?php echo htmlspecialchars($team['country']); ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>

        <!-- Fixtures Section -->
        <section>
            <h2 class="text-2xl font-semibold mb-4">Partidos</h2>
            <?php if (empty($fixtures)): ?>
                <p class="text-gray-400">No hay partidos programados aún.</p>
            <?php else: ?>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-800">
                                <th class="p-3">Fecha</th>
                                <th class="p-3">Equipo Local</th>
                                <th class="p-3">Equipo Visitante</th>
                                <th class="p-3">Resultado</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($fixtures as $fixture): ?>
                                <tr class="border-b border-gray-700">
                                    <td class="p-3"><?php echo htmlspecialchars($fixture['match_date']); ?></td>
                                    <td class="p-3"><?php echo htmlspecialchars($fixture['home_team']); ?></td>
                                    <td class="p-3"><?php echo htmlspecialchars($fixture['away_team']); ?></td>
                                    <td class="p-3">
                                        <?php
                                        if ($fixture['status'] == 'Completed') {
                                            echo "{$fixture['home_team_score']} - {$fixture['away_team_score']}";
                                        } else {
                                            echo "Pendiente";
                                        }
                                        ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </section>

        <!-- Generate Fixtures Button -->
        <div class="mt-8">
            <form action="generate_fixtures.php" method="POST">
                <input type="hidden" name="tournament_id" value="<?php echo $tournament_id; ?>">
                <input type="hidden" name="version_id" value="<?php echo $version_id; ?>">
                <input type="hidden" name="category_id" value="<?php echo $category_id; ?>">
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition duration-300">
                    Generar Partidos
                </button>
            </form>
        </div>

        <!-- Back Button -->
        <div class="mt-8">
            <a href="tournament_overview.php?tournament_id=<?php echo $tournament_id; ?>&version_id=<?php echo $version_id; ?>" 
               class="bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700 transition duration-300">
                Volver a la Vista General del Torneo
            </a>
        </div>
    </div>
</body>
</html>

