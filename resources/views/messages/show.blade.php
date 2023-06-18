@extends('layouts.app')

@section('content')
<main class="main" role="main">
  <div class="pb-5 pt-3 bg-lightest-brand">
    <div class="container">
      <div class="row">
        <div class="col-md-9 mx-auto">
          <div class="mb-2">
            <a href="/messages"><i class="icon-chevron-small-left"></i> @lang('messages.messages')</a>
          </div>
          <div class="card">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-center">
               
                @if($thread->event)
                <h5 class="card-title mb-0"><a  target="_blank" href="/groups/{{ $thread->event->getGroupFromUser(request()->user()->id)->slug }}/events/{{ $thread->event->id }}">{{ $thread->event->name }}</a> <small>({{ ucfirst($thread->getReadableType()) }})</small></h5>
                @elseif($thread->latestMessage->subject || $subject != null)
                <h5 class="card-title mb-0">{{$subject->subject}}</h5>
                @else
                <h5 class="card-title mb-0">@lang('messages.message-with') {{ $thread->otherUsers->implode('name', ', ') }}</h5>
                @endif
                <a href="/messages/{{ $thread->id }}/delete"  class="text-right text-red" onclick="return confirm('Are you sure you want to delete this thread?');">@lang('messages.delete-thread')</a>
              </div>
              @foreach($thread->messagesFor(request()->user()->id) as $message)
              <div{!! ($loop->last) ? ' id="last"' : '' !!}>
                <hr class="my-3">
                <div class="d-flex">
                  <a class="d-none d-sm-block mt-1 mr-2" href="/users/{{ $message->author->id }}" style="width: 2.5em; height: 2.5em; border-radius: 50%; background-image: url('{{ $message->author->photo_path }}'); background-size: cover; background-position: center; overflow: hidden;">
                  </a>
                  <div class="ml-0 ml-xs-3" style="flex: 1; word-break: break-word;">
                    <a href="/users/{{ $message->author->id }}" class="d-block" style="color: #343a40;"><b>{{ $message->author->name }} </b></a>
                    <span class="d-block text-muted mb-3">{{ $message->created_at->format('F j, Y') }} &centerdot; {{ $message->sent_at->format('g:ia') }}</span>
                    {!! $message->formatted_body !!}
                  </div>
                </div>
                @endforeach
            </div>
            <hr>
            <div id="newMessagePrompt" class="d-none">
              <div class="alert alert-success text-center" id="reload-page" style="cursor: pointer;">You have a new message. Click here to reload the page.</div>
            </div>
            <div id="loader">
                <img src="https://media3.giphy.com/media/3oEjI6SIIHBdRxXI40/giphy.gif?cid=ecf05e47nz5seyqeu5xp0r72usry7m24bw3kq6hwc78wy9xy&rid=giphy.gif&ct=g" alt="Loading..." />
            </div>
            <div>
              <h6 class="card-title mt-4">@lang('messages.reply')</h6>
              <form method="post" onsubmit="return validate();" enctype="multipart/form-data" id="myForm">
                @csrf

                <div class="d-flex justify-content-between gap-1 border border-dark message-section">
                  <!-- <input name="" id="" cols="30" rows="1" class="w-100 form-control border-0 message-text" placeholder="Write a message"/> -->
                  <label for="attachments">
                    <i class="fa fa-paperclip attachment p-2" style="cursor:pointer" aria-hidden="true"></i>
                  </label>
                  <textarea name="message" class="form-control border-0 message-text" rows="1" placeholder="Write a message" id="message"></textarea>
                  <input type="file" style="display:none" name="attachments[]" id="attachments" multiple>
                  <!-- add button for attachments -->
                  <button id="replyButton" type="submit" class="btn btn-secondary rounded-0" >@lang('messages.send')</button>
                 
                </div>
                <div id="attachment-thumbnail" class="mt-1 row">

                </div>
               
                <br />
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- Button trigger modal -->
  <div class="modal fade px-0" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <button type="button" class="close position-absolute zindex-dropdown right-0 text-light" data-dismiss="modal" aria-label="Close" id="cross-icon">
      <span aria-hidden="true">&times;</span>
    </button>
    <div class="modal-dialog modal-dialog-centered modal-xl m-0" role="document">

      <div class="modal-content bg-transparent border-0">
        <div class="modal-body text-center p-0" id="pop-up">
          <iframe src="" frameborder="0"></iframe>
        </div>

      </div>
    </div>
  </div>
</main>
@endsection

@section('scripts')
<script>

$("#replyButton").click(function(){
  @if(((new \Jenssegers\Agent\Agent)->isMobile()))
      $("#loader").show(); 
      @endif
      window.location.reload(); 
    setInterval(function () {
      $("#loader").hide(); 
      location.replace(location.href);
    },5000);

});

  $("#reload-page").click(function(){
    @if(((new \Jenssegers\Agent\Agent)->isMobile()))
      $("#loader").show(); 
      @endif
      location.reload(true); 
    setInterval(function () {
      $("#loader").hide();
      $("#newMessagePrompt").hide();
    },5000);
 
});

  $('#replyButton').attr('disabled', true);
  $(document).ready(function() {
    $("textarea").keyup(function(e) {
      var nodeName = e.target.nodeName.toLowerCase();
      var txtName = $("#message");
      var c = $.trim(txtName.val()).length;
      var file = $("#attachment-thumbnail").html();
      var totalfile = '';
      if (file) {
        var totalfile = file.length;
      } else {
        var totalfile = 0;
      }
      if (c  == 0 && totalfile  == 0) {
    
        $('#replyButton').attr('disabled', true);
      }else if(c  > 0) {
        $('#replyButton').attr('disabled', false);
      }else if(c  == 0 && totalfile  > 18) {
      
        $('#replyButton').attr('disabled', false);
        $("#replyButton").removeClass("disabled");
      }else{
      
        $('#replyButton').attr('disabled', true);
      }  
    });
 
  });

  function validate() {

    if ($('#message').val().trim().length < 0)

      return false;
  
    $('#replyButton').attr('disabled', true);

    return true;
  }
  window.scrollTo(0, document.body.scrollHeight);

  setInterval(checkNewMessages, 5000);

  var startTime = new Date().toISOString();


  $("#attachments").change(function(e) {

    var html = '';
    for (var i = 0; i < e.originalEvent.srcElement.files.length; i++) {

      var file = e.originalEvent.srcElement.files[i];
      var fileName = file.name;
      // trim filename if too long
      var shortFilename = fileName;
      if (fileName.length > 8) {
        shortFilename = fileName.substring(0, 8) + '...' + fileName.substring(fileName.length - 5, fileName.length);
      }
      var img = document.createElement("img");
      img.className = "";
      img.style.width = '100%';
      img.style.height = "120px";
      img.style.marginRight = "10px";
      let imgHTML = '';
      if (file.type == "application/pdf") {
        img.src = "/images/pdf.png";
      } else if (file.type == "application/vnd.openxmlformats-officedocument.wordprocessingml.document") {
        img.src = "/images/doc.png";
      } else if (file.type == "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet") {
        img.src = "/images/xls.png";
      } else if (file.type == "application/vnd.openxmlformats-officedocument.presentationml.presentation") {
        img.src = "/images/ppt.png";
      } else if (file.type == "image/png" || file.type == "image/jpeg" || file.type == "image/jpg" || file.type == "image/jfif") {
        img.src = URL.createObjectURL(file);
        img.style.width = "100%";
        img.style.height = "auto";
        img.style.maxHeight = "100%";
        img.style.maxWidth = "100%";
        img.style.width = "auto";
        img.style.height = "auto";
        img.style.position = "absolute";
        img.style.top = "0";
        img.style.bottom = "0";
        img.style.left = "0";
        img.style.right = "0";
        img.style.margin = "auto";
        imgHTML = '<div style="height: 120px;position: relative;">' + img.outerHTML + '</div>';
      } else if (file.type == 'video/mp4' || file.type == 'video/quicktime' || file.type == 'video/x-msvideo' || file.type == 'video/x-ms-wmv') {
        img.src = "/images/video.png";
      } else if (file.type == 'audio/mpeg' || file.type == 'audio/mp3' || file.type == 'audio/wav') {
        img.src = "/images/audio.png";
      } else {
        img.src = "/images/file.png";
      }
      if (imgHTML == '') {
        imgHTML = img.outerHTML + '<br>';
      }
      var html = html + '<div class="p-1 col-6 col-md-2 attachment-wrapper"><div class="bg-lightest-brand attachment-box"  >' + imgHTML + '<span style="font-size:12px">' + shortFilename + '</span></div></div>';
      $('#replyButton').attr('disabled', false);
      $("#replyButton").removeClass("disabled");

    }
    $("#attachment-thumbnail").html(html);
  });

  function checkNewMessages() {
    $.ajax({
      type: "get",
      url: "/messages/{{ $thread->id }}/status/" + startTime,
      async: true,
      success: function($result) {
        if ($result) {
          $('#newMessagePrompt').removeClass('d-none');
        }
      },
    });
  }

  function getMeta(url, callback) {
    const img = new Image();
    img.src = url;
    img.onload = function() {
      callback(this.width, this.height);
    }
  }



  function showpop(event, obj_url, obj) {
    event.preventDefault();
    var videoExtArr = ['MKV', 'WEBM', 'MPG', 'MP2', 'MPEG', 'MPE', 'MPV', 'OGG', 'MP4', 'M4P', 'M4V', 'AVI', 'WMV', 'MOV', 'QT', 'FLV', 'SWF', 'AVCHD'];
    var imageExtArr = ["JPG", "JPEG", "PNG", "GIF", 'WEBP', "JFIF"];
    var pdfExtArr = ["PDF"];
    var docExtArr = ["DOC"];
    var ext = obj_url.split('.').pop().toUpperCase();

    var element = '';

    if (/Mobi/.test(navigator.userAgent)) {
      var windowheight = 'auto';
      var windowheights = (window.innerHeight) + 'px';
      var windowwidth = '100%';
    } else {
      var windowheight = (window.innerHeight) + 'px';
      var windowwidth = (window.innerWidth);
    }

    if (ext == 'PDF') {
      if (/Mobi/.test(navigator.userAgent)) {
        element = '<iframe src="' + obj_url + '" height="' + windowheights + '"  style="border:none;width:100%!important" ></iframe>';
      } else {
        element = '<iframe src="' + obj_url + '" height="' + windowheight + '"  style="border:none;width:100%!important" ></iframe>';
      }
    } else if (videoExtArr.includes(ext)) {
      element = '<video style="border:none;max-width:70%!important;padding-top:100px;" height="' + (windowheight - 200) + '"  controls> <source src="' + obj_url + '" type="video/mp4"></video>';
    } else if (imageExtArr.includes(ext)) {
      element = '<img style="height:' + windowheight + ';width:' + windowwidth + ';margin:auto;padding:10px;" src="' + obj_url + '" />';
    } else if (docExtArr == 'DOC') {

      $('a').click(function(e) {
        e.preventDefault(); //stop the browser from following
        var extension = $(this).attr('href');
        var exts = extension.split('.').pop().toUpperCase();
        if (exts == 'DOC' || exts == 'XLSM') {
          window.location.href = obj_url;
        }
      });
      return;

    }

    $('#pop-up').html(element);
    $('#exampleModal').modal('show');
    $('.popup-preview').css('background-size', '100% 100%');

  }
</script>
@endsection