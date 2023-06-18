@extends('layouts.spa')

@section('stylesheets')
 <script src="{{asset('js/main.js')}}?v={{date('YmdHis')}}" defer></script>
@endsection

@section('content')
  <div id="otdSpa">
    <app></app>
  </div>
@endsection