function openAddEditPrizesModal(versionId) {
  document.getElementById("addEditPrizesModal").classList.remove("hidden");
  document.querySelector('input[name="versionId"]').value = versionId; // Actualiza el versionId
}

function openAddTournamentBasesModal(versionId) {
  document.getElementById("addTournamentBasesModal").classList.remove("hidden");
  document.querySelector('input[name="versionId"]').value = versionId; // Actualiza el versionId
}

function closeModal() {
  document.getElementById("addEditPrizesModal").classList.add("hidden");
  document.getElementById("addTournamentBasesModal").classList.add("hidden");
}

// Script para agregar premios
document
  .getElementById("addEditPrizesForm")
  .addEventListener("submit", function (event) {
    event.preventDefault(); // Evitar el envío normal del formulario

    const formData = new FormData(this);

    fetch("../controllers/save_tournament_prizes_bases.php", {
      method: "POST",
      body: formData,
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.status === "success") {
          alert(data.message);
          // Agregar el premio a la interfaz
          const prizeList = document.getElementById("prizeList");
          const newPrize = document.createElement("div");
          newPrize.textContent =
            document.getElementById("prizeDescription").value; // Obtener el valor del premio
          prizeList.appendChild(newPrize);
          closeModal(); // Cerrar el modal
        } else {
          alert("Error al agregar el premio.");
        }
      })
      .catch((error) => console.error("Error:", error));
  });

// Script para agregar bases
document
  .getElementById("addTournamentBasesForm")
  .addEventListener("submit", function (event) {
    event.preventDefault(); // Evitar el envío normal del formulario

    const formData = new FormData(this);

    fetch("../controllers/save_tournament_prizes_bases.php", {
      method: "POST",
      body: formData,
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.status === "success") {
          alert(data.message);
          // Agregar la base a la interfaz
          const baseList = document.getElementById("baseList");
          const newBase = document.createElement("div");
          newBase.innerHTML = `<a href="${data.file}" class="text-green-500" download>Descargar</a>`; // Enlace para descargar
          baseList.appendChild(newBase);
          closeModal(); // Cerrar el modal
        } else {
          alert("Error al agregar la base: " + data.message);
        }
      })
      .catch((error) => console.error("Error:", error));
  });

// Actualizar el nombre del archivo seleccionado
document
  .getElementById("tournamentBasesFile")
  .addEventListener("change", function () {
    const fileName = this.files[0]
      ? this.files[0].name
      : "Ningún archivo seleccionado";
    document.getElementById("selectedFileName").textContent = fileName;
  });
