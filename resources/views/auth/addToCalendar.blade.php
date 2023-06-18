<!doctype html>
<html lang="{{ App::getLocale() }}">

    <head>
        <meta charset="utf-8">
        <title>OTD Directory - Sign Up</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0, initial-scale=1.0">
        <link href="https://fonts.googleapis.com/css?family=Rubik:300,400,400i,500" rel="stylesheet">
        <link href="/assets/css/socicon.css" rel="stylesheet" type="text/css" media="all" />
        <link href="/assets/css/entypo.css" rel="stylesheet" type="text/css" media="all" />
        <link href="/css/main.css" rel="stylesheet" type="text/css" media="all" />
        <style>
          .add-to-calendar-checkbox {
            background-color: #fff;
            width: 100%;
            text-align: center;
            border: 1px solid #ced4da;
            color: #1a2b40;
            border-radius: .25rem;
            font-weight: bold;
            line-height: 2.4;
            display: block;
            margin-bottom: 1em;
          }
          .add-to-calendar-checkbox:hover {
            background-color: #e1ebf4;
            cursor: pointer;
          }
          .add-to-calendar-checkbox:checked ~ a {
            display: block;
            width: 100% !important;
            margin-left: 20px;
            margin-bottom: 0.5em;
          }
        </style>

        @include('partials.themestyles')
    </head>

    <body>

        <div class="nav-container">
        </div>
        <div class="main-container">
            <section class="fullwidth-split">
                <div class="container-fluid">
                    <div class="row no-gutters height-100 justify-content-center">
                        <div class="col-12 fullwidth-split-image bg-accent-100 d-flex justify-content-center align-items-center">
                            <div class="col-md-5 pt-5 pb-5">
                                <div class="card">
                                    <div class="card-header text-center">
                                        <h1>Add Event to Calendar</h1>
                                    </div>
                                    <div class="card-body">
                                        <div id="addToCalendarButton"></div>

                                        <a href="/onboarding" class="btn btn-primary mt-2">Continue</a>
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
        <script type="text/javascript" src="/js/ouical.min.js"></script>

        <script>

            var myCalendar = createCalendar({
              options: {
                class: 'addToCalendarButton',
              },
              data: {
                // Event title
                title: '{{ $page->event_name }}',
                // Event start date
                start: new Date('{{ $page->event_date->format('F j, Y G:i') }}'),
                duration: {{ $page->event_end_date->diffInMinutes($page->event_date) }},
                address: '{{ env("APP_URL") }}',
              }
            });

            document.querySelector('#addToCalendarButton').appendChild(myCalendar);
        </script>

    </body>

</html>
