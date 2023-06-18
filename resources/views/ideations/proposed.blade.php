@extends('ideations.layout')

@section('stylesheets')
<style>
.hover-hand:hover { cursor: pointer; }
</style>
@endsection

@section('inner-content')
    <div class="my-3">
        <h4>Proposed</h4>
    </div>
    @if ($errors->any())
        <div class="alert alert-danger">
          <ul>
              @foreach ($errors->all() as $error)
              <li>{{ $error }}</li>
              @endforeach
          </ul>
        </div>
    @endif
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
                    <span class="badge badge-secondary">@lang('ideations.proposed')</span>
                </td>
                <td class="text-right" style="vertical-align: middle;">
                    <a href="/ideations/{{ $ideation->slug }}" class="btn btn-primary">@lang('general.view')</button>
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