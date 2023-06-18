@extends('admin.layout')

@section('page-content')
    @component('admin.partials.breadcrumbs', ['links' => [
        'Groups' => '/admin/groups',
        'Sort' => '/admin/groups/sort',
    ]])
    @endcomponent

<div class="col-5 mx-auto">
  <div id="successMessage" class="d-none alert alert-dismissible alert-success">
      <button type="button" class="close" data-dismiss="alert">×</button>
      <strong>Changes saved successfully!</strong>
  </div>
    <div class="d-flex justify-content-between py-2">
        <div class="d-flex align-items-center justify-content-start">
            <h5 class="mr-4 mb-0">Sort Groups</h5><small class="text-muted"> Drag items to sort. </small>
        </div>
        <button id="submitButton" class="btn btn-sm btn-primary">@lang('general.save')</button>
    </div>
    <div id="sortable_container_header">
        @foreach($groupHeaders as $groupHeader => $groups)
        <div name="{{ $groupHeader }}" id="header_{{ str_replace(' ', '', $groupHeader) }}" class="card my-3 header">
            <div class="card-header">
                <b>{{ $groupHeader }}</b>
            </div>
            <div class="card-body p-0">
                <table class="table m-0 w-100">
                    <tbody name="{{ $groupHeader }}" class="group_container" style="min-height: 10px !important;">
                        @foreach($groups as $group)
                        <tr style="{{ $loop->iteration % 2 == 0 ? 'background-color: #e2e2e2;' : '' }}" id="group_{{ $group->id }}">
                            <td class="pl-3 w-100 d-flex flex-column" style="border-top:none;">
                                <a target="_blank" href="/admin/groups/{{ $group->id }}/subgroups">{{ $group->name }}</a>
                               @include('admin.groups.partials.indexRecursiveSort', ['group' => $group, 'count' => 1])
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endforeach
    <div>
</div>
@endsection

@section('scripts')
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script>
$(function() {
    $("#sortable_container_header").sortable({
        items: ".header",
    });
    $("#sortable_container_header").disableSelection();


    $(".group_container").sortable({
        axis: 'y',
        connectWith: ".group_container",
    });

    $(".group_container").disableSelection();

    $('#submitButton').click(function(e) {
        var categories = new Array();
        $('#sortable_container_header').children().each(function(e) {
            var header = $(this).attr('name');
            if(header)
            {
                var toAdd = [];
                toAdd[header] = [];
                $('tbody[name="'+header+'"]').children().each(function(e) {
                    toAdd[header].push($(this).attr('id').split('_')[1]);
                });
                categories.push(toAdd);
            }
        });

        var output = {};
        for(var value in categories)
        {
            Object.assign(output, categories[value]);
        }
        $.ajax({
            url: "/admin/groups/sort", 
            type : "PUT",
            data : 
            {
                "categories": output,
                "_token": "{{ csrf_token() }}",
            },
            success: function () {
                $('#successMessage').removeClass('d-none');
            }
        });
    });
});

</script>
@endsection