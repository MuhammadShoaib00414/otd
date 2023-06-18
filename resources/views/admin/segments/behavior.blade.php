@extends('admin.segments.layout')

@section('head')
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
@endsection

@section('inner-page-content')
    <div class="d-flex justify-content-between">
        <select class="custom-select" id="typeSelectBox" style="width: 300px;">
            <option value="activity">Activity</option>
            <option value="messages">Messages</option>
            <option value="introductions">Introductions</option>
            <option value="shoutouts">Shoutouts</option>
            <option value="rsvps">RSVPs</option>
            <option value="badges">Badges</option>
            <option value="newusers">New Users</option>
            <option value="monthlynewusers">Monthly New Users</option>
            <option value="totalusers">Total Users</option>
        </select>
        <input type="text" name="daterange" value="{{ ($segment->start_date) ? $segment->start_date->format('m/d/Y') : Carbon\Carbon::now()->subDays(30)->format('m/d/Y') }} - {{ ($segment->start_date) ? $segment->end_date->format('m/d/Y') : Carbon\Carbon::now()->format('m/d/Y') }}" class="form-control" style="width: 300px;" />
    </div>

    <div>
        <canvas id="chart" width="100%" height="42"></canvas>
    </div>
@endsection

@section('scripts')
  @parent
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js" integrity="sha256-Uv9BNBucvCPipKQ2NS9wYpJmi8DTOEfTA/nH2aoJALw=" crossorigin="anonymous"></script>
<script>
    var ctx = document.getElementById('chart');
    var chart = new Chart(ctx, {
      type: 'line',
      data: {
        labels: [],
        datasets: [{
          label: "",
          data: [],
          backgroundColor: "rgba(149,179,209,0.2)",
          borderColor: "rgba(48,99,150,1)",
          pointBackgroundColor: "rgba(48,99,150,1)",
          pointHighlightFill: "#fff",
          pointHighlightStroke: "rgba(151,187,205,1)",
        }],
      },
      options: {
        legend: {
          display: false,
        },
        title: {
          display: true,
          text: 'Activity'
        }
      }
    });

    var startDate = '{{ ($segment->start_date) ? $segment->start_date->format('m/d/Y') : Carbon\Carbon::now()->subDays(30)->format('m/d/Y') }}';
    var endDate = '{{ ($segment->start_date) ? $segment->end_date->format('m/d/Y') : Carbon\Carbon::now()->format('m/d/Y') }}';

    $(function() {
      $('input[name="daterange"]').daterangepicker({
        opens: 'left',
        ranges: {
           'Today': [moment(), moment()],
           'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
           'Last 7 Days': [moment().subtract(6, 'days'), moment()],
           'Last 30 Days': [moment().subtract(29, 'days'), moment()],
           'This Month': [moment().startOf('month'), moment().endOf('month')],
           'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        }
      }, function(start, end, label) {
        startDate = start.format('YYYY-MM-DD');
        endDate = end.format('YYYY-MM-DD');
        loadGraph();
      });

      $('#typeSelectBox').on('change', function () {
        loadGraph();
      });

      loadGraph();

    });

    function loadGraph() {
      $.get({
        url: '/admin/api/behavior-analytics',
        data: {
          segment: {{ $segment->id }},
          type: $('#typeSelectBox').val(),
          start_date: startDate,
          end_date: endDate,
        },
        success: function(response) {
          chart.data.labels = response.dates;
          chart.data.datasets[0].data = response.count;
          chart.update();
        },
      });
    }

    </script>
@endsection