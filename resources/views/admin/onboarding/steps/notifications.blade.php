@extends('admin.layout')

@section('page-content')

    @component('admin.partials.breadcrumbs', ['links' => [
        'Onboarding Configuration' => '/admin/system/onboarding',
        'Notifications' => '',
    ]])
    @endcomponent

    <div class="d-flex justify-content-between align-items-center mb-3" style="max-width: 750px;">
        <h2 class="mb-0 pb-0">Notifications</h2>
        <a href="/admin/system/onboarding/step-preview?step=notifications" class="btn btn-sm btn-outline-primary" onclick="openinnewwindow(this.href)" target="_blank">View step</a>
    </div>
    <hr>

    <form method="post">
        @csrf

        <div style="max-width: 750px;">
            <div class="p-3 mb-3" style="background-color: #eee; border-radius: 5px;">
                <div class="form-check">
                    <input class="mt-1.5 image_revert form-check-input" type="checkbox" id="notifications[active]" value="true" name="notifications[active]"{{ ( !isset($settings['notifications']['active']) || $settings['notifications']['active'] == true) ? ' checked' : '' }}>
                    <label style="vertical-align: middle;" class="form-check-label" for="notifications[active]">
                      <b>Active</b><br>
                      <span class="text-muted">Step is included in onboarding flow when active.</span>
                    </label>
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