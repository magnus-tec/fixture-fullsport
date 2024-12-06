<?php require_once "../controllers/controllerUserData.php"; ?>
<?php
if ($_SESSION['info'] == false) {
    header('Location: ../auth/login.php');
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Full Sports</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        :root {
            --color-dark: #0F172A;
            --color-darker: #1E293B;
            --color-purple: #7C3AED;
            --color-purple-hover: #6D28D9;
        }

        .bg-custom-dark {
            background-color: var(--color-dark);
        }

        .bg-custom-darker {
            background-color: var(--color-darker);
        }

        .bg-custom-purple {
            background-color: var(--color-purple);
        }

        .hover\:bg-custom-purple:hover {
            background-color: var(--color-purple-hover);
        }

        .text-custom-purple {
            color: var(--color-purple);
        }

        .focus\:ring-custom-purple:focus {
            --tw-ring-color: var(--color-purple);
        }
    </style>
</head>

<body class="bg-custom-dark">
    <div class="min-h-screen flex items-center justify-center p-4">
        <div class="w-full max-w-md space-y-6 bg-custom-darker p-8 rounded-xl shadow-xl">
            <a href="../index.php" class="flex justify-center">
                <div class="w-16 h-16 bg-custom-purple rounded-full flex items-center justify-center overflow-hidden">
                    <img src="../public/img/logo.png" alt="Logo" class="object-contain w-12 h-12">
                </div>
            </a>

            <div class="space-y-2 text-center">
                <h1 class="text-3xl font-bold text-white">¡Información Actualizada!</h1>
            </div>

            <?php
            if (isset($_SESSION['info'])) {
                ?>
                <div class="bg-green-500 text-white p-3 rounded-md text-center">
                    <?php echo $_SESSION['info']; ?>
                </div>
                <?php
            }
            ?>

            <form action="../auth/login.php" method="POST" class="space-y-4">
                <button type="submit" name="login-now"
                    class="w-full bg-custom-purple hover:bg-custom-purple text-white py-2 rounded-md transition-colors">
                    Iniciar Sesión Ahora
                </button>
            </form>
        </div>
    </div>
</body>
<?php require_once "../auth/footer.php"; ?>

</html>