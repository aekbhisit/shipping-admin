JsRedirect = function (url, time) {
  setTimeout(function () {
    window.location.replace(url)
  }, time)
}

CheckLogin = function () {
  event.preventDefault()
  var frm_data = new FormData($('#login_form')[0])
  $.ajax({
    url: '/admin/check_login',
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

        JsRedirect(resp.redirect, 1500)
      } else {
        if (resp.type) {
          // swal({
          //   title: resp.title,
          //   text: resp.msg,
          //   type: resp.type,
          //   confirmButtonText: resp.btn,
          // })
          noti('error', resp.msg)
        } else {
          noti('error', resp.msg)
          if (resp.focus) {
            document.getElementById(resp.focus).focus()
          }
        }
      }
    },
  })
}
