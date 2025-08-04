$(document).ready(function () {
  // init for default

  new_graph()
  active_graph()
  job_graph()
  winlose_graph()
})

new_graph = function () {
  $.ajax({
    url: '/admin/chart/customers',
    type: 'GET',
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
    },
    beforeSend: function (xhr) {
      $('#news_day').removeAttr('class')
      $('#news_week').removeAttr('class')
      $('#news_month').removeAttr('class')
    },
    success: function (resp) {
      console.log(resp)
      $('#new_spinner').hide()
      if (resp.success) {
        var container_id = '#new_graph'
        var options = set_options()
        options['colors'] = ['#2af598', '#ee0979']
        options['xaxis']['categories'] = resp.data.date
        options['series'] = [
          {
            name: 'Customer',
            data: resp.data.cust,
          },
          {
            name: 'Internal Costs',
            data: resp.data.deposit,
          },
        ]
        var chart = new ApexCharts(
          document.querySelector(container_id),
          options,
        )
        chart.render()

        $('#news_day_new').html(resp.list.day.now)
        $('#news_day_old').html(resp.list.day.old)
        $('#news_week_new').html(resp.list.week.now)
        $('#news_week_old').html(resp.list.week.old)
        $('#news_month_new').html(resp.list.month.now)
        $('#news_month_old').html(resp.list.month.old)
        if (resp.list.day.now >= resp.list.day.old) {
          $('#news_day').attr('class', 'bx bxs-up-arrow align-middle')
        } else {
          $('#news_day').attr('class', 'bx bxs-down-arrow align-middle')
        }
        if (resp.list.week.now >= resp.list.week.old) {
          $('#news_week').attr('class', 'bx bxs-up-arrow align-middle')
        } else {
          $('#news_week').attr('class', 'bx bxs-down-arrow align-middle')
        }
        if (resp.list.month.now >= resp.list.month.old) {
          $('#news_month').attr('class', 'bx bxs-up-arrow align-middle')
        } else {
          $('#news_month').attr('class', 'bx bxs-down-arrow align-middle')
        }
      } else {
        $('#new_graph').html(
          '<h3 class="text-black-50 m-5">' + resp.msg + '</h3>',
        )
      }
    },
  })
}
active_graph = function () {
  $.ajax({
    url: '/admin/chart/play',
    type: 'GET',
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
    },
    beforeSend: function (xhr) {
      $('#active_day').removeAttr('class')
      $('#active_week').removeAttr('class')
      $('#active_month').removeAttr('class')
    },
    success: function (resp) {
      $('#active_spinner').hide()
      console.log(resp)
      if (resp.success) {
        var container_id = '#active_graph'
        var options = set_options()
        options['colors'] = ['#2af598', '#ee0979']
        options['xaxis']['categories'] = resp.data.date
        options['series'] = [
          {
            name: 'Customer',
            data: resp.data.cust,
          },
        ]
        var chart = new ApexCharts(
          document.querySelector(container_id),
          options,
        )
        chart.render()

        $('#active_day_new').html(resp.list.day.now)
        $('#active_day_old').html(resp.list.day.old)
        $('#active_week_new').html(resp.list.week.now)
        $('#active_week_old').html(resp.list.week.old)
        $('#active_month_new').html(resp.list.month.now)
        $('#active_month_old').html(resp.list.month.old)

        if (resp.list.day.now >= resp.list.day.old) {
          $('#active_day').attr('class', 'bx bxs-up-arrow align-middle')
        } else {
          $('#active_day').attr('class', 'bx bxs-down-arrow align-middle')
        }
        if (resp.list.week.now >= resp.list.week.old) {
          $('#active_week').attr('class', 'bx bxs-up-arrow align-middle')
        } else {
          $('#active_week').attr('class', 'bx bxs-down-arrow align-middle')
        }
        if (resp.list.month.now >= resp.list.month.old) {
          $('#active_month').attr('class', 'bx bxs-up-arrow align-middle')
        } else {
          $('#active_month').attr('class', 'bx bxs-down-arrow align-middle')
        }
      } else {
        $('#active_graph').html(
          '<h3 class="text-black-50 m-5">' + resp.msg + '</h3>',
        )
      }
    },
  })
}

job_graph = function () {
  $.ajax({
    url: '/admin/chart/jobs',
    type: 'GET',
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
    },
    beforeSend: function (xhr) {
      $('#job_day').removeAttr('class')
      $('#job_week').removeAttr('class')
      $('#job_month').removeAttr('class')
    },
    success: function (resp) {
      $('#job_spinner').hide()
      console.log(resp)
      if (resp.success) {
        var container_id = '#job_graph'
        var options = set_options()
        options['colors'] = ['#009efd', '#ff6a00', '#000428']
        options['xaxis']['categories'] = resp.data.date
        options['series'] = [
          {
            name: 'Deposit',
            data: resp.data.deposit,
          },
          {
            name: 'Withdraw',
            data: resp.data.withdraw,
          },
          {
            name: 'Promotion',
            data: resp.data.promotion,
          },
        ]
        var chart = new ApexCharts(
          document.querySelector(container_id),
          options,
        )
        chart.render()

        $('#job_day_new').html(resp.list.day.now)
        $('#job_day_old').html(resp.list.day.old)
        $('#job_week_new').html(resp.list.week.now)
        $('#job_week_old').html(resp.list.week.old)
        $('#job_month_new').html(resp.list.month.now)
        $('#job_month_old').html(resp.list.month.old)
        if (resp.list.day.now >= resp.list.day.old) {
          $('#job_day').attr('class', 'bx bxs-up-arrow align-middle')
        } else {
          $('#job_day').attr('class', 'bx bxs-down-arrow align-middle')
        }
        if (resp.list.week.now >= resp.list.week.old) {
          $('#job_week').attr('class', 'bx bxs-up-arrow align-middle')
        } else {
          $('#job_week').attr('class', 'bx bxs-down-arrow align-middle')
        }
        if (resp.list.month.now >= resp.list.month.old) {
          $('#job_month').attr('class', 'bx bxs-up-arrow align-middle')
        } else {
          $('#job_month').attr('class', 'bx bxs-down-arrow align-middle')
        }
      } else {
        $('#job_graph').html(
          '<h3 class="text-black-50 m-5">' + resp.msg + '</h3>',
        )
      }
    },
  })
}

winlose_graph = function () {
  $.ajax({
    url: '/admin/chart/winlose',
    type: 'GET',
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
    },
    beforeSend: function (xhr) {},
    success: function (resp) {
      console.log(resp)
      $('#winlose_spinner').hide()
      if (resp.success) {
        var container_id = '#winlose_graph'
        var options = set_options()
        options['colors'] = ['#ff6a00']
        options['xaxis']['categories'] = resp.data.date
        options['series'] = [
          {
            name: 'Winlose',
            data: resp.data.winlose,
          },
        ]
        var chart = new ApexCharts(
          document.querySelector(container_id),
          options,
        )
        chart.render()
      } else {
        $('#winlose_graph').html(
          '<h3 class="text-black-50 m-5">' + resp.msg + '</h3>',
        )
      }
    },
  })
}

set_options = function () {
  data = {
    chart: {
      height: 325,
      type: 'bar',
      stacked: false,
      foreColor: '#4e4e4e',
      toolbar: {
        show: false,
      },
      dropShadow: {
        enabled: true,
        opacity: 0.1,
        blur: 3,
        left: -7,
        top: 22,
      },
    },
    plotOptions: {
      bar: {
        columnWidth: '50%',
        endingShape: 'rounded',
        dataLabels: {
          position: 'top', // top, center, bottom
        },
      },
    },
    dataLabels: {
      enabled: false,
      formatter: function (val) {
        return parseInt(val)
      },
      offsetY: -20,
      style: {
        fontSize: '14px',
        colors: ['#304758'],
      },
    },
    stroke: {
      show: !0,
      width: 2,
      colors: ['transparent'],
    },
    grid: {
      show: true,
      borderColor: 'rgba(0, 0, 0, 0.10)',
      borderColor: 'rgba(0, 0, 0, 0.10)',
    },
    series: [],
    xaxis: {
      categories: [],
    },
    fill: {
      type: 'gradient',
      gradient: {
        shade: 'dark',
        gradientToColors: ['#009efd', '#ff6a00', '#000428'],
        shadeIntensity: 1,
        type: 'vertical',
        opacityFrom: 1,
        opacityTo: 1,
        stops: [0, 100, 100, 100],
      },
    },
    colors: [],
    tooltip: {
      theme: 'dark',
      y: {
        formatter: function (val) {
          return val
        },
      },
    },
    responsive: [
      {
        breakpoint: 480,
        options: {
          chart: {
            height: 330,
            stacked: true,
          },
          legend: {
            show: !0,
            position: 'top',
            horizontalAlign: 'left',
            offsetX: -20,
            fontSize: '10px',
            markers: {
              radius: 50,
              width: 10,
              height: 10,
            },
          },
          plotOptions: {
            bar: {
              columnWidth: '30%',
            },
          },
        },
      },
    ],
  }
  return data
}
