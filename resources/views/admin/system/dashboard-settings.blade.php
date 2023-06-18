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
        'Dashboard Settings' => '',
    ]])
    @endcomponent

    <div class="mb-5">

        @foreach($errors->all() as $message)
            <div class="alert alert-dismissible alert-danger" style="max-width:450px;">
                <button type="button" class="close" data-dismiss="alert">×</button>
                <strong>{!! $message !!}</strong>
            </div>
        @endforeach

        <h5>Dashboard Settings</h5>

        <form method="post" action="/admin/system/dashboard-settings" enctype="multipart/form-data" id="form">
            @csrf

            <div class="mt-3 mb-4">
              <div>
                <label class="form-label d-block"><b>Dashboard header type</b></label>
                <div class="mb-3">
                  <div class="custom-control custom-radio custom-control-inline">
                    <input type="radio" id="image" name="dashboard_header_type" value="image" class="custom-control-input"{{ (getSetting('is_dashboard_virtual_room_enabled') == 0) ? ' checked' : '' }}>
                    <label class="custom-control-label" for="image">Image</label>
                  </div>
                  <div class="custom-control custom-radio custom-control-inline">
                    <input type="radio" id="virtual_room" name="dashboard_header_type" value="virtual_room" class="custom-control-input"{{ (getSetting('is_dashboard_virtual_room_enabled') == 1) ? ' checked' : '' }}>
                    <label class="custom-control-label" for="virtual_room">Interactive Header Image</label>
                  </div>
                </div>
                <hr>
              </div>
              <div id="dashboardTypeImage" class="{{ (getSetting('is_dashboard_virtual_room_enabled') == 0) ? '' : 'd-none' }}">
                <div class="form-row">
                  @if($dashboard_header_image && $dashboard_header_image->value)
                    <img class="mb-4"  style="max-width: 450px; max-height: 450px;" src="{{ $dashboard_header_image->value }}">
                    <br>
                    <div class="col">
                      <p class="d-flex">Upload an image to change:</p>
                      <input class="mt-2" accept=".png, .jpeg, .jpg" type="file" name="dashboard_header_image" id="dashboard_header_image">
                      <br>
                      <small class="text-muted">(Recommended size: 1900x450)</small>
                    </div>
                  @else
                  <input class="mt-2 ml-1" accept=".png, .jpeg, .jpg" type="file" name="dashboard_header_image" id="dashboard_header_image">
                  @endif
                </div>
                <div class="form-check mt-2">
                  <input class="mt-2 image_revert form-check-input" type="checkbox" id="dashboard_header_image_revert" name="dashboard_header_image_revert">
                  <label style="vertical-align: middle;" class="form-check-label" for="dashboard_header_image_revert">
                    Remove image
                  </label>
                </div>
              </div>
              <div id="dashboardTypeVirtualRoom" class="{{ (getSetting('is_dashboard_virtual_room_enabled') == 1) ? '' : 'd-none' }}">
                @if(getSetting('is_dashboard_virtual_room_enabled') == 1)
                  <div class="py-3 text-center" style="background-color: #eee;">
                    <a href="/admin/virtual-rooms/{{ getSetting('dashboard_virtual_room_id') }}/edit" class="btn btn-sm btn-primary">Edit Interactive Header Image</a>
                  </div>
                @else
                  <p class="text-center px-2 py-3 text-sm mb-0" style="background-color: #eee;">@lang('general.save') changes to enable interactive header image. You will then be able to edit it from a button in this area.</p>
                @endif
              </div>
            </div>

            <hr>

             <div class="form-group my-4 card card-body">
                <div class="form-col">
                  <label class="form-label"><b>Dashboard left nav image</b></label>
                </div>
                <div class="form-row">
                  @include('components.multi-language-image-input', ['name' => 'dashboard_left_nav_image', 'value' => $dashboard_left_nav_image ? $dashboard_left_nav_image->value : '', 'localization' => $dashboard_left_nav_image ? $dashboard_left_nav_image->localization : '', 'noRemove' => false, 'maxWidth' => '200px'])
                </div>
                <hr>
                <label for="dashboard_left_nav_image_link">Clicking on image opens url:</label>
                <input value="{{ $dashboard_left_nav_image_link->value }}" name="dashboard_left_nav_image_link" type="url" class="form-control" id="dashboard_left_nav_image_link">
                <div class="form-check mt-2">
                  <input {{ $does_dashboard_left_nav_image_open_new_tab->value ? 'checked' : '' }} class="mt-2 form-check-input" type="checkbox" name="does_dashboard_left_nav_image_open_new_tab">
                  <label style="vertical-align: middle;" class="form-check-label" for="does_dashboard_left_nav_image_open_new_tab">
                    Link opens in new tab
                  </label>
                </div>
              </div>

          <div class="form-group">
            @include('components.multi-language-text-input', ['label' => 'My groups page name (For event-only users)', 'name' => 'my_groups_page_name', 'value' => $my_groups_page_name->value, 'localization' => $my_groups_page_name->localization, 'required' => 'true', 'specificName' => true])
          </div>

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