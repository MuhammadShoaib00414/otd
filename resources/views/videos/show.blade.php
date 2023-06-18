@extends('layouts.app')

@section('content')

<div class="main-container bg-lightest-brand">
  <div class="col-9 mx-auto">
    <div class="row mt-2">
      <a id="back"> < @lang('messages.back')</a>
    </div>
  </div>
  <div class="row justify-content-center mt-2 mb-6">
    <iframe src="{{ $src }}" style="width: 70%; height: 38em;"></iframe>
  </div>
</section>
</div>
@endsection

@section('scripts')
<script>
  $('#back').prop('href', document.referrer);
</script>
@endsection