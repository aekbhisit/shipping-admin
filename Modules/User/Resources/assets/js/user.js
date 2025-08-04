$(document).ready(function () {
  // init for user
  initDatatable()
  
  // Show/hide password functionality
  $('#show_hide_password a').on('click', function(event) {
    event.preventDefault();
    if($('#show_hide_password input').attr("type") == "text"){
      $('#show_hide_password input').attr('type', 'password');
      $('#show_hide_password i').addClass( "bx-hide" );
      $('#show_hide_password i').removeClass( "bx-show" );
    }else if($('#show_hide_password input').attr("type") == "password"){
      $('#show_hide_password input').attr('type', 'text');
      $('#show_hide_password i').removeClass( "bx-hide" );
      $('#show_hide_password i').addClass( "bx-show" );
    }
  });
})

// Login function called from login form
CheckLogin = function() {
  event.preventDefault();
  
  // Get form data
  let username = $('#username').val();
  let password = $('input[name="password"]').val();
  let _token = $('input[name="_token"]').val();
  
  // Basic validation
  if (!username || !password) {
    noti('error', 'Please enter username and password');
    return false;
  }
  
  // Show loading state
  $('button[type="submit"]').prop('disabled', true).html('<i class="bx bx-loader-alt bx-spin"></i> Signing in...');
  
  $.ajax({
    url: '/admin/check_login',
    type: 'POST',
    data: {
      username: username,
      password: password,
      _token: _token
    },
    success: function(resp) {
      if (resp.success) {
        noti('success', resp.message);
        // Redirect to dashboard
        setTimeout(function() {
          window.location.href = resp.redirect;
        }, 1000);
      } else {
        noti('error', resp.message);
        $('button[type="submit"]').prop('disabled', false).html('<i class="bx bxs-lock-open"></i> Sign in');
      }
    },
    error: function(xhr) {
      let message = 'Login failed';
      if (xhr.responseJSON && xhr.responseJSON.message) {
        message = xhr.responseJSON.message;
      }
      noti('error', message);
      $('button[type="submit"]').prop('disabled', false).html('<i class="bx bxs-lock-open"></i> Sign in');
    }
  });
  
  return false;
}

// =========================  master =========================== //
initDatatable = function () {
  if ($('#user-datatable').length > 0) {
    oTable = $('#user-datatable').DataTable({
      processing: true,
      serverSide: true,
      stateSave: true,
      ajax: {
        url: '/admin/user/datatable_ajax',
      },
      columns: [
        { data: 'id', orderable: false, searchable: false },
        { data: 'name' },
        { data: 'username' },
        { data: 'role', orderable: false, searchable: false },
        { data: 'updated_at' },
        { data: 'action', orderable: false, searchable: false },
      ],
      "language": $_LANG.datatable
    })
  }
}

setReloadDataTable = function () {
  $('#user-datatable').DataTable().ajax.reload(null, false)
}

setStatus = function (id, status) {
  event.preventDefault()
  let _token = $('meta[name="csrf-token"]').attr('content')

  $.ajax({
    url: '/admin/user/set_status',
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
        setReloadDataTable()
      }
    },
  })
}

setDelete = function (id) {
  bootbox.confirm('Are you sure to delete user?', function (result) {
    if (result) {
      event.preventDefault()
      let _token = $('meta[name="csrf-token"]').attr('content')

      $.ajax({
        url: '/admin/user/set_delete',
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
            setReloadDataTable()
          }
        },
      })
    }
  })
}

setSave = function () {
  event.preventDefault()
  var frm_data = new FormData($('#user_frm')[0])
  $.ajax({
    url: '/admin/user/save',
    type: 'POST',
    contentType: false,
    data: frm_data,
    processData: false,
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
    },
    beforeSend: function (xhr) {
      var rules = {
        name: { required: true },
        username: { required: true },
        email: {
          required: true,
          email: true,
        },
        password: {
          minlength: 8,
          required: function () {
            if ($('#user_id').val() == 0) {
              return true
            } else {
              return false
            }
          },
        },
        re_password: {
          minlength: 8,
          equalTo: '#password',
          required: function () {
            if ($('#user_id').val() == 0) {
              return true
            } else {
              return false
            }
          },
        },
      }

      var messages = {
        name: 'Please enter user name',
        username: 'Please enter user username',
        email: {
          required: 'Enter a email',
          email: 'Enter valid email',
        },
        password: {
          required: 'Enter a username',
          minlength: 'Enter at least {0} characters',
        },
        re_password: {
          required: 'Enter a username',
          minlength: 'Enter at least {0} characters',
        },
      }

      frm_validate($('#user_frm'), rules, messages)

      if ($('#user_frm').valid()) {
        return $('#user_frm').valid()
      } else {
        global_loading(0)
        noti('error', 'form data invalid')
        return $('#user_frm').valid()
      }
    },
    success: function (resp) {
      if (resp.success) {
        noti('success', resp.msg)
        window.location.href = '/admin/user'
      } else {
        noti('error', resp.msg)
      }
    },
  })
}

setCheckAllPermission = function (ele, group) {
  var check = $(ele).is(':checked')
  $(ele)
    .closest('li')
    .find('input.' + group)
    .each(function () {
      if (check) {
        $(this).prop('checked', true)
      } else {
        $(this).prop('checked', false)
      }
    })
}
