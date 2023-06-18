<!doctype html>
<html lang="en">

    <head>
        <meta charset="utf-8">
        <title>OTD Directory - @lang('messages.contact-us')</title>
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
                                    <div class="card-header text-center">
                                        <h1>@lang('messages.contact-us')</h1>
                                    </div>
                                    <div class="card-body">
                                        @if(!isset($confirm))
                                        <form method="POST" class="col-md-8 mx-md-auto">
                                            @csrf
                                            @if ($errors->any())
                                                <div class="alert alert-danger">
                                                    <ul>
                                                        @foreach ($errors->all() as $error)
                                                            <li>{!! $error !!}</li>
                                                        @endforeach
                                                    </ul>
                                                </div>
                                            @endif
                                            <label for="name">@lang('general.name')</label>
                                            <input required placeholder="Name" type="text" class="form-control mb-2" name="name" id="name" value="{{ isset($user) ? $user->name : '' }}">

                                            <label for="email">@lang('messages.email-address')</label>
                                            <input required placeholder="Email Address" type="text" class="form-control mb-2" name="email" id="email" value="{{ isset($user) ? $user->email : '' }}">

                                            <label for="message">@lang('messages.what-can-we-help-you-with')</label>
                                            <textarea required style="min-height: 150px;" name="message" id="message" class="form-control"></textarea>
                                            
                                            <button type="submit" class="btn btn-primary mt-3">@lang('general.submit')</button>
                                        </form>
                                        @else
                                            <p>
                                                @lang('messages.contact-us-1')
                                            </p>
                                            <p>
                                                @lang('messages.contact-us-2')
                                            </p>
                                            <p>
                                                @lang('messages.contact-us-3')
                                            </p>
                                            <p>
                                                @lang('messages.contact-us-4')
                                            </p>
                                            <p>
                                                @lang('messages.contact-us-5')
                                            </p>
                                            <a href="{{ isset($user) ? '/home' : '/login' }}" class="btn btn-primary">@lang('messages.continue')</a>
                                        @endif
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

    </body>

</html>
