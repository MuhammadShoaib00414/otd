@extends('groups.layout')

@section('body-class', 'col-md-9')

@section('scripts')
<script>
    if(window.innerWidth <= 576) {
      $('#membersMenu').addClass('flex-column');
    }
</script>
@endsection

@section('stylesheets')
@parent
<style>
  @media(max-width: 576px){
    #membersSearchContainer{
      max-width: 500px;
    }
  }
  @media(min-width: 577px){
    #membersSearchContainer{
      min-width:560px; 
      max-width:600px;
    }
  }
</style>
@endsection

@section('inner-content')
    <div id="membersMenu" class="d-flex justify-content-between align-items-center mb-4">
      <h3 class="font-weight-bold mb-2">{{ $group->members_page }} ({{ $group->activeUsers()->count() }})</h3>
      <form method="get" action="/groups/{{ $group->slug }}/members">
        <div id="membersSearchContainer" class="input-group" style="">
          <input class="form-control form-control-lg" value="{{ request()->has('q') ? request()->q : '' }}" type="search" name="q" placeholder="@lang('groups.Search by name, job title, company or city')" />
          <div class="input-group-append">
            <button class="btn btn-lg btn-secondary" type="submit">@lang('general.search')</button>
          </div>
        </div>
      </form>
      @if($group->isUserAdmin($authUser->id))
        <a href="/groups/{{ $group->slug }}/members/manage" class="btn btn-sm btn-link-secondary"><i class="icon icon-cog mr-1"></i>@lang('general.manage') {{ $group->members_page }}</a>
      @endif
    </div>
    <div class="row justify-content-center align-items-stretch">
        @foreach($members as $user)
            <a href="/users/{{ $user->id }}" class="card col-sm-6 mx-1 mb-2 px-3 no-underline" style="flex: 1; min-width: 250px;">
                <div class="card-body d-flex align-items-center justify-content-center">
                  <div class="d-flex flex-column align-items-center justify-content-center">
                    <div class="mb-2" style="height: 5.5em; width: 5.5em; border-radius: 50%; background-image: url('{{ $user->photo_path }}'); background-size: cover; background-position: center;">
                    </div>
                    <div class="pt-1 text-center">
                      <span class="d-block" style="color: #343a40; font-weight: 600;">{{ $user->name }}
                        @if(isset($group) && $group->isUserAdmin($user->id, false))
                          <span class="badge ml-1" style="background-color: {{ getThemeColors()->primary['300'] }};">Group Admin</span>
                        @endif
                      </span>
                      <span class="d-block card-subtitle my-1 text-muted">{{ $user->job_title }}</span>
                      <span class="d-block mt-1 text-muted">{{ $user->company }}</span>
                    </div>
                  </div>
                </div>
            </a>
        @endforeach
    </div>

    <div class="d-flex justify-content-center mt-3">
      {{ $members->links() }}
    </div>

@endsection