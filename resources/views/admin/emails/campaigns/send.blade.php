@extends('admin.layout')

@push('stylestack')
    <link rel="stylesheet" href="/revolvapp-1_0_7/css/revolvapp.min.css" />
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
@endpush

@section('page-content')
    @component('admin.partials.breadcrumbs', ['links' => [
        'Email Campaigns' => '/admin/emails/campaigns',
        $campaign->email_subject => '/admin/emails/campaigns/' . $campaign->id,
        'Send Now' => '',
    ]])
    @endcomponent
<div>
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h5 class="mr-3 mb-0">{{ $campaign->email_subject }}</h5>
        </div>
    </div>
    <hr>
    <div class="row">
        <div class="col-md-6 mb-5">
            @if ($errors->any())
              <div class="alert alert-danger">
                  @foreach ($errors->all() as $error)
                      <div>{{ $error }}</div>
                  @endforeach
              </div>
          @endif
            <form method="post" action="/admin/emails/campaigns/{{ $campaign->id }}/review" id="app">
                @csrf
                @method('post')
                <div class="form-group">
                    <label>Send to group(s)</label>
                    <select class="custom-select" name="groups[]" multiple style="height: 10em;" required>
                      @foreach(App\Group::whereNull('parent_group_id')->orderBy('name', 'asc')->get() as $group)
                        <option value="{{ $group->id }}">{{ $group->name }}</option>
                        @foreach($group->subgroups as $subgroup)
                          <option value="{{ $subgroup->id }}">- {{ $subgroup->name }}</option>
                        @endforeach
                      @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>Send to users(s)</label>
                    <multiselect v-model="selected" id="ajax" label="name" track-by="id" placeholder="Type to search" open-direction="bottom" :options="options" :multiple="true" :searchable="true" :loading="isLoading" :internal-search="false" :clear-on-select="true" :close-on-select="true" :options-limit="300" :limit="3" :max-height="600" :show-no-results="true" :hide-selected="true" @search-change="asyncFind">
                      <template slot="tag" slot-scope="{ option, remove }"><span class="custom__tag"><span>@{{ option.name }}</span><span class="custom__remove" @click="remove(option)">&times;</span></span></template>
                      <template slot="clear" slot-scope="props">
                        <div class="multiselect__clear" v-if="selected.length" @mousedown.prevent.stop="clearAll(props.search)"></div>
                      </template><span slot="noResult">No elements found. Consider changing the search query.</span>
                    </multiselect>
                </div>
                <hr>
                <div class="mt-3 text-right">
                    <button type="submit" @click.prevent="submitForm()" class="btn btn-primary">Review <i class="fas fa-angle-right ml-1"></i></button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
    <script src="https://unpkg.com/vue-multiselect@2.1.0"></script>
    <script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script>
    <script>
      Vue.component('multiselect', window.VueMultiselect.default);

      var app = new Vue({
        el: '#app',
        data: {
          selected: [],
          options: [],
          isLoading: false,
          timeout: null,
        },
        methods: {
          asyncFind: function (query) {
            var vthis = this;
            clearTimeout(this.timeout);
            this.timeout = setTimeout(function () {
              this.isLoading = true;
              $.ajax({
                url: '/api/search/',
                data: { q: query },
                success: function (response) {
                  vthis.isLoading = false;
                  vthis.options = response;
                }
              });
            }, 100);
          },
          clearAll: function () {
            this.selected = [];
          },
          submitForm: function () {
            var vthis = this;
            $.each(this.selected, function(index, recipient) {
              $('<input>').attr({
                type: 'hidden',
                name: 'users[]',
                value: recipient.id
              }).appendTo('form');
            });

            $('#app').submit();
          }
        }
      })
    </script>
  @endsection