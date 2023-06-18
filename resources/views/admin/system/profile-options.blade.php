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
        'Profile Options' => '',
    ]])
    @endcomponent

    <div class="mb-5">

        @foreach($errors->all() as $message)
            <div class="alert alert-dismissible alert-danger" style="max-width:450px;">
                <button type="button" class="close" data-dismiss="alert">×</button>
                <strong>{!! $message !!}</strong>
            </div>
        @endforeach

        <h5>Profile Options</h5>

        <hr>

        <form method="post" action="/admin/system/profile-options" id="form">
            @csrf

              <div class="form-group">
                <span class="d-block mb-2">Is "What is your Superpower?" Prompt Enabled</span>
                <div class="custom-control custom-radio custom-control-inline">
                  <input type="radio" id="is_superpower_enabled" name="is_superpower_enabled" value="1" class="custom-control-input" {{ ($is_superpower_enabled == 1) ? 'checked' : '' }}>
                  <label class="custom-control-label" for="is_superpower_enabled">Enabled</label>
                </div>
                <div class="custom-control custom-radio custom-control-inline">
                  <input type="radio" id="is_superpower_disabled" name="is_superpower_enabled" value="0" class="custom-control-input" {{ $is_superpower_enabled ? '' : 'checked' }}>
                  <label class="custom-control-label" for="is_superpower_disabled">Disabled</label>
                </div>
              </div>

              <div class="form-group">
                <span class="d-block mb-2">Is "Tell us about yourself" Prompt Enabled</span>
                <div class="custom-control custom-radio custom-control-inline">
                  <input type="radio" id="is_about_me_enabled" name="is_about_me_enabled" value="1" class="custom-control-input" {{ ($is_about_me_enabled == 1) ? 'checked' : '' }}>
                  <label class="custom-control-label" for="is_about_me_enabled">Enabled</label>
                </div>
                <div class="custom-control custom-radio custom-control-inline">
                  <input type="radio" id="is_about_me_disabled" name="is_about_me_enabled" value="0" class="custom-control-input" {{ $is_about_me_enabled ? '' : 'checked' }}>
                  <label class="custom-control-label" for="is_about_me_disabled">Disabled</label>
                </div>
              </div>

              <div class="form-group">
                <span class="d-block mb-2">Enable Job Title</span>
                <div class="custom-control custom-radio custom-control-inline">
                  <input type="radio" id="is_job_title_enabled" name="is_job_title_enabled" value="1" class="custom-control-input" {{ $is_job_title_enabled ? 'checked' : '' }}>
                  <label class="custom-control-label" for="is_job_title_enabled">Enabled</label>
                </div>
                <div class="custom-control custom-radio custom-control-inline">
                  <input type="radio" id="is_job_title_disabled" name="is_job_title_enabled" value="0" class="custom-control-input" {{ $is_job_title_enabled ? '' : 'checked' }}>
                  <label class="custom-control-label" for="is_job_title_disabled">Disabled</label>
                </div>
              </div>

              <div class="form-group">
                <span class="d-block mb-2">Enable Company</span>
                <div class="custom-control custom-radio custom-control-inline">
                  <input type="radio" id="is_company_enabled" name="is_company_enabled" value="1" class="custom-control-input" {{ $is_company_enabled ? 'checked' : '' }}>
                  <label class="custom-control-label" for="is_company_enabled">Enabled</label>
                </div>
                <div class="custom-control custom-radio custom-control-inline">
                  <input type="radio" id="is_company_disabled" name="is_company_enabled" value="0" class="custom-control-input" {{ $is_company_enabled ? '' : 'checked' }}>
                  <label class="custom-control-label" for="is_company_disabled">Disabled</label>
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