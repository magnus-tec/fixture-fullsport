<?php require_once "../controllers/controllerUserData.php"; ?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - Full Sports</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

</head>

<body class="bg-[#0F172A]">
    <div class="min-h-screen flex items-center justify-center p-4">
        <div class="w-full max-w-md space-y-6 bg-[#1E293B] p-8 rounded-xl shadow-xl">
            <!-- Logo -->
            <div class="flex justify-center">
                <a href="../index.php">
                    <div class="w-16 h-16 bg-[#7C3AED] rounded-full flex items-center justify-center overflow-hidden">
                        <img src="../public/img/logo.png" alt="Logo" class="object-contain w-12 h-12">
                    </div>
                </a>
            </div>

            <!-- Título -->
            <div class="space-y-2 text-center">
                <h1 class="text-3xl font-bold text-white">Crear Cuenta</h1>
                <p class="text-gray-400">
                    ¿Ya tienes una cuenta? <a href="login.php" class="text-[#7C3AED] hover:underline">Inicia Sesión</a>
                </p>
            </div>

            <!-- Mostrar errores -->
            <?php if (!empty($errors)): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <ul>
                        <?php foreach ($errors as $error): ?>
                            <li><?php echo $error; ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <!-- Formulario -->
            <form action="register.php" method="POST" class="space-y-4" id="registerForm" autocomplete="off">
                <!-- Nombre -->
                <div>
                    <label for="name" class="text-sm font-medium text-gray-400">Nombre Completo</label>
                    <input id="name" name="name" type="text" placeholder="Tu nombre completo" required
                        value="<?php echo $name ?>"
                        class="w-full px-3 py-2 bg-[#0F172A] border border-gray-700 rounded-md text-white focus:outline-none focus:ring-2 focus:ring-[#7C3AED] focus:border-transparent">
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="text-sm font-medium text-gray-400">Email</label>
                    <input id="email" name="email" type="email" placeholder="Correo Electrónico" required
                        value="<?php echo $email ?>"
                        class="w-full px-3 py-2 bg-[#0F172A] border border-gray-700 rounded-md text-white focus:outline-none focus:ring-2 focus:ring-[#7C3AED] focus:border-transparent">
                </div>

                <!-- Contraseña -->
                <div>
                    <label for="password" class="text-sm font-medium text-gray-400">Contraseña</label>
                    <div class="relative">
                        <input type="password" id="password" name="password" placeholder="••••••••••••" required minlength="6"
                            class="w-full px-3 py-2 bg-[#0F172A] border border-gray-700 rounded-md text-white focus:outline-none focus:ring-2 focus:ring-[#7C3AED] focus:border-transparent">
                        <button type="button" id="toggle-password"
                            class="absolute inset-y-0 right-3 flex items-center text-gray-400 hover:text-[#7C3AED] transition-colors hidden">
                            <i id="eye-icon" class="fas fa-eye"></i>
                        </button>
                    </div>
                    <p class="text-xs text-gray-400 mt-1">La contraseña debe tener al menos 6 caracteres, incluyendo mayúsculas, minúsculas, un número y un carácter especial.</p>
                    <!-- Indicador de fuerza de la contraseña -->
                    <p id="password-strength" class="text-xs text-gray-400 mt-1 hidden">Contraseña débil</p>
                </div>

                <!-- Confirmar Contraseña -->
                <div>
                    <label for="confirm-password" class="text-sm font-medium text-gray-400">Confirmar Contraseña</label>
                    <input type="password" id="cpassword" name="cpassword" placeholder="••••••••••••" required
                        class="w-full px-3 py-2 bg-[#0F172A] border border-gray-700 rounded-md text-white focus:outline-none focus:ring-2 focus:ring-[#7C3AED] focus:border-transparent">
                    <!-- Mensaje de validación de coincidencia de contraseñas -->
                    <p id="password-match" class="text-xs text-green-400 mt-1 hidden">Las contraseñas coinciden</p>
                    <p id="password-mismatch" class="text-xs text-red-500 mt-1 hidden">Las contraseñas no coinciden</p>
                </div>

                <!-- Botón de Registro -->
               <button type="submit" name="signup"
                    class="w-full bg-[#7C3AED] hover:bg-[#6D28D9] text-white py-2 rounded-md transition-colors">
                    Registrarse
                </button>
            </form>

            <!-- Sección oculta para mantener el tamaño -->
            <div class="h-0"></div>
        </div>
    </div>

    <script>
        const passwordField = document.getElementById('password');
        const confirmPasswordField = document.getElementById('confirm-password');
        const togglePasswordButton = document.getElementById('toggle-password');
        const eyeIcon = document.getElementById('eye-icon');
        const passwordStrengthText = document.getElementById('password-strength');
        const passwordMatchText = document.getElementById('password-match');
        const passwordMismatchText = document.getElementById('password-mismatch');
        const registerForm = document.getElementById('registerForm');

        // Mostrar el botón de ojo solo si hay caracteres en la contraseña
        passwordField.addEventListener('input', () => {
            togglePasswordButton.style.display = passwordField.value.length > 0 ? 'block' : 'none';
            checkPasswordStrength(passwordField.value);
        });

        // Alternar visibilidad de la contraseña
        togglePasswordButton.addEventListener('click', () => {
            const type = passwordField.type === 'password' ? 'text' : 'password';
            passwordField.type = type;
            confirmPasswordField.type = type;
            eyeIcon.className = type === 'password' ? 'fas fa-eye' : 'fas fa-eye-slash';
        });

        // Función para comprobar la fuerza de la contraseña
        function checkPasswordStrength(password) {
            const strongPassword = /^(?=.[a-z])(?=.[A-Z])(?=.\d)(?=.[@$!%?&])[A-Za-z\d@$!%?&]{6,}$/;
            const mediumPassword = /^(?=.[a-z])(?=.[A-Z])(?=.\d)[A-Za-z\d@$!%?&]{6,}$/;
            
            if (strongPassword.test(password)) {
                passwordStrengthText.textContent = 'Contraseña fuerte';
                passwordStrengthText.classList.remove('text-gray-400', 'hidden');
                passwordStrengthText.classList.add('text-green-500');
            } else if (mediumPassword.test(password)) {
                passwordStrengthText.textContent = 'Contraseña media';
                passwordStrengthText.classList.remove('text-gray-400', 'hidden');
                passwordStrengthText.classList.add('text-yellow-500');
            } else {
                passwordStrengthText.textContent = 'Contraseña débil';
                passwordStrengthText.classList.remove('text-gray-400', 'hidden');
                passwordStrengthText.classList.add('text-red-500');
            }
        }

        // Validar la coincidencia de contraseñas
        confirmPasswordField.addEventListener('input', () => {
            if (confirmPasswordField.value === passwordField.value) {
                passwordMatchText.classList.remove('hidden');
                passwordMismatchText.classList.add('hidden');
            } else {
                passwordMismatchText.classList.remove('hidden');
                passwordMatchText.classList.add('hidden');
            }
        });

// Validar el formulario antes de enviarlo
registerForm.addEventListener('submit', (event) => {
    const password = passwordField.value;
    const confirmPassword = confirmPasswordField.value;
    const passwordRegex = /^(?=.[a-z])(?=.[A-Z])(?=.\d)(?=.[@$!%?&])[A-Za-z\d@$!%?&]{6,}$/;

    // Validación de la contraseña
    if (!passwordRegex.test(password)) {
        Swal.fire({
            toast: true,
            icon: 'warning',
            title: 'La contraseña debe tener al menos 6 caracteres, incluyendo mayúsculas, minúsculas, un número y un carácter especial.',
            position: 'top-right',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            background: '#1f2937',
            color: '#ffffff',
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer);
                toast.addEventListener('mouseleave', Swal.resumeTimer);
            },
        });
        event.preventDefault();
        return;
    }

    // Validar que las contraseñas coincidan
    if (password !== confirmPassword) {
        Swal.fire({
            toast: true,
            icon: 'error',
            title: 'Las contraseñas no coinciden.',
            position: 'top-right',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            background: '#1f2937',
            color: '#ffffff',
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer);
                toast.addEventListener('mouseleave', Swal.resumeTimer);
            },
        });
        event.preventDefault();
    } else {
        Swal.fire({
            toast: true,
            icon: 'success',
            title: 'Formulario enviado con éxito.',
            position: 'top',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            background: '#1f2937',
            color: '#ffffff',
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer);
                toast.addEventListener('mouseleave', Swal.resumeTimer);
            },
        });
    }
});
    </script>
</body>

</html>