@if(is_zoom_enabled())
@push('stylestack')
    @if($type == 'floating')
      <style>
        #videoConferenceContainer {
          overflow: hidden;
          display: flex;
          flex-direction: column;
          z-index: 1000;
        }

        #videoConferenceContainer .container-body {
          flex-grow: 1;
          width:  100%;
        }

        .container-body {
          display: flex;
          width: 100%;
          height: 100%;
        }

        #zoom_container{
          height: 100%;
          width: 100%;
          overflow-y: auto;
          margin: auto;
        }

        #jitsiContainer {
          display: flex;
          height: auto;
          width: 100%;
        }
        /* .video-room-small {
          width: 712px !important;
          height: 488px !important;
        } */

        .video-room-large {
          width: auto;
          flex: 1 50%;
          height: 85vh;
          z-index: 100000;
        }
     
        @media (max-width: 800px) {
            .video-room-small {
              width: 500px !important;
              height: 600px !important;
            }
            .video-room-large {
              position: absolute;
              top: 0;
              left: 0;
              width: 100%;
              height: 85vh;
            }
          }
          .spinner {
            border: 5px solid #f3f3f3;
            border-top: 5px solid {{ getThemeColors()->primary['200'] }};
            border-radius: 50%;
            width: 50px;
            height: 50px;
            margin: auto;
            animation: spin 2s linear infinite;
          }

          @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
          }

          #zoom_iframe {
            width: 100%;
            height: 100%;
            border: none;
          }

          #zoom_container{
            overflow-y: scroll;
            padding: 0;
            height: 100%;
            width: 100%;
          }
      </style>
    @elseif($type == 'inline')
      <style>
        #videoConferenceContainer {
          overflow: hidden;
          display: flex;
          flex-direction: column;
          margin-bottom: 1em;
        }

        #videoConferenceContainer .container-body {
          flex-grow: 1;
        }

        .container-body {
          display: flex;
          width: 100%;
        }

        #jitsiContainer {
          display: flex;
          height: auto;
          width: 100%;
        }

        .video-room-small {
          width: 100%;
          height: 600px;
        }

        .video-room-large {
          position: fixed;
          top: 1em;
          left: 50%;
          width: 90vw;
          transform: translateX(-50%);
          height: 85vh;
          z-index: 100000;
        }
      </style>
    @else
    <style>
        #videoConferenceContainer {
          overflow: hidden;
          display: flex;
          flex-direction: column;
          margin-bottom: 1em;
        }

        #videoConferenceContainer .container-body {
          flex-grow: 1;
        }

        .container-body {
          display: flex;
          background-color: white;
          width: 100%;
          height: 100%;
        }

        #jitsiContainer {
          display: flex;
          height: auto;
          width: 100%;
        }

        .video-room-small {
          width: 100%;
          height: 80vh;
        }

        .video-room-large {
          position: fixed;
          top: 1em;
          left: 50%;
          width: 90vw;
          transform: translateX(-50%);
          height: 85vh;
          z-index: 100000;
        }
      </style>
    @endif
@endpush


<div id="videoConferenceContainer" class="card">
  <div class="card-header p-2 flex-shrink-0" style="background-color: #eee;">
    <div class="d-flex justify-content-between align-items-center">
      <p class="mb-0"><span class="font-weight-bold">@lang('general.video_chat')</span></p>
      <a href="{{ $group->zoom_invite_link }}" id="joinInZoomApp" target="_blank" class="btn btn-primary btn-sm d-none">Join on zoom app</a>
      <div>
        <a href="#" id="videoRoomMaximizeButton"><i class="far fa-window-maximize"></i></a>
        @if(!isset($hideCloseButton) || !$hideCloseButton)
        <a href="#" class="ml-1" id="videoRoomCloseButton"><i class="far fa-window-close"></i></a>
        @endif
        <a href="#" id="videoRoomOpenButton" class="d-none ml-1"><i class="fas fa-plus-square"></i></a>
      </div>
    </div>
  </div>
  <div class="container-body card-body d-flex justify-content-center align-items-center p-0 text-size">
    <button id="join_zoom_meeting_button" class="btn btn-primary w-100 mb-2" onclick="changePostion()" style="font-size:15px;">@lang('events.Join zoom meeting')</button>
    <div id="zoom_container" class="d-none" style="width: 100%; height: 100%">
      <div class="spinner" id="zoom_loading_spinner"></div>
        <iframe id="zoom_iframe" src="" class="d-none h-100 w-100 b-0">
        </iframe>
      </div>
    </div>
</div>

@push('scriptstack')
<script>

$('#join_zoom_meeting_button').click(function() {
    if(!$('#zoom_iframe').attr('src'))
    {
      $('#zoom_iframe').attr('src', '/zoom/{{ $group->zoom_meeting_id }}?pwd={{ $group->zoom_meeting_password }}');
      $(this).addClass('d-none');
      $('#videoConferenceContainer').addClass('video-room-small');
      $('#zoom_container').removeClass('d-none');
      $('#joinInZoomApp').removeClass('d-none');
      addEventListener("beforeunload", beforeUnloadListener, {capture: true});
    }
  }); 

  const beforeUnloadListener = (event) => {
    event.preventDefault();
    return event.returnValue = "Are you sure you want to exit?";
  };

  $('#changeSize').click(function(e) {
    e.preventDefault();
    $('#zoomModal').toggleClass('zoom-sm');
  });

  $('#zoom_iframe').on('load', function() {
    if($('#zoom_loading_spinner').hasClass('d-none'))
    {
      removeEventListener("beforeunload", beforeUnloadListener, {capture: true});
      window.location.reload();
    }
    $('#zoom_loading_spinner').addClass('d-none');
    $('#zoom_iframe').removeClass('d-none');
    $('#zoom_iframe').addClass('d-block');
  });

   $('#videoRoomMaximizeButton').on('click', function (e) {
      e.preventDefault();
      $('#videoConferenceContainer').toggleClass('video-room-small').toggleClass('video-room-large');
    })
</script>
@endpush
@endif



