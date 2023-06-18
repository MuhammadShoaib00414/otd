@extends('admin.ideations.show.layout')

@section('inner-page-content')
<div class="col-md-6 mx-md-auto">
  <div class="text-right mb-2">
    <div class="d-flex flex-row-reverse">
      <div style="max-width: 300px;">
        <a class="btn btn-primary" href="/admin/ideations/{{ $ideation->id }}/members/invite">Invite user</a>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-md-12">
      <div class="card">
        @if($ideation->invited_users->count())
          <table class="table">
            <thead>
              <tr>
                <th>Name</th>
                <th></th>
              </tr>
            </thead>
            <tbody>
              @foreach($ideation->invited_users as $user)
                <tr>
                  <td><a target="_blank" href="/admin/users/{{ $user->id }}">{{ $user->name }}</a></td>
                  <td class="text-right">
                    <form action="/admin/ideations/{{ $ideation->id }}/invitations/{{ $user->id }}" method="post">
                      @csrf
                      @method('delete')
                      <button onclick="return confirm('Are you sure you want to uninvite {{ $user->name }} from {{ $ideation->name }}?');" type="submit" class="btn btn-sm btn-primary">Remove</button>
                    </form>
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        @else
          <span class="text-center my-3">No invitations...</span>
        @endif
      </div>  
    </div>
  </div>
</div>
@endsection