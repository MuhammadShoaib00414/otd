@extends('admin.layout')

@push('stylestack')
    <link rel="stylesheet" href="/revolvapp-2-3-2/css/revolvapp.min.css" />
@endpush

@section('page-content')
    @component('admin.partials.breadcrumbs', ['links' => [
        'Onboarding Emails' => '/admin/emails/welcome',
        $email->email_subject => '/admin/emails/welcome/'.$email->id,
        'Edit' => '',
    ]])
    @endcomponent

<div>
    <div class="d-flex justify-content-between align-items-center">
        <h5>Edit Welcome Email</h5>
        <form action="/admin/emails/welcome/{{ $email->id }}" method="post">
          @method('delete')
          @csrf
          <button type="submit" class="btn btn-sm btn-light mr-2" id="deleteEmail">Delete email</button>
      </form>
    </div>
    <hr>
    <div class="row">
        <div class="col-md-10 mb-5">
            @if ($errors->any())
                <div class="alert alert-danger mb-3">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
           @endif
            <form method="post" action="/admin/emails/welcome/{{ $email->id }}" id="editForm">
                {{ csrf_field() }}
                <div class="d-none">
                    <textarea name="html" id="html"></textarea>
                    <textarea name="template" id="template"></textarea>
                </div>
                <div class="form-group">
                    <label for="email_subject">Email Subject</label>
                    <input type="text" name="email_subject" id="email_subject" value="{{ $email->email_subject }}" class="form-control" required>
                </div>
                <div class="form-group mb-1">
                    <label for="send_after_days">
                        Send <input type="text" name="send_after_days" id="send_after_days" value="{{ $email->send_after_days }}" required class="form-control" style="display: inline-block; width: 3em; text-align: center;"> days after a user joins
                    </label>
                </div>
                <div class="form-group form-check">
                    <input type="checkbox" class="form-check-input" id="enabled" name="enabled" {{ ($email->enabled) ? 'checked' : '' }}>
                    <label class="form-check-label" for="enabled" name="enabled">Enabled</label>
                </div>
                <div class="form-group">
                    <div id="emailbody"></div>
                </div>
                <div class="form-group text-right">
                    <button id="saveButton" class="btn btn-lg btn-primary px-5">@lang('general.save') changes</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
    <script src="/revolvapp-2-3-2/revolvapp.min.js"></script>
    <script>
        emailEditor = Revolvapp('#emailbody', {
            editor: {
                path: '/revolvapp-1_0_7/',
                template: '/admin/emails/welcome/{{ $email->id }}/template',
            },
            image: {
                upload: '/admin/image-uploader/'
            }
        });

        $('#saveButton').on('click', function (event) {
            event.preventDefault();
            $('#saveButton').prop('disabled', true);

            var html = emailEditor.editor.getHtml();
            var template = emailEditor.editor.getTemplate();

            $('#html').val(html);
            $('#template').val(template);

            $('#editForm').submit();
        });

        $('#deleteEmail').on('click', function(event) {
          event.preventDefault();
          if (confirm('Delete this email?'))
            $('#deleteEmail').parent().submit();
        });
  </script>
@endsection