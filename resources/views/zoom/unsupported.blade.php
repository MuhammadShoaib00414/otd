<!doctype html>
<html lang="{{ App::getLocale() }}">

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
        <title>{{ getSetting('name', isset($authUser) ? $authUser->locale : 'en') }}</title>
        <link rel="icon" href="{{ config('app.url') }}/logo">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
        <link href="https://fonts.googleapis.com/css?family=Montserrat:200,300i,400,500,600,700|Playfair+Display" rel="stylesheet">
        <link href="/assets/css/socicon.css" rel="stylesheet" type="text/css" media="all" />
        <link href="/assets/css/entypo.css" rel="stylesheet" type="text/css" media="all" />
        <link href="/css/main.css?v=3" rel="stylesheet" type="text/css" media="all" />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.8.2/css/all.min.css" />
    </head>

    <body class="pt-0 border-none">
        <p class="text-muted text-center" style="top: 49%;">Sorry, Zoom Web SDK does not support use on Safari or mobile devices.</p>
    </body>
</html>
