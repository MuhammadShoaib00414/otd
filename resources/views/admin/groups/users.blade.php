@extends('admin.groups.layout')

@section('inner-page-content')
    @if(session()->has('success'))
    <div class="text-center mb-4 text-success">
        {{ session('success') }}
    </div>
    @endif
    <div class="d-flex justify-content-between">
        <h5>Users</h5>
        <div class="d-flex justify-content-end">
            <a href="/admin/groups/{{ $group->id }}/users/bulk-add" class="btn btn-sm btn-outline-secondary mr-3">Bulk-add users</a>
            <form class="form-inline mr-3" method="post">
                @csrf
                @method('PUT')
                <div class="input-group input-group-sm">
                    <input type="text" name="users" placeholder="User emails..." class="form-control form-control-sm">
                    <div class="input-group-append">
                        <button type="submit" class="btn btn-sm btn-outline-secondary">Add</button>
                    </div>
                </div>
            </form>
            <form class="form-inline">
                <div class="input-group input-group-sm">
                    <input type="text" name="q" placeholder="Search..." class="form-control form-control-sm" value="{{ Request::input('q') }}">
                    <div class="input-group-append">
                        <button type="submit" class="btn btn-sm btn-outline-secondary"><i class="fas fa-search"></i></button>
                    </div>
                </div>
            </form>
        </div>
        <!-- <div class="text-right">
            <a class="btn btn-primary btn-sm" href="/admin/users/invite">
              Add users to group
            </a>
        </div> -->
    </div>
    
    <table class="table mt-2">
        <thead>
            <tr>
                <th scope="col"><b>Name</b></th>
                <th scope="col"><b>Job title</b></th>
                <th scope="col"></th>
                <th></th>
            </tr>
        </thead>
        @foreach($users as $user)
        <tr>
            <td>{{ $user->name }}</td>
            <td>{{ $user->job_title }}</td>
            <td>
                @if($user->pivot->is_admin)<span class="badge badge-info">group admin</span>@endif
            </td>
            <td class="text-right"><a href="/admin/users/{{ $user->id }}">View</a></td>
        </tr>
        @endforeach
    </table>

    <div class="d-flex justify-content-center">
        @if(Request::has('q'))
            {{ $users->appends(['q' => Request::input('q')])->links() }}
        @else
            {{ $users->links() }}
        @endif
    </div>
@endsection