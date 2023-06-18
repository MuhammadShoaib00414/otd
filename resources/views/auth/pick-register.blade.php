<!doctype html>
<html lang="{{ App::getLocale() }}">

    <head>
        <meta charset="utf-8">
        <title>OTD Directory - Register</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0, initial-scale=1.0">
        <link href="https://fonts.googleapis.com/css?family=Rubik:300,400,400i,500" rel="stylesheet">
        <link href="/assets/css/socicon.css" rel="stylesheet" type="text/css" media="all" />
        <link href="/assets/css/entypo.css" rel="stylesheet" type="text/css" media="all" />
        <link href="/css/main.css" rel="stylesheet" type="text/css" media="all" />
        @include('partials.themestyles')
    </head>

    <style>
        .registrationpage {
          position: relative;
          transition: all 0.3s ease-in-out;
          cursor: pointer;
          text-decoration: none;
          color: black;
          font-size: 1.2em;
        }

        .registrationpage::after {
          content: '';
          position: absolute;
          z-index: -2;
          width: 100%;
          height: 100%;
          opacity: 0;
          border-radius: 5px;
          box-shadow: 0 5px 15px rgba(0,0,0,0.3);
          transition: opacity 0.3s ease-in-out;
          top: 0;
          left: 0;
        }

        .registrationpage:hover {
          color: black;
          text-decoration: none;
          transform: scale(1.02, 1.02);
        }

        .registrationpage:hover::after {
          opacity: 1;
        }
    </style>

    <body>

        <div class="nav-container">
        </div>
        <div class="main-container">
            <section class="fullwidth-split">
                <div class="container-fluid">
                    <div class="row no-gutters height-100 justify-content-center">
                        <div class="col-12 fullwidth-split-image d-flex flex-column flex-lg-row justify-content-center align-items-center px-2" style="background-color: {{ getThemeColors()->primary['100'] }}">
                            <div class="{{ (new \Jenssegers\Agent\Agent)->isMobile() ? 'row' : 'col-6' }}">
                                <img class="mx-auto" style="max-width: 100%; min-width: 75%; max-height: 90vh;" src="{{ getsetting('pick_registration_image_url', App::getLocale()) }}">
                            </div>
                            <div class="{{ (new \Jenssegers\Agent\Agent)->isMobile() ? 'row w-100' : 'col-6' }} pt-5 pb-5">
                                <div class="card w-100">
                                    @if(getSetting('is_localization_enabled'))
                                        <div class="text-center mt-2">
                                            @if(App::getLocale() == 'en')
                                                <a href="/register/pick?locale=es" class="btn btn-sm btn-secondary">En Espanol</a>
                                            @else
                                                <a href="/register/pick?locale=en" class="btn btn-sm btn-secondary">In English</a>
                                            @endif
                                        </div>
                                    @endif
                                    <div class="card-body">
                                        @foreach($pages as $page)  
                                        <a href="/register/{{ $page->slug }}" class="card card-body registrationpage">
                                            @if(!$page->ticket_id || $page->ticket->price == 0)
                                                {{ $page->name}}
                                            @else
                                            <div class="d-flex justify-content-between">
                                                <span>{{ $page->name }}</span>
                                            </div>
                                            @endif
                                        </a> 
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            <!--end of col-->
                        </div>
                        <!--end of col-->
                    </div>
                    <!--end of row-->
                </div>
                <!--end of container-->
            </section>
            <!--end of section-->
        </div>


        <script type="text/javascript" src="/assets/js/jquery.min.js"></script>
        <script type="text/javascript" src="/assets/js/popper.min.js"></script>
        <script type="text/javascript" src="/assets/js/jquery.smartWizard.min.js"></script>
        <script type="text/javascript" src="/assets/js/flickity.pkgd.min.js"></script>
        <script type="text/javascript" src="/assets/js/scrollMonitor.js"></script>
        <script type="text/javascript" src="/assets/js/smooth-scroll.polyfills.js"></script>
        <script type="text/javascript" src="/assets/js/prism.js"></script>
        <script type="text/javascript" src="/assets/js/zoom.min.js"></script>
        <script type="text/javascript" src="/assets/js/bootstrap.js"></script>
        <script type="text/javascript" src="/assets/js/theme.js"></script>

    </body>

</html>
