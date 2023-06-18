@extends('admin.layout')

@section('page-content')

    @component('admin.partials.breadcrumbs', ['links' => [
        'Users' => '/admin/users',
        $user->name => '',
    ]])
    @endcomponent

<div style="background-color: #fafbfd; margin: -1em -1em 1em -1em; padding: 1em 0 0; border-left: 1px solid #e0e2e7;">
    <div class="d-flex justify-content-between align-items-center mb-3 px-3">
        <div class="d-flex align-items-center ml-2">
          <div style="height: 5em; width: 5em; border-radius: 50%; background-image: url('{{ $user->photo_path }}'); background-size: cover; background-position: center;"></div>
          <div class="ml-3">
            @if($user->is_enabled == 0)
             <span class="badge badge-secondary mb-2">Account disabled</span>
            @endif
            @if($user->is_admin == 1)
             <span class="badge badge-primary mb-2">Super admin</span>
            @endif
            <h5>{{ $user->name }}</h5>
            <p class="mb-0">{{ $user->job_title }}</p>
          </div>
        </div>
        <div>
            @if(env('ALLOW_LOGIN_AS_USER') || getsetting('allow_admins_to_login_as_users'))
                @if(Request::is('admin/users/' . $user->id))
                    <div class="btn-group">
                      <a href="/admin/users/{{ $user->id }}/edit" class="btn btn-sm btn-primary">Edit</a>
                      <button type="button" class="btn btn-sm btn-primary dropdown-toggle dropdown-toggle-split" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <span class="sr-only">Toggle Dropdown</span>
                      </button>
                      <div class="dropdown-menu dropdown-menu-right">
                        @if(getsetting('allow_admins_to_login_as_users') || env('ALLOW_LOGIN_AS_USER'))
                        <form action="/admin/users/{{ $user->id }}/auth" method="post">
                            @csrf
                            <button type="submit" class="dropdown-item">Login as user</button>
                        </form>
                        @endif
                        @if(config('app.env') != 'production')
                        <form action="/admin/users/{{ $user->id }}/notify" method="post">
                            @csrf
                            <button type="submit" class="dropdown-item">Notify User</button>
                        </form>
                        @endif
                      </div>
                    </div>
                @endif
            @else
                @if(Request::is('admin/users/' . $user->id))
                    <a href="/admin/users/{{ $user->id }}/edit" class="btn btn-sm btn-primary">Edit</a>
                @endif
            @endif
        </div>
    </div>

    <ul class="nav nav-tabs mb-4 px-3">
        <li class="nav-item">
            <a class="nav-link{{ (Request::is('admin/users/' . $user->id)) ? ' active' : '' }}" href="/admin/users/{{ $user->id }}">Details</a>
        </li>
        <li class="nav-item">
            <a class="nav-link{{ (Request::is('*groups*')) ? ' active' : '' }}" href="/admin/users/{{ $user->id }}/groups">Groups</a>
        </li>
        <li class="nav-item">
            <a class="nav-link{{ (Request::is('*purchases*')) ? ' active' : '' }}" href="/admin/users/{{ $user->id }}/purchases">Purchases</a>
        </li>
        <li class="nav-item">
            <a class="nav-link{{ (Request::is('*categories*')) ? ' active' : '' }}" href="/admin/users/{{ $user->id }}/categories">Categories</a>
        </li>
        <li class="nav-item">
            <a class="nav-link{{ (Request::is('*activity*')) ? ' active' : '' }}" href="/admin/users/{{ $user->id }}/activity">Activity Log</a>
        </li>
    </ul>
</div>

    @yield('inner-page-content')

@endsection

@section('scripts')
    <script>
    $(function () {
        $('[data-toggle="popover"]').popover({
            trigger: 'focus'
        });
    });
    </script>
@endsection