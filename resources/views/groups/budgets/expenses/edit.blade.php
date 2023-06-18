@extends('groups.layout')

@section('stylesheets')
@parent
  <link href="https://unpkg.com/gijgo@1.9.13/css/gijgo.min.css" rel="stylesheet" type="text/css" />
  <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/timepicker/1.3.5/jquery.timepicker.min.css">
@endsection

@section('body-class', 'col-md-9')

@section('inner-content')
    <div class="d-flex justify-content-between align-items-center">
      <h3 class="font-weight-bold mb-3">@lang('budgets.Budget for'): Q{{ $budget->quarter }} {{ $budget->year }}</h3>
    </div>

      <div class="row">
        <div class="col-md-9">
          @if ($errors->any())
              <div class="alert alert-danger">
                  <ul>
                      @foreach ($errors->all() as $error)
                          <li>{{ $error }}</li>
                      @endforeach
                  </ul>
              </div>
          @endif
          <div class="card">
            <div class="card-body">
              <div class="d-flex justify-content-between">
                <h5>@lang('general.Edit Expense')</h5>
                  <form action="/groups/{{ $group->slug }}/budgets/{{ $budget->id }}/expenses/{{ $expense->id }}" method="post">
                    @method('delete')
                    @csrf
                    <button type="submit" class="btn btn-sm btn-outline-secondary mr-1" id="deleteExpense">@lang('general.delete')</button>
                  </form>
                </div>
              <form method="post" action="/groups/{{ $group->slug }}/budgets/{{ $budget->id }}/expenses/{{ $expense->id }}" enctype="multipart/form-data">
                @csrf
                @method('put')
                <div class="form-row">
                  <div class="col-6">
                    <label>@lang('general.date')</label>
                    <input type="text" name="date" class="form-control" required value="{{ old('date') ?: \Carbon\Carbon::parse($expense->date)->format('m/d/y') }}" placeholder="mm/dd/yy" id="date">
                  </div>
                  <div class="col-6">
                    <label>@lang('budgets.Amount')</label>
                    <div class="input-group mb-3">
                      <div class="input-group-prepend">
                        <span class="input-group-text" id="basic-addon1">$</span>
                      </div>
                      <input type="text" name="amount" id="amount" class="form-control" value="{{ number_format($expense->amount/100, 2) }}" required>
                    </div>
                  </div>
                </div>
                <div class="form-group">
                  <label>@lang('budgets.Category')</label>
                  <select name="category_id" class="form-control">
                    <option {{ ($expense->expense_category_id == null) ? 'selected' : ''}} value="null">@lang('budgets.No category')</option>
                    @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ ($expense->expense_category_id == $category->id) ? 'selected' : ''}}>{{ $category->name }}</option>
                    @endforeach
                  </select>
                </div>
                <div class="form-group">
                  <label>@lang('general.description')</label>
                  <input type="text" class="form-control" name="description" value="{{ $expense->description }}" required>
                </div>
                <div class="form-group">
                  <label class="d-block">@lang('budgets.Is this for an event?')</label>
                  <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="isForEvent" id="No" value="no"{{ ($expense->event_id == null) ? ' checked' : '' }}>
                    <label class="form-check-label" for="No">@lang('general.no'</label>
                  </div>
                  <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="isForEvent" id="Yes" value="yes"{{ ($expense->event_id != null) ? ' checked' : '' }}>
                    <label class="form-check-label" for="Yes">@lang('general.yes'</label>
                  </div>
                </div>
                <div class="form-group{{ ($expense->event_id == null) ? ' d-none' : '' }}" id="eventSelect">
                  <label>@lang('general.event')</label>
                  <select name="event_id" class="form-control">
                    <option {{ ($expense->event_id == null) ? 'selected' : ''}} value="null" disabled>Select an event</option>
                    @foreach($events as $event){{ $event->name }} 
                    <option value="{{ $event->id }}" {{ ($expense->event_id == $event->id) ? 'selected' : ''}}>{{ $event->date->format('m/d/y') }} - {{ $event->name }}</option>
                    @endforeach
                  </select>
                </div>
                <div class="form-group mb-3">
                  @if($expense->receipt_file_path)
                    <b>@lang('budgets.Currently attached receipt:')</b><br>
                    <a href="{{ $expense->receipt_path }}" target="_blank">
                      <i class="icon-text-document-inverted"></i> {{ $expense->receipt_file_name }}
                    </a>
                  @endif
                  <label class="mt-3 d-block">@lang('Change/upload different receipt') (@lang('general.optional'))</label>
                  <div class="custom-file">
                    <input type="file" class="custom-file-input" id="customFile" name="receipt">
                    <label class="custom-file-label" for="customFile">@lang('budgets.Choose file')/label>
                  </div>
                </div>
                <div class="text-right">
                  <button type="submit" class="btn btn-secondary">@lang('general.save')</button>
                </div>
              </form>
            </div>
          </div>
        </div>
        <div class="col-md-3">
          <p><b>@lang('budgets.Remaining')</b><br>${{ number_format($budget->remaining/100) }}</p>
          <p><b>@lang('budgets.Allocated')</b><br>${{ number_format($budget->total_budget/100) }}</p>
          <p><b>@lang('budgets.Spent')</b><br>${{ number_format($budget->spent/100) }}</p>
        </div>
@endsection

@section('scripts')
  <script src="https://unpkg.com/gijgo@1.9.13/js/gijgo.min.js" type="text/javascript"></script>
  <script src="//cdnjs.cloudflare.com/ajax/libs/timepicker/1.3.5/jquery.timepicker.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bs-custom-file-input/dist/bs-custom-file-input.min.js"></script>
  <script>
    $(document).ready(function () {
      bsCustomFileInput.init()
    })
    $('#date').datepicker({
      uiLibrary: 'bootstrap4',
      format: 'mm/dd/yy'
    });
    $(document).ready(function () {
      $("input[type=radio][name=isForEvent]").change(function (event) {
        if ($(this).val( )== 'yes')
          $('#eventSelect').removeClass('d-none');
        else
          $('#eventSelect').addClass('d-none');
      })
    });
    $('#deleteExpense').on('click', function(event) {
      event.preventDefault();
      if (confirm("@lang('budgets.Delete this expense?')"))
        $('#deleteExpense').parent().submit();
    });
  </script>
@endsection