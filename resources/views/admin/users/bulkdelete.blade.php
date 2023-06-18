@extends('admin.layout')

@section('page-content')
    <p>
        <a href="/admin/users/"><i class="fas fa-angle-left"></i> All Users</a>
    </p>
    <h5 class="card-title">Delete Users</h5>

    <p>
        This tool will allow you to bulk delete users. All the deleted users will be found on the Delete Users Tab.</p>
    </p>

    <form class="mt-2" method="post" action="/admin/users/bulk-delete-conform">
        @csrf
        <div class="form-group">
            <label>Email addresses (one per line)</label>
            <textarea required class="form-control col-lg-8" rows="4" name="emails" placeholder="email1@example.com
email2@example.com
"></textarea>
        </div>
        {{-- <div class="form-group">
            <label>List accounts that didn't match the email</label>
        <textarea class="form-control col-lg-8" rows="4" name="custom_message">
        </textarea>
        </div> --}}
        <button type="submit" class="btn btn-info mb-5">Next</button>
    </form>


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
