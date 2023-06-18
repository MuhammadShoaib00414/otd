<a href="#" class="btn btn-sm btn-outline-secondary" data-toggle="modal" data-target="#bulkAdd">Bulk add to group</a>
<div class="modal" tabindex="-1" id="bulkAdd">
  <form action="/admin/groups/bulk-add" method="post">
    @csrf
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Bulk Add Users To Group</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <p><b>Group</b></p>

            @foreach($users as $user)
              <input type="hidden" name="users[]" value="{{ $user->id }}">
            @endforeach

            @foreach(App\Group::orderBy('name', 'asc')->get() as $group)
                <div class="form-check">
                  <input class="form-check-input" type="radio" name="group_id" id="group{{ $group->id }}" value="{{ $group->id }}">
                  <label class="form-check-label" for="group{{ $group->id }}">
                    {{ $group->name }}
                  </label>
                </div>
            @endforeach

          </div>
          <div class="modal-footer justify-content-center">
            <button type="submit" class="btn btn-primary">Add Users</button>
          </div>
        </div>
      </div>
    </form>
</div>