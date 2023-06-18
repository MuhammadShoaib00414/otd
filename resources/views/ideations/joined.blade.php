@extends('ideations.layout')

@section('stylesheets')
<style>
.hover-hand:hover { cursor: pointer; }
</style>
@endsection

@section('inner-content')
    <div class="d-flex align-items-center justify-content-between mb-1 mt-4">
        <div>
            <a href="/ideations/invited" class="btn btn-outline-secondary mr-2">@lang('ideations.invited')</a>
            <a href="/ideations/joined" class="btn btn-secondary mr-2">@lang('ideations.joined')</a>
        </div>
        @if(request()->user()->is_admin || request()->user()->is_group_admin)
            @if($proposedCount)
            <div>
                <a href="/ideations/proposed">@lang('ideations.proposed-x', ['proposed' => $proposedCount])</a>
            </div>
            @endif
        @endif
        <div class="text-right d-flex flex-nowrap">
            <div class="pt-1">
                @include('partials.tutorial', ['tutorial' => \App\Tutorial::named('Ideations') ])
            </div>
            @if(request()->user()->is_admin || request()->user()->is_group_admin || !getSetting('is_ideation_approval_enabled'))
                <a href="/ideations/create" class="btn btn-outline-secondary ml-2">@lang('ideations.new-ideation')</a>
            @else
                <a href="/ideations/propose" class="btn btn-outline-secondary ml-2">@lang('ideations.propose')</a>
            @endif
        </div>
    </div>
    <div class="card">
        <table class="table mb-0">
        @forelse($ideations as $ideation)
            <tr class="hover-hand" data-url="/ideations/{{ $ideation->slug }}">
                <td style="width: 3em;">
                    <div style="height: 2.75em; width: 2.75em; border-radius: 50%; background-image: url('{{ $ideation->owner->photo_path }}'); background-size: cover; background-position: center;">
                    </div>
                </td>
                <td>
                    <b>{{ $ideation->name }}</b><br>
                    <span>{{ $ideation->owner->name }}</span>
                </td>
                <td style="vertical-align: middle;">
                    <i class="icon-chat mr-1"></i> {{ $ideation->posts()->count() }}
                </td>
                <td style="vertical-align: middle;">
                    @if($ideation->is_approved)
                        <i class="icon-user mr-1"></i>
                        @if($ideation->max_participants)
                        {{ $ideation->participants()->count() }}/{{ $ideation->max_participants }}
                        @else
                        <span style="font-size: 18px">&infin;</span>
                        @endif
                    @else
                        <span class="badge badge-secondary">@lang('ideations.proposed')</span>
                    @endif
                </td>
                <td class="text-right" style="vertical-align: middle;">
                    <a href="/ideations/{{ $ideation->slug }}" class="btn btn-primary" dusk="view-ideation{{ $loop->iteration }}">@lang('general.view')</button>
                </td>
            </tr>
        @empty
            </table>
            @include('partials.empty')
        @endforelse


        </table>
    </div>

    <div class="d-flex justify-content-center">
        {{ $ideations->links() }}
    </div>
@endsection

@section('scripts')
<script>
    $('.hover-hand').on('click', function(event) {
        window.location = event.currentTarget.getAttribute('data-url');
    });
</script>
@endsection