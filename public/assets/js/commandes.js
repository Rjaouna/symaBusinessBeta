// assets/js/commandes.js

document.addEventListener("turbo:load", () => {
  // Vérifiez si le script est déjà initialisé
  if (window.commandesInitialized) {
    return;
  }
  window.commandesInitialized = true;

  const updateCommandesData = async () => {
    try {
      const response = await axios.get("/api/commande");
      localStorage.setItem("commandesData", JSON.stringify(response.data)); // Stocker les données sous forme de chaîne JSON
    } catch (error) {
      console.error("Erreur lors de la récupération des commandes:", error);
    }
  };

  // Fonction pour récupérer les données de localStorage
  const getCommandesFromLocalStorage = () => {
    const commandesJson = localStorage.getItem("commandesData"); // Remplacez 'commandesData' par la clé que vous utilisez
    return commandesJson ? JSON.parse(commandesJson) : []; // Convertir en objet JavaScript
  };

  // Fonction pour mettre à jour le nombre total de commandes en attente
  const updateTotalEnAttente = (commandes) => {
    const totalEnAttente = commandes.filter(
      (commande) => commande.status === "en_attente"
    ).length;

    // Mettre à jour le DOM avec le nombre total des commandes en attente
    const totalEnAttenteElement = document.getElementById("totalEnAttente");
    if (totalEnAttenteElement) {
      totalEnAttenteElement.textContent = totalEnAttente;
    }
  };

  // Fonction pour mettre à jour les commandes à intervalle régulier
  const updateCommandesInterval = async () => {
    await updateCommandesData(); // Mettre à jour les données depuis l'API
    const commandesData = getCommandesFromLocalStorage(); // Récupérer les données mises à jour
    // console.log("Données de commandes :", commandesData); // Affichez les données
    updateTotalEnAttente(commandesData); // Mettre à jour le total en attente
  };

  // Mettre à jour les commandes toutes les 3 secondes (3000 ms)
  setInterval(updateCommandesInterval, 3000);

  // Appel initial pour mettre à jour le total en attente immédiatement
  updateCommandesInterval();
});
