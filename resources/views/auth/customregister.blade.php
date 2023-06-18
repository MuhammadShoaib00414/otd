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
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.8.2/css/all.min.css" />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.3/css/bootstrap.min.css" />
	    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
	    {!! NoCaptcha::renderJs() !!}
        @include('partials.themestyles')
        <style>
            #backlink {
                    z-index: 100000;
                }

            .form-check-label {
                font-size: 1em !important;
            }

            .registrationpage {
              position: relative;
              transition: all 0.3s ease-in-out;
              cursor: pointer;
              text-decoration: none;
              color: black;
              font-size: 1.2em;
              padding: 15px;
            }

            .registrationpage::after {
              content: '';
              position: absolute;
              z-index: -2;
              width: 100%;
              height: 100%;
              opacity: 0;
              border-radius: 5px;
              box-shadow: 0 5px 15px rgba(0,0,0,0.3);
              transition: opacity 0.3s ease-in-out;
              top: 0;
              left: 0;
            }

            .registrationpage:hover {
              color: black;
              text-decoration: none;
              transform: scale(1.02, 1.02);
            }

            .registrationpage:hover::after {
              opacity: 1;
            }

            .ticket-label {
                cursor:  pointer;
            }

            .ticketInput:checked + label {
                border: 1px solid {{ getThemeColors()->primary['500'] }};
                color: black;
                text-decoration: none;
                transform: scale(1.02, 1.02);
            }

            .ticketInput:checked + label::after {
              opacity: 1;
            }

            .custom-border {
                border: 1px solid #ced4da;
                border-radius: 0.2rem;
                padding: 0.8em 0.8em 0.8em 0.8em;
            }
        </style>
    </head>

    <body>
        <div class="nav-container">
        </div>
        <div class="main-container">
            <section class="fullwidth-split">
                <div class="container-fluid">
                    <div class="row no-gutters height-100 justify-content-center">
                        <div class="col-12 fullwidth-split-image bg-brand d-flex flex-column flex-lg-row justify-content-start align-items-start px-2" style="background-color: {{ getThemeColors()->primary['100'] }}">
                            @if($page->image_url)
                                <div class="{{ (new \Jenssegers\Agent\Agent)->isMobile() ? 'row' : 'col-6 pt-5' }}">
                                    <img class="mx-auto" style="max-width: 100%; min-width: 75%; max-height: 90vh; top: 0" src="{{ $page->image_url }}">
                                </div>
                            @endif
                            <div class="{{ (new \Jenssegers\Agent\Agent)->isMobile() ? 'row w-100  m-auto' : 'col-6' }} pt-5 pb-5">
                                <div class="card w-100 m-auto" id="signupform">
                                    <div id="backlink" style="cursor: pointer; font-size: 1.2em; z-index:1000;" class="ml-1 mt-1 d-md-none" onclick="window.history.back();">
                                        <i class="fas fa-arrow-left"></i>
                                    </div>
                                    <div class="card-header text-center">
                                        <h1>
                                            @if($page->prompt)
                                                {{ $page->prompt }}
                                            @elseif(Auth::check())
                                                Register
                                            @else
                                                @lang('auth.Create an Account')
                                            @endif
                                        </h1>
                                        <p>{{ $page->description }}</p>
                                    </div>
                                    @if(getSetting('is_localization_enabled') && !Auth::check())
                                        <div class="text-center mt-2">
                                            @if(App::getLocale() == 'en')
                                                <a href="/register/{{ $page->slug }}?locale=es" class="btn btn-sm btn-secondary">En Espanol</a>
                                            @else
                                                <a href="/register/{{ $page->slug }}?locale=en" class="btn btn-sm btn-secondary">In English</a>
                                            @endif
                                        </div>
                                    @endif
                                    <div class="card-body">
                                        <form method="POST" class="col-md-10 mx-md-auto" id="form">

                                            @if(Auth::check())
                                                <div class="card card-body text-center" style="background-color: #f5f3f3">
                                                    <span class="text-gray-900">Purchasing as {{ $authUser->name }}. Not you? <a href="/logout">Switch Accounts</a></span>
                                                </div>
                                                @if(request()->user()->hasBoughtTicketForRegistrationPage($page->id) > 0)
                                                    <div class="alert alert-danger text-center" role="alert">
                                                        <b>{{ $page->purchased_warning_title }}</b>
                                                        <p class="text-muted">{{ $page->purchased_warning_message }}</p>
                                                        <a class="btn btn-outline-primary mr-3" href="{{ $authUser->getReceiptUrl($page->id) }}">View Receipt</a>
                                                        @if($page->purchased_warning_url)
                                                            <a class="btn btn-primary" href="{{ $page->purchased_warning_url }}">
                                                                @if($page->purchased_warning_button_text)
                                                                    {{ $page->purchased_warning_button_text }}
                                                                @else
                                                                    Let's go
                                                                @endif
                                                            </a>
                                                        @endif
                                                    </div>
                                                @endif
                                            @else
                                                <div class="card card-body text-center" style="background-color: #f5f3f3">
                                                    <span class="text-gray-900">Already have an account? <a href="/login?next={{ '/'.request()->path() }}">Login</a></span>
                                                </div>
                                            @endif
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

                                            @if(getSetting('is_localization_enabled'))
                                                <input type="hidden" name="locale" value="{{ App::getLocale() }}">
                                            @endif


                                            @if(!isset($authUser))
                                            <div class="step" id="signup">
                                                <label for="name">@lang('auth.Full Name')</label>
                                                <input required placeholder="Full Name" type="text" class="form-control mb-2" value="{{ old('name') }}" name="name" id="name">

                                                <label for="email">@lang('auth.Email Address')</label>
                                                <input required placeholder="Email Address" type="text" class="form-control mb-2" value="{{ old('email') }}" name="email" id="email">

                                                <label for="password">@lang('auth.Password')</label>
                                                <input minlength="8" required placeholder="Password" type="password" class="form-control mb-2" name="password" id="password">

                                                <label for="password">@lang('auth.Confirm Password')</label>
                                                <input required placeholder="Confirm Password" type="password" class="form-control mb-3" name="confirmPassword" id="confirmPassword">

                                                <p class="font-bold">@lang('auth.Have a specialized access code?')</p>
                                                <label for="access_code">@lang('auth.Access code') (optional)</label>
                                                <input placeholder="Enter your code here" type="text" class="form-control mb-3" value="{{ old('access_code') }}" name="access_code" id="access_code">

                                            </div>

                                            <hr>
                                            @endif

                                            @if(is_stripe_enabled())
                                            @if($page->tickets()->count())
                                            <div id="ticketForm" class="step">
                                                <p class="text-primary-600">{{ $page->ticket_prompt }}</p>
                                                @foreach($page->tickets as $ticket)
                                                    <div class="form-check mt-1 mb-3 pl-3">
                                                      <input {{ (Auth::check() && request()->user()->hasBoughtTicketForRegistrationPage($page->id)) ? 'disabled' : '' }} class="form-check-input chargableInput" type="radio" name="ticket" id="ticket{{ $ticket->id }}" value="{{ $ticket->id }}" data-price="{{ $ticket->price }}">
                                                      <label for="ticket{{ $ticket->id }}" class="d-block">
                                                        <p class="mb-0 text-primary-900">{{ $ticket->name }} - <span class="font-weight-bold text-primary-600">{{ $ticket->display_price }}</span>
                                                        @if($ticket->description)
                                                          <p class="text-gray-600">{{ $ticket->description }}</p>
                                                        @endif
                                                      </label>
                                                      @if((Auth::check() && request()->user()->hasBoughtTicket($ticket->id) > 0))
                                                        <br><b class="text-gray-900">You have already purchased this ticket.</b>
                                                      @endif
                                                    </div>
                                                @endforeach
                                            </div>
                                            @endif

                                            <hr>

                                            @if($page->addons)
                                            <div id="addonForm" class="step">
                                                <span class="text-primary-600 mb-3">{{ $page->addon_prompt }}</span>

                                                @foreach($page->addons as $addon)
                                                <div class="form-check mt-3 pl-3">
                                                  <input class="form-check-input chargableInput" type="checkbox" value="{{ $addon['id'] }}" name="addons[]" id="addon{{ $addon['id'] }}" data-price="{{ $addon['price'] / 100 }}">
                                                  <label class="d-block" for="addon{{ $addon['id'] }}">
                                                    <p class="mb-0 text-primary-900">{{ $addon['name'] }} - <span class="font-weight-bold">${{ $addon['price'] / 100 }}</span>
                                                    @if(array_key_exists('description', $addon))
                                                        <p class="text-gray-600">{{ $addon['description'] }}</p>
                                                    @endif
                                                  </label>
                                                </div>
                                                @endforeach
                                            </div>

                                            <hr>
                                            @endif

                                            @if(($page->addons && count($page->addons)) || $page->tickets()->count() > 0)
                                            <div id="paymentForm">
                                                <div id="couponCodesContainer">
                                                    <p>Have any coupon codes? Enter them here.</p>
                                                    <div id="coupon_error" class="alert alert-danger fade show d-none mt-2"></div>
                                                    <div id="coupon_success" class="alert alert-success fade show d-none mt-2" role="alert"></div>
                                                    <div class="input-group">
                                                        <input type="text" id="coupon_code" name="coupon_code" onkeydown="return (event.keyCode!=13)" class="form-control" placeholder="Ex. EC20">
                                                        <div class="input-group-append">
                                                            <button type="button" id="checkCoupon" class="btn btn-primary">Apply</button>
                                                        </div>
                                                    </div>
                                                </div>
                                                <hr>
                                                <div class="displayIfCoupon d-none">
                                                    <div id="subtotal_container" class="mt-2">
                                                        <span>Subtotal: <b>$<span id="subtotal">0</span></b></span>
                                                    </div>
                                                    <span class="mt-1" id="coupon_confirm"></span><b id="coupon_removal"></b>
                                                </div>
                                                <div id="runningTotalContainer" class="mt-1">
                                                    <span>Total: <b>$<span id="runningTotal">0</span></b></span>
                                                </div>
                                                <label for="cardholder-name" class="mt-3 stripes">Cardholder's Name</label>
                                                <div>
                                                    <input type="text" id="cardholder-name" class="form-control stripes">
                                                </div>

                                                <div id="stripe" class="stripes">
                                                    <label for="card-element" class="mt-3">
                                                        Credit or debit card
                                                    </label>
                                                    <div id="card-element" class="custom-border">
                                                        <!-- A Stripe Element will be inserted here. -->
                                                    </div>

                                                    <!-- Used to display form errors. -->
                                                    <div id="card-errors" role="alert"></div>
                                                </div>
                                            </div>
                                            @endif
                                            @endif

                                            <!-- add terms and condittion checkbox -->
                                            <div class="form-group d-flex" style="vertical-align: top;">
                                                <input type="checkbox" name="terms" id="terms" class="mr-2" required>
                                                <label for="terms" class="form-label mb-0">
                                                I agree to the <a href="" data-toggle="modal" class="cursor-pointer trash-data" data-target="#exampleModalCenter" target="_blank" class="font-weight-bold"><u>Terms and Conditions</u></a>
                                            </label>
                                            </div>

                                            <hr>
                                            <div class="form-group{{ $errors->has('g-recaptcha-response') ? ' has-error' : '' }}">
                                                    {!! app('captcha')->display() !!}
                                            </div>
                                             
                                            @if(!Auth::check())
                                                <button class="btn btn-primary buttonload disableOnClick" disabled="true" type="submit" id="submitButton"  >Create Account  <i class="fa fa-spinner fa-spin" id="d-none"></i> </button>
                                            @else
                                            <div class="text-center">
                                                <button class="btn btn-primary disableOnClick" type="submit" id="submitButton">Complete Your Profile</button>
                                            </div>
                                          
                                            @endif
                                          
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
        <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.3/dist/jquery.validate.js"></script>
        <script src="https://js.stripe.com/v3/"></script>
        <script>
            var spinner = $('#loader');
                $(function() {
                $('form').submit(function(e) {
                    // e.preventDefault();
                    $("#d-none").show();
                   
                    });
                });
            getTotal();
            function validatePassword(){
                var password = document.getElementById("password")
                , confirm_password = document.getElementById("confirmPassword");
              
                if(password.value != confirm_password.value) {
                    confirm_password.setCustomValidity("Passwords Don't Match");
                } else {
                    confirm_password.setCustomValidity('');
                    @if($page->tickets()->count())
                        if($('#form').validate())
                            $('#signup').addClass('d-none');
                    @else
                 
                        $('#form').submit();
                    @endif
                }
                password.onchange = validatePassword;
                confirm_password.onkeyup = validatePassword;
            }

            var coupon_type = false;
            var coupon_amount = false;

            $('.chargableInput').change(function(e) {
                updateTotal();
                $('.disableOnClick').prop('disabled', false);
               
            });

            var paymentStatus = '{{($page->addons && count($page->addons)) || $page->tickets()->count() > 0}}';
            if (paymentStatus) {
                $(document).ready(function () {     
                    $('.disableOnClick').prop('disabled', true);
                    $("#d-none").hide();
                 
                });
            }else if (getTotal() <= 0) {
                $(document).ready(function () {
                    $('.disableOnClick').prop('disabled', false);
               });
           }

            function getTotal(shouldIncludeCoupon = true)
            {
                var total = 0;
                $('.chargableInput:checked').each(function(index, input) {
                    total += $(input).data('price');
                });

                if(coupon_type && coupon_amount && shouldIncludeCoupon)
                {
                    if(coupon_type == 'percent')
                        total = total * (1 - (coupon_amount / 100));
                    else if(coupon_type == 'fixed')
                        total = total - coupon_amount;
                   
                }

                if(total < 0)
                    total = 0;

                if(total == 0)
                    $('.stripes').addClass('d-none');
                else
                    $('.stripes').removeClass('d-none');
                    $("#d-none").hide();

                return Math.round(total * 100) / 100;
            }

            function updateTotal()
            { 
                var totalamout =  getTotal();
              if(totalamout > 0){
              
                stripePayment();
              }
                // stripePayment();
                $('#subtotal').html(getTotal(false));
                $('#runningTotal').html(totalamout);
            }

            $('#nextButton3').click(function(e) {
                e.preventDefault();
                $('#addonForm').addClass('d-none');
                $('#paymentForm').removeClass('d-none');

                $.ajax({
                    url: "/users/getPrice",
                    type: "get",
                    data: {
                      _token: '{{ csrf_token() }}',
                      addons: $('.ticketInput:checked').serialize(),
                      page: {{ $page->id }},
                    }
                  });
            });

            $('#checkCoupon').click(function(e) {
                e.preventDefault();
                var code = $('#coupon_code').val();
                if(!code)
                    return false;

                $.ajax({
                    url: "/register/{{ $page->slug }}/checkCoupon",
                    type: "GET",
                    data: {
                      _token: '{{ csrf_token() }}',
                      code: code,
                    },
                    success: function (response) {
                        if(!response)
                        {
                            $('#coupon_error').html('Code not found.').removeClass('d-none');
                            return false;
                        }
                        $('#coupon_error').addClass('d-none');
                        $('#coupon_success').html('You got ' + response.message + ' your purchase!').removeClass('d-none');
                        $('#coupon_code').prop('readonly', true);

                        coupon_type = response.type;
                        coupon_amount = response.amount;
                        coupon_label = response.label;

                        $('#coupon_removal').html(coupon_label);
                        $('#coupon_confirm').html('Code ' + $('#coupon_code').val() + ': ');
                        $('.displayIfCoupon').removeClass('d-none');
                        $('.disableOnClick').prop('disabled', false);
                        updateTotal();
                    }
                  });
            });




            //stripe stuff

             function stripePayment(){
                      // Create a Stripe client.
            var stripe = Stripe("{{ get_stripe_credentials('key') }}");
           
           // Create an instance of Elements.
           var elements = stripe.elements();
           // Custom styling can be passed to options when creating an Element.
           // (Note that this demo uses a wider set of styles than the guide below.)
           var style = {
           base: {
               color: '#32325d',
               fontFamily: '"Helvetica Neue", Helvetica, sans-serif',
               fontSmoothing: 'antialiased',
               fontSize: '16px',
               '::placeholder': {
               color: '#aab7c4'
               }
           },
           invalid: {
               color: '#fa755a',
               iconColor: '#fa755a'
           }
           };
           // Create an instance of the card Element.
           var card = elements.create('card', {style: style});
           // Add an instance of the card Element into the `card-element` <div>.
           card.mount('#card-element');
           // Handle real-time validation errors from the card Element.
           card.on('change', function(event) {
           var displayError = document.getElementById('card-errors');
           if (event.error) {
               displayError.textContent = event.error.message;
           } else {
               displayError.textContent = '';
           }
           });
           // Handle form submission.
           var form = document.getElementById('form');
           var cardHolderName = document.getElementById('cardholder-name');
           form.addEventListener('invalid', (e)=>{
               $('.disableOnClick').prop('disabled', false);
           },true);
           form.addEventListener('submit', async function(event) {
               event.preventDefault();
               $('.disableOnClick').prop('disabled', true);
               if(getTotal() == 0) {
                   $('.disableOnClick').prop('disabled', false);
                   form.submit();
               }
               const { paymentMethod, error } = await stripe.createPaymentMethod(
                   'card', card, {
                       billing_details: { name: cardHolderName.value }
                   }
               );
               if (error) {
                   // Inform the user if there was an error.
                   var errorElement = document.getElementById('card-errors');
                   errorElement.textContent = error.message;
               } else {
                   // Send the token to your server.
                   // console.log(paymentMethod);
                   stripeTokenHandler(paymentMethod);
               }
           });
             }
            // Submit the form with the token ID.
            function stripeTokenHandler(paymentMethod) {
                // Insert the token ID into the form so it gets submitted to the server
                var form = document.getElementById('form');
                var hiddenInput = document.createElement('input');
                hiddenInput.setAttribute('type', 'hidden');
                hiddenInput.setAttribute('name', 'paymentMethod');
                hiddenInput.setAttribute('value', paymentMethod.id);
                form.appendChild(hiddenInput);
                // Submit the form
                $("#d-none").show();
                form.submit();
            }

            $(document).ready(function () {
                var ticket = '{{ $page->tickets()->count() }}';
                var addons = '{{ $totaladdons }}';
                if(ticket > 0 && addons > 0){
                    $('input[type=radio]').attr("required", "true");
                } else if(ticket == 0 && addons > 0){
                    $('input[type=checkbox]').attr("required", "true");
                } else if(ticket > 0 && addons == 0){
                    $('input[type=radio]').attr("required", "true");
                }
            });

        </script>

    </body>

</html>
