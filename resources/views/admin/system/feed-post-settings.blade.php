@extends('admin.layout')

@push('stylestack')
    <link href="https://unpkg.com/gijgo@1.9.13/css/gijgo.min.css" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.9/dist/css/bootstrap-select.min.css">
    <style>
      .select-picker > .dropdown-toggle { border: 1px solid #ced4da; }
    </style>
@endpush

@section('page-content')

    @component('admin.partials.breadcrumbs', ['links' => [
        'Feed Post Settings' => '',
    ]])
    @endcomponent

    <div class="mb-5">

        @foreach($errors->all() as $message)
            <div class="alert alert-dismissible alert-danger" style="max-width:450px;">
                <button type="button" class="close" data-dismiss="alert">×</button>
                <strong>{!! $message !!}</strong>
            </div>
        @endforeach

        <h5>Feed &amp; Post Settings</h5>

        <hr>

        <form method="post" action="/admin/system/feed-post-settings" enctype="multipart/form-data" id="form">
            @csrf

          <div class="form-group">
            <span class="d-block mb-2">Enable Likes</span>
            <div class="custom-control custom-radio custom-control-inline">
              <input type="radio" id="is_likes_enabled_on" name="is_likes_enabled" value="1" class="custom-control-input" {{ $is_likes_enabled ? 'checked' : '' }}>
              <label class="custom-control-label" for="is_likes_enabled_on">Enabled</label>
            </div>
            <div class="custom-control custom-radio custom-control-inline">
              <input type="radio" id="is_likes_enabled_off" name="is_likes_enabled" value="0" class="custom-control-input" {{ $is_likes_enabled ? '' : 'checked' }}>
              <label class="custom-control-label" for="is_likes_enabled_off">Disabled</label>
            </div>
          </div>

          <div class="form-group">
            <span class="d-block mb-2">Enable Focus Groups</span>
            <div class="custom-control custom-radio custom-control-inline">
              <input type="radio" id="is_ideations_enabled_on" name="is_ideations_enabled" value="1" class="custom-control-input" {{ $is_ideations_enabled ? 'checked' : '' }}>
              <label class="custom-control-label" for="is_ideations_enabled_on">Enabled</label>
            </div>
            <div class="custom-control custom-radio custom-control-inline">
              <input type="radio" id="is_ideations_enabled_off" name="is_ideations_enabled" value="0" class="custom-control-input" {{ $is_ideations_enabled ? '' : 'checked' }}>
              <label class="custom-control-label" for="is_ideations_enabled_off">Disabled</label>
            </div>
          </div>

          @if($is_ideations_enabled)
          <div class="form-group">
            <span class="d-block mb-2">Ideation proposal/approval</span>
            <div class="custom-control custom-radio custom-control-inline">
              <input type="radio" id="is_ideation_approval_enabled_on" name="is_ideation_approval_enabled" value="1" class="custom-control-input" {{ $is_ideation_approval_enabled ? 'checked' : '' }}>
              <label class="custom-control-label" for="is_ideation_approval_enabled_on">Enabled</label>
            </div>
            <div class="custom-control custom-radio custom-control-inline">
              <input type="radio" id="is_ideation_approval_enabled_off" name="is_ideation_approval_enabled" value="0" class="custom-control-input" {{ $is_ideation_approval_enabled ? '' : 'checked' }}>
              <label class="custom-control-label" for="is_ideation_approval_enabled_off">Disabled</label>
            </div>
          </div>
          @endif

          <hr>

          <button type="submit" class="btn btn-info">Save changes</button>
        </form>
    </div>
@endsection


@push('scriptstack')
    @if(Session::has('success'))
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@8"></script>
        <script>
            Swal.fire({
              title: 'Success!',
              text: 'Changes saved.',
              type: 'success',
              confirmButtonText: 'Close'
            })
        </script>
    @endif
@endpush