@extends('admin.layout')
@section('page-content')
    <p>
        <a href="/admin/users/"><i class="fas fa-angle-left"></i> All Users</a>
    </p>
    @if(!empty(Session::has('message')))
    <div class="card mb-3">
        <div class="card-body py-0">
            <p>
                <b>Valid Email:</b><br>
                {{ $existingUsers->count() }} emails @if($existingUsers->count())- <a href="#" data-toggle="modal" data-target="#existingUsers">View</a>@endif<br>
            </p>
            <p>
                <b><i>Not</i> Valid Email:</b><br>
                {{ $notExistingUsers->count() }} invalid emails @if($notExistingUsers->count())- <a href="#" data-toggle="modal" data-target="#invalidEmails">View</a>@endif<br>
            </p>
        </div>
    </div>
    @endif

    <h5 class="card-title">Delete Users</h5>

    <p>
        This tool will allow you to bulk delete users. All the deleted users will be found on the Delete Users Tab.</p>
    </p>

    <form class="mt-2" method="post" action="/admin/users/bulkDeleteUsers">
        @csrf
        <div class="form-group">
            <label>Email addresses (one per line)</label>
            <textarea class="form-control col-lg-8 valid_emails" name="valid_emails" id="textString" rows="4"  placeholder="email1@example.com email2@example.com" name="valid_emails" required>@if(isset($existingUsers))@php echo str_replace('\n', PHP_EOL, implode('\n', $existingUsers->toArray()));@endphp @endif</textarea>
        </div>
        <div class="form-group">
            <label>List accounts that didn't match the email</label>
            <textarea class="form-control col-lg-8" rows="4"  placeholder="email1@example.com email2@example.com" name="invalid_emails" >@if(isset($notExistingUsers))@php echo str_replace('\n', PHP_EOL, implode('\n', $notExistingUsers->toArray()));@endphp @endif </textarea>
        </div>
        <button type="submit" class="btn btn-info mb-5">Confirm Delete</button>
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



    @if($notExistingUsers->count())
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
            @foreach($notExistingUsers as $email)
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


  $(function () {
    s = document.getElementById("textString").value;
	s = s.replace(/(^\s*)|(\s*$)/gi,"");
	s = s.replace(/[ ]{2,}/gi," ");
	s = s.replace(/\n /,"\n");
	document.getElementById("textString").value = s;
 });
</script>
@endsection
