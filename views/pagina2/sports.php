<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/fixturepro/config/connection.php';

// Obtener torneos de la base de datos
$sql = "SELECT * FROM tournaments"; // Asegúrate de que la tabla se llame 'tournaments'
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
        .card {
            background-color: #26334d;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
            text-align: center;
            color: white;
        }

        .card img {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            margin: 0 auto;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .card h3 {
            margin-top: 15px;
            font-size: 18px;
            font-weight: bold;
            color: #ffffff;
        }

        .card p {
            font-size: 14px;
            color: #A1A1AA;
            margin-bottom: 10px;
        }

        .tags {
            display: flex;
            gap: 10px;
            justify-content: center;
            margin-top: 10px;
        }

        .tag {
            background-color: rgba(59, 130, 246, 0.1);
            color: #3B82F6;
            border-radius: 15px;
            padding: 5px 10px;
            font-size: 12px;
            font-weight: bold;
        }

        .button {
            margin-top: 15px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            background-color: #4d47f5;
            color: white;
            padding: 10px 20px;
            border-radius: 20px;
            text-decoration: none;
            font-size: 14px;
            font-weight: bold;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2);
            transition: all 0.3s ease;
        }

        .button:hover {
            background-color: #3730a3;
            transform: scale(1.05);
        }
    </style>
</head>

<body>
<section style="padding: 20px; font-family: Arial, sans-serif;">
    <div style="text-align: center; margin-bottom: 20px;">
        <h2 class="text-3xl font-bold text-center mb-12">EXPLORA LOS TORNEOS POPULARES</h2>
    </div>
    <div style="display: flex; justify-content: center; gap: 20px; flex-wrap: wrap;">
        <!-- Tarjetas dinámicas generadas con PHP -->
        <?php foreach ($tournaments as $tournament): ?>
            <div class="card">
                <?php if (!empty($tournament['logo_image'])): ?>
                    <img src="/fixturepro/public/<?php echo htmlspecialchars($tournament['logo_image']); ?>"
                        alt="Logo del torneo <?php echo htmlspecialchars($tournament['name']); ?>" 
                        class="w-full h-full object-cover rounded-full">
                        <?php else: ?>
                    <img src="/fixturepro/public/img/soccer.jpg" 
                        alt="Logo predeterminado" 
                        class="w-full h-full object-cover rounded-full">
                <?php endif; ?>
                <h3 class="mt-4 font-bold text-lg"><?php echo htmlspecialchars($tournament['name']); ?></h3>
                <p class="mt-2 text-sm text-gray-400"><?php echo htmlspecialchars($tournament['description']); ?></p>
                <div class="tags mt-4">
                    <span class="tag"><?php echo htmlspecialchars($tournament['competition_type']); ?></span>
                    <span class="tag"><?php echo htmlspecialchars($tournament['sport_type']); ?></span>
                    <span class="tag"><?php echo htmlspecialchars($tournament['gender']); ?></span>
                </div>
                <a href="./pages/tournament-detail1.php?id=<?php echo htmlspecialchars($tournament['id']); ?>" class="button mt-4">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M15.75 9V5.25m0 0L12 9m3.75-3.75L17.25 9m-5.25 6v3.75m0-3.75L9 12.75m3 3l3.75-3.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Ver Torneo
                </a>
            </div>
        <?php endforeach; ?>
    </div>
</section>

</body>

</html>