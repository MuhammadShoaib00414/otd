@extends('groups.layout')

@section('body-class', 'col-md-9')

@section('inner-content')
    <div class="d-flex justify-content-between align-items-center">
      <h3 class="font-weight-bold mb-2">@lang('budgets.Budgets')</h3>
    </div>

    <div class="card" style="min-height: 60vh;">
        <div class="card-body">
            <table class="table mt-2">
                <thead>
                    <tr>
                        <th scope="col"><b>@lang('budgets.Budget')</b></th>
                        <th scope="col" class="text-right"><b>@lang('budgets.Allocated')</b></th>
                        <th scope="col" class="text-right"><b>@lang('budgets.Spent')</b></th>
                        <th scope="col" class="text-right"><b>@lang('budgets.Remaining')</b></th>
                        <th scope="col" class="text-right"></th>
                    </tr>
                </thead>
                @foreach($group->budgets()->orderBy('year', 'desc')->orderBy('quarter', 'desc')->get() as $budget)
                <tr>
                    <td>{{ $budget->year }} - Q{{ $budget->quarter }}</td>
                    <td class="text-right">${{ number_format($budget->total_budget/100, 2) }}</td>
                    <td class="text-right">${{ number_format($budget->spent/100, 2) }}</td>
                    <td class="text-right">${{ number_format($budget->remaining/100, 2) }}</td>
                    <td class="text-right"><a href="/groups/{{ $group->slug }}/budgets/{{ $budget->id }}">@lang('budgets.Open')</a></td>
                </tr>
                @endforeach
            </table>
        </div>
    </div>
@endsection