$(document).ready(function () {
    // init for job
    initDatatable();
  })
  
  // =========================  job index =========================== //
  initDatatable = function () {
    if ($('#manual-credit-datatable').length > 0) {
      oTable = $('#manual-credit-datatable').DataTable({
        processing: true,
        serverSide: true,
        stateSave: true,
        ajax: {
          url: '/admin/job/manual-credit/datatable_ajax',
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
          { data: 'job_id' },
          { data: 'job_type' },
          { data: 'cust_id' },
          { data: 'cust_user_name' },
          { data: 'amount' },
          { data: 'reason' },
          { data: 'ref_code' },
          { data: 'status' },
          { data: 'created_by' },
          { data: 'updated_at' },
          { data: 'action', orderable: false, searchable: false },
        ],
        order: [[0, 'desc']],
      })
    }
  }
  
  setReloadDataTable = function () {
    $('#manual-credit-datatable').DataTable().ajax.reload(null, false)
  }

  
  setSave = function () {
    event.preventDefault()
    tinyMCE.triggerSave()
    var frm_data = new FormData($('#manual_credit_frm')[0])
    $.ajax({
      url: '/admin/job/manual-credit/save',
      type: 'POST',
      contentType: false,
      data: frm_data,
      processData: false,
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
      },
      beforeSend: function (xhr) {
        global_loading(1)
        var rules = {
          username: { required: true },
          amount: { required: true },
          reason: { required: true },
        }
  
        var messages = {
          username: 'ต้องใส่ยูเซอร์',
          amount: 'ต้องใส่ยอดเงิน',
          reason: 'ต้องใส่เหตุผล',
        }
  
        frm_validate($('#manual_credit_frm'), rules, messages)
  
        if ($('#manual_credit_frm').valid()) {
          return $('#manual_credit_frm').valid()
        } else {
          global_loading(0)
          noti('error', 'ต้องใส่ข้อมูลให้ครบ')
          return $('#manual_credit_frm').valid()
        }
      },
      success: function (resp) {
        global_loading(0)
        if (resp.success) {
          noti('success', resp.msg)
          window.location.href = '/admin/job/manual-credit'
        } else {
          noti('error', resp.msg)
        }
      },
    })
  }

