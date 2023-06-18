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
  <a href="/groups/{{ $group->slug }}/sequence/reminders" class="d-inline-block mb-2 text-sm">
    <i class="icon-chevron-small-left"></i> <span>Back to reminders</span>
  </a>

  <div class="d-flex justify-content-between align-items-center mb-2">
    <h3 class="font-weight-bold mb-0">New Sequence Reminder</h3>
  </div>
  @if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
  
    <form id="newreminderform" method="post" action="/groups/{{ $group->slug }}/sequence/reminders">
        @csrf
         <div class="card">
            <div class="card-body">
                <div class="d-none">
                    <textarea name="html" id="html"></textarea>
                    <textarea name="template" id="template"></textarea>
                </div>
                <div class="form-group">
                    <label for="email_subject">@lang('campaigns.Email Subject')</label>
                    <input type="text" name="email_subject" id="email_subject" class="form-control" required>
                </div>
                <div class="d-flex justify-content-start">
                  <div style="max-width: 200px;">
                    <label for="send_after_days">Send reminder after</label>
                    <div class="input-group mb-3">
                      <input type="text" class="form-control" name="send_after_days" id="send_after_days" value="5" required>
                      <div class="input-group-append">
                        <span class="input-group-text" id="basic-addon2">days</span>
                      </div>
                    </div>
                  </div>
                  <div class="ml-4 pt-4">
                    <div class="custom-control custom-checkbox">
                      <input type="checkbox" class="custom-control-input" id="is_enabled" name="is_enabled"  checked>
                      <label class="custom-control-label" for="is_enabled">Enabled</label>
                    </div>
                  </div>
                </div>
            </div>
            <div class="form-group">
                <div id="emailbody"></div>
            </div>
        </div>
        <div class="form-group text-right">
            <button id="saveButton" class="btn btn-lg btn-primary px-5">@lang('general.save')</button>
        </div>
    </form>
@endsection

@section('scripts')
    <script src="/revolvapp-2-3-2/revolvapp.min.js"></script>
    <script>
        emailEditor = Revolvapp('#emailbody', {
            editor: {
                path: '/revolvapp-1_0_7/',
                template: '/email-template/',
            },
            image: {
                upload: '/groups/image-uploader/'
            }
        });

        $('#newreminderform').on('submit', function (event) {
            $('#saveButton').prop('disabled', true);

            var html = emailEditor.editor.getHtml();
            var template = emailEditor.editor.getTemplate();

            $('#html').val(html);
            $('#template').val(template);
        });
    </script>
@endsection