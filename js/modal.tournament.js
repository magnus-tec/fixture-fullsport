// // Abrir el modal
document.getElementById('openTournamentModal').addEventListener('click', function () {
    document.getElementById('tournamentModal').classList.remove('hidden');
    document.getElementById('tournamentModal').classList.add('flex');
});

// Cerrar el modal
function closeTournamentModal() {
    document.getElementById('tournamentModal').classList.add('hidden');
    document.getElementById('tournamentModal').classList.remove('flex');
}

const tournamentName = document.getElementById('tournamentName');
const urlSlug = document.getElementById('urlSlug');
const verifyUrl = document.getElementById('verifyUrl');
const tournamentForm = document.getElementById('tournamentForm');

// Generar URL automáticamente
tournamentName.addEventListener('input', () => {
    const slug = tournamentName.value
        .toLowerCase()
        .normalize('NFD')
        .replace(/[\u0300-\u036f]/g, '')
        .replace(/[^a-z0-9\s-]/g, '')
        .trim()
        .replace(/\s+/g, '-');
    urlSlug.value = slug;
});

// Verificar disponibilidad de la URL
verifyUrl.addEventListener('click', async () => {
    const slug = urlSlug.value;
    if (!slug) {
        return Swal.fire({
            toast: true,
            icon: 'warning',
            title: 'Atención: Por favor, genera una URL primero.',
            position: 'top',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            background: '#1f2937',
            color: '#ffffff',
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer);
                toast.addEventListener('mouseleave', Swal.resumeTimer);
            }
        });
    }

    try {
        const response = await fetch(`../controllers/verify_url.php?slug=${slug}`);

        // Verificamos si la respuesta es válida
        if (!response.ok) {
            throw new Error('Error en la respuesta del servidor');
        }

        const { exists, error } = await response.json();

        if (error) {
            Swal.fire({
                toast: true,
                icon: 'error',
                title: `Error: ${error}`,
                position: 'top',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                background: '#1f2937',
                color: '#ffffff',
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer);
                    toast.addEventListener('mouseleave', Swal.resumeTimer);
                }
            });
            return;
        }

        if (exists) {
            Swal.fire({
                toast: true,
                icon: 'warning',
                title: 'URL existente: La URL ya existe. Puedes personalizarla.',
                position: 'top',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                background: '#1f2937',
                color: '#ffffff',
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer);
                    toast.addEventListener('mouseleave', Swal.resumeTimer);
                }
            });
            urlSlug.readOnly = false;
        } else {
            Swal.fire({
                toast: true,
                icon: 'success',
                title: 'Perfecto😀: La URL está disponible.',
                position: 'top',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                background: '#1f2937',
                color: '#ffffff',
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer);
                    toast.addEventListener('mouseleave', Swal.resumeTimer);
                }
            });
            urlSlug.readOnly = true;
        }
    } catch (error) {
        console.error('Error verificando la URL:', error);
        Swal.fire({
            toast: true,
            icon: 'error',
            title: 'Error: Ocurrió un error al verificar la URL.',
            position: 'top',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            background: '#1f2937',
            color: '#ffffff',
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer);
                toast.addEventListener('mouseleave', Swal.resumeTimer);
            }
        });
    }
});





// // Función simple para cargar torneos
async function fetchTournaments() {
    try {
        // Fetch tournaments and user data
        const [tournamentsResponse, userResponse] = await Promise.all([
            fetch('../controllers/get_tournaments.php'),
            fetch('../controllers/controllerUserData.php')
        ]);

        const tournaments = await tournamentsResponse.json();
        const userData = await userResponse.json();

        const container = document.getElementById('tournaments-cards');

        // Create the HTML for each tournament
        const tournamentsHTML = tournaments.map(tournament => `
            <div class="bg-[#1b2238] rounded-lg p-4 shadow-xl hover:scale-105 hover:rotate-3d transition-all transform duration-300 border-2 border-white">
                <div class="text-center mb-4">
                    <h3 class="text-lg font-bold text-white">${tournament.name}</h3>
                    <p class="text-gray-400">${tournament.description}</p>
                </div>
                <div class="flex flex-wrap gap-2 mb-4 justify-center">
                    <span class="bg-[#6d28d9] text-white px-2 py-1 rounded-md text-xs border-2 border-white">${tournament.competition_type}</span>
                    <span class="bg-[#6d28d9] text-white px-2 py-1 rounded-md text-xs border-2 border-white">${tournament.sport_type}</span>
                    <span class="bg-[#6d28d9] text-white px-2 py-1 rounded-md text-xs border-2 border-white">${tournament.gender}</span>
                </div>
                <div class="flex justify-center">
                    ${tournament.created_by == userData.id
                ? `<a href="./tournament-detail.php?id=${tournament.id}" 
                              class="inline-flex items-center justify-center bg-[#6d28d9] text-white px-4 py-2 rounded-md hover:bg-[#5b21b6] transition-colors border-2 border-white">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                     stroke-width="1.5" stroke="currentColor" class="w-5 h-5 mr-2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          d="M15.75 9V5.25m0 0L12 9m3.75-3.75L17.25 9m-5.25 6v3.75m0-3.75L9 12.75m3 3l3.75-3.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Administrar
                            </a>`
                : `<a href="../pages/tournament-detail.php?id=${tournament.id}" 
                              class="inline-flex items-center justify-center bg-[#6d28d9] text-white px-4 py-2 rounded-md hover:bg-[#5b21b6] transition-colors border-2 border-white">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                     stroke-width="1.5" stroke="currentColor" class="w-5 h-5 mr-2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          d="M15.75 9V5.25m0 0L12 9m3.75-3.75L17.25 9m-5.25 6v3.75m0-3.75L9 12.75m3 3l3.75-3.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Visualizar
                            </a>`
            }
                </div>
            </div>
        `).join('');



        // Update the container with the new tournaments
        container.innerHTML = `
            <div class="bg-transparent rounded-lg p-4 lg:col-span-7">
                <h2 class="text-lg font-bold mb-4">Toda las Competiciones</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                    ${tournamentsHTML}
                </div>
            </div>
        `;
    } catch (error) {
        console.error('Error:', error);
        document.getElementById('tournaments-cards').innerHTML = '<p class="text-red-500">Error al cargar los torneos.</p>';
    }
}

document.addEventListener('DOMContentLoaded', fetchTournaments);

// // Manejar el envío del formulario
document.getElementById('tournamentForm').addEventListener('submit', function (e) {
    e.preventDefault();
    const formData = new FormData(this);

    fetch('../controllers/save_tournament.php', {
        method: 'POST',
        body: formData
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Éxito',
                    text: 'Torneo creado exitosamente.',
                    background: '#1a1d24',
                    color: '#ffffff'
                });
                closeTournamentModal();
                this.reset();
                // Recargar la lista de torneos
                fetchTournaments();
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Error al crear el torneo: ' + data.message,
                    background: '#1a1d24',
                    color: '#ffffff'
                });
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Ocurrió un error al crear el torneo.',
                background: '#1a1d24',
                color: '#ffffff'
            });
        });
});

// Cargar torneos cuando se carga la página
document.addEventListener('DOMContentLoaded', fetchTournaments);