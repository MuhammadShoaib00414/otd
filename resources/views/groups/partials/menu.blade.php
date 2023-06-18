<div class="nav flex-column">
    <a class="nav-link{{ (Request::path() == 'groups/' . $groups->slug) ? ' font-weight-bold' : '' }}" href="/groups/{{ $groups->slug }}"><i class="icon-home mr-1"></i> {{ $groups->name }}</a>
    @if($group->is_posts_enabled)
        <a class="nav-link{{ (Request::is('*posts*')) ? ' font-weight-bold' : '' }}" href="/groups/{{ $groups->slug }}/posts"><i class="icon-archive mr-1"></i> {{ $group->posts_page }}</a>
    @endif
    <a class="nav-link{{ (Request::is('*members*')) ? ' font-weight-bold' : '' }}" href="/groups/{{ $groups->slug }}/members"><i class="icon-users mr-1"></i> {{ $group->members_page }}</a>
    @if($group->is_content_enabled)
        <a class="nav-link{{ (Request::is('*content*')) ? ' font-weight-bold' : '' }}" href="/groups/{{ $groups->slug }}/content"><i class="icon-text-document-inverted mr-1"></i> {{ $group->content_page }}</a>
    @endif
    @if($group->is_events_enabled)
        <a class="nav-link{{ (Request::is('*calendar*') || Request::is('*events*')) ? ' font-weight-bold' : '' }}" href="/groups/{{ $groups->slug }}/calendar"><i class="icon-calendar mr-1"></i> {{ $group->calendar_page }}</a>
    @endif
    @if($group->is_lounge_enabled)
    <a dusk="lounge" class="nav-link" href="/groups/{{ $groups->slug }}/lounge"><i class="icon-globe mr-1"></i> {{ $group->lounge->name }}</a>
    @endif
    @if($group->is_shoutouts_enabled)
        <a class="nav-link{{ (Request::is('*shoutouts*')) ? ' font-weight-bold' : '' }}" href="/groups/{{ $groups->slug }}/shoutouts"><i class="icon-megaphone mr-1"></i> {{ $group->shoutouts_page }}</a>
    @endif
    @if($group->is_discussions_enabled)
        <a class="nav-link{{ (Request::is('*discussions*')) ? ' font-weight-bold' : '' }}" href="/groups/{{ $groups->slug }}/discussions"><i class="icon-chat mr-1"></i> {{ $group->discussions_page }}</a>
    @endif
    @if($group->is_files_enabled)
        <a dusk="files" class="nav-link{{ (Request::is('*files*')) ? ' font-weight-bold' : '' }}" href="/groups/{{ $groups->slug }}/files"><i class="icon-folder mr-1"></i> {{ $group->files_alias }}</a>
    @endif
    @if($group->subgroups()->count() && $group->hasAccessableSubgroups(request()->user()->id))
        <a class="nav-link" href="/groups/{{ $groups->slug }}/subgroups"><i class="icon-database mr-1"></i> {{ $group->subgroups_page_name }}</a>
    @endif
    @if($group->sequence && getsetting('is_sequence_enabled') && $group->is_sequence_enabled)
    <a class="nav-link{{ (Request::is('*sequence*')) ? ' font-weight-bold' : '' }}" href="/groups/{{ $groups->slug }}/sequence"><i class="fas fa-book mr-1"></i> {{ $group->sequence->name }}</a>
    @endif
    @if($group->isUserAdmin($authUser->id))
        @if($group->budgets()->count() && $group->is_budgets_enabled)
            <a class="nav-link{{ (Request::is('*budgets*')) ? ' font-weight-bold' : '' }}" href="/groups/{{ $groups->slug }}/budgets"><i class="icon-credit-card mr-1"></i> @lang('general.budgets')</a>
        @endif
        @if($group->is_reporting_enabled)
        <a class="nav-link" href="/groups/{{ $groups->slug }}/reports/demographics"><i class="icon-pie-chart mr-1"></i> @lang('messages.reports')</a>
        @endif
        @if($group->is_email_campaigns_enabled)
        <a class="nav-link" href="/groups/{{ $groups->slug }}/email-campaigns"><i class="icon-mail mr-1"></i> @lang('messages.email-campaigns')</a>
        @endif
        @if($group->is_reporting_enabled)
        <a class="nav-link{{ (Request::is('*activity*')) ? ' font-weight-bold' : '' }}" href="/groups/{{ $groups->slug }}/activity"><i class="fa fa-history mr-1"></i> @lang('messages.activity')</a>
        @endif
        <a class="nav-link{{ (Request::is('*edit*')) ? ' font-weight-bold' : '' }}" href="/groups/{{ $groups->slug }}/edit"><i class="icon-cog mr-1"></i> @lang('messages.settings')</a>
    @endif
</div>
@if($group->custom_menu)
<div class="mt-3">
    @foreach(json_decode($group->custom_menu)->groups as $group)
    <div class="mb-3">
        <p class="mb-1"><b>{{ $group->title }}</b></p>
        <div class="nav flex-column">
            @foreach($group->links as $link)
            <a class="nav-link" href="{{ $link->url }}"{{ (!Illuminate\Support\Str::contains($link->url, config('app.url'))) ? ' target="_blank"' : '' }}>{{ $link->title }}</a>
            @endforeach
        </div>
    </div>
    @endforeach
</div>
@endif