$(document).ready(function() {
  initDatatable();
});

// =========================  permission =========================== //

initDatatable = function() {
  if ($("#permission-datatable").length > 0) {
    var column = [];
    column.push({ "data": "id", searchable: false });
    column.push({ "data": "name" });
    column.push({ "data": "group" });
    column.push({ "data": "module" });
    column.push({ "data": "page" });
    column.push({ "data": "action" });
    column.push({ "data": "route_name" });
    column.push({ "data": "updated_at" });
    column.push({ "data": "manage" });

    oTable = $("#permission-datatable").DataTable({
      processing: true,
      serverSide: true,
      stateSave: true,
      ajax: {
        url: "/admin/user/permission/datatable_ajax",
      },
      "columns": column,
      "language": $_LANG.datatable
    });

    
  }
};

setReloadDataTable = function() {
  $("#permission-datatable")
    .DataTable()
    .ajax.reload(null, false);
};

setStatus = function(id, status) {
  event.preventDefault();
  let _token = $('meta[name="csrf-token"]').attr("content");

  $.ajax({
    url: "/admin/user/permission/set_status",
    type: "POST",
    data: {
      id: id,
      status: status,
      _token: _token,
    },
    success: function(resp) {
      if (resp.success) {
        noti("success", resp.msg);
        setReloadDataTable();
      } else {
        noti("error", resp.msg);
        setReloadDataTable();
      }
    },
  });
};

setDelete = function(id) {
  bootbox.confirm({
    message: $_LANG.confirm.delete,
    buttons: {
      confirm: {
        label: $_LANG.confirm.ok,
        className: "btn-success",
      },
      cancel: {
        label: $_LANG.confirm.cancel,
        className: "btn-danger",
      },
    },
    callback: function(result) {
      if (result) {
        event.preventDefault();
        let _token = $('meta[name="csrf-token"]').attr("content");

        $.ajax({
          url: "/admin/user/permission/set_delete",
          type: "POST",
          data: {
            id: id,
            status: status,
            _token: _token,
          },
          success: function(resp) {
            if (resp.success) {
              noti("success", resp.msg);
              setReloadDataTable();
            } else {
              noti("error", resp.msg);
            }
          },
        });
      }
    },
  });
};

setSave = function() {
  event.preventDefault();
  tinyMCE.triggerSave();
  

  var frm_data = new FormData($("#permission_frm")[0]);

  $.ajax({
    url: "/admin/user/permission/save",
    type: "POST",
    contentType: false,
    data: frm_data,
    processData: false,
    headers: {
      "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
    },
    beforeSend: function (xhr) {
      // fill metadata
    
      // validate
      var rules = {
        name: {
          required: true,
          maxlength: 500,
        }
      }

      var messages = $_LANG.validate.message;

      frm_validate($("#permission_frm"), rules, messages)
      var chk_valid = $("#permission_frm").valid();
      if (chk_valid) {
        return chk_valid;
      } else {
        noti('error', $_LANG.validate.error);
        return chk_valid;
      }
    },
    success: function(resp) {
      if (resp.success) {
        noti("success", resp.msg);
        window.location.href = "/admin/user/permission";
      } else {
        noti("error", resp.msg);
      }
    },
  });
};

setGeneratePermission = function () {
  // event.preventDefault();
  let _token = $('meta[name="csrf-token"]').attr("content");

  $.ajax({
    url: "/admin/user/permission/generate_permission",
    type: "POST",
    data: {
      _token: _token,
    },
    success: function (resp) {
      if (resp.success) {
        noti("success", resp.msg);
        setReloadDataTable();
      } else {
        noti("error", resp.msg);
        setReloadDataTable();
      }
    },
  });
};