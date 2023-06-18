<!doctype html>
<html lang="en">

    <head>
        <meta charset="utf-8">
        <title>Setup Your Profile</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="https://fonts.googleapis.com/css?family=Rubik:300,400,400i,500" rel="stylesheet">
        <link href="/assets/css/socicon.css" rel="stylesheet" type="text/css" media="all" />
        <link href="/assets/css/entypo.css" rel="stylesheet" type="text/css" media="all" />
        <link href="/css/main.css" rel="stylesheet" type="text/css" media="all" />
        <style>
            .btn-primary:active {
                background-color: {{ getThemeColors()->primary['600'] }} !important;
                box-shadow: none !important;
                border: none !important;
            }
            .btn-primary:focus {
                box-shadow: none !important;
                border: none !important;
            }
            .step {
                height: 100%;
                display: none;
                overflow-x: hidden;
            }
            .step-active {
                display: block;
                padding-right: 0 !important;
            }
            .optionInput:checked + .box {
                background-color: {{ getThemeColors()->accent['200'] }} !important;
            }
            .btn-outline-secondary {
                border-color: {{ getThemeColors()->accent['400'] }} !important;
                color: {{ getThemeColors()->accent['600'] }}
            }
            .btn-outline-secondary:hover {
                background-color: {{ getThemeColors()->accent['500'] }} !important;
            }
        </style>
        <link href="https://unpkg.com/gijgo@1.9.13/css/gijgo.min.css" rel="stylesheet" type="text/css" />
        <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/timepicker/1.3.5/jquery.timepicker.min.css">
        @include('partials.themestyles')
    </head>

    <body style="min-height: 100vh;">
        <form method="post" enctype="multipart/form-data" id="form" style="height: 100vh;">
        @csrf
            <div class="p-4 h-100">
                <div class="card h-100">
                    <div class="card-body h-100">
                    @php($max_count = 0)
                        @includeWhen($step == 'welcome', 'onboarding.steps.intro')
                        @includeWhen($step == 'video', 'onboarding.steps.video')
                        @if($step == 'basic')
                        
                        @includeWhen($step == 'basic', 'onboarding.steps.previewbasic', ['max_count' => ++$max_count])
                        @endif
                       
                        @if($step == 'imagebio')
                        @includeWhen($step == 'imagebio', 'onboarding.steps.imagebio', ['max_count' => ++$max_count])
                        @endif
                        @if($step == 'about')
                          @includeWhen($step == 'about', 'onboarding.steps.about' , ['max_count' => ++$max_count])
                        @endif
                        @if($step == 'taxonomy')
                        @foreach(App\Taxonomy::editable()->sortBy('profile_order_key') as $taxonomy)
                            @includeWhen($step == 'taxonomy-'.$taxonomy->id, 'onboarding.steps.categories', ['taxonomy' => $taxonomy], ['max_count' => ++$max_count])
                        @endforeach
                        @endif
                        @if($step == 'questions')
                            @includeWhen($step == 'questions', 'onboarding.steps.questions', ['max_count' => ++$max_count])
                        @endif    
                        @if($step == 'notifications')
                        @includeWhen($step == 'notifications', 'onboarding.steps.notifications', ['max_count' => ++$max_count])
                        @endif   
                        @if($step == 'groups')
                        @includeWhen($step == 'groups', 'onboarding.steps.groups', ['max_count' => ++$max_count])
                        @endif  

                        @if($step == 'gdpr')
                        @includeWhen($step == 'gdpr', 'onboarding.steps.gdpr', ['max_count' => ++$max_count])
                        @endif  

                        @if($step == 'completed')
                        @includeWhen($step == 'completed', 'onboarding.steps.final')
                        @endif  
                    </div>
                </div>
            </div>
        </form>

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
        <script src="https://unpkg.com/gijgo@1.9.13/js/gijgo.min.js" type="text/javascript"></script>
        <script src="//cdnjs.cloudflare.com/ajax/libs/timepicker/1.3.5/jquery.timepicker.min.js"></script>

        @includeWhen((new \Jenssegers\Agent\Agent)->isDesktop(), 'components.notifications.firebase')
        <script>
            $(document).ready(function() {
                $('.step').first().addClass('step-active')
                @if((new \Jenssegers\Agent\Agent)->isMobile())
                    $(document).ready(function () {

                        if (document.cookie.indexOf('device_token=') != -1) {
                            var deviceTokenCookie = document.cookie.split(';').find(row => row.startsWith(' device_token='));
                            var deviceToken = deviceTokenCookie.split('=')[1];
                            $.ajax({
                                url: '/verify-token/' + deviceToken,
                                type: 'GET',
                                success: function (data) {
                                    if (data.status == 200) {
                                        $('#add_this_device').hide();
                                    }
                                }
                            });
                        }
                    });
                    @endif
                    $(document).on('change', '.custom-control-input', function(event) {
                        let checked = event.target.checked;
                        var id = $(this).data('device-id');
                        var token = '{{ csrf_token() }}';
                        var url = '/account/push-notification/' + id;
                        if(!checked){
                            $.ajax({
                                url: url,
                                type: 'POST',
                                data: {_token: token, is_enabled: checked, type: 'disable_device'},
                                success: function(data) {

                                }
                            });
                        }
                        else{
                            $.ajax({
                                url: url,
                                type: 'POST',
                                data: {_token: token, is_enabled: checked, type: 'add_device'},
                                success: function(data) {
                                    if (!firebase.messaging.isSupported()) {
                                        $('#push-notificaitons-unsupported').removeClass('d-none');
                                        var messaging = false;
                                        var isMessagingSupported = false;
                                    }
                                    else {
                                        var messaging = firebase.messaging();
                                        var isMessagingSupported = true;
                                    }

                                    function initFirebaseMessagingRegistration() {
                                        if(!isMessagingSupported)
                                            return false;
                                        messaging.requestPermission().then(function () {
                                            return messaging.getToken()
                                        }).then(function(token) {
                                            $.ajax({
                                                type: "POST",
                                                url: "{{ route('save-token') }}",
                                                data: {
                                                    token: token,
                                                    'type': 'this_device',
                                                }
                                            });

                                        }).catch(function (err) {
                                            console.log(`Token Error :: ${err}`);
                                        });
                                    }

                                    initFirebaseMessagingRegistration();
                                },
                                error: function(data) {
                                    event.target.checked = false;
                                }
                            });
                        }


                    });

                    $(document).on('click', '.removed-device', function(event) {
                        let context = this;
                        var id = $(this).data('device-id');
                        var token = '{{ csrf_token() }}';
                        var url = '/account/push-notification/' + id;
                            $.ajax({
                                url: url,
                                type: 'POST',
                                data: {_token: token, type: 'remove_device'},
                                success: function(data) {
                                    context.closest('table tr').remove();
                                }
                            });
                    });


                var tz = Intl.DateTimeFormat().resolvedOptions().timeZone;
                if(tz)
                    $('#form').append('<input type="hidden" name="timezone" value="' + tz + '">');
                $('.next-step-button').on('click', function (e) {
                    e.preventDefault();
                    var currentStep = $(this).parents('.step');
                    currentStep.removeClass('step-active');
                    $('.step').scrollTop(0)
                    currentStep.next().addClass('step-active').scrollTop(0);
                });
                $('.previous-step-button').on('click', function (e) {
                    e.preventDefault();
                    var currentStep = $(this).parents('.step');
                    currentStep.removeClass('step-active');
                    currentStep.prev().addClass('step-active').scrollTop(0);
                });

                $(window).keydown(function(event){
                    if(event.keyCode == 13) {

                        event.preventDefault();
                        return false;
                    }
                });

                $('.groupInput').change(function(e) {
                  if($(this).is(':checked'))
                    checkParentBox(this);
                  else
                    uncheckChildren(this);
                });

                function checkParentBox(el)
                {
                  if($(el).data('parent'))
                  {
                    $('.groupInput[value="'+ $(el).data('parent') +'"]').prop('checked', true);
                    checkParentBox($('.groupInput[value="'+ $(el).data('parent') +'"]'));
                  }
                }

                function uncheckChildren(el)
                {
                  $('.groupInput[data-parent="'+ $(el).val() +'"]').each(function(index, child) {
                    $(child).prop('checked', false);
                    if($('.groupInput[data-parent="'+ $(child).val() +'"]').length)
                      uncheckChildren(child);
                  });
                }

                checkFirstNextStep();

                function checkFirstNextStep()
                {
                    if(checkFirstInputs())
                        $('#firstNextStep').removeAttr('disabled');
                    else
                    {
                        $('#firstNextStep').prop('disabled', true);
                    }
                }

                $('.firstStepInput').keyup(function () {
                    checkFirstNextStep();
                });

                function checkFirstInputs()
                {
                    var shouldEnableButton = true;
                    vals = $('.firstStepInput').each(function (ind, el) {
                        if($(el).val() == '')
                        {
                            shouldEnableButton = false;
                        }
                    });
                    return shouldEnableButton;
                }

                var preCheckedGroups = $('#groupsContainer input:checked').length;
                if(preCheckedGroups > 0 || {{ $authUser->groups()->count() }})
                    $('#groupsContainer div input').prop('required', false);
                else
                    $('#groupsContainer div input').prop('required', true);

                $(".custom_category_submit").click(function(e){
                  e.preventDefault();
                  var taxonomyDiv = $(this).parent().parent().parent().parent();

                  if(taxonomyDiv.find('.custom_category').val() == '')
                  {
                    taxonomyDiv.find('.custom_category').addClass('alert alert-danger');
                    taxonomyDiv.find('.category_error').removeClass('d-none');
                    return false;
                  }

                  $.ajax('{{ env("APP_URL") }}/options', {
                    type: 'POST',
                    data: {
                      'taxonomy_id': taxonomyDiv.find('.taxonomy_id').val(),
                      'value': taxonomyDiv.find('.custom_category').val(),
                      '_token': '{{ csrf_token() }}'
                    },
                    success: function(response){
                      taxonomyDiv.find('.custom_category').val('');
                      taxonomyDiv.find('.categories_container').append('<label id="category'+response.id+'" class="checkable-tag"><input type="checkbox" name="options[]" value="'+response.id+'" checked><div class="box">'+response.name+'</div></label>');
                      taxonomyDiv.find('.optionSuccess').removeClass('d-none');
                      if(!taxonomyDiv.find('.category_error').hasClass('d-none'))
                        taxonomyDiv.find('.category_error').addClass('d-none');
                      if(taxonomyDiv.find('.custom_category').hasClass('alert alert-danger'))
                        taxonomyDiv.find('.custom_category').removeClass('alert alert-danger');
                    },
                    error: function(response){
                        if(!taxonomyDiv.find('.optionSuccess').hasClass('d-none'))
                            taxonomyDiv.find('.optionSuccess').addClass('d-none');
                        taxonomyDiv.find('.custom_category').addClass('alert alert-danger');
                        taxonomyDiv.find('.category_error').removeClass('d-none');
                    },
                  });
                });
            });

            function requestMobilePushPermissions(event) {
                event.preventDefault();
                confirm('@lang('messages.push-notification-confirmation')');
            };

            function requestPushPermissions(event) {
                event.preventDefault();
                if (!firebase.messaging.isSupported()) {
                    $('#push-notificaitons-unsupported').removeClass('d-none');
                    var messaging = false;
                    var isMessagingSupported = false;
                }
                else {
                    var messaging = firebase.messaging();
                    var isMessagingSupported = true;
                }
                initFirebaseMessagingRegistration(messaging, isMessagingSupported);
            }
            function initFirebaseMessagingRegistration(messaging, isMessagingSupported) {
                if(!isMessagingSupported)
                    return false;

                messaging.requestPermission().then(function () {
                    return messaging.getToken()
                }).then(function(token) {
                    $.ajax({
                        type: "POST",
                        url: "{{ route('save-token') }}",
                        data: {
                            token: token,
                            'type': 'this_device',
                        },
                        success: function(data) {
                            var html = '<tr><td>%name</td><td>%type</td><td><div class="custom-control custom-checkbox-switch"><input type="checkbox" class="custom-control-input" checked="%checked" data-device-id="%id" id="switch_%id"><label class="custom-control-label" for="switch_%id"></label></div></td><td><button type="button" class="btn btn-danger btn-sm removed-device" data-device-id="%id"><i class="icon icon-trash"></i></button></td></tr>'
                            html = html.replaceAll('%name', data.data.device_name);
                            html = html.replaceAll('%type', data.data.device_type);
                            html = html.replaceAll('%id', data.data.id);
                            html = html.replaceAll('%checked', data.data.active);
                            $("#devices_table tbody").append(html);
                            $("#add_this_device").hide();
                        },
                        error: function(data) {

                        }
                    });

                }).catch(function (err) {
                    console.log(`Token Error :: ${err}`);
                });
            }
            //the following function is to prevent front-end group validation from only selecting one checkbox.
            $("#groupsContainer div input").change(function(event) {
                var selectedGroupsCount = $('#groupsContainer input:checked').length;
                if(selectedGroupsCount > 0)
                    $('#groupsContainer div input').prop('required', false);
                else
                    $('#groupsContainer div input').prop('required', true);
            });
            $('#does_work').change(function() {
              if(this.checked)
              {
                $('#non-work').addClass('d-none');
                $('#work').removeClass('d-none');
              }
              else
              {
                $('#work').addClass('d-none');
                $('#non-work').removeClass('d-none');
              }
            });

            $('#radioSMS').on('change', function (event) {
                if (event.target.checked)
                {
                    $('#cellPhoneForm').removeClass('d-none');
                    $('#emailNotificationTip').addClass('d-none');
                }
            });
            $('#radioEmail').on('change', function(event) {
                if(event.target.checked)
                {
                    $('#cellPhoneForm').addClass('d-none');
                    $('#emailNotificationTip').removeClass('d-none');
                }
            });

            $('.next-step-button').click(function(e){
                $.ajax({
                    type: "post",
                    url: "/onboarding",
                    data: $('#form').serialize(),
                });
            });
        </script>
    </body>

</html>
