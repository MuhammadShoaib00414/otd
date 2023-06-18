@extends('admin.layout')

@section('page-content')
    @component('admin.partials.breadcrumbs', ['links' => [
        'Budgets' => '/admin/budgets',
        $budget->group->name . ': ' . $budget->year . ' Q' . $budget->quarter => '/admin/budgets/'.$budget->id,
        'Edit' => '',
    ]])
    @endcomponent

  <div class="row">
    <div class="col-lg-6">
      <div class="d-flex justify-content-between">
        <h5>Edit budget</h5>
        <form action="/admin/budgets/{{ $budget->id }}" method="post">
          @method('delete')
          @csrf
          <button type="submit" class="btn btn-sm btn-outline-primary mr-1" id="deleteBudget"><i class="fa fa-trash"></i></button>
        </form>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-lg-6">
      <hr>
      <form method="post" action="/admin/budgets/{{ $budget->id }}">
          @csrf
          @method('put')
          <div class="form-group">
              <label class="form-label" for="group">Group</label>
              <select name="group_id" class="form-control" autocomplete="no">
                @foreach($groups as $item)
                  <option value="{{ $item->id }}" {{ ($budget->group_id == $item->id) ? 'selected' : '' }}>{{ $item->name }}</option>
                @endforeach
              </select>
          </div>

          <hr>
          <div class="form-row mb-3">
              <div class="col-6">
                <label class="form-label" for="year">Budget Year</label>
                <select name="year" id="year" class="form-control" autocomplete="no">
                  @foreach([date("Y") - 2, date("Y") - 1, date("Y"), date("Y") + 1] as $year)
                    <option value="{{ $year }}"{{ ($budget->year == $year) ? ' selected' : '' }}>{{ $year }}</option>
                  @endforeach
                </select>
              </div>
              <div class="col-6">
                <label class="form-label" for="quarter">Budget Quarter</label>
                <select name="quarter" id="quarter" class="form-control" autocomplete="no">
                  <option value="1"{{ ($budget->quarter == '1') ? ' selected' : '' }}>Q1</option>
                  <option value="2"{{ ($budget->quarter == '2') ? ' selected' : '' }}>Q2</option>
                  <option value="3"{{ ($budget->quarter == '3') ? ' selected' : '' }}>Q3</option>
                  <option value="4"{{ ($budget->quarter == '4') ? ' selected' : '' }}>Q4</option>
                </select>
              </div>
          </div>
          <div class="form-group">
            <label for="total_budget">Budget Amount</label>
            <div class="input-group mb-3">
              <div class="input-group-prepend">
                <span class="input-group-text" id="basic-addon1">$</span>
              </div>
              <input type="text" name="total_budget" id="total_budget" class="form-control" value="{{ number_format($budget->total_budget/100,2) }}" required>
            </div>

          </div>
          <button type="submit" class="btn btn-info">@lang('general.save') changes</button>
      </form>
    </div>
  </div>
@endsection

@section('scripts')
  <script>
    $('#deleteBudget').on('click', function(event) {
      event.preventDefault();
      if (confirm('Delete this budget?'))
        $('#deleteBudget').parent().submit();
    });
  </script>
@endsection