@extends('admin.layout')

@section('page-content')
    @component('admin.partials.breadcrumbs', ['links' => [
        'Users' => '/admin/users',
        'Invitations' => '',
    ]])
    @endcomponent

    @if(Session::has('message'))
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            {!! Session::get('messages') !!}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5>Invited Emails</h5>
        <div class="btn-group">
          <a type="button" class="btn btn-sm btn-primary" href="/admin/users/invites/create">Invite new users</a>
          <button type="button" class="btn btn-sm btn-primary dropdown-toggle dropdown-toggle-split" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <span class="sr-only">Toggle Dropdown</span>
          </button>
          <div class="dropdown-menu dropdown-menu-right">
            <a class="dropdown-item" href="/admin/users/invites/bulk-resend">Bulk resend tool</a>
            <a class="dropdown-item" href="/admin/users/invites/cleanup">Cleanup tool</a>
          </div>
        </div>
    </div>

        <form method="get" action="/admin/users/invites" class="d-flex form-inline justify-content-between mb-3 align-items-center">
            <div>
                <span class="mr-1">Showing:</span>
                <select class="custom-select custom-select-sm" name="show" onchange="this.form.submit()">
                    <option value="all"{{ (request()->has('show') && request()->show == 'all') ? ' selected' : '' }}>All</option>
                    <option value="accepted"{{ (request()->has('show') && request()->show == 'accepted') ? ' selected' : '' }}>Accepted</option>
                    <option value="invited"{{ (request()->has('show') && request()->show == 'invited') ? ' selected' : '' }}>Not yet accepted</option>
                </select>
            </div>
            <div class="input-group">
                <input style="min-width: 200px; max-width: 300px;" class="form-control" name="q" value="{{ isset($_GET['q']) ? $_GET['q'] : '' }}" placeholder="Search by email">
                <div class="input-group-append">
                    <button type="submit" class="btn btn-primary">Search</button>
                </div>
            </div>
            <div style="min-width: 15%;"></div>
        </form>
        
        <table class="table">
            <thead>
                <tr>
                    <th>
                        @if(request()->has('sort'))
                            <a href="/admin/users/invites{{ (request()->has('show')) ? '?show=' . request()->show : '' }}">email</a>
                        @else
                            <a href="/admin/users/invites?sort=email{{ (request()->has('show')) ? '&show=' . request()->show : '' }}">email</a>
                        @endif
                    </th>
                    <th>invited</th>
                    <th class="text-center">status</th>
                    <th class="text-center">invite link</th>
                </tr>
            </thead>
            @foreach ($invitations as $invitation)
            <tr>
                <td>{{ $invitation->email }}</td>
                <td>{{ $invitation->sent_at->tz($authUser->timezone)->diffForHumans() }}</td>
                <td class="text-center">
                    @if($invitation->accepted_at)
                        <span class="badge badge-primary">account created</span>
                    @else
                        <span class="badge badge-secondary">invite sent</span>
                    @endif
                </td>
                <td class="text-right mr-3">
                    @if (!$invitation->accepted_at)
                   
                    <?php $invite =  config('app.url')."/invite/".$invitation->hash; ?>
                    <a onclick="setClipboard('<?php echo  $invite?>','<?php  echo $invitation->id;?>')" class="mr-3 cursor-pointer replace"  id="elementText_{{$invitation->id}}">Invite link </a>
                    <a href="/admin/users/invites/{{ $invitation->hash }}/resend" class="mr-3">Resend</a>
                    <a onclick="return confirm('Are you sure you want to revoke the invitation sent to {{ $invitation->email }}?');" href="/admin/users/invites/{{ $invitation->hash }}/delete">Revoke</a>
                    @endif
                </td>
            </tr>
            @endforeach
        </table>
@endsection


@section('scripts')
<script>

	
    function setClipboard(value ,elmId) {
    var tempInput = document.createElement("input");
    tempInput.style = "position: absolute; left: -1000px; top: -1000px";
    tempInput.value = value;
    document.body.appendChild(tempInput);
    tempInput.select();
    document.execCommand("copy");
    document.body.removeChild(tempInput);
    $('#elementText_' + elmId).replaceWith("Copied!");
 
}
</script>
@endsection