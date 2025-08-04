setConfirmDeposit = function (status) {
  event.preventDefault();
  if (status < 8) {

    bootbox.confirm('ยืนยันใบงาน?', function (result) {
      if (result) {
        var job_id = $('#job_id').val();
        var frm_data = new FormData($('#job_deposit_frm')[0]);
        $.ajax({
          url: "/admin/job/deposit/confirm",
          type: "POST",
          contentType: false,
          data: frm_data,
          processData: false,
          headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
          success: function (resp) {
            if (resp.status) {
              noti("success", "ทำรายการสำเร็จ");
              setTimeout(location.reload(true), 2000);
            } else {
              noti("warning", resp.msg);
            }
          },
        });
      }
    });
  } else {
    bootbox.alert("รายการนี้ยกเลิก หรือ ดำเนินการเสร็จแล้ว");
  }
}

setCancelDeposit = function (status) {
  event.preventDefault();
  if (status < 8) {
    if ($('#cancel_note').val() == '') {

      noti("warning", "ต้องใส่เหตุผล");;

    } else {

      bootbox.confirm('ยกเลิกใบงาน ?', function (result) {
        if (result) {
          var job_id = $('#job_id').val();
          $.ajax({
            url: "/admin/job/deposit/cancel",
            type: "POST",
            data: {
              job_id: $('#job_id').val(),
              cancel_note: $('#cancel_note').val()
            },
            headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function (resp) {
              if (resp.status) {
                noti("success", "ทำรายการสำเร็จ");

                setTimeout(location.reload(true), 2000);
                
              } else {
                noti("warning", resp.msg);
              }
            },
          });
        }
      });

    }
  }
}

setConfirmComplete = function (id) {
 
  bootbox.confirm('ยืนยัน ว่าใบงานนี้ทำเสร็จแล้ว ?', function (result) {
    if (result) {
      var job_id = $('#job_id').val();
      $.ajax({
        url: "/admin/job/set_confirm_complete",
        type: "POST",
        data: {
          job_id: $('#job_id').val()
        },
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function (resp) {
          if (resp.status) {
            noti("success", "ปรับสถานะใบงาน สำเร็จ");
            setTimeout(location.reload(true), 2000);
          } else {
            noti("warning", resp.msg);
          }
        },
      });
    }
  });

       
}