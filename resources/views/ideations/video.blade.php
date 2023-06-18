@extends('ideations.show.layout')

@section('stylesheets')
<style>
    .btn-grey {
        background-color: #dadcdf;
        border-color: #dadcdf;
        color: #645f5f;
    }
    .btn-grey:hover {
        background-color: #ced1d5;
        border-color: #ced1d5;
        color: #645f5f;
    }
    .hover-hand:hover { cursor: pointer; }
    .nav-tabs .nav-item .nav-link:not(.active) {
      color: #515457;
    }
    .nav-item .nav-link.active {
      border-color: #1a2b40 !important;
      color: #1a2b40;
      font-weight: bold;
    }
</style>
@endsection

@section('inner-content')
  <div id="jitsiContainer" style="width: 100%; height: 85vh;">
  </div>
@endsection

@section('scripts')
  <script src='https://meet.jit.si/external_api.js'></script>
  <script>
    const domain = 'meet.onthedotglobal.com';
    const options = {
        roomName: '{{ $ideation->videoRoom->slug }}',
        width: '100%',
        height: '100%',
        parentNode: document.querySelector('#jitsiContainer'),
        userInfo: {
          email: '{{ $authUser->email }}',
          displayName: '{{ $authUser->name }}'
        },
        interfaceConfigOverwrite: {
          HIDE_DEEP_LINKING_LOGO: true,
        },
        configOverwrite: {
          disableDeepLinking: true,
        },
        devices: {},
    };

    const api = new JitsiMeetExternalAPI(domain, options);
  </script>
@endsection