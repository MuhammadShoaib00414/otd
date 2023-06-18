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

    <h5>Feature Dashboard Settings</h5>

    <hr>

    <form method="post" action="/admin/update-dashboard-settings">
        @csrf
        <div class="form-group my-3 card card-body">
            <div class="row align-items-center">
                <p class="font-weight-bold col-3">
                    Dashboard Button Colors
                </p>
                <small class="text-muted col-3">Click to select a color.</small>
            </div>

            <div class="form-row mb-4">
                <div class="col-3">
                    <label for="admin_primary_color">Primary Color</label>
                </div>
                <div class="col-3">
                    <input type="color" class="form-control form-control-sm select-color " name="admin_primary_color" value="{{ ($admin_primary_color->value ?? '' ) }}">
                </div>
            </div>
            <div class="form-row mb-4">
                <div class="col-3">
                    <label for="admin_btn_secondary">Secondary Color</label>
                </div>
                <div class="col-3">
                    <input type="color" class="form-control form-control-sm select-color" name="admin_btn_secondary" value="{{ ($admin_btn_secondary->value ?? '') }}">
                </div>
            </div>
            <div class="form-row mb-4">
                <div class="col-3">
                    <label for="admin_btn_success">Success Color</label>
                </div>
                <div class="col-3">
                    <input type="color" class="form-control form-control-sm select-color" name="admin_btn_success" value="{{ ($admin_btn_success->value ?? '') }}">
                </div>
            </div>
            <div class="form-row mb-4">
                <div class="col-3">
                    <label for="admin_btn_danger">Danger Color</label>
                </div>
                <div class="col-3">
                    <input type="color" class="form-control form-control-sm select-color" name="admin_btn_danger" value="{{ ($admin_btn_danger->value ?? '') }}">
                </div>
            </div>
            <div class="form-row mb-4">
                <div class="col-3">
                    <label for="admin_btn_warning">Warning Color</label>
                </div>
                <div class="col-3">
                    <input type="color" class="form-control form-control-sm select-color" name="admin_btn_warning" value="{{ ($admin_btn_warning->value ?? '') }}">
                </div>
            </div>
            <div class="form-row mb-4">
                <div class="col-3">
                    <label for="admin_btn_info">Info Color</label>
                </div>
                <div class="col-3">
                    <input type="color" class="form-control form-control-sm select-color" name="admin_btn_info" value="{{ ($admin_btn_info->value ?? '') }}">
                </div>
            </div>
            <div class="form-row mb-4">
                <div class="col-3">
                    <label for="admin_btn_light">Light Color</label>
                </div>
                <div class="col-3">
                    <input type="color" class="form-control form-control-sm select-color" name="admin_btn_light" value="{{ ($admin_btn_light->value ?? '') }}">
                </div>
            </div>
            <div class="form-row mb-4">
                <div class="col-3">
                    <label for="admin_btn_dark">Dark Color</label>
                </div>
                <div class="col-3">
                    <input type="color" class="form-control form-control-sm select-color" name="admin_btn_dark" value="{{ ($admin_btn_dark->value ?? '') }}">
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