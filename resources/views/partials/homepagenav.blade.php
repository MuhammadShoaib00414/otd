@if(isset($authUser))
<div class="nav flex-column">
  <a class="nav-link bg-primary-100 hover:text-primary-800" href="/spa#" style="border-radius: .25rem;"><i class="icon-home mr-1"></i> @lang('messages.my-dashboard')</a>
  <a class="nav-link bg-primary-100 hover:text-primary-800 d-md-none" href="/search"><i class="icon-magnifying-glass mr-1"></i> @lang('messages.search')</a>
  <a class="nav-link hover:text-primary-800" href="/users/{{ $authUser->id }}"><i class="icon-user mr-1"></i> @lang('messages.my-profile')</a>
  <a class="nav-link hover:text-primary-800 d-lg-none" href="/account"><i class="icon-briefcase mr-1"></i> @lang('messages.account')</a>
  
  <a class="nav-link hover:text-primary-800 d-lg-none" href="/notifications"><svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-bell-fill mr-1" fill="currentColor" xmlns="http://www.w3.org/2000/svg"><path d="M8 16a2 2 0 0 0 2-2H6a2 2 0 0 0 2 2zm.995-14.901a1 1 0 1 0-1.99 0A5.002 5.002 0 0 0 3 6c0 1.098-.5 6-2 7h14c-1.5-1-2-5.902-2-7 0-2.42-1.72-4.44-4.005-4.901z"/></svg> @lang('messages.notifications') 
    @if($authUser->unreadNotifications()->count())
      @include('components.red-dot')
    @endif
</a>

  <a class="nav-link hover:text-primary-800" href="/messages"><i class="icon-mail mr-1"></i> @lang('messages.messages') 
    @if($authUser->unreadMessageCount)
      @include('components.red-dot')
    @endif
  </a>
  @if(getsetting('is_ideations_enabled'))
  <a class="nav-link hover:text-primary-800" href="/ideations"><i class="icon-light-bulb mr-1"></i> @lang('messages.ideations') 
    @if($authUser->unread_ideation_invitations->count())
      @include('components.red-dot')
    @endif
  </a> 
  @endif
  <a class="nav-link hover:text-primary-800" href="/introductions"><i class="icon-network mr-1"></i> @lang('messages.introductions') 
    @if($authUser->unreadIntroductionCount)
      @include('components.red-dot')
    @endif
  </a>
  <a class="nav-link hover:text-primary-800" href="/shoutouts/received"><i class="icon-megaphone mr-1"></i> @lang('messages.shoutouts')
    @if($authUser->unreadShoutoutCount)
      @include('components.red-dot')
    @endif
  </a>
  <a class="nav-link hover:text-primary-800" href="/calendar"><i class="icon-calendar mr-1"></i> @lang('messages.home.leftnav.calendar') 
    @if($authUser->event_notifications_count)
      @include('components.red-dot')
    @endif
  </a>
  <a class="nav-link hover:text-primary-800" href="/browse"><i class="icon-magnifying-glass mr-1"></i> {{ getsetting('find_your_people_alias') }}</a>
  @if(getSetting('is_ask_a_mentor_enabled'))
  <a class="nav-link hover:text-primary-800" href="/mentors/ask"><i class="icon-chat mr-1"></i> {{ getsetting('ask_a_mentor_alias') }}</a>
  @endif
  @if($authUser->is_manager && getSetting('is_management_chain_enabled'))
  <a class="nav-link" href="/management/my-direct-reports"><i class="icon-pie-chart mr-1"></i> @lang('messages.management-dashboard')</a>
  @endif 
  @if($authUser->is_admin)
  <a class="nav-link" href="/admin"><i class="icon-briefcase mr-1"></i> @lang('messages.home.leftnav.admin')</a>
  @endif
  <a class="nav-link d-md-none" href="/logout" onclick="return confirm('Are you sure you want to logout?');"><i class="icon-log-out mr-1"></i> @lang('messages.logout')</a>
  <div class="mt-2 groups">
    @foreach($authUser->dashboard_groups as $groupHeader => $groups)
        <h5 class="text-uppercase text-muted mb-1 mt-1 ml-2" style="font-size: 14px;">{{ $groupHeader }}</h5>
        @foreach($groups as $group)
          <div class="nav flex-column">
            <a id="group{{ $group->id }}" class="nav-link group" href="/groups/{{ $group->slug }}">{{ $group->name }}</a>
          </div>
          @include('partials.dashboardSubgroups', ['group' => $group, 'count' => 1])
        @endforeach
    @endforeach
  </div>
  @endif
</div>