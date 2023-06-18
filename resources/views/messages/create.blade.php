@extends('layouts.app')

@section('stylesheets')
<link rel="stylesheet" href="https://unpkg.com/vue-multiselect@2.1.0/dist/vue-multiselect.min.css">
<style>
  .custom__tag {
    background-color: #f1f1f1;
    padding: 0.2em 0.5em;
    border-radius: 4px;
    margin-right: 0.25em;
  }

  .custom__remove {
    font-size: 20px;
    line-height: 3px;
    position: relative;
    top: 2px;
    padding-left: 0.1em;
  }

  .custom__remove:hover {
    cursor: pointer;
  }

  .multiselect__option--highlight {
    background: #ffc6be;
    color: #000;
  }

  .multiselect__option--highlight::after {
    background: #f19b8f;
    color: #000;
  }
</style>
@endsection

@section('content')

<main class="main" role="main">
  <div class="py-5 bg-lightest-brand">
    <div class="container">
      <div class="row">
        <div class="col-md-9 mx-auto">
          <div class="mb-2">
            <a href="/messages"><i class="icon-chevron-small-left"></i> @lang('messages.messages')</a>
          </div>
          <div class="card">
            <div class="card-body">
              <h4 class="card-title mb-2">@lang('messages.new-message')</h4>

              <form method="post" action="/messages" id="app" @click-prevent="sendMessage()" enctype="multipart/form-data">
                @csrf
                @if(request()->has('mentor') && request()->mentor)
                <input type="hidden" value="1" name="is_from_mentor">
                @endif
                <p class="text-muted mb-2">{{ getsetting('new_message_text', request()->user()->locale) }}</p>
                <div class="form-group">
                  <p><b>@lang('messages.to')</b>

                    <multiselect select-label="@lang('messages.Press enter to select')" v-model="selected" id="ajax" label="name" track-by="id" placeholder="@lang('messages.Type to search')" open-direction="bottom" :options="options" :multiple="true" :searchable="true" :loading="isLoading" :internal-search="false" :clear-on-select="true" :close-on-select="true" :options-limit="300" :limit="3" :max-height="600" :show-no-results="true" :hide-selected="true" @search-change="asyncFind">
                      <template slot="tag" slot-scope="{ option, remove }"><span class="custom__tag"><span>@{{ option.name }}</span><span class="custom__remove" @click="remove(option)">&times;</span></span></template>
                      <template slot="clear" slot-scope="props">
                        <div class="multiselect__clear" v-if="selected.length" @mousedown.prevent.stop="clearAll(props.search)"></div>
                      </template><span slot="noResult">@lang('messages.No elements found. Consider changing the search query.')</span>
                    </multiselect>
                  </div>
                  @if(config('app.url') == 'https://heroestoheroes.onthedotglobal.com')
                  <div class="form-group">
                    <label>@lang('messages.subject')</label>
                    <input type="text" name="subject" class="form-control border-1" placeholder="write a subject">
                  </div>
                @endif
                  <div class="form-group">
                    <label>@lang('messages.message')</label>

                  <div class="d-flex justify-content-between gap-1 border border-dark message-section">
                    <label for="attachments">
                      <i class="fa fa-paperclip attachment p-2" style="cursor:pointer" aria-hidden="true"></i>
                    </label>
                    <textarea name="message" id="message" class="form-control border-0 message-text" rows="1" placeholder="Write a message"></textarea>
                    <input type="file" style="display:none" name="attachments[]" id="attachments" @change="showThumnails" multiple>
                    <button id="sendButton" type="submit" class="btn btn-secondary" @click.prevent="sendMessage">@lang('messages.send')</button>
                  </div>
                  <div id="attachment-thumbnail" class="mt-1 row">

                  </div>
                </div>
                @if(request()->has('createIndividually'))
                <div class="form-check">
                  <input type="checkbox" class="form-check-input" name="createIndividually" id="createIndividually" checked>
                  <label class="form-check-label" for="createIndividually">Send individually</label>
                </div>
                @endif
              </form>

            </div>
          </div>
        </div>
      </div>

    </div>
  </div>
</main>
@endsection

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script src="https://unpkg.com/vue-multiselect@2.1.0"></script>
<script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script>
<script>
  Vue.component('multiselect', window.VueMultiselect.default);

  var app = new Vue({
    el: '#app',
    data: {
      @if($recipients)
      selected: [@foreach($recipients as $recipient) {
        name: '{{ $recipient->name }}',
        id: '{{ $recipient->id }}'
      }, @endforeach],
      @else
      selected: [],
      @endif
      options: [],
      isLoading: false,
      timeout: null,
    },
    methods: {
      asyncFind: function(query) {
        var vthis = this;
        clearTimeout(this.timeout);
        this.timeout = setTimeout(function() {
          this.isLoading = true;
          $.ajax({
            url: '/api/search/',
            data: {
              q: query
            },
            success: function(response) {
              vthis.isLoading = false;
              vthis.options = response;
            }
          });
        }, 100);
      },
      clearAll: function() {
        this.selected = [];
      },
      sendMessage: function() {
        var vthis = this;
        $.each(this.selected, function(index, recipient) {
          $('<input>').attr({
            type: 'hidden',
            name: 'recipients[]',
            value: recipient.id
          }).appendTo('#app');
        });
        if (this.selected.length > 0 && ($('#message').val().trim().length > 0 || document.getElementById('attachments').files.length > 0) && !$('#sendButton').disabled) {
          $('#sendButton').prop('disabled', true);
          $('#app').submit();
        }
      },
      showThumnails: function() {
        $('#attachment-thumbnail').html('');
        var html = '';
        var e = document.getElementById('attachments');
        // get all files from e 
        var files = e.files;
        for (var i = 0; i < files.length; i++) {

          var file = files[i];
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
          } else if (file.type == "image/png" || file.type == "image/jpeg" || file.type == "image/jpg") {
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
          var html = html + '<div class="p-1 col-6 col-md-2 attachment-wrapper"><div class="bg-lightest-brand attachment-box">' + imgHTML + '<span style="font-size:12px">' + shortFilename + '</span></div></div>';
        }
        $("#attachment-thumbnail").html(html);
      }
    }
  });
</script>
@endsection