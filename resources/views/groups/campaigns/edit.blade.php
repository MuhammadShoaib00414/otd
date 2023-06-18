@extends('groups.layout')

@section('stylesheets')
    @parent
    <style>
    .hover-hand:hover { cursor: pointer; }
    </style>
    <link rel="stylesheet" href="/revolvapp-2-3-2/css/revolvapp.min.css" />
@endsection

@section('body-class', 'col-md-9')

@section('inner-content')
    <div class="d-flex mb-3 justify-content-between align-items-center">
        <h4 class="mb-0">@lang('campaigns.Edit Campaign')</h4>
    </div>

    <form id="campaignform" method="post" action="/groups/{{ $group->slug }}/email-campaigns/{{ $campaign->id }}">
        @csrf
        @method('put')
         <div class="card">
            <div class="card-body">
                <div class="d-none">
                    <textarea name="html" id="html"></textarea>
                    <textarea name="template" id="template"></textarea>
                </div>
                <div class="form-group">
                    <label for="email_subject">@lang('campaigns.Email Subject')</label>
                    <input type="text" name="email_subject" id="email_subject" class="form-control" value="{{ $campaign->email_subject }}">
                </div>
                <div class="form-group">
                    <label for="reply_to_email">Reply to Email <small class="text-muted">(optional)</small></label>
                    <input type="text" name="reply_to_email" id="reply_to_email" class="form-control" value="{{ $campaign->reply_to_email }}">
                </div>
            </div>
            <div class="form-group">
                <div id="emailbody"></div>
            </div>
        </div>
        <div class="form-group text-right">
            <button id="saveButton" class="btn btn-lg btn-primary px-5">@lang('general.save_changes')</button>
        </div>
    </form>
@endsection

@section('scripts')
    <script src="/revolvapp-2-3-2/revolvapp.min.js"></script>
    <script>
        emailEditor = Revolvapp('#emailbody', {
            editor: {
                path: '/revolvapp-1_0_7/',
                template: '/groups/{{ $group->slug }}/email-campaigns/{{ $campaign->id }}/template',
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

            $('#campaignform').submit();
        });
    </script>
@endsection