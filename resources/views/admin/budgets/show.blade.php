@extends('admin.layout')

@section('page-content')
    @component('admin.partials.breadcrumbs', ['links' => [
        'Budgets' => '/admin/budgets',
        $budget->group->name . ': ' . $budget->year . ' Q' . $budget->quarter => ''
    ]])
    @endcomponent

  <div class="d-flex justify-content-between align-items-center">
    <div>
      <h5>{{ $budget->year }} - Q{{ $budget->quarter }}</h5>
      <p class="mb-0"><a href="/admin/groups/{{ $budget->group->id }}">{{ $budget->group->name }}</a></p>
    </div>
    <a href="/admin/budgets/{{ $budget->id }}/edit" class="btn btn-primary btn-sm">Edit</a>
  </div>
  <hr>
  <div class="card mb-4">
    <div class="card-body">
      <div class="row">
        <div class="col-md-4 text-center">
          <span style="font-size: 3em;">${{ number_format($budget->total_budget/100, 2) }}</span>
          <h6 class="title-decorative">Allocated</h6>
        </div>
        <div class="col-md-4 text-center">
          <span style="font-size: 3em;">${{ number_format($budget->spent/100, 2) }}</span>
          <h6 class="title-decorative">Spent</h6>
        </div>
        <div class="col-md-4 text-center">
          <span style="font-size: 3em;">${{ number_format($budget->remaining/100, 2) }}</span>
          <h6 class="title-decorative">Remaining</h6>
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-md-6">
        <canvas id="chartOverTime" width="100%"></canvas>
    </div>
    <div class="col-md-6">
        <canvas id="chartByCategory" width="100%"></canvas>
    </div>
  </div>

  <hr class="mt-5">

  <div class="d-flex justify-content-between mb-3">
    <p class="mb-0"><b>Budget Details</b></p>
    <div class="text-right">
      <a href="/admin/budgets/{{ $budget->id }}/expenses/export" class="btn btn-outline-dark btn-sm"><i class="fas fa-download"></i> Export</a>
      <a dusk="add_expense" href="/admin/budgets/{{ $budget->id }}/expenses/create" class="btn btn-sm btn-primary"><i class="fas fa-plus mr-1"></i> Add expense</a>
    </div>
  </div>
  
  <table class="table">
    <thead>
        <tr>
          <td><b>date</b></td>
          <td><b>description</b></td>
          <td><b>category</b></td>
          <td><b>added by</b></td>
          <td class="text-right"><b>amount</b></td>
          <td></td>
        </tr>
    </thead>
    <tbody>
      @foreach($budget->expenses as $expense)
        <tr>
          <td>{{ $expense->date->format('F j, Y') }}</td>
          <td>
            {{ $expense->description }}
            @if($expense->receipt_file_path)
              <br>
              <a href="{{ $expense->receipt_path }}" target="_blank">
                <i class="fas fa-paperclip"></i> {{ $expense->receipt_file_name }}
              </a>
            @endif
          </td>
          <td>{{ ($expense->category) ? $expense->category->name : 'Uncategorized' }}</td>
          <td><a href="/admin/users/{ $expense->user->id }}">{{ $expense->user->name }}</a></td>
          <td class="text-right">${{ number_format($expense->amount/100, 2) }}</td>
          <td><a href="/admin/budgets/{{ $budget->id }}/expenses/{{ $expense->id }}/edit" class="text-muted"><i class="fa fa-pen"></i></a>
        </tr>
      @endforeach
    </tbody>
  </table>
@endsection

@section('scripts')
  <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js" integrity="sha256-Uv9BNBucvCPipKQ2NS9wYpJmi8DTOEfTA/nH2aoJALw=" crossorigin="anonymous"></script>
  <script>
    var ctx = document.getElementById('chartByCategory');
    var myChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
          datasets: [{
              data: JSON.parse('{!! json_encode($spendByGroup->pluck('total')) !!}'),
              backgroundColor: ["rgb(255, 99, 132)","rgb(54, 162, 235)","rgb(255, 205, 86)"]
          }],
          labels: JSON.parse('{!! json_encode($spendByGroup->pluck('name')) !!}'),
        },
        options: {
          legend: {
            display: true,
          },
          title: {
            display: true,
            text: 'Spend by Category'
          },
          tooltips: {
            callbacks: {
              label: function(tooltipItem, data) {
                value = parseInt(data.datasets[0].data[tooltipItem.index]).toLocaleString();
                var label = data.labels[tooltipItem.index];
                return label + ': $' + value;
              }
            }
          }
        }
    });

    var ctx = document.getElementById('chartOverTime');
    var myChart = new Chart(ctx, {
      type: 'line',
      data: {
        labels: JSON.parse('{!! json_encode($spendOverTime->pluck('week')) !!}'),
        datasets: [{
          label: "",
          data: JSON.parse('{!! json_encode($spendOverTime->pluck('amount')) !!}'),
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
          text: 'Budget Spent Over Time'
        },
        scales: {
          xAxes: [{
            ticks: {
                maxTicksLimit: 4
            }
          }]
        },
        tooltips: {
            callbacks: {
              label: function(tooltipItem, data) {
                value = parseInt(data.datasets[0].data[tooltipItem.index]).toLocaleString();
                var label = data.labels[tooltipItem.index];
                return '$' + value;
              }
            }
          }
      }
  });
  </script>
@endsection