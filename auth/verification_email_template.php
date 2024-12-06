<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Código de verificación de correo electrónico</title>
  <style>
    body {
      margin: 0;
      padding: 0;
      font-family: Arial, sans-serif;
      background-color: #1A1D24;
    }
    .container {
      max-width: 600px;
      margin: 40px auto;
      background-color: #1E2227;
      border-radius: 12px;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.3);
      overflow: hidden;
    }
    .header {
      text-align: center;
      padding: 30px;
      background-color: #1E2227;
    }
    .header img {
      width: 80px;
      height: 80px;
      border-radius: 50%;
    }
    .header h1 {
      color: #ffffff;
      font-size: 24px;
      margin: 20px 0 0;
    }
    .content {
      padding: 30px;
      background-color: #252830;
      border-radius: 0 0 12px 12px;
    }
    .content h2 {
      color: #ffffff;
      font-size: 20px;
      margin-top: 0;
    }
    .content .code {
      font-size: 24px;
      font-weight: bold;
      color: #ffffff;
      text-align: center;
      margin: 20px 0;
    }
    .content .message {
      color: #B4B7BC;
      font-size: 16px;
      line-height: 1.6;
      margin-bottom: 20px;
    }
    .content a {
      display: inline-block;
      padding: 14px 30px;
      background-color: #8957FF;
      color: #ffffff;
      text-decoration: none;
      border-radius: 6px;
      font-weight: bold;
      text-align: center;
    }
    .footer {
      padding: 20px 30px;
      text-align: center;
      background-color: #252830;
      color: #B4B7BC;
      font-size: 14px;
    }
    .footer a {
      color: #8957FF;
      text-decoration: none;
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="header">
    <img src="https://fullsportplay.com/public/img/logo.png" alt="Logo" style="width: 80px; height: 80px; border-radius: 50%;">
      <h1>Full Sports</h1>
    </div>
    <div class="content">
      <h2>Código de Verificación de Correo Electrónico</h2>
      <div class="code">238232</div>
      <div class="message">
        Hola [Nombre del Usuario],<br><br>
        Tu código de verificación es <strong>238232</strong>. Por favor, ingresa este código en la aplicación para completar el proceso de registro.
      </div>
      <div style="text-align: center; margin-top: 30px;">
        <a href="../auth/user-otp.php">Verificar cuenta</a>
      </div>
    </div>
    <div class="footer">
      &copy; <span id="current-year"></span><a href="https://magustechnologies.com/"> Magus Technologies</a>. Todos los derechos reservados.<br>
      <a href="https://fullsportplay.com/">Desuscribirse</a> | 
      <a href="https://fullsportplay.com/">Preferencias</a>
    </div>
  </div>
  <script>
    document.getElementById("current-year").textContent = new Date().getFullYear();
</script>
</body>
</html>
