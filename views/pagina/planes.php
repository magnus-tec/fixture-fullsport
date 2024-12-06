<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Planes FutPlay</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <style>
        header {
            background: #fff;
            color: #111827;
            padding: 40px 20px;
            text-align: center;
        }

        header h1 {
            margin: 0;
            font-size: 36px;
            font-weight: 700;
        }

        header p {
            font-size: 18px;
            margin-top: 10px;
        }

        .container {
            max-width: 1200px;
            margin: auto;
            padding: 20px;
        }

        .section {
            margin-bottom: 40px;
        }

        .plans {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            justify-items: center;
            position: relative;
        }

        .row-last {
            grid-column: span 3;
            display: flex;
            justify-content: center;
            gap: 30px;
            margin-top: 15px;
            /* Más centrado entre los espacios */
        }

        .plan-card {
            background: #26334d;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 15px;
            width: 260px; /* Aumentar ligeramente el tamaño de las tarjetas */
            position: relative;
            overflow: hidden;
        }

        .plan-card h3 {
            font-size: 22px; /* Ajustar el tamaño de la fuente del título */
            color: #fff;
            margin: 0 0 10px;
            font-weight: 700;
            text-align: center;
        }

        .plan-card p {
            font-size: 18px;
            color: #7834dd;
            font-weight: 700;
            text-align: center;
            margin: 10px 0;
        }

        .plan-card ul {
            list-style: none;
            padding: 0;
            margin: 15px 0 0 0;
        }

        .plan-card ul li {
            margin-bottom: 8px;
            padding-left: 15px;
            position: relative;
            font-size: 12px; /* Mantener el tamaño de la fuente pequeño */
            color: #fff;
        }

        .plan-card ul li::before {
            content: "✔";
            color: #7834dd;
            position: absolute;
            left: 0;
        }

        .button-container {
            text-align: center;
            /* Centra solo el botón */
            margin-top: 15px;
        }

        .button {
            display: inline-block;
            padding: 10px 15px;
            background: linear-gradient(90deg, #6a5acd, #483d8b);
            color: #fff;
            font-weight: 700;
            font-size: 14px; /* Fuente más pequeña */
            text-align: center;
            text-decoration: none;
            border-radius: 8px;
            transition: transform 0.2s, box-shadow 0.2s;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .button:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 12px rgba(0, 0, 0, 0.2);
            background: linear-gradient(90deg, #483d8b, #6a5acd);
        }

        .button:active {
            transform: translateY(0);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .contact-info {
            text-align: center;
            margin-top: 40px;
        }

        .contact-info p {
            margin: 5px 0;
            font-size: 18px;
            color: #fff;
        }

        footer {
            background: #003366;
            color: white;
            text-align: center;
            padding: 10px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="section">
            <h2 class="text-3xl font-bold text-center mb-12">CONOCE NUESTROS PLANES</h2>
            <div class="plans">
                <!-- Primera fila con tres planes -->
                <div class="plan-card">
                    <h3>BÁSICO</h3>
                    <p>S/. 9 por equipo</p>
                    <ul>
                        <li>Logo y banner del campeonato</li>
                        <li>Fases y grupos</li>
                        <li>Marcador en vivo y tiempo real</li>
                        <li>Tabla de posiciones</li>
                        <li>Un administrador</li>
                    </ul>
                    <div class="button-container">
                        <a href="#" class="button mt-4">
                            Saber Mas
                        </a>
                    </div>
                </div>
                <div class="plan-card">
                    <h3>LIGHT</h3>
                    <p>S/. 19 por equipo</p>
                    <ul>
                        <li>Código QR del campeonato</li>
                        <li>Ubicación Google Maps</li>
                        <li>Reglamento del campeonato</li>
                        <li>Hasta 2 administradores</li>
                        <li>Medios ilimitados</li>
                    </ul>
                    <div class="button-container">
                        <a href="#" class="button mt-4">
                            Saber Mas
                        </a>
                    </div>
                </div>
                <div class="plan-card">
                    <h3>STANDARD</h3>
                    <p>S/. 29 por equipo</p>
                    <ul>
                        <li>Página web del campeonato</li>
                        <li>Tabla de goleadores</li>
                        <li>Estadísticas de jugador</li>
                        <li>Hasta 4 patrocinadores</li>
                        <li>Hasta 4 administradores</li>
                    </ul>
                    <div class="button-container">
                        <a href="#" class="button mt-4">
                            Saber Mas
                        </a>
                    </div>
                </div>
                <!-- Segunda fila centrada -->
                <div class="row-last">
                    <div class="plan-card">
                        <h3>PREMIUM</h3>
                        <p>S/. 39 por equipo</p>
                        <ul>
                            <li>Torneos y categorías</li>
                            <li>Actas de los partidos</li>
                            <li>Hasta 8 administradores</li>
                            <li>Medios ilimitados</li>
                            <li>Patrocinadores destacados</li>
                        </ul>
                        <div class="button-container">
                            <a href="#" class="button mt-4">
                                Saber Mas
                            </a>
                        </div>
                    </div>
                    <div class="plan-card">
                        <h3>PRO</h3>
                        <p>S/. 49 por equipo</p>
                        <ul>
                            <li>Manejo de árbitros</li>
                            <li>Encuestas para seguidores</li>
                            <li>Votaciones por jugadores</li>
                            <li>Administradores ilimitados</li>
                            <li>Torneo destacado en página web</li>
                        </ul>
                        <div class="button-container">
                            <a href="#" class="button mt-4">
                                Saber Mas
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
