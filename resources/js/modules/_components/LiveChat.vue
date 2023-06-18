<style>
    .chat-room-small-floating {
      width: 300px;
      height: auto;
      max-height: 400px;
      margin-left: 1em;
    }
    .chat-room-small-floating .message-thread-floating {
      height: 40vh;
      display: flex;
      flex-direction: column-reverse;
    }
    .chat-room-large-floating {
      height: 85vh;
      flex: 1 50%;
      width: auto;
    }
    .chat-room-small-floating .message-thread-floating {
      height: auto;
    }
    @media (max-width: 800px) {
      .chat-room-small-floating {
        max-height: 400px;
        width: 50%;
      }
    }
    .chat-room-small-inline {
      position: relative;
      width: 100%;
      max-height: 400px;
    }
    .chat-room-small-inline .message-thread-inline {
      display: flex;
      flex-direction: column-reverse;
    }
    .chat-room-large-inline {
      position: fixed;
      top: 50%;
      left: 50%;
      width: 90vw;
      height: 85vh;
      transform: translate(-50%, -50%);
      z-index: 1000;
    }
    .chat-room-small-inline .message-thread-inline {
      height: auto;
    }
</style>

<template>
  <div class="card d-flex flex-column align-items-stretch" :class="{ 'chat-room-small-floating': type == 'floating' && display != 'large', 'chat-room-large-floating': type == 'floating' && display == 'large', 'chat-room-small-inline': type == 'inline' && display != 'large', 'chat-room-large-inline': type == 'inline' && display == 'large' }" id="chatRoom" style="font-size: 14px; z-index: 1000;">
    <div class="card-header p-2 flex-shrink-0" style="background-color: #eee;">
      <div class="d-flex justify-content-between align-items-center">
        <p class="mb-0"><span class="font-weight-bold">Chat</span><span class="ml-2">{{ onlineUserCount }} Online</span></p>
        <div>
          <a href="#" v-if="display == 'small'" @click.prevent="display = 'large'"><i class="far fa-window-maximize"></i></a>
          <a href="#" v-if="display != 'closed'" @click.prevent="display = 'closed'" class="ml-1"><i class="far fa-window-close"></i></a>
          <a href="#" v-if="display == 'closed'" @click.prevent="display = 'small'" class="ml-1"><i class="fas fa-plus-square"></i></a>
        </div>
      </div>
    </div>
   

    <div id="chatContainer" class="card-body p-2 text-left message-thread scroll-left" :class="{ 'd-none': display == 'closed' }" style="height: 300px;overflow: auto;">
      <div id="chats">
        <div v-for="item in messages">
          <b>{{ item.user.name }}</b> {{ item.message }}
        </div>
      </div>
    </div>
    <div class="card-footer p-0 flex-shrink-0" :class="{ 'd-none': display == 'closed' }">
      <textarea class="form-control" aria-label="Chat room message box" placeholder="Type a message..." name="message" v-model="message" @keyup.enter="sendMessage()" style="border: 0; font-size: 14px;"></textarea>
    </div>
  </div>
</template>

<script>
  import Pusher from 'pusher-js'

    export default {
        props: ['room', 'type'],
        data() {
            return {
              messages: [],
              message: '',
              channel: null,
              display: 'closed',
              pusher: false,
            }
        },
          computed: {
            onlineUserCount() {
              if (this.channel)
                return this.channel.members.count;
              else
                return 1;
            },
          },
          methods: {
            scrollToBottom() {
              $("#chatContainer").animate({ scrollTop: $("#chatContainer")[0].scrollHeight }, 500);
              var height = 500; 
              $('#chatContainer').each(function(i, value){
              height += parseInt($(this).height());
          });
           height += '';
              $('div').animate({scrollTop: height});
            },
            sendMessage() {
              if (this.message.length > 1) {
                var messageObj = {
                  user: {
                    id: this.$user.id,
                    name: this.$user.name,
                  },
                  message: this.message,
                };
                this.channel.trigger('client-new-message', messageObj);
                this.messages.push(messageObj);
                axios.post('/chat-rooms/'+this.room.id+'/messages', {
                    '_method': 'PUT',
                    'message': this.message,
                });
              }
              this.message = '';
              this.scrollToBottom();
            },
            recieveMessage(data) {
              this.messages.push(data);
              this.scrollToBottom();
            },
            unsubscribe() {
              this.pusher.unsubscribe();
            }
          },
          created() {
            axios.get('/chat-rooms/'+this.room.id+'/messages')
                 .then((response) => {
                    this.messages = response.data;
                    this.display = 'small';
                    setTimeout(() => {
                      this.scrollToBottom();
                    }, "1000");
                 });
            Pusher.logToConsole = true;
            this.pusher = new Pusher('42467c097b855a0e2c50', {
              cluster: 'us2',
              auth: {
                headers: {
                  'X-CSRF-Token': document.head.querySelector("[name~=csrf][content]").content,
                }
              }
            });
            this.channel = this.pusher.subscribe('presence-chatroom-' + this.room.id);
            this.channel.bind('joined', (data) => {
              app.messages.push(JSON.stringify(data));
            });
            this.channel.bind('client-new-message', (data) => {
              this.recieveMessage(data);
            });
          }
    } 
</script>