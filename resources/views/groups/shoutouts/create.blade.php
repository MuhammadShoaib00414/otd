@extends('groups.layout')

@section('inner-content')
      <h4 class="mb-2">{{ $group->name }}</h4>
      @if ($errors->any())
      <div class="alert alert-danger">
        <ul>
          @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
      @endif
      <div class="card">
        <div class="card-body">
          <h5 class="card-title">@lang('messages.new-shoutout')</h5>
          <form method="post" action="/groups/{{ $group->slug }}/shoutouts" id="app">
            @csrf
            <div class="form-group">
              <label>@lang('shoutouts.who_is_this_shoutout_about')</label>
              <v-select :options="options" v-model="selectedRecipient" required></v-select>
            </div>
            <div class="form-group">
              <label>@lang('shoutouts.reason')</label>
              <textarea rows="4" class="form-control mb-3" name="reason" required></textarea>
            </div>
            <div class="d-flex justify-content-between">
              @if($group->isUserAdmin(request()->user()->id))
                <div class="form-check">
                  <input type="checkbox" class="form-check-input" name="post_as_group">
                  <label class="form-check-label" for="post_as_group">@lang('shoutouts.post_as_group')</label>
                </div>
              @else
                <div></div>
              @endif
              <button class="btn btn-secondary" :disabled="submitButtonDisabled" @click.prevent="sendMessage">@lang('shoutouts.submit_shoutout')</button>
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
      
      var app = new Vue({
        el: "#app",
        data: {
          submitingForm: false,
          options: {!! $recipients !!},
          selectedRecipient: '',
        },
        computed: {
          submitButtonDisabled: function () {
            return this.selectedRecipient == '' || this.submitingForm;
          }
        },
        methods: {
          sendMessage: function () {
            var that = this;
            this.submitingForm = true;
            $('<input>').attr('type', 'hidden')
                        .attr('name', 'recipient')
                        .attr('value', that.selectedRecipient.value)
                        .appendTo('#app');
            $('#app').submit();
          },
          selectCreateYourOwn: function () {
            this.selectedSituation = 'custom';
          },
        },
      })
    </script>
  @endsection