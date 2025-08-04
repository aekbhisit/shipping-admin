$(document).ready(function() {
  // init for default
  initDatatable();
});

// =========================  default ======================================= //
initDatatable = function() {
  if ($("#default-datatable").length > 0) {
    oTable = $("#default-datatable").DataTable({
      processing: true,
      serverSide: true,
      stateSave: true,
      rowReorder: {
        dataSrc: "sequence",
      },
      columnDefs: [
        { orderable: true, className: "reorder", targets: 0 },
        { orderable: false, targets: "_all" },
      ],
      ajax: {
        url: "/admin/default/datatable_ajax",
        data: function (d) {
          if ($('.datatable_filter').length > 0) {
            $(".datatable_filter").serializeArray().map(function (x) {
              if (x.name != '_token') {
                d[x.name] = x.value;
              }
            });
            console.log(d);
          }
        }
      },
      columns: [
        { data: "sequence", orderable: true, searchable: false },
        { data: "image", orderable: false, searchable: false },
        { data: "name_th", orderable: true },
        { data: "updated_at", orderable: true },
        { data: "action", orderable: false, searchable: false },
      ],
      language: {
        lengthMenu: "แสดง _MENU_ รายการ",
        zeroRecords: "ขออภัย - ไม่พบรายการ",
        decimal: "",
        emptyTable: "ไม่พบข้อมูล",
        info: "แสดง หน้า _PAGE_ จาก _PAGES_",
        infoEmpty: "ไม่พบรายการ",
        infoFiltered: "(กรองข้อมูลจาก _MAX_ รายการ)",
        infoPostFix: "",
        thousands: ",",
        loadingRecords: "กำลังโหลด...",
        processing: "กำลังโหลด...",
        search: "ค้นหา:",
        zeroRecords: "ไม่พบรายการที่ค้นหา",
        paginate: {
          first: "หน้าแรก",
          last: "หน้าสุดท้าย",
          next: "ถัดไป",
          previous: "ก่อนหน้า",
        },
        aria: {
          sortAscending: ": เรียงข้อมูลจากน้อยไปมาก",
          sortDescending: ": เรียงข้อมูลจากมากไปน้อย",
        },
      },
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
        url: "/admin/default/set_re_order",
        type: "POST",
        data: {
          sort_json: sort_json,
          _token: _token,
        },
        success: function(resp) {
          if (resp.success) {
            setReloadDataTable();
          } else {
            // noti("error", resp.msg);
          }
        },
      });
    });
  }
};

setReloadDataTable = function() {
  $("#default-datatable")
    .DataTable()
    .ajax.reload(null, false);
};

setUpdateStatus = function(id, status) {
  event.preventDefault();
  let _token = $('meta[name="csrf-token"]').attr("content");

  $.ajax({
    url: "/admin/default/set_status",
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
    message: "ยืนยันลบ แบนเนอร์ ?",
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
          url: "/admin/default/set_delete",
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
      }
    },
  });
};


setSave = function() {
  event.preventDefault();
  tinyMCE.triggerSave();

  var frm_data = new FormData($("#default_frm")[0]);

  $.ajax({
    url: "/admin/default/save",
    type: "POST",
    contentType: false,
    data: frm_data,
    processData: false,
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    },
    beforeSend: function (xhr) {
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

      if ($_config.text_1) {
        rules.text_1_th = { required: true, maxlength: 700 };
        rules.text_1_en = { required: true, maxlength: 700 };
      }

      if ($_config.text_2) {
        rules.text_2_th = { required: true, maxlength: 700 };
        rules.text_2_en = { required: true, maxlength: 700 };
      }

      if ($_config.desc_1) {
        rules.desc_1_th = { required: true };
        rules.desc_1_th = { required: true };
      }

      if ($_config.desc_2) {
        rules.desc_2_th = { required: true };
        rules.desc_2_th = { required: true };
      }

      if ($_config.link) {
        rules.link = { required: true };
      }

      if ($_config.category) {
        rules.category_id = { required: true };
      }

      var messages = $_LANG.validate.message;

      frm_validate($("#default_frm"), rules, messages)
      var chk_valid = $("#default_frm").valid();
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
        window.location.href = "/admin/default";
      } else {
        noti("error", resp.msg);
      }
    },
  });
};

