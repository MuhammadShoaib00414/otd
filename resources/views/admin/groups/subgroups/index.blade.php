@extends('admin.groups.layout')

@section('inner-page-content')
<div class="col-5 mx-auto">
  <div id="successMessage" class="alert alert-dismissible alert-success d-none">
      <button type="button" class="close" data-dismiss="alert">×</button>
      <strong>Changes saved successfully!</strong>
  </div>
    <div class="card">
        <div class="d-flex justify-content-between card-header">
            <h5>Subgroups</h5>
            <small class="text-muted"> Drag items to order them. </small>
            <div class="text-right">
                <a class="btn btn-outline-primary btn-sm" href="/admin/groups/create?parent={{ $group->id }}">
                  Add Subgroup
                </a>
            </div>
            <button id="submitButton" class="btn btn-sm btn-primary">@lang('general.save') order</button>
        </div>
        <div class="card-body p-0" id="sortable_container">
            @foreach($subgroups as $subgroup)
                <div id="subgroup_{{ $subgroup->id }}" class="row py-2 pl-3 m-0 subgroup" style="{{ ($loop->iteration % 2 == 0) ? 'background: lightgrey;' : '' }}">
                    <td><a href="/admin/groups/{{ $subgroup->id }}/subgroups">{{ $subgroup->name }}</a></td>
                </div>
            @endforeach
        <div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script>
    $("#sortable_container").sortable({
        items: ".subgroup",
    });
    $("#sortable_container").disableSelection();

    $('#submitButton').click(function(e) {
        var subgroups = [];
        $('#sortable_container').children().each(function(e) {
            var subgroup = $(this).attr('id');
            if(subgroup)
                subgroups.push(subgroup.split('_')[1]);
        });

        $.ajax({
            url: "/admin/groups/{{ $group->id }}/subgroups/sort", 
            type: "PUT",
            data: 
            {
                "subgroups": subgroups,
                "_token": "{{ csrf_token() }}",
            },
            success: function () {
                $('#successMessage').removeClass('d-none');
            }
        });
    });
</script>
@endsection