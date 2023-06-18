@extends('layouts.app')

@section('stylesheets')
  
@endsection

@section('content')

  <main class="main" role="main">
    <div id="jitsiContainer" style="width: 100%; height: 85vh;">
    </div>
  </main>
@endsection

  @push('scriptstack')
   <script src='https://meet.jit.si/external_api.js'></script>
    <script>
      const domain = 'meet.onthedotglobal.com';
      const options = {
          roomName: '{{ $slug }}',
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
  @endpush