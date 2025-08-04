$(document).ready(function () {
  // init for default
  initDatatable()
})

// =========================  default ======================================= //
initDatatable = function () {
  if ($('#data-datatable').length > 0) {
    oTable = $('#data-datatable').DataTable({
      processing: true,
      serverSide: true,
      stateSave: true,
      ajax: {
        url: '/admin/statement/sms/datatable_ajax',
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
        { data: 'id', orderable: true, searchable: false },
        { data: 'statement_id', orderable: true },
        { data: 'job_id', orderable: true },
        { data: 'phone', orderable: true },
        { data: 'port', orderable: true },
        { data: 'message', orderable: true },
        { data: 'bank_no', orderable: true },
        { data: 'amount', orderable: true },
        { data: 'balance', orderable: true },
        { data: 'date_time', orderable: true },
        { data: 'bank_id', orderable: true },
        { data: 'acc_id', orderable: true },
        { data: 'bank_account', orderable: true },
        { data: 'bank_number', orderable: true },
        { data: 'status', orderable: true },
        { data: 'created_at', orderable: true },
        { data: 'action', orderable: false, searchable: false },
      ],
      language: $_LANG.datatable,
      order: [[0, 'desc']],
    })
  }
}

setReloadDataTable = function () {
  $('#data-datatable').DataTable().ajax.reload(null, false)
}

setStatus = function (id, status) {
  event.preventDefault()
  let _token = $('meta[name="csrf-token"]').attr('content')

  $.ajax({
    url: '/admin/statement/sms/set_status',
    type: 'POST',
    data: {
      id: id,
      status: status,
      _token: _token,
    },
    success: function (resp) {
      if (resp.success) {
        noti('success', resp.msg)
        setReloadDataTable()
      } else {
        noti('error', resp.msg)
      }
    },
  })
}

setDelete = function (id) {
  bootbox.confirm({
    message: $_LANG.confirm.delete,
    buttons: {
      confirm: {
        label: $_LANG.confirm.ok,
        className: 'btn-success',
      },
      cancel: {
        label: $_LANG.confirm.cancel,
        className: 'btn-danger',
      },
    },
    callback: function (result) {
      if (result) {
        event.preventDefault()
        let _token = $('meta[name="csrf-token"]').attr('content')

        $.ajax({
          url: '/admin/statement/sms/set_delete',
          type: 'POST',
          data: {
            id: id,
            _token: _token,
          },
          success: function (resp) {
            if (resp.success) {
              noti('success', resp.msg)
              setReloadDataTable()
            } else {
              noti('error', resp.msg)
            }
          },
        })
      }
    },
  })
}
