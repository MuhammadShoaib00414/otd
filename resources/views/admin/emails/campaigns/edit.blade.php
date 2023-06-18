@extends('admin.layout')


@push('stylestack')
<link rel="stylesheet" href="/revolvapp-2-3-2/css/revolvapp.min.css" />
<style>
.rex-toolbar-container.rex-toolbar-sticky{
  z-index: 0 !important;
}
</style>
@endpush

@section('page-content')
    @component('admin.partials.breadcrumbs', ['links' => [
        'Email Campaigns' => '/admin/emails/campaigns',
        $campaign->email_subject => '/admin/emails/campaigns/' . $campaign->id,
        'Edit' => '',
    ]])
    @endcomponent
<div>
    <h5>Edit Campaign</h5>
    <hr>
    <div class="row">
        <div class="col-md-10 mb-5">
            <form id="form" method="post" action="/admin/emails/campaigns/{{ $campaign->id }}" enctype="multipart/form-data">
                {{ csrf_field() }}
                <div class="d-none">
                    <textarea name="html" id="html"></textarea>
                    <textarea name="template" id="template"></textarea>
                </div>
                <div class="form-group">
                    <label for="email_subject">Email Subject</label>
                    <input type="text" name="email_subject" id="email_subject" value="{{ $campaign->email_subject }}" class="form-control">
                </div>
                <div class="form-group">
                    <div id="emailbody"></div>
                </div>
                <div class="form-group">
                    <label for="reply_to_email">Reply to Email <small class="text-muted">(optional)</small></label>
                    <input type="text" name="reply_to_email" id="reply_to_email" class="form-control" value="{{ $campaign->reply_to_email }}">
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
                template: '/admin/emails/campaigns/{{ $campaign->id }}/template',
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

            $('#form').submit();
        });
    </script>
@endsection