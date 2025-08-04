
$(document).ready(function() {
  // init for logs
  initDatatable();
});

// =========================  logs =========================== //

initDatatable = function() {
  if ($("#logs-datatable").length > 0) {
    oTable = $("#logs-datatable").DataTable({
      processing: true,
      serverSide: true,
      stateSave: true,
      ajax: {
        url: "/admin/logs/datatable_ajax",
      },
      columns: [
        { data: "id", orderable: true, searchable: false },
        { data: "log_name" },
        { data: "description" },
        { data: "subject_type" },
        { data: "event" },
        { data: "subject_id" },
        { data: "updated_at" },
        { data: "action", orderable: false, searchable: false },
      ],
      language: $_LANG.datatable,
    });
  }
};

ReloadDataTable = function() {
  $("#logs-datatable")
    .DataTable()
    .ajax.reload(null, false);
};



