
$(document).ready(function() {
  // init for tag
  initDatatable();
});

// =========================  tag =========================== //

initDatatable = function() {
  if ($("#tag-datatable").length > 0) {
    oTable = $("#tag-datatable").DataTable({
      processing: true,
      serverSide: true,
      stateSave: true,
      ajax: {
        url: "/admin/tag/datatable_ajax",
      },
      columns: [
        { data: "id", orderable: true, searchable: false },
        { data: "type" },
        // { data: "head" },
        { data: "action", orderable: false, searchable: false },
      ],
      language: $_LANG.datatable,
    });
  }
};

ReloadDataTable = function() {
  $("#tag-datatable")
    .DataTable()
    .ajax.reload(null, false);
};

setSave = function() {
  event.preventDefault();
  tinyMCE.triggerSave();

  var frm_data = new FormData($("#tag_frm")[0]);

  $.ajax({
    url: "/admin/tag/save",
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
        window.location.href = "/admin/tag";
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
          url: "/admin/websetting/tag/set_delete",
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

setStatus = function(id, status) {
  event.preventDefault();
  let _token = $('meta[name="csrf-token"]').attr("content");

  $.ajax({
    url: "/admin/websetting/tag/set_status",
    type: "POST",
    data: {
      id: id,
      status: status,
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
};
