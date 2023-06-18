@extends('admin.layout')

@section('page-content')

    @component('admin.partials.breadcrumbs', ['links' => [
        'Points' => '',
    ]])
    @endcomponent

    <div>
        <h5>Points</h5>
        <p>Manage actions and the rewarded points for a given action.</p>
    </div>
    
    <form method="post" action="/admin/points" class="mb-4">
        @csrf
        @method('PUT')

        <div class="form-group">
            <span class="d-block mb-2">Show points to users</span>
            <div class="custom-control custom-radio custom-control-inline">
              <input type="radio" id="is_points_enabled" name="is_points_enabled" value="1" class="custom-control-input" {{ $is_points_enabled ? 'checked' : '' }}>
              <label class="custom-control-label" for="is_points_enabled">Enabled</label>
            </div>
            <div class="custom-control custom-radio custom-control-inline">
              <input type="radio" id="is_points_disabled" name="is_points_enabled" value="0" class="custom-control-input" {{ $is_points_enabled ? '' : 'checked' }}>
              <label class="custom-control-label" for="is_points_disabled">Disabled</label>
            </div>
        </div>

        <table class="table mt-2">
            <thead>
                <tr>
                    <th scope="col"><b>Action</b></th>
                    <th scope="col"><b>Points Rewarded</b></th>
                </tr>
            </thead>
            @foreach($points as $point)
            <tr>
                <td style="vertical-align: middle;"><b>{{ $point->name }}</b><br>{{ $point->description }}</td>
                <td style="vertical-align: middle;"><input type="text" name="points[{{ $point->key }}]" class="form-control form-control-sm" value="{{ $point->value }}" style="max-width: 10em;"></td>
            </tr>
            @endforeach
        </table>
        <div class="text-right">
            <button type="submit" class="btn btn-info">@lang('general.save') changes</button>
        </div>
    </form>
@endsection

@push('scriptstack')
    @if(Session::has('success'))
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@8"></script>
        <script>
            Swal.fire({
              title: 'Success!',
              text: 'Changes saved.',
              type: 'success',
              confirmButtonText: 'Close'
            })
        </script>
    @endif
@endpush