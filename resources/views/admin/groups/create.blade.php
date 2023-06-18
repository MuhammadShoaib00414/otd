@extends('admin.layout')

@push('stylestack')
    <link href="https://unpkg.com/gijgo@1.9.13/css/gijgo.min.css" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.9/dist/css/bootstrap-select.min.css">
    <style>
      .select-picker > .dropdown-toggle { border: 1px solid #ced4da; }
      .dropdown.bootstrap-select.select-picker.form-control.dropup.show {
            z-index: 9999;
        }
    </style>
@endpush

@section('page-content')

    @component('admin.partials.breadcrumbs', ['links' => [
        'Groups' => '/admin/groups',
        'New Group' => '',
    ]])
    @endcomponent

<div>

  <h5>Add Group</h5>

  @foreach($errors->all() as $message)
    <div class="alert alert-dismissible alert-danger" style="max-width:450px;">
        <button type="button" class="close" data-dismiss="alert">×</button>
        <strong>{!! $message !!}</strong>
    </div>
  @endforeach

  <form method="post" action="/admin/groups" id="form">
      @csrf
      <div class="form-group mb-2">
          <label for="name">Group name</label>
          <input type="text" name="name" id="name" class="form-control" style="max-width: 450px;" required>
      </div>
      <div class="form-group mb-2" style="max-width: 600px;">
          <label for="slug">Your vanity URL</label>
          <div class="input-group mb-3">
              <div class="input-group-prepend">
                  <span class="input-group-text" id="basic-addon3">{{ config('app.url') }}/groups/</span>
              </div>
            <input style="min-width: 150px; max-width: 250px;" type="text" name="slug" id="slug" class="form-control" required aria-describedby="basic-addon3">
          </div>
      </div>

      <div class="form-group mb-3" style="max-width: 450px;">
            <label for="group_admin">Group Admin</label>
            <select class="select-picker form-control" id="group_admin" name="group_admin" required data-live-search="true" >
              <option selected disabled>Select one</option>
              @foreach($users as $user)
                  <option value="{{ $user->id }}">{{ $user->name }}</option>
              @endforeach
          </select>
      </div>

      <div class="form-group mb-3" style="max-width: 450px;">
            <label for="parent_group_id">Parent Group (optional)</label>
            <select class="select-picker form-control" id="parent_group_id" name="parent_group_id" data-live-search="true">
              <option{{ (request()->has('parent')) ? '' : ' selected' }} disabled>Select one</option>
              @foreach($groups as $group)
                  <option value="{{ $group->id }}"{{ (request()->has('parent') && request()->input('parent') == $group->id) ? ' selected' : '' }}>{{ $group->name }}</option>
              @endforeach
          </select>
      </div>

      <div class="form-check mb-3">
        <input type="hidden" name="is_private" value="0">
        <input type="checkbox" class="form-check-input" name="is_private" value="1">
        <label class="form-check-label" for="is_private">Private group</label>
      </div>

      <div class="form-check mb-3">
        <input type="hidden" name="should_display_dashboard" value="0">
        <input type="checkbox" class="form-check-input" name="should_display_dashboard" value="1">
        <label class="form-check-label" for="should_display_dashboard">Display on personalized dashboard of group members </label>
        @include('partials.subtext', ['subtext' => 'This option only applies to subgroups, not parent groups. Check this box if you want this group name with a link to show up on the left-hand menu of a user’s personalized dashboard. Think of this as a shortcut, where users can easily access groups they will visit frequently. If selected, be sure to edit the “Menu Header for Group Name” with a name that will help users identify the category for this group.'])
      </div>

      <button type="submit" class="btn btn-info">Create group</button>
  </form>
</div>
@endsection


@push('scriptstack')
    <script src="https://unpkg.com/gijgo@1.9.13/js/gijgo.min.js" type="text/javascript"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.9/dist/js/bootstrap-select.min.js"></script>
    <script>
        $.fn.selectpicker.Constructor.BootstrapVersion = '4.4.1';
        $('.select-picker').selectpicker();

        $('#form').submit(function(e) {
            var input = $(this).find("input[name=slug]");
            input.val(string_to_slug(input.val()));
        });

        $("#slug").keyup(function(e){
            if(e.key != ' ')
                $(this).val(string_to_slug($(this).val()));
        });

        $('#name').change(function() {
            $('#slug').val(string_to_slug($(this).val()));
        });

        function string_to_slug (str) {
            str = str.replace(/^\s+|\s+$/g, ''); // trim
            str = str.toLowerCase();
          
            // remove accents, swap ñ for n, etc
            var from = "àáäâèéëêìíïîòóöôùúüûñç·/_,:;";
            var to   = "aaaaeeeeiiiioooouuuunc------";
            for (var i=0, l=from.length ; i<l ; i++) {
                str = str.replace(new RegExp(from.charAt(i), 'g'), to.charAt(i));
            }

            str = str.replace(/[^a-z0-9 -]/g, '') // remove invalid chars
                .replace(/\s+/g, '-') // collapse whitespace and replace by -
                .replace(/-+/g, '-'); // collapse dashes
                console.log(str);
            return str;
        }
    </script>
@endpush