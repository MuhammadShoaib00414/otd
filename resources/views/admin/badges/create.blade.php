@extends('admin.layout')

@section('page-content')

    @component('admin.partials.breadcrumbs', ['links' => [
        'Badges' => '/admin/badges',
        'Create Badge' => '',
    ]])
    @endcomponent

  <h5>Add Badge</h5>

  <form method="post" action="/admin/badges" enctype="multipart/form-data">
      @csrf
      @if($errors)
        @foreach($errors->all() as $error)
          <div class="alert alert-danger w-50">
            {{ $error }}
          </div>
        @endforeach
      @endif
      <div class="w-50">
        @include('components.multi-language-text-input', ['name' => 'name', 'label' => 'Badge name', 'required' => true])
      </div>
      <div class="w-50">
        @include('components.multi-language-text-area', ['name' => 'description', 'label' => 'Description', 'required'])
      </div>
      <div class="form-group w-50 mb-2">
          <label class="form-check-label" for="enabled">Action That Earns Badge</label>
          <select class="form-control" name="action">
            <option>User makes 1 successful introduction</option>
            <option>User makes 5 successful introductions</option>
            <option>User makes 15 successful introductions</option>
            <option>User responds to 5 messages</option>
            <option>User completely fills out profile</option>
            <option>User marks themselves as a "mentor" in profile</option>
            <option>User is part of three groups or more</option>

          </select>
      </div>
      <div class="form-group w-50 mb-2">
        <label for="icon" class="form-check-label">Add icon for badge</label>
        <input type="file" class="form-control border-0" name="icon" id="icon" accept="image/*" required>
        <div class="mt-2" style="max-width:250px;width:100%">
            <img src="" alt="Badge icon" id="preview-image" class="img-fluid d-none">
        </div>
      </div>
      <button type="submit" class="btn btn-info">Create badge</button>
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
                $("#preview-image").removeClass("d-none");
            }

            reader.readAsDataURL(file);
        }
      });
    });
  </script>

@endsection
