@extends('admin.layout')

@section('page-content')

    @component('admin.partials.breadcrumbs', ['links' => [
        'Badges' => '/admin/badges',
        'Edit Badge' => '',
    ]])
    @endcomponent

<div class="col-sm-12 col-md-6 col-lg-6">
  <div class="d-flex justify-content-between">
    <h5>Edit Badge</h5>
    @if($badge->created_by_id)
      <form method="post" action="/admin/badges/{{ $badge->id }}">
        @method('delete')
        @csrf
        <button onclick="return confirm('Are you sure you want to delete this badge? This action cannot be undone.');" type="submit" class="btn btn-sm btn-outline-secondary">Delete</button>
      </form>
    @endif
  </div>
</div>

  <form method="post" action="/admin/badges/{{ $badge->id }}" enctype="multipart/form-data">
      @csrf
      @method('PUT')
      <div class="w-50">
          @include('components.multi-language-text-input', ['name' => 'name', 'label' => 'Badge name', 'required' => true, 'value' => $badge->name, 'localization' => $badge->localization])
      </div>
      <div class="w-50">
          @include('components.multi-language-text-area', ['name' => 'description', 'label' => 'Description', 'required', 'value' => $badge->description, 'localization' => $badge->localization])
      </div>
      <div class="form-group w-50 mb-3">
          <label class="form-check-label" for="enabled">Action That Earns Badge</label>
          <select class="form-control" name="action" >
            <option>User makes 1 successful introduction</option>
            <option>User makes 5 successful introductions</option>
            <option>User makes 15 successful introductions</option>
            <option>User responds to 5 messages</option>
            <option>User completely fills out profile</option>
            <option>User is part of three groups or more</option>
          </select>
      </div>
      <div class="form-group w-50 mb-3">
        <label for="icon" class="form-check-label">Badge icon</label>
        <input type="file" class="form-control border-0 text-white"  name="icon" id="icon" accept="image/*">
        <div class="mt-2" style="max-width:250px;width:100%">
            <img src="{{ ltrim($badge->icon, '/') }}" alt="Badge icon" id="preview-image" class="img-fluid">
        </div>
      </div>
      <div class="form-check w-50 mb-3 pl-0">
        <input type="checkbox" class="custom-checkbox" name="is_enabled" id="is_enabled" {{ $badge->is_enabled ? 'checked' : '' }}>
        <label class="form-check-label" for="is_enabled">Enabled</label>
      </div>
      <button type="submit" class="btn btn-info">@lang('general.save') changes</button>
  </form>
@endsection

@section('scripts')

  <script>
    $(document).ready(function() {
      // show uploaded icon preview
      $('#icon').on('change', function() {
        var file = $(this).get(0).files[0];
        console.log(file);
        if(file){
            var reader = new FileReader();

            reader.onload = function(){
                $("#preview-image").attr("src", reader.result);
            }

            reader.readAsDataURL(file);
        }
      });
    });
  </script>

@endsection
