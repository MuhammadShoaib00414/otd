@extends('groups.layout')

@section('stylesheets')
    @parent
    <link rel="stylesheet" href="/redactorx-1-2-0/redactorx.min.css" />
    <link href="https://unpkg.com/gijgo@1.9.13/css/gijgo.min.css" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/timepicker/1.3.5/jquery.timepicker.min.css">
    <style>
      input[name="responsive"] {
        display: none;
      }
      input[name="responsive"] + span {
        display: none;
      }
    </style>
    <script src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.x.x/dist/alpine.min.js" defer></script>
@endsection

@section('inner-content')
      @if ($errors->any())
          <div class="alert alert-danger">
              <ul>
                  @foreach ($errors->all() as $error)
                      <li>{{ $error }}</li>
                  @endforeach
              </ul>
          </div>
      @endif
      <div x-data="{ submitted: false }">
          <h5 class="card-title">@lang('posts.new_post')</h5>
          <form id="postForm" method="post" action="/groups/{{ $group->slug }}/posts" enctype="multipart/form-data">
            @csrf
            <textarea id="content" rows="8" class="form-control mb-3" name="content" onfocus="showTooltip();" required></textarea>
            <!-- <input class="form-control-file form-control-lg" name="photo" id="photoUpload" type="file"/> -->
            @if($group->can_group_admins_schedule_posts && $group->isUserAdmin(request()->user()->id))
              <div class="card mt-4">
                <div class="card-body">
                  <p><b>@lang('posts.schedule_to_post_on')</b> (@lang('posts.leave_empty_to_post_now'))</p>
                    <div class="form-row mb-3">
                      <div class="col">
                        <label>@lang('general.date')</label>
                        <input type="text" name="date" class="form-control" placeholder="mm/dd/yy" id="date">
                      </div>
                      <div class="col">
                        <label>@lang('general.time') <small class="text-muted">({{ request()->user()->timezone}})</small></label>
                        <input type="text" name="time" class="form-control" placeholder="hh:mm pm" id="time">
                      </div>
                    </div> 
                  </div>
              </div>
            @endif

            <div class="d-none">
              <div id="emptyLinkGroup" class="form-group">
                <div class="form-row mb-2">
                  <div class="col-2">
                    <label class="mr-1 mt-4" for="links[][title]" id="newLinkTitleLabel"><b>@lang('events.Title')</b></label>
                  </div>
                  <div class="{{ getsetting('is_localization_enabled') ? 'col-5' : 'mt-auto col' }}">
                    @if(getsetting('is_localization_enabled'))
                    <p>@lang('messages.english')</p>
                    @endif
                    <input maxlength="50" class="form-control" type="text" id="newLinkTitle">
                  </div>
                  @if(getsetting('is_localization_enabled'))
                  <div class="col-5">
                    <p>@lang('messages.spanish')</p>
                    <input maxlength="50" class="form-control" type="text" id="newLinkTitleEs">
                  </div>
                  @endif
                </div>
                <div class="form-row">
                  <div class="col-2">
                    <label for="links[][url]" id="newLinkUrlLabel"><b>@lang('general.url')</b></label>
                  </div>
                  <div class="col">
                    <input class="form-control" type="url" id="newLinkUrl">
                  </div>
                </div>
                <a href="#" class="text-small removeLinkButton">@lang('general.remove')</a>
                <hr class="my-1">
              </div>
              <hr class="my-2">
            </div>

            <div class="mt-3 mb-1" id="linksContainer">
              <div id="links">
              </div>
              <button id="addLinkButton" class="btn btn-secondary btn-sm mb-3">@lang('events.Add link')</button>
            </div>

            <div class="d-flex justify-content-between mt-2">
              @if($group->isUserAdmin(request()->user()->id))
                <div class="form-check ml-2">
                  <input type="checkbox" class="form-check-input" id="post_as_group" name="post_as_group">
                  <label class="form-check-label" for="post_as_group" style="font-size: 16px;">@lang('posts.post_as_group')</label>
                </div>
              @else
                <div></div>
              @endif
              <button id="submitButton" type="submit" class="btn btn-secondary">@lang('general.post')</button>
            </div>
          </form>
        </div>
      </div>
@endsection

@section('scripts')
  <script src="/redactorx-1-2-0/redactorx.min.js"></script>
  <script src="/assets/js/es.js"></script>
  <script src="https://unpkg.com/gijgo@1.9.13/js/gijgo.min.js" type="text/javascript"></script>
  <script src="//cdnjs.cloudflare.com/ajax/libs/timepicker/1.3.5/jquery.timepicker.min.js"></script>
  <script>
    $('#date').datepicker({
      uiLibrary: 'bootstrap4',
      format: 'mm/dd/yy'
    });
    $('#time').timepicker({
        timeFormat: 'h:mm p',
        dropdown: false,
    });
    // $(document).ready(function(){
    //   $('#photoUpload').change(function(){
    //     $('#content').removeAttr('required');
    //   });
    // });
      RedactorX('#content', {
          format: true,
          editor: {
            lang: '{{ request()->user()->locale }}',
          },
          image: {
              upload: '/user-api/image-uploader',
              multiple: false,
              url: false
          },
      });

      $('#postForm').submit(function(){
          $('#submitButton').prop('disabled', true);
      });

      $('#addLinkButton').on('click', function (e) {
        e.preventDefault();
        var numberOfLinks = $('#links').children().length;
        if(numberOfLinks >= 1)
        {
          $(this).addClass('d-none');
        }
        var newLinkGroup = $('#emptyLinkGroup').clone().appendTo('#links');
        newLinkGroup.attr('id', '');
        newLinkGroup.find('#newLinkTitle').attr('name', 'links['+numberOfLinks+'][title]').attr('id', 'links['+numberOfLinks+'][title]');
        newLinkGroup.find('#newLinkTitleEs').attr('name', 'localization[es][links]['+(numberOfLinks)+'][title]').attr('id', 'localization[es][links]['+numberOfLinks+'][title]');
        newLinkGroup.find('#newLinkTitleLabel').attr('for', 'links['+numberOfLinks+'][title]').attr('id', '');
        newLinkGroup.find('#newLinkUrl').attr('name', 'links['+numberOfLinks+'][url]').attr('id', 'links['+numberOfLinks+'][url]');;
        newLinkGroup.find('#newLinkUrlLabel').attr('for', 'links['+numberOfLinks+'][url]').attr('id', '');
      });

      $('#linksContainer').on('click', '.removeLinkButton', function (e) {
        e.preventDefault();
        $(this).parent().remove();
        $('#addLinkButton').removeClass('d-none');
      });

      $('.rx-container').prop('title', "@lang('posts.Click on the + symbol to add images, text or embed videos')").tooltip('show');

      setInterval(function () {
         $('.rx-container').tooltip('dispose');
      }, 60 * 100);
  </script>
@endsection