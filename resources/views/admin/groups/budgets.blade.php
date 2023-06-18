@extends('admin.groups.layout')

@section('inner-page-content')
    <div class="d-flex justify-content-between">
        <h5>Budgets</h5>
        <a class="btn btn-primary btn-sm" href="/admin/budgets/create?group={{ $group->id }}">
          New budget
        </a>
    </div>
    
    <table class="table mt-2">
        <thead>
            <tr>
                <th scope="col"><b>Budget</b></th>
                <th scope="col" class="text-right"><b>Allocated</b></th>
                <th scope="col" class="text-right"><b>Spent</b></th>
                <th scope="col" class="text-right"><b>Remaining</b></th>
                <th scope="col" class="text-right"></th>
            </tr>
        </thead>
        @foreach($group->budgets as $budget)
        <tr>
            <td>{{ $budget->year }} - Q{{ $budget->quarter }}</td>
            <td class="text-right">${{ number_format($budget->total_budget/100, 2) }}</td>
            <td class="text-right">${{ number_format($budget->spent/100, 2) }}</td>
            <td class="text-right">${{ number_format($budget->remaining/100, 2) }}</td>
            <td class="text-right"><a href="/admin/budgets/{{ $budget->id }}">View</a></td>
        </tr>
        @endforeach
    </table>
@endsection