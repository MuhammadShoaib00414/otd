@extends('ideations.layout')

@section('stylesheets')
<style>
.hover-hand:hover { cursor: pointer; }
</style>
@endsection

@section('inner-content')
    <div>
        @if(session()->has('error'))
        <div class="alert alert-danger mt-3">
            <p>{{ session('error') }}</p>
        </div>
        @endif
    </div>
    <div class="d-flex align-items-center justify-content-between mb-1 mt-4">
        <div>
            <a href="/ideations/invited" class="btn btn-secondary mr-2">@lang('ideations.invited')</a>
            <a href="/ideations/joined" class="btn btn-outline-secondary mr-2">@lang('ideations.joined')</a>
        </div>
        @if(request()->user()->is_admin || request()->user()->is_group_admin)
            @if($proposedCount)
            <div>
                <a href="/ideations/proposed">@lang('ideations.proposed-x', ['proposed' => $proposedCount])</a>
            </div>
            @endif
        @endif
        <div class="text-right">
            @if(request()->user()->is_admin || request()->user()->is_group_admin || !getSetting('is_ideation_approval_enabled'))
                <a href="/ideations/create" class="btn btn-outline-secondary">@lang('ideations.new-ideation')</a>
            @else
                <a href="/ideations/propose" class="btn btn-outline-secondary">@lang('ideations.propose')</a>
            @endif
        </div>
    </div>
    <div class="card">
        <table class="table mb-0">
        @forelse($ideations as $ideation)
            <tr>
                <td style="width: 3em;">
                    <div style="height: 2.75em; width: 2.75em; border-radius: 50%; background-image: url('{{ $ideation->owner->photo_path }}'); background-size: cover; background-position: center;">
                    </div>
                </td>
                <td>
                    <b><a href="/ideations/{{ $ideation->slug }}/viewInvitation">{{ $ideation->name }}</a></b>
                    @if($ideation->currentUserInvitation($user_id, $ideation->id) && $ideation->currentUserInvitation($user_id, $ideation->id)->read_at == null)
                        <span class="badge badge-danger">@lang('general.new-lc')</span>
                    @endif
                    <br>
                    <span>{{ $ideation->owner->name }}</span>
                </td>
                <td style="vertical-align: middle;">
                    <i class="icon-chat mr-1"></i> {{ $ideation->posts()->count() }}
                </td>
                <td style="vertical-align: middle;">
                    <i class="icon-user mr-1"></i>
                    @if($ideation->max_participants)
                    {{ $ideation->participants()->count() }}/{{ $ideation->max_participants }}
                    @else
                    <span style="font-size: 18px">&infin;</span>
                    @endif
                </td>
                <td class="text-right" style="vertical-align: middle;">
                    <a href="/ideations/{{ $ideation->slug }}/viewInvitation" class="btn btn-outline-secondary view-invitation">@lang('general.view')</a>
                    @if($ideation->is_joinable)
                        <form action="/ideations/{{ $ideation->slug }}/join" method="post" class="d-inline-block">
                            @csrf
                            <button type="submit" class="btn btn-primary">@lang('general.join')</button>
                        </form>
                    @endif
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