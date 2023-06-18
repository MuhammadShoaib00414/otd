@extends('admin.layout')

@section('page-content')

    @component('admin.partials.breadcrumbs', ['links' => [
        'Onboarding Configuration' => '/admin/system/onboarding',
        'Completed' => '',
    ]])
    @endcomponent

    <div class="d-flex justify-content-between align-items-center mb-3" style="max-width: 750px;">
        <h2 class="mb-0 pb-0">Completed</h2>
        <a href="/admin/system/onboarding/step-preview?step=completed" class="btn btn-sm btn-outline-primary" onclick="openinnewwindow(this.href)" target="_blank">View step</a>
    </div>
    <hr>

    <form method="post">
        @csrf

        <div style="max-width: 750px;">
            <div class="p-3 mb-3" style="background-color: #eee; border-radius: 5px;">
                <div class="form-check">
                    <input class="mt-1.5 image_revert form-check-input" type="checkbox" id="groups[active]" value="true" name="groups[active]" checked disabled>
                    <label style="vertical-align: middle;" class="form-check-label" for="groups[active]">
                      <b>Active</b><br>
                    </label>
                </div>
            </div>

            <div class="form-group my-3">
                <label for="completed[header]">Title</label>
                <input type="text" class="form-control" name="completed[header]" id="completed[header]" value="{{ $settings['completed']['header'] }}" required>
            </div>

            <div class="form-group my-3">
                <label for="completed[subhead]">Subhead</label>
                <textarea type="text" class="form-control" name="completed[subhead]" id="completed[subhead]">{{ $settings['completed']['subhead'] }}</textarea>
            </div>

            <div class="form-group my-3">
                <label for="completed[button]">Button Text</label>
                <input type="text" class="form-control" name="completed[button]" id="completed[button]" value="{{ $settings['completed']['button'] }}" required>
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
        function openinnewwindow(a)
        {
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