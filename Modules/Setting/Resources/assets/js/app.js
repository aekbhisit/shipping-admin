setSave = function () {
  event.preventDefault()
  var frm_data = new FormData($('#setting_frm')[0])
  $.ajax({
    url: '/admin/setting/web/save',
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
        window.location.reload()
      } else {
        noti('error', resp.msg)
      }
    },
  })
}
