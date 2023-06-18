@push('stylestack')
    @if($type == 'floating')
        <style>
          .chat-room-small {
            width: 300px;
            height: auto;
            max-height: 100%;
            margin-left: 1em;
          }
          .chat-room-small .message-thread {
            height: 40vh;
            display: flex;
            flex-direction: column-reverse;
          }
          .chat-room-large {
            height: 85vh;
            flex: 1 50%;
            width: auto;
          }
          .chat-room-small .message-thread {
            height: auto;
          }
          @media (max-width: 800px) {
            .chat-room-small {
              max-height: 400px;
              width: 50%;
            }
          }
        </style>
    @endif
    @if($type == 'inline')
        <style>
          .chat-room-small {
            position: relative;
            width: 100%;
          }
          .chat-room-small .message-thread {
            display: flex;
            flex-direction: column-reverse;
          }
          .chat-room-large {
            position: fixed;
            top: 50%;
            left: 50%;
            width: 90vw;
            height: 85vh;
            transform: translate(-50%, -50%);
            z-index: 1000;
          }
          .chat-room-small .message-thread {
            height: auto;
          }
        </style>
    @endif
@endpush


<div class="card d-flex flex-column align-items-stretch" :class="{ 'chat-room-small': display != 'large', 'chat-room-large': display == 'large' }" id="chatRoom" style="font-size: 14px; z-index: 1000;">
  <div class="card-header p-2 flex-shrink-0" style="background-color: #eee;">
    <div class="d-flex justify-content-between align-items-center">
      <p class="mb-0"><span class="font-weight-bold">@lang('general.chat')</span><span class="ml-2">@{{ onlineUserCount }} @lang('general.online')</span></p>
      <div>
        <a href="#" v-if="display == 'small'" @click.prevent="display = 'large'"><i class="far fa-window-maximize"></i></a>
        <a href="#" v-if="display != 'closed'" @click.prevent="display = 'closed'" class="ml-1"><i class="far fa-window-close"></i></a>
        <a href="#" v-if="display == 'closed'" @click.prevent="display = 'small'" class="ml-1"><i class="fas fa-plus-square"></i></a>
      </div>
    </div>
  </div>
  <div class="card-body p-2 text-left message-thread" :class="{ 'd-none': display == 'closed' }" style="overflow-y: scroll;min-height: 200px;background: white;max-height: 200px;">
    <div>
      <div v-for="item in messages" v-linkified>
        <b>@{{ item.user.name }}</b> @{{ item.message }}
      </div>
    </div>
  </div>
  <div class="card-footer p-0 flex-shrink-0" :class="{ 'd-none': display == 'closed' }">
    <textarea class="form-control" aria-label="Chat room message box" placeholder="Type a message..." name="message" v-model="message" @keyup.enter="sendMessage()" style="border: 0; font-size: 14px;"></textarea>
  </div>
</div>


@push('scriptstack')
  <script src="https://js.pusher.com/7.0/pusher.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/linkifyjs@2.1.6/dist/linkify.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/linkifyjs@2.1.6/dist/linkify-element.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script>
  <script>
    window.Vue.directive('linkified', function (el, binding) {
      linkifyElement(el, binding.value);
    });

    var chatRoom = new Vue({
      el: '#chatRoom',
      data: {
        messages: [],
        message: '',
        channel: null,
        display: 'closed',
        pusher: false,
      },
      computed: {
        onlineUserCount: function() {
          if (this.channel)
            return this.channel.members.count;
          else
            return 1;
        },
      },
      methods: {
        sendMessage: function () {
          vthis = this;
          if (vthis.message.length > 1) {
            var messageObj = {
              user: {
                id: '{{ request()->user()->id }}',
                name: '{{ request()->user()->name }}',
              },
              message: vthis.message,
            };
            vthis.channel.trigger('client-new-message', messageObj);
            vthis.messages.push(messageObj);
            $.ajax({
              url: '/chat-rooms/{{ $room->id }}/messages',
              method: 'post',
              data: {
                '_token': '{{ csrf_token() }}',
                '_method': 'PUT',
                'message': vthis.message,
              }
            });
          }
          vthis.message = '';
        },
        recieveMessage: function (data) {
          vthis.messages.push(data);
        },
        unsubscribe: function() {
          vthis.pusher.unsubscribe();
        }
      },
      created: function () {
        vthis = this;
        $.ajax({
            url: '/chat-rooms/{{ $room->id }}/messages',
            method: 'get',
            success: function (data) {
              vthis.messages = data;
              vthis.display = 'small';
            }
          });
        Pusher.logToConsole = true;
        var pusher = new Pusher('42467c097b855a0e2c50', {
          cluster: 'us2',
          auth: {
            headers: {
              'X-CSRF-Token': "{{ csrf_token() }}"
            }
          }
        });
        this.pusher = pusher;
        this.channel = pusher.subscribe('presence-chatroom-{{ $room->id }}');
        this.channel.bind('joined', function(data) {
          app.messages.push(JSON.stringify(data));
        });
        this.channel.bind('client-new-message', function (data) {
          vthis.recieveMessage(data);
        });
      }
    });

    $(window).bind('beforeunload', function(){
      
    });
  </script>
@endpush