@extends('groups.layout')
@section('head')
    <style>
    .select-picker {
        border: 1px solid #444546!important;
    }
    </style>
@endsection
@section('body-class', 'col-md-9')

@section('inner-content')
    <div class="col-md-6">
      <h3 class="font-weight-bold mb-2">Add Member</h3>
      <form action="/groups/{{ $group->slug }}/members/add" method="post">
        @csrf
        <select required name="user" class="select-picker border form-control mb-3" required data-live-search="true">
           <option selected disabled>Select one</option>
          @foreach($users as $user)
            <option value="{{ $user->id }}">{{ $user->name }}</option>
          @endforeach
        </select>
        <button type="submit" class="btn btn-secondary">Add</button>
      </form>
    </div>


@endsection

@section('scripts')
    <script src="https://unpkg.com/gijgo@1.9.13/js/gijgo.min.js" type="text/javascript"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.9/dist/js/bootstrap-select.min.js"></script>
    <script>
        $.fn.selectpicker.Constructor.BootstrapVersion = '4.4.1';
        $('.select-picker').selectpicker();

    </script>
@endsection
