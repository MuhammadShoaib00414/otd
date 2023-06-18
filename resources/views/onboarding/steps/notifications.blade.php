<div class="step">
    <div class="row justify-content-around align-items-center">
        <div class="col-12">
             <h6 class="title-decorative mb-2">@lang('messages.step-x', ['step' => ($max_count) ])</h6>
            <div class="col-md-8 mx-md-auto">
                <p style="font-size: 1.5em;" class="font-weight-bold mb-0">@lang('messages.notifications')</p>
                <small class="text-muted mb-2">@lang('messages.change-under-account')</small>
                <div  style="max-width: 800px;">
              
                @if(config('app.url') == 'https://todayisagoodday.onthedotglobal.com')
                    <small class="text-muted mb-2"> <b>Please add your device below to receive notifications of upcoming programs. You can always adjust your notification settings under Account settings. If you are setting this account up on a desktop, be sure to do the same when you download the app on your phone and vice versa under Account settings.</b></small>
                @endif
                </div>
                <div class="form-group" style="max-width:450px;">
                    <label for="notification_frequency">
                        @if(true)
                            @lang('messages.notification-frequency-prompt')
                        @else
                            @lang('messages.notification-frequency-prompt-no-sms')
                        @endif
                    </label>

                    <div>
                        @foreach($frequencyOptions as $option)
                        <div class="custom-control custom-radios custom-control-inline">
                          <input type="radio" id="frequency{{ $option }}" name="notification_frequency" value="{{ $option }}" class="custom-control-inputs"{{ request()->user()->notification_frequency == $option ? ' checked' : ''}}>
                          <label class="custom-control-label" for="frequency{{ $option }}">@lang("notifications.{$option}")</label>
                        </div>
                        @endforeach
                    </div>
                </div>

                <div class="form-group d-none" style="max-width:450px;">
                    <label for="notification_method">@lang('messages.notification-method-prompt')</label>
                    <div>
                        <div class="custom-control custom-radio custom-control-inline">
                          <input type="radio" id="radioEmail" name="notification_method" value="email" class="custom-control-input" checked>
                          <label class="custom-control-label" for="radioEmail">@lang('messages.email')</label>
                        </div>
                        <div class="custom-control custom-radio custom-control-inline">
                          <input type="radio" id="radioSMS" name="notification_method" value="sms" class="custom-control-input">
                          <label class="custom-control-label" for="radioSMS">@lang('messages.text-message')</label>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    @if($isRegistered == 0)
                        <label for="add_this_device">@lang('messages.enable-notification-on-device') &nbsp;&nbsp;</label>
                        <button type="button" class="btn btn-primary btn-sm send-push-notification" id="add_this_device" @if((new \Jenssegers\Agent\Agent)->isMobile()) onclick="requestMobilePushPermissions(event)" @else onclick="requestPushPermissions(event)" @endif>@lang('messages.add-device')</button>
                    @endif
                    <h6>Devices List</h6>
                    <div class="table-responsive">
                        <table class="table table-striped table-sm" id="devices_table">
                            <thead>
                                <tr>
                                    <th>@lang('messages.device-name')</th>
                                    <th>@lang('messages.device-type')</th>
                                    <th>@lang('messages.enable-device')</th>
                                    <th>@lang('messages.action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($user->devices as $ud)
                                <tr>
                                    <td>{{ $ud->device_name }}</td>
                                    <td>{{ $ud->device_type }}</td>
                                    <td>
                                        <div class="custom-control custom-checkbox-switch">
                                            <input type="checkbox" class="custom-control-input" @if($ud->active == 1) checked @endif  data-device-id="{{$ud->id}}" id="switch_{{$ud->id}}">
                                            <label class="custom-control-label" for="switch_{{$ud->id}}"></label>
                                        </div>
                                        <!-- <button type="button" class="btn btn-danger btn-sm send-push-notification" data-device-id="{{ $ud->id }}">@lang('messages.send-push-notification')</button> -->
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-danger btn-sm removed-device" data-device-id="{{ $ud->id }}"><i class="icon icon-trash"></i></button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>

                    </div>
                </div>
            </div>
            <div class="d-flex justify-content-between mt-4">
                <button class="btn btn-outline-secondary previous-step-button" type="button">@lang('messages.previous-step')</button>
                <button dusk="next" class="btn btn-primary next-step-button" type="submit">@lang('messages.next-step')</button>
            </div>
        </div>
    </div>
</div>
