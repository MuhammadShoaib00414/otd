@extends('groups.layout')

@section('body-class', 'col-md-9')

@section('inner-content')
    <div class="d-flex justify-content-between align-items-center">
      <h3 class="font-weight-bold mb-3">@lang('budgets.Budget for'): Q{{ $budget->quarter }} {{ $budget->year }}</h3>
    </div>

    <div class="card">
        <div class="card-body">
          <div class="row">
            <div class="col-md-4 text-center">
              <span style="font-size: 3em;">${{ number_format($budget->total_budget/100, 2) }}</span>
              <h6 class="title-decorative">@lang('budgets.Allocated')</h6>
            </div>
            <div class="col-md-4 text-center">
              <span style="font-size: 3em;">${{ number_format($budget->spent/100, 2) }}</span>
              <h6 class="title-decorative">@lang('budgets.Spent')</h6>
            </div>
            <div class="col-md-4 text-center">
              <span style="font-size: 3em;">${{ number_format($budget->remaining/100, 2) }}</span>
              <h6 class="title-decorative">@lang('budgets.Remaining')</h6>
            </div>
          </div>
        </div>
      </div>

    <div class="card">
        <div class="card-body">
            <div class="d-flex justify-content-between mb-3">
                <p class="mb-0"><b>@lang('budgets.Expenses')</b></p>
                <a href="/groups/{{ $group->slug }}/budgets/{{ $budget->id }}/expenses/create" class="btn btn-sm btn-secondary">Add expense</a>
            </div>
            <table class="table">
                <thead>
                    <tr>
                      <td><b>@lang('budgets.date')</b></td>
                      <td><b>@lang('budgets.category')</b></td>
                      <td><b>@lang('budgets.description')</b></td>
                      <td class="text-right"><b>@lang('budgets.amount')</b></td>
                      <td></td>
                    </tr>
                </thead>
                <tbody>
                  @foreach($budget->expenses as $expense)
                    <tr>
                      <td>{{ $expense->date->format('M j, Y') }}</td>
                      <td>{{ ($expense->category) ? $expense->category->name : __('Uncategorized') }}</td>
                      <td>
                        {{ $expense->description }}
                        @if($expense->receipt_file_path)
                          <br>
                          <a href="/groups/{{ $budget->group->slug }}/budgets/{{ $budget->id }}/expenses/{{ $expense->id }}/download" target="_blank">
                            <i class="icon-text-document-inverted"></i> {{ $expense->receipt_file_name }}
                          </a>
                        @endif
                      </td>
                      <td class="text-right">${{ number_format($expense->amount/100, 2) }}</td>
                      <td><a href="/groups/{{ $group->slug }}/budgets/{{ $budget->id }}/expenses/{{ $expense->id }}/edit"><i class="icon-pencil"></i></a></td>
                    </tr>
                  @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection

@section('scripts')
<script>
  $(function () {
    $('[data-toggle="tooltip"]').tooltip()
  })
</script>
@endsection