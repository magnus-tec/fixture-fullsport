    <!-- Tournament Modal -->
    <div id="tournamentModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center">
        <div class="bg-[#1b2238] p-6 rounded-lg w-full max-w-xl">
            <h2 class="text-xl font-bold mb-4">Crear Nueva Competición</h2>

            <form id="tournamentForm" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium mb-1">Nombre de tu Competición:</label>
                    <input type="text" name="name" id="tournamentName"
                        class="w-full p-2 rounded bg-gray-800 border border-gray-700" required>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Descripción Corta:</label>
                    <textarea name="description" id="tournamentDescription"
                        class="w-full p-2 rounded bg-gray-800 border border-gray-700" required></textarea>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium mb-1">Tipo Competición:</label>
                        <select name="competition_type" id="competitionType"
                            class="w-full p-2 rounded bg-gray-800 border border-gray-700" required>
                            <option value="Aficionado">Aficionado</option>
                            <option value="Profesional">Profesional</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1">Deporte:</label>
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
                    <label class="block text-sm font-medium mb-1">Género:</label>
                    <select name="gender" id="gender" class="w-full p-2 rounded bg-gray-800 border border-gray-700"
                        required>
                        <option value="General">General</option>
                        <option value="Varones">Varones</option>
                        <option value="Mujeres">Mujeres</option>
                        <option value="Menores">Menores</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Nombre único - URL de torneo:</label>
                    <div class="flex gap-2">
                        <input type="text" name="url_slug" id="urlSlug"
                            class="flex-1 p-2 rounded bg-gray-800 border border-gray-700" readonly>
                        <button type="button" id="verifyUrl"
                            class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">Verificar</button>
                    </div>
                    <p class="text-xs text-gray-400 mt-1">Si personalizas este campo, podrás tener una URL para tu
                        campeonato como tú quieras y que nadie más tendrá.</p>
                </div>

                <div class="flex justify-end gap-2 mt-6">
                    <button type="button" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700"
                        onclick="closeTournamentModal()">Cancelar</button>
                    <button type="submit"
                        class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">Crear</button>
                </div>
            </form>
        </div>
    </div>