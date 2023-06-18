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
        'Platform Branding' => '',
    ]])
    @endcomponent

    <div class="mb-5">

        @foreach($errors->all() as $message)
            <div class="alert alert-dismissible alert-danger" style="max-width:450px;">
                <button type="button" class="close" data-dismiss="alert">×</button>
                <strong>{!! $message !!}</strong>
            </div>
        @endforeach

        <h5>Platform Branding</h5>

        <hr>

        <form method="post" action="/admin/system/platform-branding" enctype="multipart/form-data" id="form">
            @csrf

            @include('components.multi-language-text-input', ['label' => 'Organization name', 'name' => 'name', 'value' => $orgName->value, 'localization' => $orgName->localization, 'specificName' => 'true'])
            @include('components.multi-language-text-input', ['label' => 'Send Emails From', 'name' => 'from_email_name', 'value' => $from_email_name->value, 'localization' => $from_email_name->localization, 'specificName' => 'true'])

            <hr>

            <div class="form-group my-3">
                <div class="form-col">
                  <label class="form-label"><b>Website logo</b></label>
                </div>
                @if($logo && $logo->value)
                  @include('components.multi-language-image-input', ['name' => 'logo', 'value' => $logo->value, 'localization' => $logo->localization, 'revert' => 'true'])
                @else
                <input class="mt-2 ml-1" accept=".png, .jpeg, .jpg" type="file" name="logo" id="logo">
                @endif
            </div>

            <hr>

            <div class="form-group my-3">
                <div class="row align-items-center">
                  <p class="font-weight-bold col-3">
                    Theme Colors
                  </p>
                  <small class="text-muted col-3">Click to select a color.</small>
                </div>
                <div class="form-row mb-2">
                  <div class="col-3">
                    <label for="primary_color">Primary Color</label>
                  </div>
                  <div class="col-3">
                    <input type="color" class="form-control form-control-sm" name="primary_color" value="{{ $primary_color->value }}">
                  </div>
                </div>
                <div class="form-row mb-2">
                  <div class="col-3">
                    <label for="accent_color">Accent Color</label>
                  </div>
                  <div class="col-3">
                    <input type="color" class="form-control form-control-sm" name="accent_color" value="{{ $accent_color->value }}">
                  </div>
                </div>
                <div class="form-row mb-2">
                  <div class="col-3">
                    <label for="accent_color">Navbar Color</label>
                  </div>
                  <div class="col-3">
                    <input type="color" class="form-control form-control-sm" name="navbar_color" value="{{ $navbar_color->value }}">
                  </div>
                </div>
                <div class="form-row mb-2">
                  <div class="col-3">
                    <label for="accent_color">Group Header Color</label>
                  </div>
                  <div class="col-3">
                    <input type="color" class="form-control form-control-sm" name="group_header_color" value="{{ $group_header_color }}">
                  </div>
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