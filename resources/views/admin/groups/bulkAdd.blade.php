@extends('admin.groups.layout')

@section('inner-page-content')
    @if(session()->has('success'))
    <div class="text-center mb-4 text-success">
        {{ session('success') }}
    </div>
    @endif
    <div class="d-flex justify-content-between">
        <h5>Users</h5>
        <form class="form-inline mr-3" method="post" action="/admin/groups/{{ $group->id }}/assign">
            @csrf
            @method('PUT')
            <button onclick="return confirm('This action will add ALL users to this group. Are you sure you want to proceed?');" type="submit" class="btn btn-sm btn-danger">Add all users</button>
        </form>
    </div>
    <button class="btn btn-primary mt-2 mb-4" data-toggle="collapse" href="#byUser" role="button" aria-expanded="false" aria-controls="byUser" id="byUserButton">Select Users</button>
    <button class="btn btn-primary mt-2 ml-2 mb-4" data-toggle="collapse" href="#byGroup" role="button" aria-expanded="false" aria-controls="byGroup" id="byGroupButton">Add By Group</button>
    <form action="/admin/groups/{{ $group->id }}/users/bulk-add" method="post" id="app">
        @method('put')
        @csrf
        <div class="collapse mb-2" id="byUser">
          <div class="col-md-6 card card-body">
            @foreach($users as $user)
                <div class="form-check">
                  <input id="user{{ $user->id }}" type="checkbox" name="users[]" value="{{ $user->id }}">
                  <label class="form-check-label" for="user{{ $user->id }}">
                    {{ $user->name }}
                  </label>
                </div>
            @endforeach
          </div>
        </div>

        <div class="collapse mb-2" id="byGroup">
          <div class="col-md-6 card card-body">
            <span class="text-muted mb-2">This will add each selected group's users to this group.</span>
            @each('admin.groups.partials.groupcheckbox', $otherGroups, 'otherGroup')
          </div>
        </div>

        <div class="col-6">
            <label for="ajax">Search users</label>
            <multiselect v-model="selected" id="ajax" label="name" track-by="id" placeholder="Type to search" open-direction="bottom" :options="options" :multiple="true" :searchable="true" :internal-search="true" :clear-on-select="true" :close-on-select="true" :options-limit="300" :limit="3" :max-height="600" :show-no-results="true" :hide-selected="true">
                <template slot="tag" slot-scope="{ option, remove }">
                    <span class="custom__tag">
                        <span>@{{ option.name }}</span>
                        <span class="custom__remove mr-1" @click="remove(option)">&times;</span>
                    </span>
                </template>
                <template slot="clear" slot-scope="props">
                    <div class="multiselect__clear" v-if="selected.length" @mousedown.prevent.stop="clearAll(props.search)"></div>
                </template>
                <span slot="noResult">No elements found. Consider changing the search query.</span>
            </multiselect>
        </div>

        <button type="submit" class="btn btn-primary my-3" @click.prevent="saveUsers">@lang('general.save')</button>
    </form>
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
          selected: [],
          options: [
            @foreach($users as $user)
                {
                    name: '{{ $user->name }}',
                    id: '{{ $user->id }}',
                },
            @endforeach
           ],
          isLoading: false,
          timeout: null,
        },
        methods: {
          clearAll: function () {
            this.selected = [];
          },
          saveUsers: function () {
            $.each(this.selected, function(index, user) {
              $('<input>').attr({
                type: 'hidden',
                name: 'users[]',
                value: user.id
              }).appendTo('form');
            });
            $('#app').submit();
          },
        }
      });

      $('#byGroupButton').click(function() {
        $('#byUser').removeClass('show');
      });

      $('#byUserButton').click(function() {
        $('#byGroup').removeClass('show');
      });
</script>
<link rel="stylesheet" href="https://unpkg.com/vue-multiselect@2.1.0/dist/vue-multiselect.min.css">
@endsection