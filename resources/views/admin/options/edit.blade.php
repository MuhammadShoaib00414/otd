@extends('admin..layout')

@section('page-content')
  <h5>Edit {{ $option->taxonomy->name }}</h5>
  @if ($errors->any())
      <div class="alert alert-danger">
          <ul>
              @foreach ($errors->all() as $error)
                  <li>{{ $error }}</li>
              @endforeach
          </ul>
      </div>
  @endif
  <form method="post" action="/admin/options/{{ $option->id }}" enctype="multipart/form-data">
      @csrf
      @method('PUT')
      @include('components.multi-language-text-input', ['label' => singular($option->taxonomy->name) . ' name', 'name' => 'name', 'value' => $option->name, 'localization' => $option->localization])

       <div class="form-group mb-2">
        <input type="checkbox" id="custom">
        <label class="form-check-label" for="custom">Custom grouping</label>
      </div>
      <div class="form-group mb-4">
        <label class="form-check-label" for="enabled">Grouping</label>
        <select id="setparent" class="custom-select d-block" name="parent" style="max-width: 450px;">
          <option disabled selected>Choose one</option>
          @foreach($parents as $parent)
          <option value="{{ $parent->parent }}"{{ ($parent->parent == $option->parent) ? 'selected' : '' }}>{{ $parent->parent }}</option>
          @endforeach
        </select>
        <input class="form-control d-none" name="disabledParent" type="text" id="customParent" style="max-width:450px;">
      </div> 

      <div class="d-flex flex-column mb-4 {{ $option->taxonomy->is_badge ? '' : 'd-none' }}">
        <p class="mb-2">Icon</p>
        @if($option->icon)
          <img class="mb-2" src="{{ $option->icon }}" style="height: 3em; width: 3em;">
          <small class="text-muted mb-2">Upload an image to change.</small>
        @endif
        <input type="file" name="icon" id="icon">
        <div class="custom-control custom-checkbox mt-3">
          <input type="checkbox" class="custom-control-input" name="use_default_icon" id="use_default_icon">
          <label class="custom-control-label" for="use_default_icon">Revert to default</label>
        </div>
      </div>

      <button type="submit" class="btn btn-info mt-2">@lang('general.save') changes</button>
  </form>

  <hr class="my-4">

  <form action="/admin/options/{{ $option->id }}" method="post">
      @method('delete')
      @csrf
      <button type="submit" class="btn btn-sm btn-light mr-2" id="deleteEvent">Delete {{ singular($option->taxonomy->name) }}</button>
  </form>
@endsection

@section('scripts')
  <script>
    $('#deleteEvent').on('click', function(event) {
      event.preventDefault();
      if (confirm('Delete this {{ singular($option->taxonomy->name) }}?'))
        $('#deleteEvent').parent().submit();
    });

    $('#custom').change(function(){
      toggleCustom();
    });

    function toggleCustom()
    {
      var custom = $('#customParent');
      var set = $('#setparent');
      set.toggleClass('d-none d-block');
      custom.toggleClass('d-none d-block');

      if($('#custom').prop('checked'))
      {
        $('#setparent').attr('name', 'nothing');
        $('#customParent').attr('name', 'parent');
      } else {
        $('#customParent').attr('name', 'nothing');
        $('#setparent').attr('name', 'parent');
      }
    }

    $('#use_default_icon').change(function(e) {
      if($(this).is(":checked"))
        $('#icon').addClass('d-none');
      else
        $('#icon').removeClass('d-none');
    });
  </script>
@endsection