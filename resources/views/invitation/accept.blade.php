<!doctype html>
<html lang="en">

    <head>
        <meta charset="utf-8">
        <title>OTD Directory - Sign Up</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="https://fonts.googleapis.com/css?family=Rubik:300,400,400i,500" rel="stylesheet">
        <link href="/assets/css/socicon.css" rel="stylesheet" type="text/css" media="all" />
        <link href="/assets/css/entypo.css" rel="stylesheet" type="text/css" media="all" />
        <link href="/css/main.css" rel="stylesheet" type="text/css" media="all" />

        
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.3/css/bootstrap.min.css" />
	    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
        {!! NoCaptcha::renderJs() !!}
    </head>

    <body> 

        <div class="nav-container">
        </div>
        <div class="main-container">
            <section class="fullwidth-split">
                <div class="container-fluid">
                    <div class="row no-gutters height-100 justify-content-center">
                        <div class="col-12 col-lg-6 fullwidth-split-image bg-dark d-flex justify-content-center align-items-center">
                            <!-- <img alt="Image" src="assets/img/photo-man-diary.jpg" class="bg-image position-absolute opacity-30" /> -->
                            <div class="col-12 col-sm-8 col-lg-9 text-center pt-5 pb-5">
                                <!-- <img alt="Image" src="assets/img/logo-white.svg" class="mb-4 logo-lg" /> -->

                                <div id="carouselExampleControls" class="carousel slide" data-ride="carousel">
                              <div class="carousel-inner">
                                @foreach(\App\HomePageImage::where('lang', (request()->has('locale') ? request()->locale : \Illuminate\Support\Facades\App::getLocale()))->get() as $image)
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
                        </div>
                        <!--end of col-->
                        <div class="col-12 col-sm-8 col-lg-6 fullwidth-split-text">
                            <div class="col-12 col-lg-8 align-self-center">
                                <div class="text-center mb-4">
                                    <h1 class="h2 mb-2">@lang('messages.get-started')</h1>
                                    <span>@lang('messages.invite-description')</span>
                                </div>
                                @if ($errors->any())
                                    <div class="alert alert-danger">
                                        <ul>
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif
                                <form class="mb-4" method="post">
                                  @csrf
                                    @if(request()->has('locale'))
                                        <input type="hidden" name="locale" value="{{ request()->locale }}">
                                    @endif
                                    <div class="form-group">
                                        <label for="signup-name">@lang('messages.full-name')</label>
                                        <input class="form-control form-control-lg" type="name" name="name" id="signup-name" placeholder="First &amp; last name" />
                                    </div>
                                    <div class="form-group">
                                        <label for="signup-email">@lang('messages.email-address')</label>
                                        <input class="form-control form-control-lg" type="email" name="email" id="signup-email" placeholder="Email address" value="{{ $invite->email }}"/>
                                    </div>
                                    <div class="form-group">
                                        <label for="signup-password">@lang('messages.choose-a-password')</label>
                                        <input class="form-control form-control-lg" type="password" name="password" id="signup-password" placeholder="Enter a password" />
                                    </div>

                                    <div class="form-group{{ $errors->has('g-recaptcha-response') ? ' has-error' : '' }}">      
                                        {!! app('captcha')->display() !!}
                                    </div>
                                    <div>
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" value="agree" name="agree-terms" id="check-agree">
                                            <label class="custom-control-label text-small" for="check-agree">I agree to the <a  href="" data-toggle="modal" class="cursor-pointer trash-data" data-target="#exampleModalCenter">Terms &amp; Conditions</a>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="text-center mt-4">
                                        <button type="submit" class="btn btn-lg btn-primary">@lang('messages.create-account')</button>
                                    </div>
                                </form>
                                <div class="text-center">
                                    <span class="text-small">@lang('messages.already-have-an-account') <a href="/login">@lang('messages.log-in-here')</a>
                                    </span>
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

        <div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <button type="button" data-dismiss="modal" aria-label="Close" id="cross-icon" class="close position-absolute zindex-dropdown right-0 text-light"><span aria-hidden="true">×</span></button>
            <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
                <div class="modal-content bg-transparent" >
                    <iframe src="/terms-and-conditions" class="responsive-iframe-model"></iframe> 
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