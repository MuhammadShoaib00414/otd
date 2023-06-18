@extends('layouts.app')

@section('stylesheets')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.2/css/all.min.css" integrity="sha256-zmfNZmXoNWBMemUOo1XUGFfc0ihGGLYdgtJS3KCr/l0=" crossorigin="anonymous" />
<style>
  .select-option {
    display: inline-block;
    margin-right: 1em;
    border: 1px solid #eee;
    padding: 0 .5em;
    border-radius: 3px;
  }
  .select-option:hover {
    text-decoration: none;
    background-color: #eee;
  }
  .select-option-selected {
    background-color: {{ getThemeColors()->accent['300'] }};
  }
  .select-option-selected:hover {
    background-color: {{ getThemeColors()->accent['300'] }} !important;
  }
  .taxonomyTitle {
    color: {{ getThemeColors()->accent['400'] }}!important;
  }

  .pointer-on-hover:hover {
    cursor: pointer;
  }
</style>
@endsection

@section('content')
<div style="background-color: #565d6b;">
  <div class="container-fluid py-3" style="position: relative;">
    <a href="/home" class="d-inline-block mb-2" style="font-size: 14px; color: #fff;"><i class="icon-chevron-small-left"></i> @lang('messages.my-dashboard')</a>
    <h4 class="mb-2 text-center no-underline" style="color: #fff;">{{ getsetting('ask_a_mentor_alias') }}</h4>
    <p class="mx-md-auto" style="color: #fff; font-size: 14px; max-width:1000px;">@lang('mentor.description')</p>
  </div>
</div>

<div class="container bg-lightest-brand py-4">
  <div class="row" id="askAMentor">
    <div class="col-md-6">
      
      <div class="taxonomy mb-3" v-for="taxonomy in taxonomies">
        <h4 class="mb-1 pointer-on-hover taxonomyTitle" style="color: #f48f82;" @click="taxonomy.display = !taxonomy.display">
          <i class="fas fa-caret-right mr-1" style="color: #343a40;" :class="{ 'fa-rotate-90': taxonomy.display }"></i>
          @{{ taxonomy.name }}
          <small class="d-block ml-3" style="color: #343a40;">@lang('mentor.select-up-to-three')</small>
        </h4>
        <div v-show="taxonomy.display">
          <div v-for="(optionGroup, groupName) in taxonomy.options" class="mb-2">
            <p class="font-weight-bold mb-0">@{{ groupName }}</p>
            <a href="#" v-for="option in optionGroup" class="select-option" :class="{ 'select-option-selected': selected.options.includes(option) }" @click.prevent="toggleOption(option)">
              @{{ option.name }}
            </a>
          </div>
        </div>
      </div>
      
    </div>
    <div class="col-md-6">
      <div class="text-center py-2 mb-3" style="border-top: 1px solid rgb(205, 208, 212); border-bottom: 1px solid rgb(205, 208, 212);">
        <h4 class="mb-0">
          <span v-if="!selected.options.length">@lang('mentor.all-mentors')</span>
          <span v-if="selected.options.length">@lang('mentor.your-search')</span>
        </h4>
        <p>@lang('mentor.results'): @{{ results.length }}</p>
      </div>

      <div v-for="user in results" :key="componentKey">
        <a data-toggle="collapse" :href="'#user'+user.id" role="button" aria-expanded="false" :aria-controls="'#user'+user.id" class="card mt-2 px-1 no-underline mb-0">
          <div class="card-body p-1">
            <div class="ml-1 d-flex align-items-center">
              <div style="height: 3em; width: 3em; border-radius: 50%; background-size: cover; background-position: center; flex-shrink: 0;" :style="{ 'background-image': `url(${user.photo_path})` }">
              </div>
              <div class="ml-3">
                <span class="d-block mb-1" style="font-size: 0.85em; color: #343a40; font-weight: 600;">@{{ user.name }}</span>
                <span class="d-block card-subtitle mb-1 text-muted" style="font-size: 0.85em; line-height: 1.2;">@{{ user.job_title }}</span>
                <span class="d-block card-subtitle text-muted" style="font-size: 0.85em;">@{{ user.company }}</span>
              </div>
            </div>
          </div>
        </a>
        <div class="collapse" :id="'user'+user.id">
          <div class="mt-0 card card-body">
            <div class="d-flex justify-content-between">
              <div class="col">
                <b class="mt-auto mb-auto">@lang('mentor.categories-include'):</b>
              </div>
              <div class="btn-group">
                <a target="_blank" :href="'/users/'+user.id" class="btn btn-primary">@lang('general.view')</a>
                <a target="_blank" :href="'/messages/new?user='+user.id+'&mentor=true'" class="btn btn-primary mt-auto"><i class="icon-mail"></i></a>
              </div>
            </div>
            <div class="d-flex justify-content-end">
              <div class="col">
                <div v-for="(option,index) in user.options">
                  <div v-if="index < 5">
                    @{{ option.name }}
                  </div>
                </div>
                <div v-if="user.options.length == 0">
                  @lang('mentor.no-categories-set').
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script>
<script>
  var askAMentor = new Vue({
    el: '#askAMentor',
    data: {
      photo_path: "/images/profile-icon-empty.png",
      hello: 'Ask a Mentor',
      taxonomies: JSON.parse({!! json_encode($taxonomies) !!}),
      selected: {
        '_token': "{{ csrf_token() }}",
        options: [],
      },
      showList: {
        hustles: false,
        skills: true,
        keywords: false,
      },
      results: [],
    },
    created: function () {
      this.taxonomies = JSON.parse(this.taxonomies);
      this.loadResults();
    },
    methods: {
      toggleOption: function (option) {
        if (this.selected.options.includes(option))
          this.selected.options.splice(this.selected.options.indexOf(option), 1);
        else
          this.selected.options.push(option);
        this.loadResults();
      },
      loadResults: function () {
        vthis = this;
        vthis.results = [];
        if(this.currentRequest)
          this.currentRequest.abort();
        this.currentRequest = $.ajax({
          url: '/api/mentor-results',
          method: 'post',
          data: vthis.selected,
          success: function (data) {
            vthis.results = data;
            vthis.componentKey += 1;
          }
        })
      }
    }
  });
</script>
@endsection