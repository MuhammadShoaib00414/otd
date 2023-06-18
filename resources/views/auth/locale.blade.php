<!doctype html>
<html lang="en">

    <head>
        <meta charset="utf-8">
        <title>OTD Directory - Sign Up</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0, initial-scale=1.0">
        <link href="https://fonts.googleapis.com/css?family=Rubik:300,400,400i,500" rel="stylesheet">
        <link href="/assets/css/socicon.css" rel="stylesheet" type="text/css" media="all" />
        <link href="/assets/css/entypo.css" rel="stylesheet" type="text/css" media="all" />
        <link href="/css/main.css" rel="stylesheet" type="text/css" media="all" />
        @include('partials.themestyles')
    </head>

    <body>

        <div class="nav-container">
        </div>
        <div class="main-container">
            <section class="fullwidth-split">
                <div class="container-fluid">
                    <div class="row no-gutters height-100 justify-content-center">
                        <div class="col-12 fullwidth-split-image bg-brand d-flex justify-content-center align-items-center" style="background-color: #34567f;">
                            <div class="col-md-5 pt-5 pb-5">
                                <div class="card">
                                    <div class="card-body">
                                        <form method="get" class="col-md-8 mx-md-auto">
                                            <label for="locale">@lang('general.select-one')</label>
                                            <select id="locale" name="locale" class="custom-select">
                                                <option value="en">English</option>
                                                <option value="es">Español</option>
                                            </select>
                                            <button type="submit" class="btn btn-primary mt-4">@lang('general.continue')</button>
                                        </form>
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
