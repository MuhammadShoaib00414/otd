@extends('layouts.app')

@section('content')

  <main class="main" role="main">
    <div class="pb-5 pt-3 bg-lightest-brand">
      <div class="container">
        <div class="row">
          <div class="col-md-12 mx-auto">
          <a href="/home" class="d-inline-block mb-3" style="font-size: 14px;"><i class="icon-chevron-small-left"></i> @lang('messages.my-dashboard')</a>
          <h3> @lang('messages.points-youve-earned') </h3>
            <div class="card">
              <div class="card-body">
                <table class="table">
                  <thead>
                    <tr>
                      <th> @lang('messages.date') </th>
                      <th> @lang('messages.action') </th>
                      <th> @lang('messages.points') </th>
                    </tr>
                  </thead>
                    <tbody>
                      @foreach($awardedPoints as $awardedPoint)
                        <tr>
                          <td style="min-width: 8em;"><span>{{ $awardedPoint->created_at->tz(request()->user()->timezone)->toFormattedDateString() }}</span><br>
                              <span>{{ $awardedPoint->created_at->tz(request()->user()->timezone)->format('h:i a') }}</span></td>
                          <td><span><b>{{ $awardedPoint->point->name }}</b></span>
                              <span class="d-block text-muted">{{ $awardedPoint->point->description }}</span></td>
                          <td class="text-center" style="vertical-align: middle;"><span class="my-auto">{{ $awardedPoint->point->value }}</span></td>
                        </tr>
                      @endforeach
                    </tbody>
                  </table>
                </div>
              </div>
              <div class="d-flex justify-content-center">
              {{ $awardedPoints->links() }}
            </div>
          </div>
        </div>
      </div>
    </div>
  </main>
  @endsection