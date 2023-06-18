<!doctype html>
<html lang="{{ App::getLocale() }}">

    <head>
        <!-- Global site tag (gtag.js) - Google Analytics -->
        <meta charset="utf-8">
        <title>{{ getSetting('name', isset($authUser) ? $authUser->locale : 'en') }}</title>
        <meta name="csrf-token" content="{{ csrf_token() }}" />
        <meta name="robots" content="noindex">
        <link rel="icon" href="{{ config('app.url') }}/logo">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
        <link href="https://fonts.googleapis.com/css?family=Montserrat:200,300i,400,500,600,700|Playfair+Display" rel="stylesheet">
        <link href="/assets/css/socicon.css" rel="stylesheet" type="text/css" media="all" />
        <link href="/assets/css/entypo.css" rel="stylesheet" type="text/css" media="all" />
        <link href="/css/main.css?v=3" rel="stylesheet" type="text/css" media="all" />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.8.2/css/all.min.css" />
        <link href="/assets/css/like.css" rel="stylesheet" type="text/css" media="all" />
        
        <style>
            .btn-primary:active, .btn-outline-primary:active {
                background-color: {{ getThemeColors()->primary['600'] }} !important;
            }
            .btn-secondary:active, .btn-outline-secondary:active {
                background-color: {{ getThemeColors()->accent['600'] }} !important;
            }
            .btn:focus {
                box-shadow: none !important;
                border: none !important;
            }
            .btn:active {
                box-shadow: none !important;
                border: none !important;
            }
            .spinner {
                border: 5px solid #f3f3f3;
                border-top: 5px solid {{ getThemeColors()->primary['200'] }};
                border-radius: 50%;
                width: 50px;
                height: 50px;
                margin: auto;
                animation: spin 2s linear infinite;
              }
              @keyframes spin {
                0% { transform: rotate(0deg); }
                100% { transform: rotate(360deg); }
              }
            .no-scroll-body { height: 100vh; overflow-y: hidden; }
            .redactor-output img {
                max-width: 100%;
            }
            #searchInput::placeholder {
                color: black;
                opacity: 0.7;
            }
            .navbar-nav {
                z-index: 100000;
            }
            .dropdown {
                z-index: 5;
            }
            .redactor-output iframe {
                max-width: 100%;
            }
            #notificationBell:hover {
                text-decoration:  none;
            }
            @media (max-width: 767px) {
                body {
                    padding-top: 3em;
                }
                #logoContainer {
                    /* margin-left: auto; */
                    margin-right: auto;
                    left: 0;
                    right: 0;
                    text-align: center;
                }
                .custom-navbar {
                    top: 0;
                    width: 100vw;
                }
                #backlink {
                    z-index: 100000;
                }
            }
            
            @supports (-webkit-touch-callout: none) {
                body {
                    padding-top: 3em;
                }
                #logoContainer {
                    margin-left: auto;
                    margin-right: auto;
                    left: 0;
                    right: 0;
                    text-align: center;
                }
                .custom-navbar {
                    top: 0;
                    width: 100vw;
                }
                #backlink {
                    z-index: 100000;
                }
                .d-ios-none {
                    display: none !important;
                }
                .d-ios-block {
                    display: block !important;
                }
                .d-ios-flex {
                    display: flex !important;
                }
                .col-ios-12 {
                    -ms-flex: 0 0 100%;
                    flex: 0 0 100%;
                    max-width: 100%;
                }
            }
            .embed-responsive {
                position: relative;
                padding: 0;
                padding-top: 0px;
                padding-bottom: 0px;
                margin: 0;
                padding-bottom: 56.25%;
                padding-top: 25px;
                height: 0;
            }
            .card iframe {
                max-width: 100%;
            }
        </style>
        @yield('stylesheets')
        @stack('stylestack')
        @include('partials.themestyles')
    </head>

    <body class="pt-0">
        <div style="max-width: 95em; margin: 0 auto; outline: 1px solid #e1e3e8;">
            <div class="nav-container custom-navbar" style="z-index: 1000;">
                <div>
                    <div class="container-fluid">
                        <nav class="navbar navbar-expand-lg px-0 py-0">
                            <a aria-label="home" id="logoContainer" class="navbar-brand" href="/home" style="height: 3.5em;">
                                <img aria-hidden="true" src="{{ getSetting('logo', request()->user() ? request()->user()->locale : (request()->has('locale') ? request()->locale : 'en') ) }}" style="height: 100%;">
                            </a>
                            <a id="notificationBell" style="text-decoration: none;" href="/notifications" class="mr-3 mv-show d-none">
                                    <svg aria-hidden="true" width="22px" height="22px" viewBox="0 0 16 16" class="bi bi-bell-fill" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                      <path d="M8 16a2 2 0 0 0 2-2H6a2 2 0 0 0 2 2zm.995-14.901a1 1 0 1 0-1.99 0A5.002 5.002 0 0 0 3 6c0 1.098-.5 6-2 7h14c-1.5-1-2-5.902-2-7 0-2.42-1.72-4.44-4.005-4.901z"/>
                                    </svg>
                                    <span id="unreadNotificationCount" aria-hidden="true" style="vertical-align: top; font-size:0.7em; position:absolute; transform: translateX(-17%); height: 8px; width: 8px; border-radius: 50%; display: inline-block;" class="badge badge-danger {{ $authUser->unreadNotifications()->count() ? '' : 'd-none' }}">
                                    </span>
                                </a>
                            <button class="navbar-toggler py-0" id="showMobileMenu" type="button">
                                <i class="fas fa-bars fa-lg" style="height: 100%;"></i>
                            </button>
                            <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                                @if(isset($authUser))
                                @if(!request()->is('*search*'))
                                    <form method="GET" action="/search" class="mr-3" style="display:flex; align-items: center;">
                                        <i class="icon icon-magnifying-glass mx-2" style="position: absolute; cursor: pointer; z-index: 5; font-size: 1.2em; opacity: 0.6"></i>
                                        <input type="search" placeholder="@lang('general.search')" class="form-control" name="q" style="min-width: 300px; background-color: rgba(255, 255, 255, 0.4); padding-left: 35px; border: 1px solid rgba(250, 250, 250, 0.4); z-index: 1; border-radius: 10px;" require id="searchInput">
                                    </form>
                                @endif
                                @if(request()->route()->named('home') || request()->route()->named('group_home'))
                                    <div class="mr-2 my-auto">
                                        @include('partials.tutorial', ['tutorial' => \App\Tutorial::where('name', 'Personal Dashboard')->first()])
                                    </div>
                                @endif
                                <a id="notificationBell" style="text-decoration: none;" href="/notifications" class="mr-3">
                                    <svg aria-hidden="true" width="22px" height="22px" viewBox="0 0 16 16" class="bi bi-bell-fill" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                      <path d="M8 16a2 2 0 0 0 2-2H6a2 2 0 0 0 2 2zm.995-14.901a1 1 0 1 0-1.99 0A5.002 5.002 0 0 0 3 6c0 1.098-.5 6-2 7h14c-1.5-1-2-5.902-2-7 0-2.42-1.72-4.44-4.005-4.901z"/>
                                    </svg>
                                    <span id="unreadNotificationCount" aria-hidden="true" style="vertical-align: top; font-size:0.7em; position:absolute; transform: translateX(-17%); height: 9px; width: 5px; border-radius: 50%; display: inline-block;" class="badge badge-danger {{ $authUser->unreadNotifications()->count() ? '' : 'd-none' }}">
                                    </span>
                                </a>
                                <ul class="navbar-nav" role="presentation">
                                    <li class="nav-item d-block d-lg-none text-right"><a class="nav-link" href="/messages">@lang('messages.messages')</a></li>

                                    <li class="nav-item d-block d-lg-none text-right"><a class="nav-link" href="/introductions">@lang('messages.introductions') @if($authUser->unread_ideation_invitations->count())<span class="badge badge-danger">{{ $authUser->unread_ideation_invitations->count() }}</span>@endif</a></li>

                                    <li class="nav-item d-block d-lg-none text-right"><a class="nav-link" href="/account">@lang('messages.account')</a></li>

                                    <li class="nav-item d-block d-lg-none text-right"><a class="nav-link" href="/users/{{ $authUser->id }}">@lang('messages.profile')</a></li>

                                    @if(getsetting('is_stripe_enabled') || request()->user()->receipts()->exists())
                                    <li class="nav-item d-block d-lg-none text-right"><a class="nav-link" href="/purchases">Purchases</a></li>
                                    @endif
                                    <li class="nav-item d-block d-lg-none text-right"><a class="nav-link" href="/logout">@lang('messages.logout')</a></li>

                                    <li class="d-none d-lg-block nav-item dropdown">
                                        <a class="nav-link dropdown-toggle dropdown-toggle p-lg-0" href="#" id="dropdown01" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <span>{{ $authUser->name }}</span>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-sm dropdown-menu-right" aria-labelledby="dropdown01" style="z-index: 100000;">
                                            <a class="dropdown-item" href="/messages">@lang('messages.messages') @if($authUser->unreadMessageCount)<span class="badge badge-danger">{{ $authUser->unreadMessageCount }}</span>@endif</a>
                                            <a class="dropdown-item" href="/browse">{{ getsetting('find_your_people_alias') }}</a>
                                            
                                            @if(config('app.url') != 'https://todayisagoodday.onthedotglobal.com')
                                                <a class="dropdown-item" href="/introductions">@lang('messages.introductions') @if($authUser->unreadIntroductionCount)<span class="badge badge-danger">{{ $authUser->unreadIntroductionCount }}</span>@endif</a>
                                                <a class="dropdown-item" href="/shoutouts/received">@lang('messages.shoutouts') @if($authUser->unreadShoutoutCount)<span class="badge badge-danger">{{ $authUser->unreadShoutoutCount }}</span>@endif</a>
                                            @endif
                                            
                                            @if(getsetting('is_ideations_enabled'))
                                            <a class="dropdown-item" href="/ideations">@lang('messages.ideations') @if($authUser->unread_ideation_invitations->count())<span class="badge badge-danger">{{ $authUser->unread_ideation_invitations->count() }}</span>@endif</a>
                                            @endif
                                            @if(getsetting('is_stripe_enabled') || request()->user()->receipts()->exists())
                                            <a class="dropdown-item" href="/purchases">Purchases</a>
                                            @endif
                                            <a class="dropdown-item" href="/account">@lang('messages.account')</a>
                                            <a class="dropdown-item" href="/users/{{ $authUser->id }}">@lang('messages.profile')</a>
                                            <div class="dropdown-divider"></div>
                                            <a class="dropdown-item" href="/logout">@lang('messages.logout')</a>
                                        </div>
                                    </li>
                                </ul>
                                @endif
                            </div> 
                            <!--end nav collapse-->
                        </nav>
                    </div>
                    <!--end of container-->

                </div>
            </div>
            <div id="mobileMenu" class="bg-primary-100 text-primary-700 d-none font-black" style="z-index: 1000000; position: fixed; top: 0; left: 0; width: 100%; height: 100%;">
                <div style="height: 100%; width: 100%; overflow-y: scroll;">
                    <span class="font-weight-bold text-primary-700 mb-0 mt-2 ml-2">Menu</span>
                    <a href="#" class="px-2" id="closeMenuButton" style="color: #000; position: fixed; top: 0; right: 0.25em; font-size: 32px; font-weight: bold;">&times;</a>
                    <div class="mt-1">
                        @include('partials.homepagenav')
                    </div>
                    <div class="ml-2 mb-4">
                        <hr>
                        <a href="/logout">Logout</a>
                    </div>
                </div>
            </div>

            <div class="bg-lightest-brand">
              @yield('content')
            </div>

            <footer class="footer-short bg-lightest-brand">
                <div class="container">
                    <hr>
                    <nav class="d-flex justify-content-between align-items-center flex-wrap">
                        <div>
                            <ul class="list-inline">
                                <li class="list-inline-item">
                                    <a href="/logLeadRedirect?next=https://onthedotglobal.com/" target="_blank">Powered by OnTheDot</a>
                                </li>
                            </ul>
                        </div>
                        @if(getsetting('is_technical_assistance_link_enabled') && getsetting('technical_assistance_email'))
                            <div class="text-sm-center">
                                <ul class="list-inline">
                                    <li class="list-inline-item">
                                        @if(Auth::check() && request()->user()->locale == 'en')
                                        <span style="color:#adb5bd;">@lang('general.Have a problem/question') <a style="color:#656f7b;" href="mailto:{{ getsetting('technical_assistance_email') }}" target="_blank">@lang('general.Contact support')</a> @lang('general.for technical assistance')</span>
                                        @elseif(Auth::check() && request()->user()->locale == 'es')
                                            <span style="color:#adb5bd;">¿Tiene un problema o una pregunta? <a style="color:#656f7b;" href="mailto:{{ getsetting('technical_assistance_email') }}" target="_blank">Contáctenos </a> si necesita ayuda técnica.</span>
                                        @endif
                                    </li>
                                </ul>
                            </div>
                        @endif
                        <!--end of col-->
                        <div class="text-sm-right">
                            <ul class="list-inline d-none">
                                <li class="list-inline-item">
                                    <a href="https://twitter.com/onthedotglobal" target="_blank"><i class="socicon-twitter"></i></a>
                                </li>
                                <li class="list-inline-item">
                                    <a href="https://www.facebook.com/onthedotglobal" target="_blank"><i class="socicon-facebook"></i></a>
                                </li>
                                <li class="list-inline-item">
                                    <a href="https://www.instagram.com/onthedotglobal/" target="_blank"><i class="socicon-instagram"></i></a>
                                </li>
                            </ul>
                        </div>
                        <!--end of col-->
                    </nav>
                    <!--end of row-->
                    <div class="row">
                        <div class="col">
                            <small>&copy; 2020 OnTheDot - All Rights Reserved</small>
                        </div>
                        <!--end of col-->
                    </div>
                    <!--end of row-->
                </div>
                <!--end of container-->
                @if((new \Jenssegers\Agent\Agent)->isMobile() && !request()->is('*spa*'))
                    <div style="position:fixed; bottom: 0; background-color: white; width: 100vw; z-index: 10000000;" class="py-1">
                        @include('components.mobilenav', ['links' => \App\MobileLink::all()])
                    </div>
                @endif
            </footer>
        </div>


        <script type="text/javascript" src="/assets/js/jquery.min.js"></script>
        <script type="text/javascript" src="/assets/js/popper.min.js"></script>
        <script type="text/javascript" src="/assets/js/flickity.pkgd.min.js"></script>
        <script type="text/javascript" src="/assets/js/scrollMonitor.js"></script>
        <script type="text/javascript" src="/assets/js/smooth-scroll.polyfills.js"></script>
        <script type="text/javascript" src="/assets/js/prism.js"></script>
        <script type="text/javascript" src="/assets/js/zoom.min.js"></script>
        <script type="text/javascript" src="/assets/js/bootstrap.js"></script>
        <script type="text/javascript" src="/assets/js/theme.js"></script>
        <script src="{{asset('assets/js/comment.js')}}"></script>
        <script>
            $(document).ready(function(){
                if(/iPhone/i.test(navigator.userAgent)){
                    $('#unreadNotificationCount').css('top','16px');
                    $('#unreadNotificationCount').css('right','57px');

                }
            })
            @if(!request()->is('*discussions*'))
                $('.redactor-output p a').each(function(link) {
                    var postId = $(this).parent().parent().data('postid');
                    var href = {'next': $(this).attr('href')};
                    $(this).attr('href', '/posts/' + postId + '/log?' + $.param(href));
                });

                $('.postLinkButton').each(function(link) {
                    var postId = $(this).data('postid');
                    var href = {'next': $(this).attr('href')};
                    $(this).attr('href', '/posts/' + postId + '/log?' + $.param(href));
                });
            @endif
            setInterval(getUnreadNotificationCount, 60000);

            function getUnreadNotificationCount()
            {
                $.ajax({
                    type: "get",
                    url: "/api/unread-notifications",
                    async: true,
                    success: function($result) {
                        if($result > 0)
                        {
                            $('#unreadNotificationCount').removeClass('d-none');
                        }
                        else
                        {
                            $('#unreadNotificationCount').addClass('d-none');
                        }
                    },
                });
            }
            $('.deletePostButton').on('click', function(event) {
              event.preventDefault();
              if (confirm('Delete?'))
                $(this).parent().submit();
            });
            $('#showMobileMenu').on('click', function () {
                $('#mobileMenu').removeClass('d-none');
                $('body').addClass('no-scroll-body');
            });
            $('#closeMenuButton').on('click', function(e) {
                e.preventDefault();
                $('#mobileMenu').addClass('d-none');
                $('body').removeClass('no-scroll-body');
            });

            @if((new \Jenssegers\Agent\Agent)->isMobile())
                $('a').attr('target', '_self');
            @endif

            @if(isset($authUser))
                document.cookie = 'userId={{ $authUser->id }};';
                document.cookie = '_token={{ csrf_token() }}';
            @endif
        </script>
        @include('components.notifications.firebase')
        @yield('scripts')
        @stack('scriptstack')
    </body>
</html>
