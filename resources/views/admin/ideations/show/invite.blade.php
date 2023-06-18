@extends('admin.ideations.show.layout')

@section('inner-page-content')
<div class="col-md-6 mx-md-auto">
  <h4>Invite User</h4>
  <div class="card p-3">
    <form action="/admin/ideations/{{ $ideation->id }}/members/invite" method="POST">
      @csrf
      <div class="d-flex">
        <select name="userId" class="selectpicker" data-live-search="true" required>
          @foreach($users as $user)
            <option data-token="{{ $user->name }}" value="{{ $user->id }}">{{ $user->name }}</option>
          @endforeach
        </select> 
      </div>
      <button type="submit" class="btn btn-primary mt-3">Invite</button>
    </form>
  </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/js/bootstrap-select.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/js/i18n/defaults-*.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/css/bootstrap-select.min.css">
  <script>
    $(document).ready( function () {
      $('.selectpicker').selectpicker();
    });
  </script>
@endsection