<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($title) ? $title : 'Full Sports | Página Principal'; ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        :root {
            --primary-color: #6B46C1;
            --secondary-color: #805AD5;
            --accent-color: #38B2AC;
        }
    </style>
</head>
<body class="min-h-screen bg-gray-900 text-white">
    <?php
		include __DIR__ . '/views/pagina/header.php';
        include __DIR__ . '/views/pagina/home.php';
        include __DIR__ . '/views/pagina/buscar-torneos.php';
        include __DIR__ . '/views/pagina/sports.php';
		include __DIR__ . '/views/pagina/footer.php';
    ?>
</body>
</html>