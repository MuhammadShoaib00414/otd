<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>@lang('auth.Login') - OTD Directory</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <link href="https://fonts.googleapis.com/css?family=Rubik:300,400,400i,500" rel="stylesheet">
    <link href="/assets/css/socicon.css" rel="stylesheet" type="text/css" media="all" />
    <link href="/assets/css/entypo.css" rel="stylesheet" type="text/css" media="all" />
    <link href="{{ asset('/css/main.css') }}" rel="stylesheet" type="text/css" media="all" />
    @include('partials.themestyles')
    <style>
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
    </style>
</head>

<body class="bg-lightest-brand">

    <div class="nav-container">
    </div>
    <div class="main-container">
        <section class="space-sm">
            <div class="container align-self-start">
                <div class="row mb-5">
                    <div class="col text-center">
                        <!--  <a href="#">
                                <img alt="Image" src="assets/img/logo-gray.svg" />
                            </a> -->
                    </div>
                    <!--end of col-->
                </div>
                <!--end of row-->
                <div class="row justify-content-center">
                    <div class="col-12 col-md-8 col-lg-7">
                        <div class="card card-lg text-center">
                            <div class="card-body">
                                <div class="mb-3">
                                    <h1 class="h2">@lang('auth.Sign in')</h1>
                                </div>
                                @if($errors->count())
                                <div class="alert alert-danger text-center">@lang('auth.Incorrect email and/or password')</div>
                                @endif
                                <div class="row no-gutters justify-content-center">
                                    <form class="text-left col-lg-8" method="POST" action="{{ route('login') }}">
                                        {{ csrf_field() }}
                                        <div class="form-group">
                                            <label for="login-email">@lang('auth.Email Address')</label>
                                            <input class="form-control form-control-lg" type="email" name="email" id="login-email" placeholder="Email Address" />
                                        </div>
                                        @if(request()->has('next'))
                                        <input type="hidden" name="next" value="{{ request()->next }}">
                                        @endif
                                        <div class="form-group">
                                            <label for="login-password">@lang('auth.Password')</label>
                                            <input class="form-control form-control-lg" type="password" name="password" id="login-password" placeholder="Enter a password" />
                                            <small>@lang('auth.Forgot password?') <a href="/password/reset">@lang('auth.Reset here')</a>
                                            </small>
                                        </div>
                                        <div>
                                            <div class="form-group align-items-center">
                                                <input type="checkbox" class="control-input" value="remember-me" name="remember" checked>
                                                <label class=" text-small" for="check-remember">@lang('auth.Remember me next time')</label>
                                            </div>
                                        </div>
                                        <div class="text-center mt-3">
                                            <button type="submit" class="btn btn-lg btn-primary">@lang('auth.Login')</button>
                                        </div>
                                    </form>
                                </div>
                                <!--end of row-->
                            </div>
                        </div>
                       @if(getsetting('open_registration') == 1);
                            @if(\App\RegistrationPage::where('is_welcome_page_accessible', 1)->count() && getsetting('open_registration'))

                            <div class="text-center">
                                <span class="text-small">@lang('auth.Dont have an account yet?') <a href="/register/{{ $registration_pages->first()->slug }}{{ request()->has('locale') ? '?locale='.request()->locale : '' }}">@lang('auth.Create one')</a>
                                </span>
                            </div>
                            @else
                            <div class="text-center">
                                <span class="text-small">@lang('auth.Dont have an account yet?') <a href="/register/pick{{ request()->has('locale') ? '?locale='.request()->locale : '' }}">@lang('auth.Create one')</a>
                                </span>
                            </div>
                            @endif
                        @endif
                    </div>
                    <!--end of col-->
                </div>
                <!--end of row-->
            </div>
            <div class="text-center">
                <a href="/privacy-policy" style="font-size: 12px;">Privacy Policy</a>
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