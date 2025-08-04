$(document).ready(function () {
  // init for news
  initDatatable()
})

// =========================  news =========================== //

initDatatable = function () {
  if ($('#tag-datatable').length > 0) {
    oTable = $('#tag-datatable').DataTable({
      processing: true,
      serverSide: true,
      stateSave: true,
      ajax: {
        url: '/admin/setting/tag/datatable_ajax',
      },
      columns: [
        { data: 'id', orderable: true, searchable: false },
        { data: 'type' },
        // { data: 'head' },
        { data: 'action', orderable: false, searchable: false },
      ],
    })
  }
}

ReloadDataTable = function () {
  $('#slug-datatable').DataTable().ajax.reload(null, false)
}

setSave = function () {
  event.preventDefault()
  var frm_data = new FormData($('#tag_frm')[0])

  $.ajax({
    url: '/admin/setting/tag/save',
    type: 'POST',
    contentType: false,
    data: frm_data,
    processData: false,
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
    },
    success: function (resp) {
      if (resp.success) {
        noti('success', resp.msg)
        window.location.href = '/admin/setting/tag/index'
      } else {
        noti('error', resp.msg)
      }
    },
  })
}

setDelete = function (id) {
  bootbox.confirm({
    message: 'ยืนยันลบ ?',
    buttons: {
      confirm: {
        label: 'ตกลง',
        className: 'btn-success',
      },
      cancel: {
        label: 'ยกเลิก',
        className: 'btn-danger',
      },
    },
    callback: function (result) {
      if (result) {
        event.preventDefault()
        let _token = $('meta[name="csrf-token"]').attr('content')

        $.ajax({
          url: '/admin/setting/tag/set_delete',
          type: 'POST',
          data: {
            id: id,
            _token: _token,
          },
          success: function (resp) {
            if (resp.success) {
              noti('success', resp.msg)
              ReloadDataTable()
            } else {
              noti('error', resp.msg)
            }
          },
        })
      }
    },
  })
}
setStatus = function (id, status) {
  event.preventDefault()
  let _token = $('meta[name="csrf-token"]').attr('content')

  $.ajax({
    url: '/admin/setting/tag/set_status',
    type: 'POST',
    data: {
      id: id,
      status: status,
      _token: _token,
    },
    success: function (resp) {
      if (resp.success) {
        noti('success', resp.msg)
        ReloadDataTable()
      } else {
        noti('error', resp.msg)
      }
    },
  })
}
