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
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8 my-5">
                    <div class="card">
                        <div class="card-header">{{ __('Reset Password') }}</div>

                        <div class="card-body">
                            <form method="POST" action="{{ route('password.update') }}">
                                @csrf

                                <input type="hidden" name="token" value="{{ $token }}">

                                <div class="form-group row">
                                    <label for="email" class="col-md-4 col-form-label text-md-right">{{ __('E-Mail Address') }}</label>

                                    <div class="col-md-6">
                                        <input id="email" type="email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" name="email" value="{{ $email ?? old('email') }}" required autofocus>

                                        @if ($errors->has('email'))
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $errors->first('email') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="password" class="col-md-4 col-form-label text-md-right">{{ __('New Password') }}</label>

                                    <div class="col-md-6">
                                        <input id="password" type="password" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" name="password" required>

                                        @if ($errors->has('password'))
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $errors->first('password') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="password-confirm" class="col-md-4 col-form-label text-md-right">{{ __('Confirm New Password') }}</label>

                                    <div class="col-md-6">
                                        <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required>
                                    </div>
                                </div>

                                <div class="form-group row mb-0">
                                    <div class="col-md-6 offset-md-4">
                                        <button type="submit" class="btn btn-primary">
                                            {{ __('Reset Password') }}
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
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

