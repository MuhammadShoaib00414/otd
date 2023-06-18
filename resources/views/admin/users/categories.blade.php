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
    @if(session()->has('success'))
        <div class="alert alert-success" role="alert">
          {{ session('success') }}
        </div>
    @endif
    <form method="post" action="/admin/users/{{ $user->id }}/categories">
        @csrf
        @foreach(App\Taxonomy::orderBy('profile_order_key', 'asc')->get() as $taxonomy)
          @if($taxonomy->groupedOptionsWithOrderKey('profile', false)->count())
          <div>
                <h4>{{ $taxonomy->name }}</h4>
                <div>
                  @foreach($taxonomy->groupedOptionsWithOrderKey('profile', false) as $groupName => $cats)
                    <div>
                        <p class="font-weight-bold mb-0">{{ $groupName }}</p>
                        @foreach ($cats as $option)
                            <div>
                                <label for="option{{ $option->id }}" class="checkable-tag">
                                  <input type="checkbox" id="option{{ $option->id }}" name="options[]" value="{{ $option->id }}" {{ ($user->hasOption($option->id)) ? 'checked' : '' }}>
                                  <span>{{ $option->name }}</span>
                                </label>
                            </div>
                        @endforeach
                    </div>
                  @endforeach
                </div>
          </div>
          <hr>
          @endif
        @endforeach
        <div class="text-right">
            <button type="submit" class="btn btn-lg btn-primary mb-4">@lang('general.save') changes</button>
        </div>
    </form>
@endsection

@section('scripts')

@endsection