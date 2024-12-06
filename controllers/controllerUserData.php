<?php
session_start();
require "../config/connection.php";
require '../vendor/autoload.php'; // Incluye PHPMailer

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$email = "";
$name = "";
$errors = array();

function getCurrentUser() {
    global $con;
    if (isset($_SESSION['user_id'])) {
        $userId = $_SESSION['user_id'];
        $query = "SELECT id, email, name, role FROM usertable WHERE id = ?";
        $stmt = $con->prepare($query);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($user = $result->fetch_assoc()) {
            return [
                'success' => true,
                'id' => $user['id'],
                'email' => $user['email'],
                'name' => $user['name'],
                'role' => $user['role']
            ];
        }
    }
    
    return [
        'success' => false,
        'message' => 'No hay sesión activa o usuario no encontrado'
    ];
}

// If this file is accessed directly, return the current user data
if (basename($_SERVER['PHP_SELF']) == basename(__FILE__)) {
    header('Content-Type: application/json');
    echo json_encode(getCurrentUser());
}

// Función para enviar correo usando PHPMailer
function enviarCorreo($email, $subject, $message)
{
    $mail = new PHPMailer(true);
    try {
        // Configuración del servidor SMTP
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com'; // Cambia por tu servidor SMTP
        $mail->SMTPAuth = true;
        $mail->Username = 'full.sport.envios@gmail.com'; // Tu correo
        $mail->Password = 'orqsyhgtuiqysrww'; // Tu contraseña
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        // Configuración del correo
        $mail->setFrom('full.sport.envios@gmail.com', 'Full Sport Play');
        $mail->addAddress($email);
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $message;

        $mail->send();
        return true;
    } catch (Exception $e) {
        return false;
    }
}

// Si el usuario hace clic en el botón de registro
if (isset($_POST['signup'])) {
    $name = mysqli_real_escape_string($con, $_POST['name']);
    $email = mysqli_real_escape_string($con, $_POST['email']);
    $password = mysqli_real_escape_string($con, $_POST['password']);
    $cpassword = mysqli_real_escape_string($con, $_POST['cpassword']);

    if ($password !== $cpassword) {
        $errors['password'] = "¡La confirmación de la contraseña no coincide!";
    }

    // Comprobación de existencia de correo electrónico
    $email_check = "SELECT * FROM usertable WHERE email = '$email'";
    $res = mysqli_query($con, $email_check);
    if (mysqli_num_rows($res) > 0) {
        $errors['email'] = "¡El correo electrónico que ingresaste ya existe!";
    }

    // Asignación de rol
    $role = 'Usuario';
    $role_check = "SELECT * FROM usertable";
    $res_role = mysqli_query($con, $role_check);
    if (mysqli_num_rows($res_role) === 0) {
        $role = 'Administrador'; // El primer usuario registrado será administrador
    }

    if (count($errors) === 0) {
        $encpass = password_hash($password, PASSWORD_BCRYPT);
        $code = rand(999999, 111111);
        $status = "noverificado";

        // Insertar datos con rol asignado
        $insert_data = "INSERT INTO usertable (name, email, password, code, status, role) 
                        VALUES('$name', '$email', '$encpass', '$code', '$status', '$role')";
        $data_check = mysqli_query($con, $insert_data);

        if ($data_check) {
            $subject = "Codigo de verificacion de correo electronico";
            $message = file_get_contents('../auth/verification_email_template.php');
            $message = str_replace('238232', $code, $message);
            $message = str_replace('[Nombre del Usuario]', $name, $message);  
            $message = str_replace('https://fullsportplay.com/public/img/logo.png', 'https://fullsportplay.com/public/img/logo.png', $message);
            if (enviarCorreo($email, $subject, $message)) {
                $info = "Hemos enviado un código de verificación a tu correo electrónico - $email";
                $_SESSION['info'] = $info;
                $_SESSION['email'] = $email;
                $_SESSION['password'] = $password;
                header('location: ../auth/user-otp.php');
                exit();
            } else {
                $errors['otp-error'] = "¡Error al enviar el código!";
            }
        } else {
            $errors['db-error'] = "¡Error al insertar datos en la base de datos!";
        }
    }
}

// Verificar código de correo electrónico
if (isset($_POST['check'])) {
    $_SESSION['info'] = "";
    $otp_code = mysqli_real_escape_string($con, $_POST['otp']);
    $check_code = "SELECT * FROM usertable WHERE code = $otp_code";
    $code_res = mysqli_query($con, $check_code);
    if (mysqli_num_rows($code_res) > 0) {
        $fetch_data = mysqli_fetch_assoc($code_res);
        $fetch_code = $fetch_data['code'];
        $email = $fetch_data['email'];
        $code = 0;
        $status = 'verified';
        $update_otp = "UPDATE usertable SET code = $code, status = '$status' WHERE code = $fetch_code";
        $update_res = mysqli_query($con, $update_otp);
        if ($update_res) {
            $_SESSION['name'] = $fetch_data['name'];
            $_SESSION['email'] = $email;
            $_SESSION['role'] = $fetch_data['role'];
            header('location: ../auth/login.php');
            exit();
        } else {
            $errors['otp-error'] = "¡Error al actualizar el código!";
        }
    } else {
        $errors['otp-error'] = "¡Código incorrecto!";
    }
}

// Inicio de sesión del usuario
if (isset($_POST['login'])) {
    $email = mysqli_real_escape_string($con, $_POST['email']);
    $password = mysqli_real_escape_string($con, $_POST['password']);
    $check_email = "SELECT * FROM usertable WHERE email = '$email'";
    $res = mysqli_query($con, $check_email);

    if (mysqli_num_rows($res) > 0) {
        $fetch = mysqli_fetch_assoc($res);
        $fetch_pass = $fetch['password'];

        if (password_verify($password, $fetch_pass)) {
            $_SESSION['user_id'] = $fetch['id']; // Guarda el ID del usuario en la sesión
            $_SESSION['email'] = $email;
            $_SESSION['role'] = $fetch['role'];
            $status = $fetch['status'];

            if ($status == 'verified') {
                if ($_SESSION['role'] == 'Administrador') {
                    header('location: ../views/index.php');
                } else {
                    header('location: ../views/index.php');
                }
                exit();
            } else {
                $_SESSION['info'] = "Aún no has verificado tu correo electrónico - $email";
                header('location: ../auth/user-otp.php');
            }
        } else {
            $errors['email'] = "¡Correo electrónico o contraseña incorrectos!";
        }
    } else {
        $errors['email'] = "Parece que aún no eres miembro. Haz clic en el enlace de abajo para registrarte.";
    }
}

if (isset($_POST['check-email'])) {
    $email = mysqli_real_escape_string($con, $_POST['email']);
    $check_email = "SELECT * FROM usertable WHERE email='$email'";
    $run_sql = mysqli_query($con, $check_email);

    // Verificar si el correo existe
    if (mysqli_num_rows($run_sql) > 0) {
        // Obtener el nombre del usuario
        $user = mysqli_fetch_assoc($run_sql);
        $name = $user['name']; // Asignamos el nombre del usuario

        // Generar un código de restablecimiento
        $code = rand(999999, 111111);
        $insert_code = "UPDATE usertable SET code = $code WHERE email = '$email'";
        $run_query = mysqli_query($con, $insert_code);

        if ($run_query) {
            // Configuración del correo
            $subject = "Código de restablecimiento de contraseña";
            $message = file_get_contents('../auth/recuperar_password_template.php');
            
            // Reemplazar los valores dinámicos en el mensaje
            $message = str_replace('238232', $code, $message);
            $message = str_replace('[Nombre del Usuario]', $name, $message);  // Incluir el nombre del usuario
            $message = str_replace('https://fullsportplay.com/public/img/logo.png', 'https://fullsportplay.com/public/img/logo.png', $message);

            // Enviar el correo
            if (enviarCorreo($email, $subject, $message)) {
                $info = "Hemos enviado un código de restablecimiento de contraseña a tu correo electrónico - $email";
                $_SESSION['info'] = $info;
                $_SESSION['email'] = $email;
                header('location: ../auth/reset-code.php');
                exit();
            } else {
                $errors['otp-error'] = "¡Error al enviar el código!";
            }
        } else {
            $errors['db-error'] = "¡Algo salió mal!";
        }
    } else {
        $errors['email'] = "¡Esta dirección de correo electrónico no existe!";
    }
}


// Si el usuario hace clic en el botón verificar código de restablecimiento
if (isset($_POST['check-reset-otp'])) {
    $_SESSION['info'] = "";
    $otp_code = mysqli_real_escape_string($con, $_POST['otp']);
    $check_code = "SELECT * FROM usertable WHERE code = $otp_code";
    $code_res = mysqli_query($con, $check_code);
    if (mysqli_num_rows($code_res) > 0) {
        $fetch_data = mysqli_fetch_assoc($code_res);
        $email = $fetch_data['email'];
        $_SESSION['email'] = $email;
        $info = "Por favor, crea una nueva contraseña que no uses en otro sitio.";
        $_SESSION['info'] = $info;
        header('location: ../auth/new-password.php');
        exit();
    } else {
        $errors['otp-error'] = "¡Código incorrecto!";
    }
}

// Si el usuario hace clic en el botón cambiar contraseña
if (isset($_POST['change-password'])) {
    $_SESSION['info'] = "";
    $password = mysqli_real_escape_string($con, $_POST['password']);
    $cpassword = mysqli_real_escape_string($con, $_POST['cpassword']);
    if ($password !== $cpassword) {
        $errors['password'] = "¡La confirmación de la contraseña no coincide!";
    } else {
        $code = 0;
        $email = $_SESSION['email'];
        $encpass = password_hash($password, PASSWORD_BCRYPT);
        $update_pass = "UPDATE usertable SET code = $code, password = '$encpass' WHERE email = '$email'";
        $run_query = mysqli_query($con, $update_pass);
        if ($run_query) {
            $info = "Tu contraseña ha sido cambiada. Ahora puedes iniciar sesión con tu nueva contraseña.";
            $_SESSION['info'] = $info;
            header('Location: ../auth/password-changed.php');
        } else {
            $errors['db-error'] = "¡Error al cambiar tu contraseña!";
        }
    }
}

// Si el usuario hace clic en el botón iniciar sesión ahora
if (isset($_POST['login-now'])) {
    header('Location: ../views/login.php');
}
?>