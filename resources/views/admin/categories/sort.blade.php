@extends('admin.categories.layout')

@section('inner-page-content')
<div class="col-4 mx-auto" style="margin-bottom: 100px;">
    <div id="successMessage" class="d-none alert alert-dismissible alert-success">
          <button type="button" class="close" data-dismiss="alert">×</button>
          <strong>Changes saved successfully!</strong>
      </div>
    <div class="mb-2">
        <div class="d-flex justify-content-between ml-2 align-items-center">
            <h5 class="mr-3 mt-1">Sort Taxonomies</h5>
            <form method="GET" action="/admin/categories/sort" onchange="this.submit();">
                <select id="sort" name="sort" class="custom-select">
                    <option value="profile" {{ $sortType == 'profile' ? 'selected':'' }}>Profile</option>
                    <option value="mentor" {{ $sortType == 'mentor' ? 'selected':'' }}>Ask a Mentor</option>
                    <option value="browse" {{ $sortType == 'browse' ? 'selected':'' }}>Find My People</option>
                </select>
            </form>
            <button id="saveButton" class="btn btn-primary btn-sm">@lang('general.save')</button>
        </div>
    </div>

    <div id="sortable_container" class="mx-auto">
        @foreach($taxonomies as $taxonomy)
            <div class="card card-header taxonomy mb-2" id="taxonomy_{{ $taxonomy->id }}">
                {{ $taxonomy->name }}
            </div>
        @endforeach
    </div>
</div>
@endsection

@section('scripts')
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script>
$(function() {
    $("#sortable_container").sortable({
        items: ".taxonomy",
    });
    $("#sortable_container").disableSelection();

    $('#saveButton').click(function(e) {
        var sortedItems = [];
        
        $('.taxonomy').each(function (e) {
            sortedItems.push($(this).prop('id').split('_')[1]);
        });

        $.ajax({
            url: "/admin/categories/sort", 
            type : "PUT",
            data : 
            {
                "orderType": "{{ $sortType }}",
                "taxonomies": sortedItems,
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