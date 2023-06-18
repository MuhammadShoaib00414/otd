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
        'GDPR Settings' => '',
    ]])
    @endcomponent

    <div class="mb-5">

        @foreach($errors->all() as $message)
            <div class="alert alert-dismissible alert-danger" style="max-width:450px;">
                <button type="button" class="close" data-dismiss="alert">×</button>
                <strong>{!! $message !!}</strong>
            </div>
        @endforeach

        <h5>GDPR Settings</h5>

        <hr>

        <form method="post" action="/admin/system/gdpr-settings" id="form">
            @csrf

            <div class="form-group">
                <label for="gdpr_prompt">GDPR Prompt</label>
                <input type="text" class="form-control" name="gdpr_prompt" id="gdpr_prompt" value="{{ getsetting('gdpr_prompt') }}">
              </div>
              <div class="form-group">
                <label for="gdpr_checkbox_label">GDPR Checkbox Label</label>
                <input type="text" class="form-control" name="gdpr_checkbox_label" id="gdpr_checkbox_label" value="{{ getsetting('gdpr_checkbox_label') }}">
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