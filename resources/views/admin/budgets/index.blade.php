@extends('admin.layout')

@section('page-content')
    @component('admin.partials.breadcrumbs', ['links' => [
        'Budgets' => '/admin/budgets',
    ]])
    @endcomponent
    
    <h4>Budgets</h4>
    <div class="row">
        <div class="col-md-6">
          <canvas id="chartByGroup" width="100%" height="60"></canvas>
        </div>
        <div class="col-md-6">
          <canvas id="chartByQuarter" width="100%" height="60"></canvas>
        </div>
    </div>
    <hr class="mt-5 mb-4">
    <div class="d-flex justify-content-between">
      <h5>Budgets</h5>
      <div class="text-right">
          <a href="/admin/budgets/export" class="btn btn-outline-dark btn-sm"><i class="fas fa-download"></i> Export</a>
          <a class="btn btn-primary btn-sm" href="/admin/budgets/create">
            Add Budget
          </a>
      </div>
  </div>
<table class="table mt-2">
    <thead>
        <tr>
            <th scope="col"><b>Budget</b></th>
            <th scope="col" class="text-right"><b>Allocated</b></th>
            <th scope="col" class="text-right"><b>Spent</b></th>
            <th scope="col" class="text-right"><b>Remaining</b></th>
            <th></th>
        </tr>
    </thead>
    @foreach($budgets as $budget)
    @if($budget->group)
      <tr>
          <td>
              <b>{{ $budget->group->name }}</b><br>
              {{ $budget->year }} - Q{{ $budget->quarter }}
          </td>
          <td class="text-right" style="vertical-align: middle;">${{ number_format($budget->total_budget/100, 2) }}</td>
          <td class="text-right" style="vertical-align: middle;">${{ number_format($budget->spent/100, 2) }}</td>
          <td class="text-right" style="vertical-align: middle;">${{ number_format($budget->remaining/100, 2) }}</td>
          <td class="text-right" style="vertical-align: middle;">
            <a href="/admin/budgets/{{ $budget->id }}/edit">Edit</a> - 
            <a href="/admin/budgets/{{ $budget->id }}">View</a>
          </td>
      </tr>
    @endif
    @endforeach
</table>
@endsection

@section('scripts')
  <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js" integrity="sha256-Uv9BNBucvCPipKQ2NS9wYpJmi8DTOEfTA/nH2aoJALw=" crossorigin="anonymous"></script>
  <script>
    var ctx = document.getElementById('chartByGroup');
    var myChart = new Chart(ctx, {
      type: 'bar',
      data: {
        labels: JSON.parse('{!! json_encode($spendByGroup->pluck('groupName')) !!}'),
        datasets: [{
          label: "Spent",
          data: JSON.parse('{!! json_encode($spendByGroup->pluck('spent')) !!}'),
          backgroundColor: "rgba(255,137,126,0.8)",
          borderColor: "rgba(48,99,150,1)",
          pointBackgroundColor: "rgba(48,99,150,1)",
          pointHighlightFill: "#fff",
          pointHighlightStroke: "rgba(151,187,205,1)",
        },
        {
          label: "Budget Total",
          data: JSON.parse('{!! json_encode($spendByGroup->pluck('budgetTotal')) !!}'),
          backgroundColor: "rgba(140,197,255,1)",
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
          text: 'By Group - YTD'
        },
      }
    });

    var ctx2 = document.getElementById('chartByQuarter');
    var myChart = new Chart(ctx2, {
      type: 'bar',
      data: {
        labels: ["Q1", "Q2", "Q3", "Q4"],
        datasets: [
        @foreach($budgetBreakdown as $group)
          {
            label: "{{ $group->name }} - Allocated",
            data: JSON.parse('{!! json_encode($group->quarters) !!}'),
            backgroundColor: "{{ ($loop->iteration > 12) ? $colors[$loop->iteration % 12] : $colors[$loop->iteration] }}",
            borderColor: "rgba(48,99,150,1)",
            pointBackgroundColor: "rgba(48,99,150,1)",
            pointHighlightFill: "#fff",
            pointHighlightStroke: "rgba(151,187,205,1)",
            stack: "Allocated"
          },
          {
            label: "{{ $group->name }} - Spent",
            data: JSON.parse('{!! json_encode($group->spentQuarters) !!}'),
            backgroundColor: "{{ ($loop->iteration > 12) ? $colors[$loop->iteration % 12] : $colors[$loop->iteration] }}",
            borderColor: "rgba(48,99,150,1)",
            pointBackgroundColor: "rgba(48,99,150,1)",
            pointHighlightFill: "#fff",
            pointHighlightStroke: "rgba(151,187,205,1)",
            stack: "Spent"
          },
        @endforeach
        ]
      },
      options: {
        legend: {
          display: false,
        },
        title: {
          display: true,
          text: 'By Quarter - {{ date('Y') }}'
        },
        scales: {
            xAxes: [{
                stacked: true
            }],
            yAxes: [{
                stacked: true
            }]
        }
      }
    });
  </script>
@endsection