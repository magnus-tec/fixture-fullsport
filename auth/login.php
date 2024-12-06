<?php require_once "../controllers/controllerUserData.php"; ?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - Full Sports</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
</head>

<body class="bg-[#0F172A]">
    <div class="min-h-screen flex items-center justify-center p-4">
        <div class="w-full max-w-md space-y-6 bg-[#1E293B] p-8 rounded-xl shadow-xl">
            <a href="../index.php" class="flex justify-center">
                <div class="w-16 h-16 bg-[#7C3AED] rounded-full flex items-center justify-center overflow-hidden">
                    <img src="../public/img/logo.png" alt="Logo" class="object-contain w-12 h-12">
                </div>
            </a>

            <div class="space-y-2 text-center">
                <h1 class="text-3xl font-bold text-white">Iniciar Sesión</h1>
                <p class="text-gray-400">
                    ¿Aún no eres miembro? <a href="register.php" class="text-[#7C3AED] hover:underline">Regístrate</a>
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

            <form class="space-y-4" action="login.php" method="POST" autocomplete="off">
                <div class="space-y-2">
                    <label for="email" class="text-sm font-medium text-gray-400">Email</label>
                    <input id="email" name="email" type="email" placeholder="Correo Electrónico" required
                        value="<?php echo $email ?>"
                        class="w-full px-3 py-2 bg-[#0F172A] border border-gray-700 rounded-md text-white focus:outline-none focus:ring-2 focus:ring-[#7C3AED] focus:border-transparent">
                </div>

                <div class="space-y-2 relative">
                    <label for="password" class="text-sm font-medium text-gray-400">Contraseña</label>
                    <div class="relative">
                        <input id="password" name="password" type="password" placeholder="••••••••••••" required
                            class="w-full px-3 py-2 bg-[#0F172A] border border-gray-700 rounded-md text-white focus:outline-none focus:ring-2 focus:ring-[#7C3AED] focus:border-transparent">
                        <button type="button" id="toggle-password"
                            class="absolute inset-y-0 right-3 flex items-center text-gray-400 hover:text-[#7C3AED] transition-colors hidden">
                            <i id="eye-icon" class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>

                <div class="flex items-center justify-center">
                    <a href="forgot-password.php" class="text-sm text-[#7C3AED] hover:underline text-center">
                        ¿Has olvidado tu contraseña?
                    </a>
                </div>


                <button type="submit" name="login"
                    class="w-full bg-[#7C3AED] hover:bg-[#6D28D9] text-white py-2 rounded-md transition-colors">
                    Iniciar Sesión
                </button>
            </form>

            <!-- Sección oculta para mantener el tamaño -->
            <div class="h-20"></div>
        </div>
    </div>

    <script>
        const passwordField = document.getElementById('password');
        const togglePasswordButton = document.getElementById('toggle-password');
        const eyeIcon = document.getElementById('eye-icon');

        // Mostrar el botón solo si hay caracteres
        passwordField.addEventListener('input', () => {
            togglePasswordButton.style.display = passwordField.value.length > 0 ? 'block' : 'none';
        });

        // Alternar visibilidad de la contraseña
        togglePasswordButton.addEventListener('click', () => {
            const type = passwordField.type === 'password' ? 'text' : 'password';
            passwordField.type = type;
            eyeIcon.className = type === 'password' ? 'fas fa-eye' : 'fas fa-eye-slash';
        });
    </script>
</body>
<?php require_once "../auth/footer.php"; ?>

</html>