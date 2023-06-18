<!doctype html>
<html lang="{{ App::getLocale() }}">

    <head>
        <!-- Global site tag (gtag.js) - Google Analytics -->
        <meta charset="utf-8">
        <title>{{ getSetting('name', isset($authUser) ? $authUser->locale : 'en') }}</title>
        <link rel="icon" href="{{ config('app.url') }}/logo">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
        <meta name="csrf" content="{{ csrf_token() }}">
        <meta name="robots" content="noindex">
        <link href="https://fonts.googleapis.com/css?family=Montserrat:200,300i,400,500,600,700|Playfair+Display" rel="stylesheet">
        <link href="/assets/css/socicon.css" rel="stylesheet" type="text/css" media="all" />
        <link href="/assets/css/entypo.css" rel="stylesheet" type="text/css" media="all" />
        <link href="/css/main.css?v=4" rel="stylesheet" type="text/css" media="all" />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.8.2/css/all.min.css" />
        <link href="/assets/css/like.css" rel="stylesheet" type="text/css" media="all" />
        @if(env('ENABLE_DEBUGBAR')==true && request()->user()->email=='cm@ipx.org')
             <meta name="prevent_looping_notifications" content="true">
        @endif
        <style>
            figure > img {
                max-width: 100%;
            }
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
            .likeButton {
                color: #fd00009e;
                cursor: pointer;
                font-size: 1.5em;
                margin-top: -3px;
                margin-bottom: -3px;
            }

            .likeCount {
                font-size: 0.7em;
                margin-left: 4px;
                cursor:pointer;
            }
            .likeCount:hover {
                text-decoration:underline;
            }

            .userWhoLikedRow {
                display: flex;
                flex-wrap: nowrap;
                justify-content: space-between;
                text-align: left;
                align-items: center;
                text-decoration: none;
            }

            .userWhoLikedImg {
                height: 2.5em; 
                width: 2.5em; 
                border-radius: 50%; 
                background-size: cover; 
                background-position: center;
            }

            .userWhoLikedName {
                color: #0000009e;
            }
            @supports (-webkit-touch-callout: none) {
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
            /* footer style */
            .footer-content div:first-child {
                min-width: 7%;
            }
            .footer-content div:nth-child(2) {
                min-width: 12%;
                cursor: pointer;
            }
            .icon-heart-outlined{
}
        </style>
        @yield('stylesheets')
        @stack('stylestack')
        @include('partials.themestyles')
    </head>

    <body class="pt-0 pb-5">
        @yield('content')
        <script type="text/javascript" src="/assets/js/jquery.min.js"></script>
        <script type="text/javascript" src="/assets/js/popper.min.js"></script>
        <script type="text/javascript" src="/assets/js/flickity.pkgd.min.js"></script>
        <script type="text/javascript" src="/assets/js/scrollMonitor.js"></script>
        <script type="text/javascript" src="/assets/js/smooth-scroll.polyfills.js"></script>
        <script type="text/javascript" src="/assets/js/prism.js"></script>
        <script type="text/javascript" src="/assets/js/zoom.min.js"></script>
        <script type="text/javascript" src="/assets/js/bootstrap.js"></script>
        <script type="text/javascript" src="/assets/js/theme.js"></script>
        <script>
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
        @includeWhen((new \Jenssegers\Agent\Agent)->isDesktop(), 'components.notifications.firebase')
        @yield('scripts')
        @stack('scriptstack')
    </body>
</html>