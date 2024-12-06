<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Full Sports - Explora tus deportes favoritos</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Estilos personalizados */
        .card-gradient {
            background: linear-gradient(135deg, #1e293b, #26334d);
        }

        .card-hover {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .card-hover:hover {
            transform: translateY(-10px) scale(1.05);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.3);
        }

        .icon-hover {
            transition: color 0.3s ease, transform 0.3s ease;
        }

        .card-hover:hover .icon-hover {
            transform: rotate(10deg) scale(1.2);
        }

        h2 {
            font-size: 2rem;
            font-weight: 700;
            color: #1E293B;
            margin-bottom: 30px;
            text-align: center;
        }
    </style>
</head>

<body class="bg-gray-900 text-gray-100">
    <section class="py-16">
        <div class="container mx-auto px-6">
            <!-- Título -->
            <h2>
                EXPLORA LO QUE PUEDES HACER CON FULL SPORT PLAY 
            </h2>

            <!-- Cards -->
            <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-10">
                <!-- Card 1 -->
                <div class="card-gradient p-8 rounded-lg text-center card-hover">
                    <div class="icon-hover text-red-500 text-5xl mb-6">
                        <i class="fas fa-project-diagram"></i>
                    </div>
                    <h3 class="text-2xl font-semibold mb-4 text-white">Organiza Torneos</h3>
                    <p class="text-gray-300">
                        Crea torneos, abre inscripciones, genera fixtures y mantén una tabla de posiciones actualizada.
                    </p>
                </div>

                <!-- Card 2 -->
                <div class="card-gradient p-8 rounded-lg text-center card-hover">
                    <div class="icon-hover text-blue-500 text-5xl mb-6">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <h3 class="text-2xl font-semibold mb-4 text-white">Administra Equipos</h3>
                    <p class="text-gray-300">
                        Gestiona equipos, participa en torneos, genera estadísticas y mantén fichas de control
                        detalladas.
                    </p>
                </div>

                <!-- Card 3 -->
                <div class="card-gradient p-8 rounded-lg text-center card-hover">
                    <div class="icon-hover text-green-500 text-5xl mb-6">
                        <i class="fas fa-trophy"></i>
                    </div>
                    <h3 class="text-2xl font-semibold mb-4 text-white">Sé una Leyenda</h3>
                    <p class="text-gray-300">
                        Construye tu perfil de futbolista con estadísticas de goles, asistencias, y partidos.
                    </p>
                </div>

                <!-- Card 4 -->
                <div class="card-gradient p-8 rounded-lg text-center card-hover">
                    <div class="icon-hover text-yellow-500 text-5xl mb-6">
                        <i class="fas fa-users"></i>
                    </div>
                    <h3 class="text-2xl font-semibold mb-4 text-white">Únete a la Comunidad</h3>
                    <p class="text-gray-300">
                        Conéctate con nuestra comunidad internacional de embajadores del fútbol.
                    </p>
                </div>
            </div>
        </div>
    </section>
</body>

</html>