@extends('admin.categories.layout')

@section('inner-page-content')
<div class="col-6 mx-auto" style="margin-bottom: 100px;">
    <div id="successMessage" class="d-none alert alert-dismissible alert-success">
        <button type="button" class="close" data-dismiss="alert">×</button>
        <strong>Changes saved successfully!</strong>
    </div>
    <div class="card">
        <div class="card-header">
            <h5>Bulk-Edit Groupings</h5>
        </div>
        <div class="card-body">
            <form action="/admin/categories/{{ $taxonomy->id }}/groupings" method="post">
                @csrf
                @method('put')
                    <p>Leave blank for no grouping.</p>
                @foreach($groupings as $grouping)
                    @if($grouping['value'] == null || $grouping['value'] == '')
                        <?php $grouping['value'] = 'Empty Grouping'; ?>
                    @endif
                    <div class="d-flex justify-content-around">
                        <div class="form-group">
                            <label for="{{ $grouping['value'] }}">
                                {{ $grouping['value'] != '' ? $grouping['value'] : '(No Grouping)' }}
                                @if($taxonomy->options()->where('parent', $grouping['value'])->exists())
                                    <small class="text-muted">({{ $taxonomy->options()->where('parent', $grouping['value'])->count() }} options)</small>
                                @endif
                            </label>
                            <input type="text" name="groupings[{{ $grouping['value'] }}]" id="{{ $grouping['value'] }}" class="form-control" value="{{ $grouping['value'] == 'Empty Grouping' ? null : $grouping['value'] }}">
                        </div>
                        @if(getsetting('is_localization_enabled'))
                            <div class="form-group">
                            <label for="localization[{{ $grouping['value'] }}]">
                                Spanish
                            </label>
                            <input type="text" name="localization[{{ $grouping['value'] }}]" id="localization{{ $grouping['value'] }}" class="form-control" value="{{ isset($grouping['localization']) && isset($grouping['localization']['es']) && isset($grouping['localization']['es']['parent']) ? $grouping['localization']['es']['parent'] : '' }}">
                        </div>
                        @endif
                    </div>
                @endforeach
                <button type="submit" class="btn btn-primary">Save Changes</button>
            </form>
        </div>
    </div>
</div>
@endsection