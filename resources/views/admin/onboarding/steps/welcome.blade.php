@extends('admin.layout')

@section('page-content')

    @component('admin.partials.breadcrumbs', ['links' => [
        'Onboarding Configuration' => '/admin/system/onboarding',
        'Welcome Message' => '',
    ]])
    @endcomponent

    <div class="d-flex justify-content-between align-items-center" style="max-width: 700px;">
        <h2 class="mb-0 pb-0">Welcome Message</h2>
        <a href="/admin/system/onboarding/step-preview?step=welcome" class="btn btn-sm btn-outline-primary" onclick="openinnewwindow(this.href)" target="_blank">View step</a>
    </div>
    <hr>

    <form method="post">
        @csrf

        <div style="max-width: 700px;">
            <div class="p-3" style="background-color: #eee; border-radius: 5px;">
                <div class="form-check">
                    <input class="mt-1.5 image_revert form-check-input" type="checkbox" id="intro[inactive]" value="true" name="intro[active]"{{ ( !isset($settings['intro']['active']) || $settings['intro']['active'] == true) ? ' checked' : '' }}>
                    <label style="vertical-align: middle;" class="form-check-label" for="intro[inactive]">
                      <b>Active</b><br>
                      <span class="text-muted">Step is included in onboarding flow when active.</span>
                    </label>
                  </div>
            </div>

            <div class="form-group my-3">
                <label for="intro[title]">Title</label>
                <input type="text" class="form-control" name="intro[title]" id="intro[title]" value="{{ $settings['intro']['title'] }}" required>
            </div>

            <div class="form-group my-3">
                <label for="intro[prompt]">Prompt</label>
                <input type="text" class="form-control" name="intro[prompt]" id="intro[prompt]" value="{{ $settings['intro']['prompt'] }}" required>
            </div>

            <div class="form-group my-3">
                <label for="intro[description]">Description</label>
                <textarea type="text" class="form-control" name="intro[description]" id="intro[description]" required>{{ $settings['intro']['description'] }}</textarea>
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