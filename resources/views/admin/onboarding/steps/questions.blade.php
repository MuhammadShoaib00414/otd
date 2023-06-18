@extends('admin.layout')

@section('page-content')

    @component('admin.partials.breadcrumbs', ['links' => [
        'Onboarding Configuration' => '/admin/system/onboarding',
        'Profile Questions' => '',
    ]])
    @endcomponent

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="mb-0 pb-0">Profile Questions</h2>
        <a href="/admin/system/onboarding/step-preview?step=questions" class="btn btn-sm btn-outline-primary" onclick="openinnewwindow(this.href)" target="_blank">View step</a>
    </div>
    <hr>

    <form method="post">
        @csrf

        <div class="row">
            <div class="col-12 col-md-8">

                <div class="p-3" style="background-color: #eee; border-radius: 5px;">
                    <div class="form-check">
                        <input class="mt-1.5 image_revert form-check-input" type="checkbox" id="questions[active]" value="true" name="questions[active]"{{ ( !isset($settings['questions']['active']) || $settings['questions']['active'] == true) ? ' checked' : '' }}>
                        <label style="vertical-align: middle;" class="form-check-label" for="questions[active]">
                          <b>Active</b><br>
                          <span class="text-muted">Step is included in onboarding flow when active.</span>
                        </label>
                    </div>
                </div>

                <div class="form-group my-3">
                    <label for="questions[prompt]">Title</label>
                    <input type="text" class="form-control" name="questions[prompt]" id="questions[prompt]" value="{{ $settings['questions']['prompt'] }}" required>
                </div>

                <div class="form-group my-3">
                    <label for="questions[description]">Description</label>
                    <textarea type="text" class="form-control" name="questions[description]" id="questions[description]">{{ $settings['questions']['description'] }}</textarea>
                </div>

                <div class="mb-5">
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </div>

            <div class="col-12 col-md-4 h-full">
                @if(App\Question::enabled()->topLevel()->orderBy('order_key', 'asc')->count() == 0)
                <div class="alert alert-danger">
                    <b>Warning!</b><br>There are 0 custom profile questions enabled, so even if this step is set to active on this page, the profile questions step will not display in onboarding.
                </div>
                @endif

                <div class="card h-100">
                    <div class="card-body pb-0">
                        <p><b>Edit Profile Questions</b></p>
                        <p>To edit the profile questions users can answer in onboarding and when editing their profile, visit <a href="/admin/questions" style="text-decoration: underline;">Profile Questions Configuration</a>.</p>
                    </div>
                </div>
            </div>
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