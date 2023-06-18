@extends('admin.layout')

@section('page-content')
    @component('admin.partials.breadcrumbs', ['links' => [
        'Users' => '/admin/users',
        $user->name => '/admin/users/'.$user->id,
        'Edit' => '',
    ]])
    @endcomponent

    <h5>Edit User</h5>

    <form method="post" action="/admin/users/{{ $user->id }}">
        @csrf
        @method('PUT')
        <div class="form-group">
            <p>{{ $user->name }}<br />
            {{ $user->email }}</p>
        </div>
        <div class="form-group mb-3">
            <label class="form-check-label d-block" for="enabled">Status</label>
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="radio" name="is_enabled" id="true" value="true"{{ ($user->is_enabled) ? 'checked' : '' }}>
              <label class="form-check-label" for="true">Enabled</label>
            </div>
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="radio" name="is_enabled" id="false" value="false"{{ (!$user->is_enabled) ? 'checked' : '' }}>
              <label class="form-check-label" for="false">Disabled</label>
            </div>
        </div>
        <div class="form-group form-check">
            <input type="checkbox" class="form-check-input" name="is_admin" id="admin" {{ ($user->is_admin) ? 'checked' : '' }}>
            <label class="form-check-label" for="admin">Admin account</label>
        </div>
        <div class="form-group form-check">
            <input type="checkbox" class="form-check-input" name="is_hidden" id="hidden" {{ ($user->is_hidden) ? 'checked' : '' }}>
            <label class="form-check-label" for="hidden">Hidden</label>
        </div>
        <div class="form-group form-check">
            <input type="checkbox" class="form-check-input" name="is_event_only" id="is_event_only" {{ ($user->is_event_only) ? 'checked' : '' }}>
            <label class="form-check-label" for="is_event_only">Limited Access Invitation</label>
        </div>
        @if(\App\Setting::where('name', 'is_management_chain_enabled')->first()->value)
          <div style="max-width: 650px;">
            <hr>
            @foreach(App\Title::all() as $title)
            <div class="form-group">
              <label for="title{{ $title->id }}" class="d-block">{{ $title->name }} <span class="text-muted">(User ID)</span></label>
              <input type="text" id="title{{ $title->id }}" name="titles[{{ $title->id }}]" class="d-inline-block form-control" style="max-width: 100px;" value="{{ ($user->titles->where('id', $title->id)->first()) ? $user->titles->where('id', $title->id)->first()->pivot->assigned->id : '' }}">
              @if($user->titles->where('id', $title->id)->first())<p class="d-inline-block ml-2">Currently: {{ $user->titles->where('id', $title->id)->first()->pivot->assigned->name }}</p>@endif
            </div>
            @endforeach
          </div>
        @endif
        <button type="submit" class="btn btn-info">@lang('general.save') changes</button>
    </form>

    <hr class="my-4">

      <form action="/admin/users/{{ $user->id }}" method="post">
          @method('delete')
          @csrf
          <button type="submit" class="btn btn-sm btn-light mr-2" id="deleteUser">Delete user</button>
      </form>
@endsection

@section('scripts')
  <script>
    $('#deleteUser').on('click', function(event) {
      event.preventDefault();
      if (confirm('Delete this user?'))
        $('#deleteUser').parent().submit();
    });
  </script>
@endsection