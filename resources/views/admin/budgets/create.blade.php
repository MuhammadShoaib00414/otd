@extends('admin.layout')

@section('page-content')
    @component('admin.partials.breadcrumbs', ['links' => [
        'Budgets' => '/admin/budgets',
        'Create' => '',
    ]])
    @endcomponent

  <h5>Create a budget</h5>

  <div class="row">
    <div class="col-lg-6">
      <hr>
      @if ($errors->any())
          <div class="alert alert-danger">
              <ul>
                  @foreach ($errors->all() as $error)
                      <li>{{ $error }}</li>
                  @endforeach
              </ul>
          </div>
      @endif
      <form method="post" action="/admin/budgets">
          @csrf
          <div class="form-group">
              <label class="form-label" for="group">Group</label>
              <select name="group_id" class="form-control" autocomplete="no" required>
                @if(!isset($group))
                  <option disabled selected value="">Select one</option>
                @endif
                @foreach($groups as $item)
                  <option value="{{ $item->id }}" {{ (isset($group) && $group == $item->id) ? 'selected' : '' }}>{{ $item->name }}</option>
                @endforeach
              </select>
          </div>

          <hr>
          <div class="form-row mb-3">
              <div class="col-6">
                <label class="form-label" for="year">Budget Year</label>
                <select name="year" id="year" class="form-control" autocomplete="no">
                  @foreach([date("Y")-2, date("Y")-1, date("Y"), date("Y") + 1] as $year)
                    <option value="{{ $year }}">{{ $year }}</option>
                  @endforeach
                </select>
              </div>
              <div class="col-6">
                <label class="form-label" for="quarter">Budget Quarter</label>
                <select name="quarter" id="quarter" class="form-control" autocomplete="off">
                  <option value="1">Q1</option>
                  <option value="2">Q2</option>
                  <option value="3">Q3</option>
                  <option value="4">Q4</option>
                </select>
              </div>
          </div>
          <div class="form-group">
            <label for="total_budget">Budget Amount</label>
            <div class="input-group mb-3">
              <div class="input-group-prepend">
                <span class="input-group-text" id="basic-addon1">$</span>
              </div>
              <input type="text" name="total_budget" id="total_budget" class="form-control" autocomplete="off" required onkeypress='validate(event)'>
            </div>

          </div>
          <button type="submit" class="btn btn-info">Create budget</button>
      </form>
    </div>
  </div>
@endsection

@section('scripts')
<script>
function validate(evt) {
  var theEvent = evt || window.event;

  // Handle paste
  if (theEvent.type === 'paste') {
      key = event.clipboardData.getData('text/plain');
  } else {
  // Handle key press
      var key = theEvent.keyCode || theEvent.which;
      key = String.fromCharCode(key);
  }
  var regex = /[0-9]|\./;
  if( !regex.test(key) ) {
    theEvent.returnValue = false;
    if(theEvent.preventDefault) theEvent.preventDefault();
  }
}
</script>
@endsection