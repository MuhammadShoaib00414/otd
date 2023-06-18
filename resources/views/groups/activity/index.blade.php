@extends('groups.layout')

@section('inner-content')
<h3 class="mb-2">@lang('groups.Activity')</h3>
<div class="card">
    <table class="table">
        <tr class="card-header">
            <td><b>@lang('groups.Action')</b></td>
            <td class="text-right"><b>@lang('general.users')</b></td>
            <td class="text-right"><b>@lang('groups.Total views')</b>
            @if($group->is_reporting_user_data_enabled)
                <td></td>
            @endif
        </tr>
        <tbody>
            @foreach($clicks as $click)
                <tr>
                    <td>@lang('activity.'.$click->action)</td>
                    <td class="text-right">{{ $click->userCount }}</td>
                    <td class="text-right">{{ $click->count }}</td>
                    @if($group->is_reporting_user_data_enabled)
                        <td class="text-right"><a href="/groups/{{ $group->slug }}/activity/{{ str_replace(' ', '-', $click->action) }}">@lang('groups.view')</a></td>
                    @endif
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection