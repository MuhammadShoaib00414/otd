@extends('admin.layout')

@section('page-content')

    @component('admin.partials.breadcrumbs', ['links' => [
        'Users' => '/admin/users',
        'Invitations' => '/admin/users/invites',
        'Send Invites' => '',
    ]])
    @endcomponent

    @if(Session::has('messages'))
    <div class="card mb-3">
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            {!! Session::get('messages') !!}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    
        <div class="card-body py-0">
            <p>
                <b>Sent To:</b><br>
                {{ $sentTo->count() }} emails
            </p>
            <p>
                <b><i>Not</i> Sent To:</b><br>
                {{ $existingUsers->count() }} emails already registered @if($existingUsers->count())- <a href="#" data-toggle="modal" data-target="#existingUsers">View</a>@endif<br>
                {{ $existingInvites->count() }} emails already invited @if($existingInvites->count())- <a href="#" data-toggle="modal" data-target="#existingInvites">View</a>@endif<br>
                {{ $invalidEmails->count() }} invalid emails @if($invalidEmails->count())- <a href="#" data-toggle="modal" data-target="#invalidEmails">View</a>@endif<br>
            </p>
        </div>
    </div>
    @endif

    <h5 class="card-title">Invite Users</h5>

    <p>
        This tool <i>will not</i> send invites to emails that already have received an invitation or already have an account.</p>
    </p>
    
    <form class="mt-2" method="post" action="/admin/users/invites">
        @csrf
        <div class="form-group">
            <label>Email addresses (one per line)</label>
            <textarea required class="form-control col-lg-8" rows="4" name="emails" placeholder="email1@example.com
email2@example.com
"></textarea>
            <small class="form-text text-muted">An invitation to create an account will be sent to each email address entered.</small>
        </div>
        <div class="form-group">
            <label>Custom message</label>
            <textarea class="form-control col-lg-8" rows="4" name="custom_message"></textarea>
        </div>
        @if(getsetting('is_localization_enabled'))
        <div class="form-group mb-3" style="max-width: 400px;">
          <label for="locale">Language</label>
          <select class="custom-select" name="locale" id="locale">
            <option selected value="en">English</option>
            <option value="es">Español</option>
          </select>
        </div>
        @endif
        <div class="form-check mb-3">
          <input class="form-check-input" checked type="radio" name="is_event_only" id="standard" value="0">
          <label class="form-check-label mr-5" for="standard">
            Standard
          </label>
          <input class="form-check-input" type="radio" value="1" name="is_event_only" id="event_only">
          <label class="form-check-label mr-5" for="event_only">
            Limited Access invitation
          </label>
          <input class="form-check-input" type="radio" name="is_event_only" value="0" id="assign_to_group">
          <label class="form-check-label" for="assign_to_group">
            Assign to specific community and/or event groups
          </label>
        </div>
        <div id='event_only_groups_display' class='collapse div1 mb-3'>
            <p class="text-muted">Invited users will be limited to this group.</p>
            
            <div class="form-group mt-3">
              <label for="event_only_expires_at">Event access expiration date</label>
              <input type="date" name="event_only_expires_at" class="form-control" style="max-width: 200px;" id="event_only_expires_at" aria-describedby="expires_at_help">
              <small id="expires_at_help" class="form-text text-muted">Leave blank to never expire.</small>
            </div>
        </div>
        <div id='assign_to_groups_display' class='collapse div1 mb-3'>
            <p class="text-muted">Invited users will be auto-assigned (but not limited to) selected group.</p>
        </div>
        <div id="list_groups" class="collapse">
          <table class="table col-6">
            <thead>
              <tr>
                <th>Group</th>
                <th class="text-center">Member</th>
                <th class="text-center">Admin</th>
              </tr>
            </thead>
            <tbody>
              @foreach($groups as $group)
                  @include('admin.users.invites.partials.group', ['group' => $group, 'count' => 0])
              @endforeach
            </tbody>
          </table>
        </div>
        <button type="submit" class="btn btn-info mb-5">Send invitations</button>
    </form>

    @if($existingUsers->count())
    <div class="modal" tabindex="-1" role="dialog" id="existingUsers">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Emails That Already Have Accounts</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            @foreach($existingUsers as $email)
            {{ $email }}<br>
            @endforeach
          </div>
        </div>
      </div>
    </div>
    @endif

    @if($existingInvites->count())
    <div class="modal" tabindex="-1" role="dialog" id="existingInvites">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Emails That Have Already Been Invited</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            @foreach($existingInvites as $email)
            {{ $email }}<br>
            @endforeach
          </div>
        </div>
      </div>
    </div>
    @endif

    @if($invalidEmails->count())
    <div class="modal" tabindex="-1" role="dialog" id="invalidEmails">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Invalid Emails</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            @foreach($invalidEmails as $email)
            {{ $email }}<br>
            @endforeach
          </div>
        </div>
      </div>
    </div>
    @endif

@endsection
@section('scripts')
<script>
  $('#event_only').change(function (event)
  {
    $('#assign_to_groups_display').collapse('hide');
    $('#event_only_groups_display').collapse('show');
    $('#list_groups').collapse('show');
  });
  $('#assign_to_group').change(function (event)
  {
    $('#assign_to_groups_display').collapse('show');
    $('#event_only_groups_display').collapse('hide');
    $('#list_groups').collapse('show');
  });
  $('#standard').change(function (event)
  {
    $('#assign_to_groups_display').collapse('hide');
    $('#event_only_groups_display').collapse('hide');
    $('#list_groups').collapse('hide');
  });
</script>
@endsection