<!doctype html>
<html lang="en">

    <head>
        <!-- Global site tag (gtag.js) - Google Analytics -->
        <script async src="https://www.googletagmanager.com/gtag/js?id=UA-86306701-2"></script>
        <script>
          window.dataLayer = window.dataLayer || [];
          function gtag(){dataLayer.push(arguments);}
          gtag('js', new Date());
          gtag('config', 'UA-86306701-2');
        </script>

        <meta charset="utf-8">
        <title>{{ getSetting('name') }}</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="A robust suite of app and landing page templates by Medium Rare">
        <link href="https://fonts.googleapis.com/css?family=Montserrat:200,300i,400,500,600,700|Playfair+Display" rel="stylesheet">
        <link href="/assets/css/socicon.css" rel="stylesheet" type="text/css" media="all" />
        <link href="/assets/css/entypo.css" rel="stylesheet" type="text/css" media="all" />
        <link href="/css/main.css?v=2" rel="stylesheet" type="text/css" media="all" />
        @include('partials.themestyles')
    </head>

    <body class="bg-lightest-brand">
        <div class="main-container">
            <section class="space-sm" style="padding-top: 1.5em;">
                <div class="container">
                    <div class="row">
                        <div class="col text-center">
                            <img src="{{ \App\Setting::where('name', 'logo')->first()->value }}" style="height: 3.5em; max-height: 70px; object-fit: cover;">
                        </div>
                        <!--end of col-->
                    </div>
                    <!--end of row-->
                </div>
                <!--end of container-->
            </section>
            <section class="height-80 flush-with-above">
                <div class="container">
                    <div class="row justify-content-between align-items-center">
                        <div class="col-12 col-md-6 mb-4">
                            <div id="carouselExampleControls" class="carousel slide" data-ride="carousel">
                              <div class="carousel-inner">
                                @foreach($home_page_images as $image)
                                    @if($image->image_url)
                                    <div class="carousel-item {{ $loop->first ? 'active' : '' }}">
                                      <img class="d-block w-100" src="{{ $image->image_url }}" alt="First slide">
                                    </div>
                                    @endif
                                @endforeach
                              </div>
                              <a class="carousel-control-prev" href="#carouselExampleControls" role="button" data-slide="prev">
                                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                <span class="sr-only">Previous</span>
                              </a>
                              <a class="carousel-control-next" href="#carouselExampleControls" role="button" data-slide="next">
                                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                <span class="sr-only">Next</span>
                              </a>
                            </div>
                        </div>
                        <!--end of col-->
                        <div class="col-12 col-md-7 col-lg-5 mb-4 text-center text-md-left">
                            @if(getsetting('is_localization_enabled') && !Auth::check())
                                <div class="pb-2">
                                    @if(!request()->has('locale') || request()->locale != 'es')
                                        <a href="?locale=es" class="btn btn-outline-secondary btn-sm">En Español</a>
                                    @else
                                        <a href="?locale=en" class="btn btn-outline-secondary btn-sm">In English</a>
                                    @endif
                                </div>
                            @endif
                            <h1 class="display-4">Welcome</h1>
                            <h2 class="lead">{!! nl2br(App\Setting::where('name', '=', 'homepage_text')->first()->value) !!}</h2>
                            <div>
                                @auth
                                    <a href="{{ route('spa') }}" class="btn btn-primary btn-lg">@lang('messages.lets-go')</a>
                                @else
                                    @if($registration_pages->count() && getsetting('open_registration'))
                                        @if($registration_pages->count() >= 1)
                                            <a dusk="signup" href="/register/{{ $registration_pages->first()->slug }}{{ request()->has('locale') ? '?locale='.request()->locale : '' }}" class="btn btn-primary btn-lg mr-2">@lang('messages.signup')</a>
                                        @endif
                                    @endif
                                    <a href="{{ route('login') }}{{ request()->has('locale') ? '?locale='.request()->locale : '' }}" class="btn btn-primary btn-lg">@lang('messages.login')</a>
                                @endauth
                            </div>
                        </div>
                        <!--end of col-->
                    </div>
                    <!--end of row-->
                </div>
                <!--end of container-->
            </section>
            <!--end of section-->
        </div>

        <div class="modal fade" id="video-modal" tabindex="-1" aria-labelledby="video-modal-label" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-center-viewport">
                <div class="modal-content">
                    <div class="embed-responsive embed-responsive-16by9">
                        <iframe class="embed-responsive-item" data-src=_global.iframeSrc allowfullscreen></iframe>
                    </div>
                </div>
            </div>
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
