$(document).ready(function () {
  console.log('setBindPusher');
  // if($('#job_noti_cnt').attr('data-cnt')==0){
  //   $('#job_noti_cnt').hide();
  // }else{
  //   $('#job_noti_cnt').show();
  // }
  setBindPusher();
});
  
setBindPusher = function(){

    Pusher.logToConsole = true;

    var pusher = new Pusher('379fb6d599735678cb89', {
      cluster: 'ap1'
      });

    var channel = pusher.subscribe('maxmunbet');
  channel.bind(PUSHER_JOB_NEW, function(data) {
      console.log(data);
      console.log(JSON.parse(data));
      if (typeof show_new_job_row === "function"){
        console.log('show_new_job_row function');
        show_new_job_row(JSON.parse(data));
        // setAddJobRow(data);
      }

      if (typeof show_new_job_noti === "function") {
        console.log('show_new_job_noti function');
        show_new_job_noti(JSON.parse(data));
      }
      playAudio();
    });

  channel.bind(PUSHER_JOB_LOCKED, function(data) {
      console.log(data);
      console.log(JSON.parse(data));
      if (typeof hide_new_job_row === "function") {
        hide_new_job_row(JSON.parse(data));
      }

      if (typeof hide_new_job_noti === "function") {
        hide_new_job_noti(JSON.parse(data));
      }
    });
    // play  mp3
    // var video = document.getElementById("musicplayer");
    function playAudio() {
      console.log('play new_job_noti_mp3 ');
      document.getElementById("new_job_noti_mp3").play();
    }

  }

show_new_job_row = function (data){
  console.log('show_new_job_row');
  var attr_status =  $('#job-datatable').attr('attr-status');
  if ($('#job-datatable').length > 0 && attr_status =='waiting') {
    console.log('#job-datatable');
    setAddJobRow(data);
  }
}

hide_new_job_row = function (data){
  var attr_status = $('#job-datatable').attr('attr-status');
  if ($('#job-datatable').length > 0 && attr_status == 'waiting') {
    removeJobRow(data);
  }
}

show_new_job_noti = function (data) {
  console.log('show_new_job_noti');
  if ($('#new_job_noti_containter').length){
    console.log('new_job_noti_containter');
    setAddJobNoti(data);
  }
}

hide_new_job_noti = function (data){
  if ($('#new_job_noti_containter').length > 0) {
    removeJobNoti(data);
  }
}


setAddJobNoti = function (data) {
  console.log('setAddJobNoti');
  // console.log('setAddJobNoti');
  if ($('#new_job_noti_containter').length) {
    var noti = '';
    if (data.type == 1) {
      noti = '<a id="job_noti_id_' + data.id + '" class="dropdown-item" href="/admin/job/deposit/' + data.id + '">';
      noti += '<div class="d-flex align-items-center">';
      noti += '<div class="notify bg-light-success text-success">';
      noti += '<div class="font-22 text-success"><i class=" bx bx-bell-plus"></i></div>';
      noti += '</div>';
      noti += '<div class="flex-grow-1">';
      noti += '<h6 class="msg-name">ใบงานฝาก ' + data.amount + ' บาท<span class="msg-time float-end">' + show_time_ago(data.created_at) + '</span></h6>';
      noti += '<p class="msg-info">ยูเซอร์: ' + data.username + ' ใบงาน #' + data.id + '</p>';
      noti += '</div>';
      noti += '</div>';
      noti += '</a>';

    } else {

      noti = '<a id="job_noti_id_' + data.id + '" class="dropdown-item" href="/admin/job/withdraw/' + data.id + '">';
      noti += '<div class="d-flex align-items-center">';
      noti += '<div class="notify bg-light-danger text-danger">';
      noti += '<div class="font-22 text-danger"><i class=" bx bx-bell-minus"></i></div>';
      noti += '</div>';
      noti += '<div class="flex-grow-1">';
      noti += '<h6 class="msg-name">ใบงานถอน ' + data.amount + ' บาท<span class="msg-time float-end">' + data.created_at + '</span></h6>';
      noti += '<p class="msg-info">ยูเซอร์: ' + data.username + ', ใบงาน #' + data.id + '</p>';
      noti += '</div>';
      noti += '</div>';
      noti += '</a>';

    }
    $('#new_job_noti_containter').prepend(noti);
    show_noti_add_cnt();
    // noti('warning', 'ใบงานมาใหม่ #' + data.id );
  }
}

removeJobNoti = function (data) {
  $('#job_noti_id_' + data.job_id).hide();
  show_noti_remove_cnt(data);
}

show_noti_add_cnt = function () {
  $('#job_noti_cnt').attr('data-cnt', parseInt($('#job_noti_cnt').attr('data-cnt')) + 1);
  $('#job_noti_cnt').html($('#job_noti_cnt').attr('data-cnt'));

  if ($('.admin_menu_job').length > 0) {
    $('.admin_menu_job').find('.badge').html($('#job_noti_cnt').attr('data-cnt'));
  }

  if ($('.admin_menu_job_new').length > 0) {
    $('.admin_menu_job_new').find('.badge').html($('#job_noti_cnt').attr('data-cnt'));
  }

  $('#job_noti_cnt').show();
}

show_noti_remove_cnt = function (data) {
  $('#job_noti_id_' + data.job_id).hide();
  $('#job_noti_cnt').attr('data-cnt', parseInt($('#job_noti_cnt').attr('data-cnt')) - 1);

  if (parseInt($('#job_noti_cnt').attr('data-cnt')) <= 0 ){
    $('#job_noti_cnt').attr('data-cnt', 0 );
  }

  $('#job_noti_cnt').html($('#job_noti_cnt').attr('data-cnt'));

  if ($('.admin_menu_job').length>0){
    $('.admin_menu_job').find('.badge').html($('#job_noti_cnt').attr('data-cnt'));
  }

  if ($('.admin_menu_job_new').length > 0) {
    $('.admin_menu_job_new').find('.badge').html($('#job_noti_cnt').attr('data-cnt'));
  }

  if ($('#job_noti_cnt').attr('data-cnt') == 0) {
    $('#job_noti_cnt').hide();
  } else {
    $('#job_noti_cnt').show();
  }
}

