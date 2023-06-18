<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Privacy Policy - {{ getSetting('name') }}</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="A robust suite of app and landing page templates by Medium Rare">
        <link href="https://fonts.googleapis.com/css?family=Montserrat:200,300i,400,500,600,700|Playfair+Display" rel="stylesheet">
        <link href="/assets/css/socicon.css" rel="stylesheet" type="text/css" media="all" />
        <link href="/assets/css/entypo.css" rel="stylesheet" type="text/css" media="all" />
        <link href="/css/main.css?v=2" rel="stylesheet" type="text/css" media="all" />
        @include('partials.themestyles')
    </head>

    <body class="bg-lightest-brand">
        <div class="main-container" style="max-width: 1000px;overflow: hidden;">
           <div class="row">
                <div class="col-md-12 p-4">
                      {!! $tesrm_and_conditions->value !!}
                </div>
           </div>
        </div>
    </body>
    </html>