$(document).ready(function () {
  $("#myDataTable").DataTable({
    paging: true,
    searching: true,
    ordering: true,
    info: true,
    lengthMenu: [10, 20, 30, 50],
    language: {
      info: "",
      infoEmpty: "Aucune entrée à afficher",
      lengthMenu: "Afficher _MENU_ par page",
      search: "",
      searchPlaceholder: "Rechercher", // Ajout du placeholder
      paginate: {
        first: "",
        last: "",
        next: "",
        previous: "",
      },
      zeroRecords: "Aucun enregistrement correspondant trouvé",
    },
  });
});


