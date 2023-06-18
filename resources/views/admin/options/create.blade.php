@extends('admin..layout')

@section('page-content')
  <h5>New {{ singular($taxonomy->name) }}</h5>
  @if ($errors->any())
      <div class="alert alert-danger">
          <ul>
              @foreach ($errors->all() as $error)
                  <li>{{ $error }}</li>
              @endforeach
          </ul>
      </div>
  @endif
  <form method="post" action="/admin/options" enctype="multipart/form-data">
      @csrf
      <input type="hidden" name="taxonomy_id" value="{{ $taxonomy->id }}">

      <div class="col-8">
        @include('components.multi-language-text-input', ['label' => singular($taxonomy->name) . ' name', 'name' => 'name'])
      </div>

      <!-- <div class="form-group mb-2">
        <input type="checkbox" id="custom">
        <label class="form-check-label" for="custom">Custom grouping</label>
      </div> -->
      <!-- <div class="form-group mb-3">
        <label class="form-check-label" for="enabled">Grouping</label>
        <select id="setparent" class="custom-select d-block" name="parent" style="max-width: 450px;">
          <option disabled selected>Choose one</option>
          @foreach($parents as $parent)
          <option>{{ $parent->parent }}</option>
          @endforeach
        </select>
        <input class="form-control d-none" name="disabledParent" type="text" id="customParent" style="max-width:450px;">
      </div> -->

      <div class="form-group {{ $taxonomy->is_badge ? '' : 'd-none' }}">
        <label for="icon">Icon</label><br>
        <input type="file" name="icon" id="icon">
      </div>

      <button type="submit" class="btn btn-info mt-2">@lang('general.save')</button>
  </form>
@endsection

@section('scripts')
  <script>
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
      } 
      else 
      {
        $('#customParent').attr('name', 'nothing');
        $('#setparent').attr('name', 'parent');
      }

    }
  </script>
@endsection