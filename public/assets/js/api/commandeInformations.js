const updateStatutCommandesChart = async () => {
  try {
    const response = await axios.get("/api/commande");
    console.log(response.data); // Affichez les données reçues de l'API
  } catch (error) {
    console.error("Erreur lors de la récupération des commandes:", error);
  }
};

updateStatutCommandesChart();
