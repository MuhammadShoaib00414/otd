@extends('layouts.app')

@section('content')
      @if ($errors->any())
      <div class="alert alert-danger">
        <ul>
          @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
      @endif
      <div class="col-6 my-3 mx-auto">
        <h5 class="card-title">@lang('shoutouts.new_shoutout')</h5>
      </div>
      <div class="card col-6 mx-auto">
        <div class="card-body">
          <form method="post" action="/shoutouts" id="app">
            @csrf
            <div class="form-group">
              <label>@lang('shoutouts.who_is_this_shoutout_about')</label>
              <v-select :options="options" v-model="selectedRecipient" v-on:input="change()" required id="recipient" style="z-index: 1;"></v-select>
            </div>
            <div class="form-group">
              <label>@lang('shoutouts.reason')</label>
              <textarea rows="4" class="form-control mb-3" name="reason" required></textarea>
            </div>
            <div class="mb-2" id="groupsContainer">
              <p>@lang('shoutouts.post_to_groups')</p>
              <div id="groupsInputContainer">
                <small>@lang('shoutouts.select_a_recipient').</small>
              </div>
            </div>
            <div class="form-group mb-3">
              <label>@lang('shoutouts.post_to_users_feeds') <small class="text-muted">(@lang('general.optional'))</small></label>
              <v-select :options="options" v-model="selectedRecipients" :multiple="true" id="postTo" style="z-index: 0;"></v-select>
            </div>
            <div class="d-flex justify-content-between">
              <button type="button" class="btn btn-secondary" id="submitButton">@lang('shoutouts.submit_shoutout')</button>
            </div>
          </form>
        </div>
      </div>
@endsection

  @section('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/vue-select/2.5.1/vue-select.js"></script>
    <script>
      Vue.component('v-select', VueSelect.VueSelect);

      var postToFeeds = new Vue({
        el: '#postTo',
        data: {
          options: {!! $recipients !!},
          selectedRecipients: '',
        },
        methods: {
          compile: function()
          {
            if(this.selectedRecipients.length)
            {
              this.selectedRecipients.forEach(function(user) {
                $('<input>').attr('type', 'hidden')
                          .attr('name', 'users[]')
                          .attr('value', user.value)
                          .appendTo('#app');
              });
            }
          }
        },
      });
      
      var app = new Vue({
        el: "#recipient",
        data: {
          submitingForm: false,
          options: {!! $recipients !!},
          selectedRecipient: '',
          groups: JSON.parse(`{!! json_encode($groups) !!}`),
        },
        computed: {
          submitButtonDisabled: function () {
            return this.selectedRecipient == null || this.submitingForm || this.feeling == null || this.action == null || this.reason == null || this.request == null;
          }
        },
        methods: {
          change: function() {
            if(this.selectedRecipient) {
              $('#groupsInputContainer').empty();
              var self = this;
              var hasCommonGroups = false;
              Object.entries(this.selectedRecipient.groups).forEach(([name, id]) => {
                hasCommonGroups = true;
                self.addGroup({'name': name, 'id': id});
              });
              if(!hasCommonGroups)
                $('#groupsInputContainer').append('<small> @lang('shoutouts.no_common_groups') ' + this.selectedRecipient.label + '</small>');
            }
          },
          addGroup: function(group) {
            var input = '<div class="form-check"><input type="checkbox" value="'+group.id+'" name="groups[]" id="'+group.id+'" class="form-check-input"><label for="'+group.id+'" class="form-check-label">'+group.name+'</label></div>';
            $('#groupsInputContainer').append(input);
          },
          submit: function () {
            var that = this;
            this.submitingForm = true;
            if(this.selectedRecipient)
            {
              $('<input>').attr('type', 'hidden')
                          .attr('name', 'recipient')
                          .attr('value', that.selectedRecipient.value)
                          .appendTo('#app');
              $('#app').submit();
            }
          },
          selectCreateYourOwn: function () {
            this.selectedSituation = 'custom';
          },
        },
      })

      $('#submitButton').click(function(e)
      {
        e.preventDefault();
        postToFeeds.compile();
        app.submit();
      });
    </script>
  @endsection