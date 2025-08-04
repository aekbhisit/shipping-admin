$(document).ready(function() {
  initDatatable();
});

// =========================  role =========================== //

initDatatable = function() {
  if ($("#role-datatable").length > 0) {
    var column = [];
    column.push({ "data": "id", searchable: false });
    column.push({ "data": "name" });
    column.push({ "data": "updated_at" });
    column.push({ "data": "manage" });

    oTable = $("#role-datatable").DataTable({
      processing: true,
      serverSide: true,
      stateSave: true,
      rowReorder: {
        dataSrc: "sequence",
      },
      columnDefs: [
        { orderable: true, className: "reorder", targets: 0 },
        { orderable: true, targets: "_all" },
      ],
      ajax: {
        url: "/admin/user/role/datatable_ajax",
      },
      "columns": column,
      "language": $_LANG.datatable
    });

    oTable.on("row-reorder", function(e, diff, edit) {
      var moving = {};
      $.each(diff, function(key, row) {
        console.log(key + ": " + row + "position" + row.newPosition);
        var rowData = oTable.row(row.node).data();
        moving[rowData.id] = row.newData;
      });
      var sort_json = JSON.stringify(moving);

      let _token = $('meta[name="csrf-token"]').attr("content");
      $.ajax({
        url: "/admin/user/role/set_re_order",
        type: "POST",
        data: {
          sort_json: sort_json,
          _token: _token,
        },
        success: function(resp) {
          if (resp.success) {
            setReloadDataTable();
          } else {
            noti("error", resp.msg);
          }
        },
      });
    });
  }
};

setReloadDataTable = function() {
  $("#role-datatable")
    .DataTable()
    .ajax.reload(null, false);
};

setStatus = function(id, status) {
  event.preventDefault();
  let _token = $('meta[name="csrf-token"]').attr("content");

  $.ajax({
    url: "/admin/user/role/set_status",
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
          url: "/admin/user/role/set_delete",
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
  
  // fill metadata
  var frm_data = new FormData($("#role_frm")[0]);

  $.ajax({
    url: "/admin/user/role/save",
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
        },
        permissions: {
          required: true,
        }
      }

      var messages = $_LANG.validate.message;

      frm_validate($("#role_frm"), rules, messages)
      var chk_valid = $("#role_frm").valid();
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
        window.location.href = "/admin/user/role";
      } else {
        noti("error", resp.msg);
      }
    },
  });
};


setCheckAllPermission = function (ele) {
  var check = $(ele).is(":checked");
  $('#show_roles').find(':checkbox').each(function () {
    if (check) {
      $(this).prop("checked", true);
    } else {
      $(this).prop("checked", false);
    }
  });
};

setCheckAllModulePermission = function (ele, module) {
  var check = $(ele).is(":checked");
  $(ele)
    .closest("li")
    .find("input.module_" + module)
    .each(function () {
      if (check) {
        $(this).prop("checked", true);
      } else {
        $(this).prop("checked", false);
      }
    });
};

setCheckAllModuleGroupPermission = function (ele, module, group) {
  var check = $(ele).is(":checked");
  $(ele)
    .closest("li")
    .find("input.module_" + module + "_" + group)
    .each(function () {
      if (check) {
        $(this).prop("checked", true);
      } else {
        $(this).prop("checked", false);
      }
    });
};



