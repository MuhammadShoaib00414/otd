@extends('admin.layout')

@section('page-content')

@component('admin.partials.breadcrumbs', ['links' => [
'Onboarding Configuration' => '/admin/system/onboarding',
'Basic Details' => '',
]])
@endcomponent

<div class="d-flex justify-content-between align-items-center mb-3" style="max-width: 750px;">
    <h2 class="mb-0 pb-0">Basic Details</h2>
    <a href="/admin/system/onboarding/step-preview?step=basic" class="btn btn-sm btn-outline-primary" onclick="openinnewwindow(this.href)" target="_blank">View step</a>
</div>
<hr>

<form method="post">
    @csrf

    <div style="max-width: 750px;">

        <div class="p-3" style="background-color: #eee; border-radius: 5px;">
            <div class="form-check">
                <input class="mt-1.5 image_revert form-check-input" type="checkbox" id="basic[active]" value="true" name="basic[active]" {{ ( !isset($settings['basic']['active']) || $settings['basic']['active'] == true) ? ' checked' : '' }}>
                <label style="vertical-align: middle;" class="form-check-label" for="basic[active]">
                    <b>Active</b><br>
                    <span class="text-muted">Step is included in onboarding flow when active.</span>
                </label>
            </div>
        </div>

        <div class="form-group my-3">
            <label for="basic[title]">Title</label>
            <input type="text" class="form-control" name="basic[title]" id="basic[title]" value="{{ $settings['basic']['title'] }}" required>
        </div>

        <div class="form-group my-3">
            <label for="basic[prompt]">Prompt</label>
            <input type="text" class="form-control" name="basic[prompt]" id="basic[prompt]" value="{{ $settings['basic']['prompt'] }}" required>
        </div>

        <div class="form-group my-3">
            <label for="basic[prompt]">Edit Name Field Title</label>
            <input type="text" class="form-control" name="is_name_lable" id="" value="{{ $setting['is_name_lable'] }}">
        </div>

        <div class="row">
            <div class="col-md-4">
                <div class="form-group my-3">
                    <input type="checkbox" name="is_location" id="is_location" @if($setting['is_location'] == 'on') checked @endif>
                    <label for="is_location">Show Location </label>
                </label>
            </div>
            </div>
            <div class="col-md-4">
                <div class="form-group my-3">
                    <input type="checkbox" name="is_location_required" id="is_location_required" @if($setting['is_location_required'] == 'on') checked @endif>
                    <label for="is_location_required">Required Location </label>
                </label>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group my-3">
                    <input type="checkbox" name="is_gender" id="is_gender" @if($setting['is_gender'] == 'on') checked @endif>
                    <label for="is_gender">Gender Selection</label>
                </label>
                </div>
            </div>
        </div>        

    </div>

    <div class="mb-5">
        <button type="submit" class="btn btn-primary">Save</button>
    </div>

</form>

@endsection

@push('scriptstack')
<script>


    var a;

    function openinnewwindow(a) {
        window.open(a,
            'open_window',
            'menubar=no, toolbar=no, location=no, directories=no, status=no, scrollbars=no, resizable=no, dependent, width=800, height=620, left=0, top=0')
        event.preventDefault();
        return false;
    }
</script>
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