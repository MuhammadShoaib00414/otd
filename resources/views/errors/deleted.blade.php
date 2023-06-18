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
            <section class="height-80 flush-with-above">
                <div class="container text-center">
                    <h2>Uh oh!</h2>
                    <p>
                        {{ $message }}
                    </p>
                    <p>
                        <a href="#" onclick="window.history.back()">Go back</a>
                    </p>
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