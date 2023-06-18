@extends('layouts.app')

@section('content')
  <div class="bg-light-secondary-brand py-3">
    <h2 class="font-weight-bold text-center">{{ getSetting('my_groups_page_name', $authUser->locale) }}</h2>
  </div>
  <div class="container my-3" style="font-size: 16px;">
    @foreach($authUser->dashboard_groups as $groupHeader => $groups)
      <h5 class="text-uppercase text-muted mb-1 mt-1 ml-2" style="font-size: 14px;">{{ $groupHeader }}</h5>
      @foreach($groups as $group)
        @include('groups.partials.grouplisting', ['group' => $group])
      @endforeach
    @endforeach
  </div>
@endsection