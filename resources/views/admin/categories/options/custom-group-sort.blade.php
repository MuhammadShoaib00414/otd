@extends('admin.categories.layout')


@section('inner-page-content')
<div class="col-6 mx-auto" style="margin-bottom: 100px;">
    <div id="successMessage" class="d-none alert alert-dismissible alert-success">
        <button type="button" class="close" data-dismiss="alert">×</button>
        <strong>Changes saved successfully!</strong>
    </div>
    <div class="mb-2">
        <div class="d-flex justify-content-between ml-2 align-items-center">
            <h5 class="mr-3 mt-1">Sort {{ $taxonomy->name }}</h5>
            <form method="GET" action="/admin/categories/{{ $taxonomy->id }}/sort" onchange="this.submit();">
                <select id="sort" name="sort" class="custom-select">
                    <option value="profile" {{ $sortType == 'profile' ? 'selected':'' }}>Profile</option>
                    <!-- <option value="mentor" {{ $sortType == 'mentor' ? 'selected':'' }}>Ask a Mentor</option>
                    <option value="browse" {{ $sortType == 'browse' ? 'selected':'' }}>Find My People</option> -->
                </select>
            </form>
            <div class="dropdown">
                <button id="copyButton" class="btn btn-outline-primary btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Copy order from...</button>
                <div class="dropdown-menu">

                    <span style="cursor: pointer;" id="alphabetize" class="alphabetizeButton dropdown-item">Alphabetize</span>
                </div>
            </div>
            <button id="saveButton" class="btn btn-primary btn-sm">@lang('general.save')</button>
        </div>
    </div>

    <div id="sortable_container_parents" class="mx-auto">

        @foreach($groupedOptions as $parent => $options)
        @if(!empty($parent))
        <div class="card card-header parent card-body col p-0  option mb-3" id="parent_{{ $parent }}">
            <div id="option_{{$options->first()->id }}" class="option p-3" style="{{ ($loop->iteration % 2 == 0) ? 'background-color: #e2e2e2;' : '' }}">
                <span>{{ $parent }}</span>
            </div>
        </div>
        @endif
        @endforeach
    </div>


</div>
@endsection

@section('scripts')
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>


<script>
    $(function() {
        $("#sortable_container_parents").sortable({
            items: ".parent",
        });
        // $("#sortable_container_parents").disableSelection();

        // $(".sortable_container_options").sortable({
        //     axis: 'y',
        //     connectWith: ".option",
        // });
        //$(".sortable_container_options").disableSelection();

        $('#saveButton').click(function(e) {
            var sortedItems = [];

            $('.parent').each(function() {
                var parentRaw = $(this).prop('id').split('_')[1];
                
                if (parentRaw == "")
                    parentRaw = "empty";
                var toAdd = [];
                toAdd[parentRaw] = [];

                $(this).find('.option').each(function() {
                    toAdd[parentRaw].push($(this).prop('id').split('_')[1]);
                });

                sortedItems.push(toAdd);
            });

            var output = {};
            for (var value in sortedItems) {
                Object.assign(output, sortedItems[value]);
            }
           

            $.ajax({
                url: "/admin/categories/{{ $taxonomy->id }}/sort/alphabetizecustomgroup",
                type: "PUT",
                data: {
                    "orderType": "{{ $sortType }}",
                    "groupedOptions": (output),
                    "_token": "{{ csrf_token() }}",
                },
                success: function() {
                    $('#successMessage').removeClass('d-none');
                }
            });
        });

        $('.copyFrom').click(function(e) {
            var copyFromString = $(this).html();
            var copyFrom = $(this).prop('id');
            if (!confirm('Are you sure you want to copy the order from "' + copyFromString + '" and save it?'))
                return false;

            $.ajax({
                url: "/admin/categories/{{ $taxonomy->id }}/sort/copy",
                type: "PUT",
                data: {
                    "to": "{{ $sortType }}",
                    "from": copyFrom,
                    "_token": "{{ csrf_token() }}",
                },
                success: function() {
                    location.reload();
                }
            });
        });

        $('.alphabetizeButton').click(function(e) {
            if (!confirm('Are you sure you want to alphabetize these categories and save?'))
                return false;

                var parentItems = $('.parent').toArray();
                var sortedItems = [];
                parentItems.sort(function(a, b) {
                    var textA = $(a).text().toUpperCase();
                    var textB = $(b).text().toUpperCase();
                    return (textA < textB) ? -1 : (textA > textB) ? 1 : 0;
                });
                
                parentItems.forEach(function(item) {
                    var parentRaw = item.getAttribute('id').split('_')[1];
                    // console.log($('parentRaw', this).prop('id'), parentRaw);
                    if (parentRaw == "")
                        parentRaw = "empty";
                    var toAdd = [];
                    toAdd[parentRaw] = [];
                    
                    var options = item.querySelectorAll('.option');
                    options.forEach(function(element) {
                        var id = element.getAttribute('id');
                        var parentId = id.split('_')[1];
                        
                        if (!toAdd[parentRaw]) {
                            toAdd[parentRaw] = [];
                        }
                        
                        toAdd[parentRaw].push(parentId);
                    });
                    // item.find('.option').each(function() {
                    //     toAdd[parentRaw].push($(this).getAttribute('id').split('_')[1]);
                    // });

                    sortedItems.push(toAdd);
                });
               

                var output = {};
                for (var value in sortedItems) {
                    Object.assign(output, sortedItems[value]);
                }
                $.ajax({
                    url: "/admin/categories/{{ $taxonomy->id }}/sort/alphabetizecustomgroup",
                    type: "PUT",
                    data: {
                        "orderType": "{{ $sortType }}",
                        "groupedOptions": (output),
                        "_token": "{{ csrf_token() }}",
                    },
                    success: function() {
                        $('#successMessage').removeClass('d-none');
                        location.reload();
                    }
                });
            // $.ajax({
            //     url: "/admin/categories/{{ $taxonomy->id }}/sort/alphabetizecustomgroup",
            //     type: "PUT",
            //     data: {
            //         "orderType": "{{ $sortType }}",
            //         "_token": "{{ csrf_token() }}",
            //     },
            //     success: function() {
            //         // location.reload();
            //     }
            // });
        });
    });
</script>
@endsection