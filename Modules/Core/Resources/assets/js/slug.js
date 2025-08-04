
$(document).ready(function() {
  // init for slug
  initDatatable();
});

// =========================  slug =========================== //

initDatatable = function() {
  if ($("#slug-datatable").length > 0) {
    oTable = $("#slug-datatable").DataTable({
      processing: true,
      serverSide: true,
      stateSave: true,
      ajax: {
        url: "/admin/slug/datatable_ajax",
      },
      columns: [
        { data: "id", orderable: true, searchable: false },
        { data: "level" },
        { data: "slug" },
        { data: "module" },
        { data: "method" },
        { data: "data_id" },
        { data: "action", orderable: false, searchable: false },
      ],
      language: $_LANG.datatable,
    });
  }
};

ReloadDataTable = function() {
  $("#slug-datatable")
    .DataTable()
    .ajax.reload(null, false);
};

setSave = function() {
  event.preventDefault();
  tinyMCE.triggerSave();

  var frm_data = new FormData($("#slug_frm")[0]);

  $.ajax({
    url: "/admin/slug/save",
    type: "POST",
    contentType: false,
    data: frm_data,
    processData: false,
    headers: {
      "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
    },
    success: function(resp) {
      if (resp.success) {
        noti("success", resp.msg);
        window.location.href = "/admin/slug";
      } else {
        noti("error", resp.msg);
      }
    },
  });
};

setDelete = function(id) {
  bootbox.confirm({
    message: "ยืนยันลบ ?",
    buttons: {
      confirm: {
        label: "ตกลง",
        className: "btn-success",
      },
      cancel: {
        label: "ยกเลิก",
        className: "btn-danger",
      },
    },
    callback: function(result) {
      if (result) {
        event.preventDefault();
        let _token = $('meta[name="csrf-token"]').attr("content");

        $.ajax({
          url: "/admin/slug/set_delete",
          type: "POST",
          data: {
            id: id,
            _token: _token,
          },
          success: function(resp) {
            if (resp.success) {
              noti("success", resp.msg);
              ReloadDataTable();
            } else {
              noti("error", resp.msg);
            }
          },
        });
      }
    },
  });
};

