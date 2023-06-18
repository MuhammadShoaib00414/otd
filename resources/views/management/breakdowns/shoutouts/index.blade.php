@extends('layouts.app')

@section('stylesheets')
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
@endsection

@section('content')
<div style="background-color: #565d6b;">
  <div class="container-fluid py-3" style="position: relative;">
    <a href="/home" class="d-inline-block mb-2" style="font-size: 14px; position: absolute; top: 50%; color: #fff; transform: translateY(-50%);"><i class="icon-chevron-small-right"></i> @lang('messages.my-dashboard')</a>
    <h4 class="mb-0 text-center no-underline" style="color: #fff;">Management Dashboard</h4>
  </div>
</div>
<div class="main-container bg-lightest-brand py-4">
  <div class="container">
    <div class="row">
      <div class="col-12">
        <div class="card"> 
          <div class="card-body">
            <div class="d-flex justify-content-between">
              <div>
                <a href="/management/my-direct-reports" class="btn btn-secondary">My Direct Reports</a>
                <a href="/management/organization" class="btn btn-outline-secondary">My Organization</a>
              </div>
              <input type="text" name="daterange" value="{{ Carbon\Carbon::now()->subDays(30)->format('m/d/Y') }} - {{ Carbon\Carbon::now()->format('m/d/Y') }}" class="form-control" style="width: 300px;" />
            </div>
            <hr>
            <h4 class="card-title">Shoutouts Made</h4>
            <table class="table">
              <thead>
                <tr>
                  <td><b>user</b></td>
                  <td class="text-right"><b>shoutouts made</b></td>
                  <td></td>
                </tr>
              </thead>
              <tbody>
                @foreach($results as $result)
                <tr>
                  <td><a href="/users/{{ $result->id }}">{{ $result->name }}</a></td>
                  <td class="text-right">{{ $result->count }}</td>
                  <td class="text-right"><a href="/management/breakdowns/shoutouts-made/{{ $result->id }}?count={{ $result->count }}">View Shoutouts</a></td>
                </tr>
                @endforeach
              </tbody>
            </table>

          </div>
        </div>

      </div>
    </div>
  </div>
</div>
@endsection