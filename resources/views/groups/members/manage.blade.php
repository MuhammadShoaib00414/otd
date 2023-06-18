@extends('groups.layout')

@section('body-class', 'col-md-9')

@section('inner-content')
    <div class="d-flex justify-content-between align-items-center">
      <h3 class="font-weight-bold mb-2">{{ $group->members_page }}</h3>
      <div>
        @include('messages.partials.create', ['users' => $users, 'createIndividually' => true])
        <a href="/groups/{{ $group->slug }}/members/add" class="btn btn-sm btn-secondary">@lang('general.add') {{ $group->members_page }}</a>
      </div>
    </div>

    @if (session('message'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('message') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
    @endif

    <div>
      <div class="card">
        <table class="table">
          <tr>
            <td style="border-top: 0;"><b>@lang('general.name')</b></td>
            <td style="border-top: 0;"><b>@lang('general.job_title')</b></td>
            <td style="border-top: 0;"><b>@lang('role')</b></td>
            <td style="border-top: 0;"></td>
          </tr>
          @foreach($users as $user)
           @if($user->is_hidden == 0)
          <tr>
            <td>{{ $user->name }}
              @if(isset($group) && $group->isUserAdmin($user->id, false))
                <span class="badge ml-1" style="background-color: {{ getThemeColors()->primary['300'] }};">Group Admin</span>
              @endif
            </td>
            <td>{{ $user->job_title }}</td>
            <td class="text-center">@if($group->isGroupAdmin($user->id))<span class="badge badge-secondary">@lang('general.group_admin')</span>@endif</td>
            <td>
              @if($user->id !== $authUser->id)
              <div class="dropdown" style="z-index: unset;">
                <a href="#" type="button" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="white-space: nowrap;">
                  @lang('general.manage')
                </a>
                <div class="dropdown-menu dropdown-menu-right" style="z-index: 100000;">
                  @if($group->isGroupAdmin($user->id))
                  <a class="dropdown-item" href="/groups/{{ $group->slug }}/members/toggle-admin?targetuser={{ $user->id }}">@lang('general.make_regular_member')</a>
                  @else
                  <a class="dropdown-item" href="/groups/{{ $group->slug }}/members/toggle-admin?targetuser={{ $user->id }}">@lang('general.make_group_admin')</a>
                  @endif
                  <a class="dropdown-item" href="/groups/{{ $group->slug }}/members/remove?targetuser={{ $user->id }}">@lang('general.remove_from_group')</a>
                </div>
              </div>
              @endif
            </td>
          </tr>
          @endif
          @endforeach
        </table>
        <div class="card-footer">
          <div class="d-flex justify-content-center">
            {{ $users->links() }}
          </div>  
        </div>
      </div>
    </div>


@endsection