@extends('admin.layout')

@push('scriptstack')
    <link rel="stylesheet" href="/revolvapp-2-3-2/css/revolvapp.min.css" />
@endpush

@section('page-content')
    @component('admin.partials.breadcrumbs', ['links' => [
        'Email Notifications' => '/admin/emails/notifications',
        $email->name => '/admin/emails/notifications/'.$email->id,
        'Edit' => '',
    ]])
    @endcomponent

    <div class="d-flex align-items-center mb-4">
        <h4 class=" mr-4">Edit <i>{{ $email->name }}</i> Email {{ request()->locale == 'es' ? '(spanish)' : '' }}</h4>
        @if(!request()->has('locale') || request()->locale == 'en')
            <a href="/admin/emails/notifications/{{ $email->id }}/edit?locale=es" class="btn btn-sm btn-outline-primary">Switch to Spanish</a>
        @else
            <a href="/admin/emails/notifications/{{ $email->id }}/edit?locale=en" class="btn btn-sm btn-outline-primary">Switch to English</a>
        @endif
    </div>

    <div class="row">
        <div class="col-12 col-sm-8">
            <form id="emailForm" action="/admin/emails/notifications/{{ $email->id }}" method="post">
                @csrf
                @method('post')
                @if(request()->has('locale'))
                    <input type="hidden" name="locale" value="{{ request()->locale }}">
                @endif  
                <div class="d-none">
                    <textarea name="html" id="html"></textarea>
                    <textarea name="template" id="template"></textarea>
                </div>
                <div class="form-check mb-3">
                  <input class="form-check-input" type="checkbox" name="enabled" id="enabled" @if($email->is_enabled) checked @endif>
                  <label class="form-check-label" for="enabled">
                    Is Enabled
                  </label>
                </div>
                <div class="form-group">
                    <label for="subject">Subject</label>
                    <input type="text" id="subject" name="subject" class="form-control" value="{{ $email->subjectWithLocale(request()->locale) }}">
                </div>
                <div class="form-group">
                    <div id="emailbody"></div>
                </div>
                <button type="submit" class="mb-4 btn btn-primary" id="saveButton">@lang('general.save') changes</button>
            </form>
        </div>
        <div class="col-12 col-sm-4">
            <p><b>Message variables</b></p>
            <p>You can use the following variables in your email subject and body.</p>
            <table class="table">
                <tr>
                    <td><b>Variable</b></td>
                    <td><b>Maps to</b></td>
                </tr>
                @if(!Request::is('*/8/*'))
                <tr>
                    <td>@name</td>
                    <td>Recipient's name</td>
                </tr>
                @endif
                @if(Request::is('*/14/*'))
                <tr>
                    <td>@notifications</td>
                    <td>Notification feed</td>
                </tr>
                @endif
                <tr>
                    <td>@email</td>
                    <td>Recipient's email</td>
                </tr>
                <tr>
                    <td>@cta</td>
                    <td>URL of CTA of email</td>
                </tr>
                @if(Request::is('*/7/*'))
                <tr>
                    <td>@groupName</td>
                    <td>Name of the reported post's group</td>
                </tr>
                <tr>
                    <td>@reportedBy</td>
                    <td>Name of the user who reported the post</td>
                </tr>
                @elseif(Request::is('*/8/*'))
                <tr>
                    <td>@custom_message</td>
                    <td>Message from the invitation</td>
                </tr>
                @endif
            </table>
        </div>
    </div>
@endsection

@push('scriptstack')
    <script src="/revolvapp-2-3-2/revolvapp.min.js"></script>
    <script>
        emailEditor = Revolvapp('#emailbody', {
            editor: {
                path: '/revolvapp-1_0_7/',
                template: '/admin/emails/notifications/{{ $email->id }}/template{{ request()->has("locale") ? "?locale=".request()->locale : "" }}',
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

            $('#emailForm').submit();
        });
    </script>
@endpush