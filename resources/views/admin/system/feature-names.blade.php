@extends('admin.layout')

@push('stylestack')
<link href="https://unpkg.com/gijgo@1.9.13/css/gijgo.min.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.9/dist/css/bootstrap-select.min.css">
<style>
    .select-picker>.dropdown-toggle {
        border: 1px solid #ced4da;
    }
</style>
@endpush

@section('page-content')

@component('admin.partials.breadcrumbs', ['links' => [
'Feature Settings' => '',
]])
@endcomponent

<div class="mb-5">

    @foreach($errors->all() as $message)
    <div class="alert alert-dismissible alert-danger" style="max-width:450px;">
        <button type="button" class="close" data-dismiss="alert">×</button>
        <strong>{!! $message !!}</strong>
    </div>
    @endforeach

    <h5>Feature Settings</h5>

    <hr>

    <form method="post" action="/admin/system/feature-settings">
        @csrf

        <div class="form-group">
            <span class="d-block mb-2">Ask a Mentor</span>
            <div class="custom-control custom-radio custom-control-inline">
                <input type="radio" id="ask_a_mentor_on" name="ask_a_mentor" value="1" class="custom-control-input" {{ $ask_a_mentor ? 'checked' : '' }}>
                <label class="custom-control-label" for="ask_a_mentor_on">Enabled</label>
            </div>
            <div class="custom-control custom-radio custom-control-inline">
                <input type="radio" id="ask_a_mentor_off" name="ask_a_mentor" value="0" class="custom-control-input" {{ $ask_a_mentor ? '' : 'checked' }}>
                <label class="custom-control-label" for="ask_a_mentor_off">Disabled</label>
            </div>
        </div>

        <div class="form-group">

            <label for="ask_a_mentor_alias">Ask a Mentor Alias</label>
            <input type="text" required name="ask_a_mentor_alias" id="ask_a_mentor_alias" class="form-control w-50" value="{{ $ask_a_mentor_alias->value }}">
        </div>

        <div class="form-group">
            <label for="ask_a_mentor_alias">Find Your People Alias</label>
            <input type="text" required name="find_your_people_alias" id="find_your_people_alias" class="form-control w-50" value="{{ $find_your_people_alias->value }}">
        </div>

            <div class="form-group">
                <label for="gdpr_checkbox_label">Pages Alias</label>
                <input type="text" required class="form-control w-50" name="pages" id="pages" value="{{ $pages ? $pages : 'Pages' }}">
            </div>

            <div class="form-group my-3 card card-body">
            <div class="row align-items-center">
                <p class="font-weight-bold col-3">
                    Dashboard Button Colors
                </p>
                <small class="text-muted col-3">Click to select a color.</small>
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