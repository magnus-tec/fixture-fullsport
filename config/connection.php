<?php
// Carga las credenciales desde variables de entorno
$db_host = getenv('DB_HOST') ?: 'localhost';
$db_user = getenv('DB_USER') ?: 'root';
$db_password = getenv('DB_PASSWORD') ?: '';
$db_name = getenv('DB_NAME') ?: 'sistema_fixture';

try {
    // Crear la conexión
    $con = mysqli_connect($db_host, $db_user, $db_password, $db_name);

    // Verifica la conexión
    if (mysqli_connect_errno()) {
        throw new Exception('Error al conectar a la base de datos: ' . mysqli_connect_error());
    }

    // Configura la conexión para usar UTF-8
    mysqli_set_charset($con, 'utf8mb4');

    // Mensaje opcional (solo para pruebas)
    // echo "Conexión exitosa";
} catch (Exception $e) {
    // Muestra un mensaje y termina el script
    die('Excepción capturada: ' . $e->getMessage());
}
?>
