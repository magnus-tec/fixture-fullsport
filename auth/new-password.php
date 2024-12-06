<?php require_once "../controllers/controllerUserData.php"; ?>
<?php
$email = $_SESSION['email'];
if ($email == false) {
    header('Location: ../auth/login.php');
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nueva Contraseña - Full Sports</title>
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
                <h1 class="text-3xl font-bold text-white">Nueva Contraseña</h1>
            </div>

            <form action="new-password.php" method="POST" autocomplete="off" class="space-y-4">
                <?php
                if (isset($_SESSION['info'])) {
                    ?>
                    <div class="bg-green-500 text-white p-3 rounded-md text-center">
                        <?php echo $_SESSION['info']; ?>
                    </div>
                    <?php
                }
                ?>
                <?php
                if (count($errors) > 0) {
                    ?>
                    <div class="bg-red-500 text-white p-3 rounded-md text-center">
                        <?php
                        foreach ($errors as $showerror) {
                            echo $showerror . '<br>';
                        }
                        ?>
                    </div>
                    <?php
                }
                ?>
                <div class="space-y-2">
                    <label for="password" class="text-sm font-medium text-gray-400">Crear Nueva Contraseña</label>
                    <input id="password" name="password" type="password" required
                        class="w-full px-3 py-2 bg-custom-dark border border-gray-700 rounded-md text-white focus:outline-none focus:ring-2 focus:ring-custom-purple focus:border-transparent"
                        placeholder="••••••••••••">
                </div>
                <div class="space-y-2">
                    <label for="cpassword" class="text-sm font-medium text-gray-400">Confirmar Contraseña</label>
                    <input id="cpassword" name="cpassword" type="password" required
                        class="w-full px-3 py-2 bg-custom-dark border border-gray-700 rounded-md text-white focus:outline-none focus:ring-2 focus:ring-custom-purple focus:border-transparent"
                        placeholder="••••••••••••">
                </div>

                <button type="submit" name="change-password"
                    class="w-full bg-custom-purple hover:bg-custom-purple text-white py-2 rounded-md transition-colors">
                    Cambiar Contraseña
                </button>
            </form>
        </div>
    </div>
</body>
<?php require_once "../auth/footer.php"; ?>

</html>