<?php require_once "../controllers/controllerUserData.php"; ?>
<?php
$email = $_SESSION['email'];
if ($email == false) {
    header('Location: ../auth/login.php');
}
?>
<?php require_once "../controllers/auth_controller.php"; ?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verificación de Código - Full Sports</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-[#0F172A]">
    <div id="loading" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-75 z-50">
        <img src="../public/img/loading.gif" alt="Cargando..." class="w-32 h-32">
    </div>

    <div class="min-h-screen flex items-center justify-center p-4">
        <div class="w-full max-w-md space-y-6 bg-[#1E293B] p-8 rounded-xl shadow-xl">
            <a href="/" class="flex justify-center">
                <div class="w-16 h-16 bg-[#7C3AED] rounded-full flex items-center justify-center overflow-hidden">
                    <img src="/img/logo.png" alt="Logo" class="object-contain w-12 h-12">
                </div>
            </a>

            <div class="space-y-2 text-center">
                <h1 class="text-3xl font-bold text-white">Código de Verificación</h1>
                <p class="text-gray-400">
                    <?php
                    if (isset($_SESSION['info'])) {
                        echo $_SESSION['info'];
                    }
                    ?>
                </p>
            </div>

            <?php
            if (count($errors) > 0) {
                echo "<div class='bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative' role='alert'><ul>";
                foreach ($errors as $showerror) {
                    echo "<li>$showerror</li>";
                }
                echo "</ul></div>";
            }
            ?>

            <form action="reset-code.php" method="POST" autocomplete="off">
                <div class="space-y-4">
                    <div class="space-y-2">
                        <label for="otp" class="text-sm font-medium text-gray-400">Código de Verificación</label>
                        <input type="number" name="otp" placeholder="Ingresa el código" required
                            class="w-full px-3 py-2 bg-[#0F172A] border border-gray-700 rounded-md text-white focus:outline-none focus:ring-2 focus:ring-[#7C3AED] focus:border-transparent">
                    </div>

                    <button type="submit" name="check-reset-otp"
                        class="w-full bg-[#7C3AED] hover:bg-[#6D28D9] text-white py-2 rounded-md transition-colors">
                        Verificar Código
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>
<?php require_once "../auth/footer.php"; ?>

</html>