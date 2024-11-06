$(document).ready(function () {
  $("#myDataTable").DataTable({
    paging: true,
    searching: true,
    ordering: true,
    info: true,
    lengthMenu: [10, 20, 30, 50],
    language: {
      info: "Affichage des entrées _START_ à _END_ sur _TOTAL_",
      infoEmpty: "Aucune entrée à afficher",
      lengthMenu: "Afficher _MENU_ cartes Sim par page",
      search: "Rechercher :",
      searchPlaceholder: "Ex : 13596585246584", // Ajout du placeholder
      paginate: {
        first: "Premier",
        last: "Dernier",
        next: "Suivant",
        previous: "Précédent",
      },
      zeroRecords: "Aucun enregistrement correspondant trouvé",
    },
  });
});


