<!-- Tournament Modal -->
<div id="tournamentModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center">
    <div class="bg-[#1A1D24] p-6 rounded-lg w-full max-w-xl">
        <h2 class="text-xl font-bold mb-4">Crear Nueva Competición</h2>

        <form id="tournamentForm" class="space-y-4">
            <div>
                <label for="tournamentName" class="block text-sm font-medium mb-1">Nombre de tu Competición:</label>
                <input type="text" name="name" id="tournamentName"
                    class="w-full p-2 rounded bg-gray-800 border border-gray-700" required>
            </div>

            <div>
                <label for="tournamentDescription" class="block text-sm font-medium mb-1">Descripción Corta:</label>
                <textarea name="description" id="tournamentDescription"
                    class="w-full p-2 rounded bg-gray-800 border border-gray-700" required></textarea>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="competitionType" class="block text-sm font-medium mb-1">Tipo Competición:</label>
                    <select name="competition_type" id="competitionType"
                        class="w-full p-2 rounded bg-gray-800 border border-gray-700" required>
                        <option value="Aficionado">Aficionado</option>
                        <option value="Profesional">Profesional</option>
                    </select>
                </div>

                <div>
                    <label for="sportType" class="block text-sm font-medium mb-1">Deporte:</label>
                    <select name="sport_type" id="sportType"
                        class="w-full p-2 rounded bg-gray-800 border border-gray-700" required>
                        <option value="Futbol">Fútbol</option>
                        <option value="Futbol 7">Fútbol 7</option>
                        <option value="Futbol 8">Fútbol 8</option>
                        <option value="Fulbito">Fulbito</option>
                        <option value="Futsal">Futsal</option>
                    </select>
                </div>
            </div>

            <div>
                <label for="gender" class="block text-sm font-medium mb-1">Género:</label>
                <select name="gender" id="gender" class="w-full p-2 rounded bg-gray-800 border border-gray-700" required>
                    <option value="General">General</option>
                    <option value="Varones">Varones</option>
                    <option value="Mujeres">Mujeres</option>
                    <option value="Menores">Menores</option>
                </select>
            </div>

            <div>
                <label for="urlSlug" class="block text-sm font-medium mb-1">Nombre único - URL de torneo:</label>
                <div class="flex gap-2">
                    <input type="text" name="url_slug" id="urlSlug"
                        class="flex-1 p-2 rounded bg-gray-800 border border-gray-700" readonly required>
                    <button type="button" id="verifyUrl"
                        class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
                        Verificar
                    </button>
                </div>
                <p class="text-xs text-gray-400 mt-1">La URL se generará automáticamente. Verifícala antes de continuar.</p>
            </div>

            <div class="flex justify-end gap-2 mt-6">
                <button type="button" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700"
                    onclick="closeTournamentModal()">
                    Cancelar
                </button>
                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
                    Crear
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openTournamentModal() {
        document.getElementById('tournamentModal').classList.remove('hidden');
        document.getElementById('tournamentModal').classList.add('flex');
    }

    function closeTournamentModal() {
        document.getElementById('tournamentModal').classList.remove('flex');
        document.getElementById('tournamentModal').classList.add('hidden');
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
        if (!slug) return alert('Por favor, genera una URL primero.');

        try {
            const response = await fetch(`controllers/verify_url.php?slug=${slug}`);
            const { exists } = await response.json();

            if (exists) {
                alert('La URL ya existe. Puedes personalizarla.');
                urlSlug.readOnly = false;
            } else {
                alert('La URL está disponible.');
                urlSlug.readOnly = true;
            }
        } catch (error) {
            console.error('Error verificando la URL:', error);
            alert('Ocurrió un error al verificar la URL.');
        }
    });
</script>
