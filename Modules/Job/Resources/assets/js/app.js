$(document).ready(function () {
    // init for job
    initDatatable();
  })
  
  // =========================  job index =========================== //
  initDatatable = function () {
    if ($('#job-datatable').length > 0) {
      oTable = $('#job-datatable').DataTable({
        processing: true,
        serverSide: true,
        stateSave: true,
        ajax: {
          url: '/admin/job/datatable_ajax',
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
          { data: 'id' },
          { data: 'type' },
          { data: 'code' },
          { data: 'cust_id' },
          { data: 'cust_user_id' },
          { data: 'total_amount' },
          { data: 'bank' },
          { data: 'status' },
          { data: 'created_by' },
          { data: 'locked_by' },
          { data: 'created_at' },
          { data: 'action', orderable: false, searchable: false },
        ],
        order: [[0, 'desc']],
      })
    }
  }
  
  setReloadDataTable = function () {
    $('#job-datatable').DataTable().ajax.reload(null, false)
  }
  
  setStatus = function (id, status) {
    event.preventDefault()
    let _token = $('meta[name="csrf-token"]').attr('content')
  
    $.ajax({
      url: '/admin/job/set_status',
      type: 'POST',
      data: {
        id: id,
        status: status,
        _token: _token,
      },
      success: function (resp) {
        if (resp.success) {
          core_noti('success', resp.msg)
          setReloadDataTable()
        } else {
          core_noti('error', resp.msg)
        }
      },
    })
  }
  
  setDelete = function (id) {
    bootbox.confirm('ยืนยันลบ ลูกค้า?', function (result) {
      if (result) {
        event.preventDefault()
        let _token = $('meta[name="csrf-token"]').attr('content')
  
        $.ajax({
          url: '/admin/job/set_delete',
          type: 'POST',
          data: {
            id: id,
            status: status,
            _token: _token,
          },
          success: function (resp) {
            if (resp.success) {
              core_noti('success', resp.msg)
              setReloadDataTable()
            } else {
              core_noti('error', resp.msg)
            }
          },
        })
      }
    })
  }
  
  setSave = function () {
    event.preventDefault()
    tinyMCE.triggerSave()
    var frm_data = new FormData($('#job_frm')[0])
    $.ajax({
      url: '/admin/job/save',
      type: 'POST',
      contentType: false,
      data: frm_data,
      processData: false,
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
      },
      beforeSend: function (xhr) {
        core_global_loading(1)
        var rules = {
          name: { required: true },
          domain: { required: true },
          prefix: { required: true },
        }
  
        var messages = {
          name: 'ต้องใส่ชื่อ',
          domain: 'ต้องใส่โดเมน',
          prefix: 'ต้องใส่ prefix',
        }
  
        core_frm_validate($('#job_frm'), rules, messages)
  
        if ($('#job_frm').valid()) {
          return $('#job_frm').valid()
        } else {
          core_global_loading(0)
          core_noti('error', 'form data invalid')
          return $('#job_frm').valid()
        }
      },
      success: function (resp) {
        core_global_loading(0)
        if (resp.success) {
          core_noti('success', resp.msg)
          window.location.href = '/admin/job'
        } else {
          core_noti('error', resp.msg)
        }
      },
    })
  }

  setAddJobRow = function(data=''){
    console.log('setAddJobRow');
    console.log(data);
    var result = $('#job-datatable').DataTable().row.add({
      "id": data.id,
      "type": data.type_show,
      "code": data.code,
      "cust_id": data.customer,
      "cust_user_id": data.username,
      "total_amount": data.amount,
      "bank": data.bank,
      "status": data.status,
      "created_by": data.created_by,
      "locked_by": data.locked_by,
      "created_at": show_time_ago(data.created_at),
      "action": data.action
    }).node();
    $(result).attr('id', data.id) ;
    $(result).addClass(data.class);
    $('#job-datatable tbody').prepend(result);
    // var result = $('#job-datatable').DataTable().row.add(row) ; //.draw(false);

  }

  removeJobRow = function (data) {
    var job_id = data.job_id ;
    $('#job-datatable tbody').find('#'+job_id).hide();
  }

