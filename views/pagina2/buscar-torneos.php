<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Full Sports - Explora tus deportes favoritos</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Estilo personalizado del buscador */
        .custom-search {
            display: flex;
            align-items: center;
            background-color: #1e293b;
            /* Fondo oscuro */
            border-radius: 9999px;
            /* Bordes completamente redondeados */
            padding: 10px 20px;
            /* Espaciado interno */
            border: 1px solid #4b5563;
            /* Borde sutil */
        }

        .custom-search input {
            background: none;
            border: none;
            outline: none;
            color: #9ca3af;
            /* Color del texto */
            font-size: 16px;
            flex: 1;
            /* Expandir el input dentro del contenedor */
        }

        .custom-search input::placeholder {
            color: #9ca3af;
            /* Color del placeholder */
        }

        .custom-search svg {
            color: #9ca3af;
            /* Color del icono */
            margin-right: 10px;
        }

        .custom-search input:focus {
            color: #ffffff;
            /* Color del texto al enfocar */
        }

        /* Estilo de la lista desplegable */
        .dropdown-list {
            position: absolute;
            margin-top: 8px;
            /* Separación entre input y lista */
            z-index: 10;
            background-color: #ffffff;
            /* Fondo blanco */
            border: 1px solid #e5e7eb;
            /* Borde claro */
            border-radius: 8px;
            /* Bordes redondeados */
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
            /* Sombra ligera */
            width: 100%;
            /* Alinear con el buscador */
        }

        .dropdown-list ul {
            max-height: 240px;
            /* Altura máxima */
            overflow-y: auto;
            /* Scroll si supera la altura */
        }

        .dropdown-list li {
            padding: 10px 15px;
            cursor: pointer;
            transition: background-color 0.2s;
        }

        .dropdown-list li:hover {
            background-color: #f3f4f6;
            /* Fondo al pasar el cursor */
        }
    </style>
</head>

<body class="bg-gray-900 text-gray-100">
    <section class="py-20">
        <div class="container mx-auto px-4">
            <!-- Buscador -->
            <div class="relative w-full max-w-md mx-auto mb-12">
                <div class="custom-search">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <input type="text" id="tournamentSearch" placeholder="Buscar torneo..."
                        oninput="filterTournaments()">
                </div>

                <!-- Lista desplegable -->
                <div id="tournamentList" class="dropdown-list hidden text-black">
                    <ul>
                        <?php
                        require_once $_SERVER['DOCUMENT_ROOT'] . '/fixturepro/config/connection.php';
                        $sql = "SELECT id, name FROM tournaments";
                        $result = mysqli_query($con, $sql);
                        while ($tournament = mysqli_fetch_assoc($result)) {
                            echo '<li onclick="selectTournament(' . $tournament['id'] . ')">'
                                . htmlspecialchars($tournament['name']) .
                                '</li>';
                        }
                        ?>
                    </ul>
                </div>
            </div>
            <!-- Título -->
            <h2 class="text-3xl font-bold text-center mb-12">
                CON FIXTURE PODRÁS:
            </h2>
            <!-- Cards -->
            <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-8">
                <div class="bg-[#26334d] p-6 rounded-lg text-white  text-center hover:shadow-xl hover:scale-105 transition-all duration-300 animate__animated animate__fadeInUp">
                    <i class="fas fa-project-diagram text-red-500 text-4xl mb-4"></i>
                    <h3 class="text-xl font-semibold mb-2">ORGANIZA TORNEOS</h3>
                    <p class="text-white-600">Crea un torneo, apertura inscripciones, genera fixture, tabla de
                        posiciones...</p>
                </div>
                <div class="bg-[#26334d] p-6 rounded-lg text-white  text-center hover:shadow-xl hover:scale-105 transition-all duration-300 animate__animated animate__fadeInUp">
                    <i class="fas fa-shield-alt text-blue-500 text-4xl mb-4"></i>
                    <h3 class="text-xl font-semibold mb-2">ADMINISTRA EQUIPOS</h3>
                    <p class="text-white-600">Crear un equipo y participa en torneos, genera estadísticas, fichas de
                        control...</p>
                </div>
                <div class="bg-[#26334d] p-6 rounded-lg text-white  text-center hover:shadow-xl hover:scale-105 transition-all duration-300 animate__animated animate__fadeInUp">
                    <i class="fas fa-trophy text-green-500 text-4xl mb-4"></i>
                    <h3 class="text-xl font-semibold mb-2">SE UNA LEYENDA</h3>
                    <p class="text-white-6000">Ten un perfil de futbolista, con tu historial de goles, asistencias,
                        partidos.</p>
                </div>
                <div class="bg-[#26334d] p-6 rounded-lg text-white  text-center hover:shadow-xl hover:scale-105 transition-all duration-300 animate__animated animate__fadeInUp">
                    <i class="fas fa-users text-yellow-500 text-4xl mb-4"></i>
                    <h3 class="text-xl font-semibold mb-2">CIEF</h3>
                    <p class="text-white-600">Sé parte de nuestra comunidad internacional de embajadores del fútbol.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- JavaScript -->
    <script>
        function filterTournaments() {
            const searchInput = document.getElementById("tournamentSearch").value.toLowerCase().trim();
            const items = document.querySelectorAll("#tournamentList li");
            const tournamentList = document.getElementById("tournamentList");

            if (searchInput === "") {
                tournamentList.classList.add("hidden");
                return;
            }

            let hasVisibleItems = false;

            items.forEach(item => {
                const text = item.textContent.toLowerCase();
                if (text.includes(searchInput)) {
                    item.style.display = "block";
                    hasVisibleItems = true;
                } else {
                    item.style.display = "none";
                }
            });

            tournamentList.classList.toggle("hidden", !hasVisibleItems);
        }

        function selectTournament(id) {
            window.location.href = "views/login.php?id=" + id;
        }
    </script>
</body>

</html>