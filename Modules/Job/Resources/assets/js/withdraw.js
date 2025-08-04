setConfirmTransferWithdraw = function(status) {
  event.preventDefault();
  if (status < 7) {
    if ($('#bank_id').val() == 0) {
    
      noti("warning", 'ต้องเลือกธนาคารก่อน');
      // bootbox.alert("ต้องเลือกธนาคารก่อน");

    } else {
      bootbox.confirm('ยืนยันใบงาน ?', function (result) {
        if (result) {
    
          $.ajax({
            url: "/admin/job/withdraw/tranfer",
            type: "POST",
            data: {
              job_id: $('#job_id').val(),
              bank_id: $('#bank_id').val()
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

setConfirmWithdraw = function (status) {
  event.preventDefault();
  if (status < 8) {
    bootbox.confirm('ยืนยัน ถอนเงิน ?', function (result) {
      if (result) {
        var job_id = $('#job_id').val();
        $.ajax({
          url: "/admin/job/withdraw/confirm",
          type: "POST",
          data: {
            job_id: $('#job_id').val()
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

setCancelWithdraw = function (status, refund = 1) {
  event.preventDefault();
  if (status < 8) {
    if ($('#cancel_note').val() == '') {
        noti("warning", "ต้องใส่เหตุผล");
    } else {

      bootbox.confirm('ยืนยัน ยกเลิกใบงาน ?', function (result) {
        if (result) {
          var job_id = $('#job_id').val();
          $.ajax({
            url: "/admin/job/withdraw/cancel",
            type: "POST",
            data: {
              job_id: $('#job_id').val(),
              refund: refund,
              cancel_note: $('#cancel_note').val()
            },
            headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function (resp) {
              if (resp.status) {
                noti("success", "ยกเลิกใบงาน สำเร็จ");
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
              noti("success", "ทำรายการสำเร็จ");
              setTimeout(location.reload(true), 2000);
            } else {
              noti("warning", "ต้องใส่ข้อมูล");
            }
          },
        });
      }
    });
}