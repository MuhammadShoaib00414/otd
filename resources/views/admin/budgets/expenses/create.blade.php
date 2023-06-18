@extends('admin.layout')

@section('page-content')
    @component('admin.partials.breadcrumbs', ['links' => [
        'Budgets' => '/admin/budgets',
        $budget->group->name . ': ' . $budget->year . ' Q' . $budget->quarter => '/admin/budgets/'.$budget->id,
        'Create Expense' => '',
    ]])
    @endcomponent

  <h5>{{ $budget->year }} - Q{{ $budget->quarter }}</h5>
  <p><a href="/admin/groups/{{ $budget->group->id }}">{{ $budget->group->name }}</a></p>
  <hr>
  <div class="row">
    <div class="col-md-8">
        <div class="card mb-4">
          <div class="card-body">
            <h5 class="mb-4">Add Expense</h5>
            <form method="post" action="/admin/budgets/{{ $budget->id }}/expenses">
              @csrf
              <div class="form-row">
                <div class="col-6">
                  <label>Date</label>
                  <input type="text" name="date" class="form-control" required value="{{ old('date') ?: \Carbon\Carbon::parse($placeholderDate)->format('m/d/y') }}" placeholder="mm/dd/yy" id="date">
                </div>
                <div class="col-6">
                  <label>Amount</label>
                  <div class="input-group mb-3">
                    <div class="input-group-prepend">
                      <span class="input-group-text" id="basic-addon1">$</span>
                    </div>
                    <input type="text" name="amount" id="amount" class="form-control" required>
                  </div>
                </div>
              </div>
              <div class="form-group">
                <label>Category</label>
                <select name="category_id" class="form-control">
                  <option selected value="null">No category</option>
                  @foreach($categories as $category)
                  <option value="{{ $category->id }}">{{ $category->name }}</option>
                  @endforeach
                </select>
              </div>
              <div class="form-group">
                <label>Description</label>
                <input type="text" class="form-control" name="description" required>
              </div>
              <div class="form-group">
                <label class="d-block">Is this for an event?</label>
                <div class="form-check form-check-inline">
                  <input class="form-check-input" type="radio" name="isForEvent" id="No" value="no" checked>
                  <label class="form-check-label" for="No">No</label>
                </div>
                <div class="form-check form-check-inline">
                  <input class="form-check-input" type="radio" name="isForEvent" id="Yes" value="yes">
                  <label class="form-check-label" for="Yes">Yes</label>
                </div>
              </div>
              <div class="form-group d-none" id="eventSelect">
                <label>Event</label>
                <select name="event_id" class="form-control">
                  <option selected disabled value="null">Select an event</option>
                  @foreach($events as $event){{ $event->name }} 
                  <option value="{{ $event->id }}">{{ $event->date->format('m/d/y') }} - {{ $event->name }}</option>
                  @endforeach
                </select>
              </div>
              <div class="text-right">
                <button type="submit" class="btn btn-secondary">@lang('general.save') expense</button>
              </div>
            </form>
          </div>
        </div>
    </div>
    <div class="col-md-4">
      <p><b>Remaining</b><br>${{ number_format($budget->remaining / 100) }}</p>
      <p><b>Allocated</b><br>${{ number_format($budget->total_budget / 100) }}</p>
      <p><b>Spent</b><br>${{ number_format($budget->spent / 100) }}</p>
    </div>
  </div>
@endsection

@section('scripts')
  <script>
    $(document).ready(function () {
      $("input[type=radio][name=isForEvent]").change(function (event) {
        if ($(this).val( )== 'yes')
          $('#eventSelect').removeClass('d-none');
        else
          $('#eventSelect').addClass('d-none');
      })
    });
  </script>
@endsection