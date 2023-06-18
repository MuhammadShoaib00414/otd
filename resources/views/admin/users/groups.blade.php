@extends('admin.users.layout')

@section('head')
    <style>
    .rotated {
        -webkit-transform: rotate(90deg);
        -moz-transform: rotate(90deg);
        -ms-transform: rotate(90deg);
        -o-transform: rotate(90deg);
        transform: rotate(90deg); 
    }
    </style>
@endsection

@section('inner-page-content')
    @if(request()->session()->has('error'))
        <div class="alert alert-danger"><b>Error:</b> User must be a member of at least one group.</div>
    @endif
    <form method="post" action="/admin/users/{{ $user->id }}/groups">
        @csrf
        <table class="table" id="table">
            <tr>
                <td style="margin-top: 0;"><b>Group</b></td>
                <td class="text-center" style="margin-top: 0;"><b>Member</b></td>
                <td class="text-center" style="margin-top: 0;"><b>Admin</b></td>
            </tr>
            @foreach($groups as $group)
                @include('admin.users.partials.groupsRecursive', ['group' => $group, 'count' => 0, 'topParentGroupId' => $group->id])
            @endforeach
        </table>
        <div class="text-right">
            <button type="submit" class="btn btn-lg btn-primary mb-4">@lang('general.save') changes</button>
        </div>
    </form>
@endsection

@section('scripts')
<script>

    function toggleChildren(groupId) {
        var expanded = $('#'+groupId+' > td > i').hasClass('rotated');
        $('#'+groupId+' > td > i').toggleClass('rotated');
        if (expanded)
            $('.belongsTo'+groupId).addClass('d-none');
        else
            $('.belongsTo'+groupId).removeClass('d-none');
    }

    $('.membership-checkbox').on('click', function (e) {
        if ($(this).is(':checked'))
            checkParentBox(this);
        else
            uncheckChildBox(this);
    });

    function checkParentBox(checkboxElement) {
        var parentId = $(checkboxElement).attr('data-immediate-parent');
        var parentCheckbox = $('#group'+parentId);
        if (parentCheckbox.length) {
            parentCheckbox.prop('checked', true);
            checkParentBox(parentCheckbox);
        }
    }
    function uncheckChildBox(checkboxElement) {
        var id = $(checkboxElement).attr('value');
        var childCheckbox = $('input[data-immediate-parent='+id+']');
        if (childCheckbox.length) {
            $(childCheckbox).each(function(index, check) {
                $(check).prop('checked', false);
                $('#admin' + $(check).data('group-id')).prop('checked', false);
                uncheckChildBox(check);
            });
        }
    }

    function getSecondPart(str) {
        return str.split('-')[1];
    }

    $('.admin-checkbox').change(function(e) {
        if($(this).is(':checked')) {
            $('#group' + $(this).prop('id').split('admin')[1]).prop('checked', true);
            checkParentBox($('#group' + $(this).prop('id').split('admin')[1]));
        }
    });

    $('.membership-checkbox').change(function(e) {
        if(!$(this).is(':checked'))
            $('#admin' + $(this).data('group-id')).prop('checked', false);
    });
    $('.admin-checkbox').change(function(e) {
        if($(this).is(':checked'))
            $('#group' + $(this).data('group-id')).prop('checked', true);
    });
</script>
@endsection