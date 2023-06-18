@extends('admin.layout')

@section('page-content')

    @component('admin.partials.breadcrumbs', ['links' => [
        'Onboarding Configuration' => '/admin/system/onboarding',
        'About Me' => '',
    ]])
    @endcomponent

    <div class="d-flex justify-content-between align-items-center mb-3" style="max-width: 750px;">
        <h2 class="mb-0 pb-0">About Me</h2>
        <a href="/admin/system/onboarding/step-preview?step=about" class="btn btn-sm btn-outline-primary" onclick="openinnewwindow(this.href)" target="_blank">View step</a>
    </div>
    <hr>

    <form method="post">
        @csrf

        <div>
            <div style="max-width: 750px;">

                <div class="p-3" style="background-color: #eee; border-radius: 5px;">
                    <div class="form-check">
                        <input class="mt-1.5 image_revert form-check-input" type="checkbox" id="about[active]" value="true" name="about[active]"{{ ( !isset($settings['about']['active']) || $settings['about']['active'] == true) ? ' checked' : '' }}>
                        <label style="vertical-align: middle;" class="form-check-label" for="about[active]">
                          <b>Active</b><br>
                          <span class="text-muted">Step is included in onboarding flow when active.</span>
                        </label>
                      </div>
                </div>

                <div class="form-group my-3">
                    <label for="about[title]">Title</label>
                    <input type="text" class="form-control" name="about[title]" id="about[title]" value="{{ $settings['about']['title'] }}" required>
                </div>

                <div class="form-group my-3">
                    <label for="about[prompt]">Prompt</label>
                    <input type="text" class="form-control" name="about[prompt]" id="about[prompt]" value="{{ $settings['about']['prompt'] }}" required>
                </div>

                <div class="mb-5">
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </div>

            <div class="col-md-4 h-full">
                @if(!getSetting('is_about_me_enabled') && !getSetting('is_ask_a_mentor_enabled'))
                <div class="alert alert-danger">
                    <b>Warning!</b><br>The <i>About Me</i> prompt, and <i>Ask A Mentor</i> are disabled, so this step will not display in onboarding even if it is set to <i>active</i>.
                </div>
                @endif
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