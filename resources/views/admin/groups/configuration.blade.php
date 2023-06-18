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
        'Groups' => '/admin/groups',
        'Group Configuration' => '',
    ]])
    @endcomponent

    <div class="mb-5">

        @foreach($errors->all() as $message)
            <div class="alert alert-dismissible alert-danger" style="max-width:450px;">
                <button type="button" class="close" data-dismiss="alert">×</button>
                <strong>{!! $message !!}</strong>
            </div>
        @endforeach

        <h5>Group Configuration</h5>

        <hr>

        <form method="post" action="/admin/groups/configuration">
            @csrf

            <div class="form-group">
                @include('components.multi-language-text-input', ['label' => 'My groups page name (For event-only users)', 'name' => 'my_groups_page_name', 'value' => $my_groups_page_name->value, 'localization' => $my_groups_page_name->localization, 'required' => 'true', 'specificName' => true])
            </div>

            <div class="form-group">
                <span class="d-block mb-2">Show join button on group pages</span>
                <div class="custom-control custom-radio custom-control-inline">
                  <input type="radio" id="show_join_button_on_group_pages_on" name="show_join_button_on_group_pages" value="1" class="custom-control-input" {{ $show_join_button_on_group_pages ? 'checked' : '' }}>
                  <label class="custom-control-label" for="show_join_button_on_group_pages_on">Enabled</label>
                </div>
                <div class="custom-control custom-radio custom-control-inline">
                  <input type="radio" id="show_join_button_on_group_pages_off" name="show_join_button_on_group_pages" value="0" class="custom-control-input" {{ $show_join_button_on_group_pages ? '' : 'checked' }}>
                  <label class="custom-control-label" for="show_join_button_on_group_pages_off">Disabled</label>
                </div>
            </div>

           <!--  <div class="form-group">
                <span class="d-block mb-2">Join group codes</span>
                <div class="custom-control custom-radio custom-control-inline">
                  <input type="radio" id="join_group_codes_on" name="are_group_codes_enabled" value="1" class="custom-control-input" {{ $are_group_codes_enabled ? 'checked' : '' }}>
                  <label class="custom-control-label" for="join_group_codes_on">Enabled</label>
                </div>
                <div class="custom-control custom-radio custom-control-inline">
                  <input type="radio" id="are_group_codes_enabled_off" name="are_group_codes_enabled" value="0" class="custom-control-input" {{ $are_group_codes_enabled ? '' : 'checked' }}>
                  <label class="custom-control-label" for="are_group_codes_enabled_off">Disabled</label>
                </div>
            </div> -->

            <div class="form-group d-none">
                <span class="d-block mb-2">When a user becomes a group admin </span>
                <div class="custom-control custom-radio custom-control-inline">
                  <input type="radio" id="group_admins_hide" name="group_admins" value="hide" class="custom-control-input" {{ $group_admins == 'hide' ? 'checked' : '' }}>
                  <label class="custom-control-label" for="group_admins_hide">Auto-hide</label>
                </div>
                <div class="custom-control custom-radio custom-control-inline">
                  <input type="radio" id="group_admins_show" name="group_admins" value="show" class="custom-control-input" {{ $group_admins == 'show' ? 'checked' : '' }}>
                  <label class="custom-control-label" for="group_admins_show">Auto-show</label>
                </div>
                <div class="custom-control custom-radio custom-control-inline">
                  <input type="radio" id="group_admins_nothing" name="group_admins" value="nothing" class="custom-control-input" {{ $group_admins == 'nothing' ? 'checked' : '' }}>
                  <label class="custom-control-label" for="group_admins_nothing">Do nothing</label>
                </div>
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