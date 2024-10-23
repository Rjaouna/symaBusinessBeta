document.addEventListener("DOMContentLoaded", function () {
  const firstSerialNumber = document.getElementById(
    "carte_sim_batch_firstSerialNumber"
  );
  const lastSerialNumber = document.getElementById(
    "carte_sim_batch_lastSerialNumber"
  );
  const typeCarteSim = document.getElementById("carte_sim_batch_typeCarteSim");
  const importButton = document.getElementById("importValidation");
  const flexCheckDefault = document.getElementById("flexCheckDefault");

  // Désactiver le champ typeCarteSim au chargement de la page
  typeCarteSim.setAttribute("disabled", "disabled");

  // Function to validate the input
  function validateForm() {
    const firstSerialValid = /^\d{14}$/.test(firstSerialNumber.value);
    const lastSerialValid = /^\d{14}$/.test(lastSerialNumber.value);
    const typeValid = typeCarteSim.value !== "";

    // Enable the import button only if all fields are valid and checkbox is checked
    if (
      firstSerialValid &&
      lastSerialValid &&
      typeValid &&
      flexCheckDefault.checked
    ) {
      importButton.removeAttribute("disabled"); // Enable button
    } else {
      importButton.setAttribute("disabled", "disabled"); // Disable button
    }

    // Enable/disable the select box based on the validity of first and last serial numbers
    if (firstSerialValid && lastSerialValid) {
      typeCarteSim.removeAttribute("disabled"); // Enable select
    } else {
      typeCarteSim.setAttribute("disabled", "disabled"); // Disable select
    }
  }

  // Add event listeners to fields for validation
  firstSerialNumber.addEventListener("input", validateForm);
  lastSerialNumber.addEventListener("input", validateForm);
  typeCarteSim.addEventListener("change", validateForm);
  flexCheckDefault.addEventListener("change", validateForm); // Listen for changes to the checkbox
});

// Activer le bouton après activation de la checkbox
function compareSerialNumbers() {
  // Récupérer les valeurs des numéros de série
  const firstSerial = document.getElementById(
    "carte_sim_batch_firstSerialNumber"
  ).value;
  const lastSerial = document.getElementById(
    "carte_sim_batch_lastSerialNumber"
  ).value;

  // Vérifier que les deux numéros ont la même longueur
  if (firstSerial.length !== lastSerial.length) {
    document.getElementById("result").innerHTML =
      "Les numéros de série doivent avoir la même longueur.";
    return;
  }

  const length = firstSerial.length;
  let startDiffIndex = -1;
  let endDiffIndex = -1;

  // Identifier les indices de début et de fin de la différence
  for (let i = 0; i < length; i++) {
    if (firstSerial[i] !== lastSerial[i]) {
      if (startDiffIndex === -1) {
        startDiffIndex = i; // Début de la différence
      }
      endDiffIndex = i; // Fin de la différence (sera mis à jour)
    }
  }

  // Si aucune différence n'est trouvée
  if (startDiffIndex === -1) {
    document.getElementById("result").innerHTML =
      "Les numéros de série sont identiques.";
    return;
  }

  // Identifier les parties constantes et variables
  const beforeDiff = firstSerial.slice(0, startDiffIndex);
  const afterDiff = firstSerial.slice(endDiffIndex + 1);

  // Vérification si la différence commence dans les quatre derniers chiffres
  if (startDiffIndex >= length - 4) {
    // Calculer le nombre total entre les deux numéros, en tenant compte des zéros
    const firstNumber = BigInt(firstSerial);
    const lastNumber = BigInt(lastSerial);

    // Vérifier la différence et s'assurer qu'elle ne dépasse pas 1000
    if (lastNumber - firstNumber > 1000n) {
      document.getElementById("result").innerHTML =
        "La différence ne peut pas dépasser 1000.";
      return;
    }

    const totalCards = lastNumber - firstNumber + 1n; // +1 pour inclure le premier numéro

    // Afficher le résultat
    const typeCarteSim = document.getElementById(
      "carte_sim_batch_typeCarteSim"
    );
    document.getElementById("result").innerHTML = `
      <div>Vous allez maintenant importer <strong>${totalCards.toString()} X ${
      typeCarteSim.value
    }</strong> !</div>
      <div>Numéro de série de départ : <strong>${firstSerial}</strong></div>
      <div>Numéro de série de fin : <strong>${lastSerial}</strong></div>
    `;
  } else {
    // Si la différence est au milieu de la chaîne
    const firstVariable = firstSerial.slice(startDiffIndex, endDiffIndex + 1);
    const lastVariable = lastSerial.slice(startDiffIndex, endDiffIndex + 1);

    // Convertir les parties variables en entiers
    const firstVariableNumber = BigInt(firstSerial.slice(startDiffIndex));
    const lastVariableNumber = BigInt(lastSerial.slice(startDiffIndex));

    // Calculer le nombre total de cartes SIM entre les deux valeurs
    const totalCards = lastVariableNumber - firstVariableNumber + BigInt(1); // +1 pour inclure le premier numéro

    // Afficher le résultat
    const typeCarteSim = document.getElementById(
      "carte_sim_batch_typeCarteSim"
    );
    document.getElementById("result").innerHTML = `
      <div>Vous allez maintenant importer <strong>${totalCards.toString()} X ${
      typeCarteSim.value
    }</strong> !</div>
      <div>Numéro de série de départ : <strong>${beforeDiff}${firstVariable}${afterDiff}</strong></div>
      <div>Numéro de série de fin : <strong>${beforeDiff}${lastVariable}${afterDiff}</strong></div>
          <div class="form-check">
      <input class="form-check-input" type="checkbox" value="" id="flexCheckDefault">
      <label class="form-check-label" for="flexCheckDefault">
        <span class="badge badge-warning">Cochez cette case pour activer le bouton "Lancer l'import"</span>
      </label>
    </div>
    `;
  }

  // Ajouter un écouteur d'événements pour la case à cocher
  const flexCheckDefault = document.getElementById("flexCheckDefault");
  const importButton = document.getElementById("importValidation");

  // Vérifie si la case est cochée et active/désactive le bouton
  flexCheckDefault.addEventListener("change", function () {
    if (flexCheckDefault.checked) {
      importButton.removeAttribute("disabled"); // Activer le bouton
    } else {
      importButton.setAttribute("disabled", "disabled"); // Désactiver le bouton
    }
  });
}
