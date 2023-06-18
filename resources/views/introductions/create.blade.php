@extends('layouts.app')

@section('stylesheets')
<style>
  .selectRecipient .dropdown-toggle:after {
    display: none;
  }
</style>
@endsection

@section('content')

  <main class="main" role="main">
    <div class="py-5 bg-lightest-brand">
      <div class="container">
        <div class="row">
          <div class="col-md-9 mx-auto">
            <a href="/introductions" class="d-inline-block mb-3" style="font-size: 14px;"><i class="icon-chevron-small-left"></i> @lang('introductions.introductions')</a>
            <div class="card">
              <div class="card-body">
                @if ($errors->any())
                  <div class="alert alert-danger">
                      <ul>
                          @foreach ($errors->all() as $error)
                              <li>{{ $error }}</li>
                          @endforeach
                      </ul>
                  </div>
                @endif
                <h4 class="card-title mb-4">@lang('introductions.new-introduction')</h4>
                
                <form method="post" action="/introductions" id="app">
                  @csrf
                  <div class="form-group">
                    @if($recipient)
                    <p><b>@lang('introductions.introduce'):</b> {{ $recipient->name }}
                    <input type="hidden" name="users[]" value="{{ $recipient->id }}" id="introduce">
                    @else
                    <div class="form-row mb-3">
                      <div class="col" style="flex: 0 1">
                        <p class="mt-1"><b>@lang('introductions.to'):</b></p>
                      </div>
                      <div class="col" style="flex: 1 0;">
                        <v-select dusk="user1" id="user1" name="user1" :options="options" v-model="sendTo"></v-select>
                      </div>
                    </div>
                    @endif
                  </div>
                  <div class="form-row mb-3">
                    <div class="col" style="flex: 0 1">
                      <p class="mt-1"><b>@lang('introductions.to'):</b></p>
                    </div>
                    <div class="col" style="flex: 1 0;">
                      <v-select style="z-index: 0;" dusk="user2" name="user2" id="user2" :options="options" v-model="selectedRecipient"></v-select>
                    </div>
                  </div>
                  <div class="form-group">
                    <label>@lang('introductions.get-things-started')</label>
                    <textarea class="form-control" rows="5" name="message" placeholder="@lang('introductions.create-prompt')"></textarea>
                  </div>
                  <button type="submit" class="btn btn-secondary" dusk="submit" @click.prevent="sendMessage" :disabled="submitButtonDisabled">@lang('general.send')</button>
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
    <script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/vue-select/2.5.1/vue-select.js"></script>
    <script>
      Vue.component('v-select', VueSelect.VueSelect);
      
      var app = new Vue({
        el: "#app",
        data: {
          submitingForm: false,
          options: {!! $recipients !!},
          selectedRecipient: '',
          sendTo: '',
        },
        created: function () {
          that = this
          $.get('/load-templates', function (response) {
            that.types = response;
          })
        },
        computed: {
          submitButtonDisabled: function () {
            return this.selectedRecipient == null || this.submitingForm;
          }
        },
        methods: {
          sendMessage: function () {
            var that = this;
            this.submitingForm = true;
            $('<input>').attr('type', 'hidden')
                        .attr('name', 'users[]')
                        .attr('value', that.selectedRecipient.value)
                        .appendTo('#app');
            if ($('#introduce').length == 0) {
              $('<input>').attr('type', 'hidden')
                        .attr('name', 'users[]')
                        .attr('value', that.sendTo.value)
                        .appendTo('#app');
            }
            $('#app').submit();
          },
          selectCreateYourOwn: function () {
            this.selectedSituation = 'custom';
          },
        },
      })
    </script>
  @endsection