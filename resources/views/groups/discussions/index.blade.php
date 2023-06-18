@extends('groups.layout')

@section('stylesheets')
@parent
<style>
.hover-hand:hover { cursor: pointer; }
</style>
@endsection

@section('body-class', 'col-md-9')

@section('inner-content')
    <div class="d-flex mb-3 justify-content-between align-items-center">
        <h4 class="mb-0">{{ $group->discussions_page }} ({{ $discussions->count() }})</h4>
        <form>
            <div class="input-group">
                <input type="text" class="form-control" name="q" value="{{ (request()->has('q')) ? request()->input('q') : '' }}" placeholder="@lang('general.Search for...')">
                <div class="input-group-prepend">
                    <button type="submit" class="btn btn-light" style="border: 1px solid #ced4da; border-left: 0; background-color: #fff; color: #1a2b40;">
                        <i class="icon-magnifying-glass"></i>
                    </button>
                </div>
            </div>
        </form>
        <a href="/groups/{{ $group->slug }}/discussions/create" class="btn btn-sm btn-secondary">@lang('discussions.New Discussion')</a>
    </div>

    <div class="card">
        <table class="table mb-0">
        @forelse($discussions as $thread)
            <tr class="hover-hand" data-url="/groups/{{ $group->slug }}/discussions/{{ $thread->slug }}">
                <td style="width: 3em;">
                    <div style="height: 2.75em; width: 2.75em; border-radius: 50%; background-image: url('{{ $thread->owner->photo_path }}'); background-size: cover; background-position: center;">
                    </div>
                </td>
                <td>
                    <b>{{ $thread->name }}</b><br>
                    <span>{{ $thread->owner->name }}</span>
                </td>
                <td style="vertical-align: middle;">
                    <i class="icon-chat mr-1"></i> {{ $thread->posts()->count() }}
                </td>
                <td class="text-right" style="vertical-align: middle;">
                    {{ $thread->updated_at->tz(request()->user()->timezone)->diffForHumans() }}
                </td>
            </tr>
        @empty
        </table>
        @include('partials.empty')

        @endforelse


        </table>
    </div>

    <div class="d-flex justify-content-center">
        {{ $discussions->links() }}
    </div>
@endsection

@section('scripts')
<script>
    $('.hover-hand').on('click', function(event) {
        window.location = event.currentTarget.getAttribute('data-url');
    });
</script>
@endsection