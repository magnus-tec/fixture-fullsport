<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/fixturepro/config/connection.php'; //'../config/connection.php'

// Obtener torneos de la base de datos
$sql = "SELECT t.*, tv.id AS version_id FROM tournaments t JOIN tournament_versions tv ON t.id = tv.tournament_id"; // Asegúrate de que la tabla se llame 'tournaments'
$stmt = $con->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();
$tournaments = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Full Sports - Explora tus deportes favoritos</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="/fixturepro/css/app.css" rel="stylesheet">
    <script src="/fixturepro/js/app.js"></script>
    <style>
        /* Estilos generales */
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f5f5f5;
            color: #333;
            margin: 0;
            padding: 0;
        }

        h2 {
            font-size: 2rem;
            font-weight: 700;
            color: #1E293B;
            margin-bottom: 30px;
        }

        /* Buscador */
        .custom-search {
            display: flex;
            align-items: center;
            background-color: #1E293B;
            border-radius: 30px;
            padding: 10px 16px;
            border: 1px solid #4B5563;
            margin: 0 auto 30px;
            width: 100%;
            max-width: 400px;
        }

        .custom-search input {
            background: none;
            border: none;
            outline: none;
            color: #ffffff;
            font-size: 14px;
            flex: 1;
        }

        .custom-search input::placeholder {
            color: #9CA3AF;
        }

        .custom-search svg {
            color: #9CA3AF;
            margin-right: 8px;
        }

        .custom-search input:focus {
            color: #ffffff;
        }

        /* Tarjetas de torneos */
        .card-container {
            display: flex;
            justify-content: center;
            gap: 15px;
            flex-wrap: wrap;
            padding: 0 10px;
        }

        .card {
            width: 250px;
            background: #1F2937;
            border-radius: 12px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            padding: 0;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            text-align: center;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            margin-bottom: 15px;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 12px rgba(0, 0, 0, 0.3);
        }

        /* Imagen como portada */
        .card-img {
            width: 100%;
            height: 120px;
            object-fit: cover;
            border-top-left-radius: 12px;
            border-top-right-radius: 12px;
        }

        .card-content {
            padding: 15px;
            background-color: #1F2937;
            border-bottom-left-radius: 12px;
            border-bottom-right-radius: 12px;
        }

        .card h3 {
            margin: 8px 0;
            font-size: 16px;
            font-weight: 600;
            color: #ffffff;
        }

        .card p {
            font-size: 12px;
            color: #E5E7EB;
            margin: 6px 0 10px;
        }

        .tags {
            display: flex;
            gap: 6px;
            flex-wrap: wrap;
            justify-content: center;
            margin-top: 8px;
        }

        .tag {
            font-size: 10px;
            padding: 4px 8px;
            background: #3B4B63;
            color: #E5E7EB;
            border-radius: 16px;
        }

        /* Botón de ver torneo */
        .button {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 6px 12px;
            background: linear-gradient(90deg, #6A5ACD, #483D8B);
            color: #fff;
            font-size: 12px;
            font-weight: 600;
            text-decoration: none;
            border-radius: 20px;
            margin-top: auto;
            transition: background 0.3s ease, box-shadow 0.3s ease;
        }

        .button svg {
            margin-right: 6px;
            width: 16px;
            height: 16px;
        }

        .button:hover {
            background: linear-gradient(90deg, #483D8B, #6A5ACD);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
        }
    </style>
</head>

<body>
    <section style="padding: 50px 20px; text-align: center;">
        <h2>EXPLORA LOS TORNEOS POPULARES</h2>

        <!-- Buscador -->
        <div class="custom-search">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
            <input type="text" id="tournamentSearch" placeholder="Buscar torneo..." oninput="filterTournaments()">
        </div>

        <!-- Lista de torneos -->
        <div class="card-container">
            <?php foreach ($tournaments as $tournament): ?>
                <div class="card">
                    <!-- Imagen como portada -->
                    <?php if (!empty($tournament['logo_image'])): ?>
                        <img src="/fixturepro/public/<?php echo htmlspecialchars($tournament['logo_image']); ?>"
                            alt="Logo del torneo <?php echo htmlspecialchars($tournament['name']); ?>" class="card-img">
                    <?php else: ?>
                        <img src="/fixturepro/public/img/sportslogo.png" alt="Logo predeterminado" class="card-img">
                    <?php endif; ?>
                    <!-- Contenido debajo de la imagen -->
                    <div class="card-content">
                        <h3><?php echo htmlspecialchars($tournament['name']); ?></h3>
                        <p><?php echo htmlspecialchars($tournament['description']); ?></p>
                        <div class="tags">
                            <span class="tag"><?php echo htmlspecialchars($tournament['competition_type']); ?></span>
                            <span class="tag"><?php echo htmlspecialchars($tournament['sport_type']); ?></span>
                            <span class="tag"><?php echo htmlspecialchars($tournament['gender']); ?></span>
                        </div><br>
                        <a href="./vista_paginas/tournament_overview_pagina.php?tournament_id=<?php echo htmlspecialchars($tournament['id']); ?>&version_id=<?php echo htmlspecialchars($tournament['version_id']); ?>" class="button mt-4">
                            Ver Torneo
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

    </section>
</body>

</html>