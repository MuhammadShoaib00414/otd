@extends('layouts.app')

@section('stylesheets')
  @parent
  <style>
    .pagination {
      justify-content: center;
    }
  </style>
  <link rel="stylesheet" href="/redactorx-1-2-0/redactorx.min.css" />
@endsection

@section('content')

<div class="main-container bg-lightest-brand">
  <div class="row justify-content-center mt-4 mb-6">
    <div class="col-12 col-sm-8 col-md-6">
      @if (count($posts) > 0)
          @foreach ($posts as $post)
              @include('partials.feed', ['post' => $post, 'group' => null])
          @endforeach
      @else
          @include('partials.empty')
      @endif
    </div>
  </div>
</section>
</div>
@endsection