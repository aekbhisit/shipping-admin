$(document).ready(function () {
  // init for table all
  initDatatable();
  setLinkType();
});

// =========================  content =========================== //
initDatatable = function () {
  if ($('#menu-datatable').length > 0) {
    var re_order_at_row;
    var re_order_next_by_row;
    var column = [];
    column.push({ "data": "sequence", searchable: false });
    if ($_config.image) {
      column.push({ "data": "image", orderable: false, searchable: false });
    }
    column.push({ "data": "name_th" });
    column.push({ "data": "link" });
    column.push({ "data": "location" });
    column.push({ "data": "sort" });
    column.push({ "data": "updated_at" });
    column.push({ "data": "action" });

    oTable = $('#menu-datatable').DataTable({
      "processing": true,
      "serverSide": true,
      "stateSave": true,
      "rowReorder": {
        "dataSrc": "sequence",
      },
      "columnDefs": [
        { "orderable": true, "className": "reorder", "targets": 0 },
        { "orderable": false, "targets": "_all" },
      ],
      "ajax": {
        "url": "/admin/menu/datatable_ajax"
      },
      "columns": column,
      "language": $_LANG.datatable
    });

    oTable.on('pre-row-reorder', function (e, node, index) {
      re_order_at_row = node.node.id;
    });

    oTable.on("row-reorder", function (e, diff, edit) {
      var moving = {};
      $.each(diff, function (key, row) {
        if (re_order_at_row == row.node.id) {
          console.log('move row:' + row.node.id + ' to next:' + $(row.node).prev().attr('id'));
          re_order_next_by_row = $(row.node).prev().attr('id')
        }
      });

      let _token = $('meta[name="csrf-token"]').attr("content");
      $.ajax({
        url: "/admin/menu/set_move_node",
        type: "POST",
        data: {
          node_id: re_order_at_row,
          next_by: re_order_next_by_row,
          _token: _token,
        },
        success: function (resp) {
          if (resp.success) {
            setReloadDataTable();
          } else {
            noti("error", resp.msg);
          }
        },
      });
    });
  }
}

setReloadDataTable = function () {
  $('#menu-datatable').DataTable().ajax.reload(null, false);
}

setUpdateStatus = function (id, status) {
  // event.preventDefault();
  let _token = $('meta[name="csrf-token"]').attr('content');
  $.ajax({
    url: "/admin/menu/set_status",
    type: "POST",
    data: {
      id: id,
      status: status,
      _token: _token
    },
    success: function (resp) {
      if (resp.success) {
        noti('success', resp.msg);
        setReloadDataTable();
      } else {
        noti('error', resp.msg);
        setReloadDataTable();
      }
    },
  });
}

setDelete = function (id) {
  bootbox.confirm({
    message: $_LANG.confirm.delete,
    buttons: {
      confirm: {
        label: $_LANG.confirm.ok,
        className: 'btn-success'
      },
      cancel: {
        label: $_LANG.confirm.cancel,
        className: 'btn-danger'
      }
    },
    callback: function (result) {
      if (result) {
        event.preventDefault();
        let _token = $('meta[name="csrf-token"]').attr('content');
        $.ajax({
          url: "/admin/menu/set_delete",
          type: "POST",
          data: {
            id: id,
            status: status,
            _token: _token
          },
          success: function (resp) {
            if (resp.success) {
              noti('success', resp.msg);
              setReloadDataTable();
            } else {
              noti('error', resp.msg);
              setReloadDataTable();
            }
          },
        });
      }
    }
  })
}

setSave = function () {
  tinyMCE.triggerSave();
  // fill metadata
  mwzGenerateMatadata(0);
  var frm_data = new FormData($('#menu_frm')[0]);
  var type = $('#type').val();
  $.ajax({
    url: "/admin/menu/save",
    type: "POST",
    contentType: false,
    data: frm_data,
    processData: false,
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    },
    beforeSend: function (xhr) {
      // fill metadata


      // validate
      var rules = {
        name_th: {
          required: true,
          maxlength: 500,
        },
        name_en: {
          required: true,
          maxlength: 500,
        }
      }

      if ($_config.desc) {
        rules.desc_th = { required: true, maxlength: 500 };
        rules.desc_en = { required: true, maxlength: 500 };
      }

      if ($_config.detail) {
        rules.detail_th = { required: true };
        rules.detail_en = { required: true };
      }

      var messages = $_LANG.validate.message;

      frm_validate($("#menu_frm"), rules, messages)
      var chk_valid = $("#menu_frm").valid();
      if (chk_valid) {
        return chk_valid;
      } else {
        noti('error', $_LANG.validate.error);
        return chk_valid;
      }
    },
    success: function (resp) {
      if (resp.success) {
        noti('success', resp.msg);
        window.location.href = '/admin/menu/';
      } else {
        noti('error', resp.msg);
      }
    },
  });
}

setUpdateSort = function (id, move) {
  event.preventDefault();

  var _token = $('meta[name="csrf-token"]').attr('content');
  var type = $('#menu-datatable').data('type');

  $.ajax({
    url: "/admin/menu/sort",
    type: "POST",
    data: {
      id: id,
      move: move,
      type: type,
      _token: _token
    },
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    },
    success: function (resp) {
      setReloadDataTable();
    },
  });
}

setLinkType = function(type=''){
  $('.menu-link-type').fadeOut();
  if (type==''){
    type = $('#link_type').val() ;
  }
  console.log(type);
  if(type==1){
    $('#show-slug-select').fadeIn();
  }
  if (type==2) {
    $('#show-url-input').fadeIn();
  }
}


