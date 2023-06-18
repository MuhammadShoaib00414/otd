@extends('layouts.app')

@section('content')

<div class="container">
    <div class="row mx-md-auto py-3">
        <div class="col-md-6 text-center">
            <h3>@lang('messages.account-details')</h3>
        </div>
        <div class="col-md-6 text-center">
                <span><b style="text-transform: uppercase; letter-spacing: 0.05em;margin-left: 50px;">  <a  href="#myModal"  data-toggle="modal"  class="trigger-btn btn btn-outline-primary ml-1">@lang('messages.delete-account')</a></span>
        </div>
    </div>

	<div class="col-md-9 mx-md-auto">
		@if(session()->has('success'))
			<div class="alert alert-success">
				{{ session('success') }}
			</div>
		@elseif($errors->any())
		    <div class="alert alert-danger">
		        <ul>
		            @foreach ($errors->all() as $error)
		                <li>{{ $error }}</li>
		            @endforeach
		        </ul>
		    </div>
		@endif

		<div class="card mb-4">
			<div class="card-body">
				<form method="post" action="/account">
					@method('put')
					@csrf
					<h4 class="mb-2">@lang('messages.notifications')</h4>
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
							  <input type="radio" id="frequency{{ $option }}" name="notification_frequency" value="{{ $option }}" class="custom-control-inputs"{{ $user->notification_frequency == $option ? ' checked' : ''}}>
							  <label class="custom-control-label" for="frequency{{ $option }}">@lang("notifications.{$option}")</label>
							</div>
							@endforeach
						</div>
					</div>
					<div class="form-group">
						@if($isRegistered == 0)
							<label for="add_this_device">@lang('messages.enable-notification-on-device') &nbsp;&nbsp;</label>
							<button type="button" class="btn btn-primary btn-sm send-push-notification" id="add_this_device" @if((new \Jenssegers\Agent\Agent)->isMobile()) onclick="requestMobilePushPermissions(event)" @else onclick="requestPushPermissions(event)" @endif>@lang('messages.add-device')</button>
						@endif
						<h6>Devices List</h6>
						<div class="table-responsive">
							<table class="table table-striped table-sm ">
								<thead>
									<tr>
										<th>@lang('messages.device-name')</th>
										<th>@lang('messages.device-type')</th>
										<th>@lang('messages.enable-device')</th>
										<th>@lang('messages.action')</th>
									</tr>
								</thead>
								<tbody id="appendDevice">
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
											<button type="button" class="btn btn-danger btn-sm removed-device" data-device-id="{{ $ud->id }}"><i class="fa fa-trash"></i></button>
										</td>
									</tr>
									@endforeach
								</tbody>
							</table>
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

					<div class="form-check mb-0 d-none">
						<input class="form-check-input" type="checkbox" name="enable_push_notifications" id="enable_push_notifications" {{ $user->enable_push_notifications ? 'checked' : '' }}>
						<label class="form-check-label" for="enable_push_notifications" style="font-size: 0.95em">Send me push notifications</label>
					</div>
					<small class="text-muted mb-2 d-none">Push notifications will be sent immediately</small>
					<!-- <p id="push-notificaitons-unsupported" class="d-none" style="color:red">We do not support push notifications for this browser yet. push notifications are only supported in chrome and firefox.</p> -->

					<div class="form-group {{ $user->notification_method == "sms" ? '' : 'd-none'}}" id="cellPhoneForm" style="max-width: 450px;">
						<label for="phone">@lang('messages.cell-number')</label>
						<div class="input-group">
							<div class="input-group-prepend">
								<div class="input-group-text">+1</div>
							</div>
							<input type="tel" name="phone" class="form-control"value="{{ substr($user->phone, 2) }}">
						</div>
					</div>

					<br>

					@if(getSetting('is_localization_enabled'))
					<div class="form-group" style="max-width: 450px;">
						<label for="locale">@lang('messages.language')</label>
						<select class="custom-select" name="locale" id="locale">
							<option value="en" {{ $authUser->locale == 'en' ? 'selected' : '' }}>English</option>
							<option value="es" {{ $authUser->locale == 'es' ? 'selected' : '' }}>Español</option>
						</select>
					</div>
					@endif
					<button type="submit" class="btn btn-primary">@lang('general.save')</button>
				</form>
			</div>
		</div>

		@if(getsetting('is_gdpr_enabled'))
		<div class="card card-body">
			<h4 class="mb-2">Profile Visibility</h4>
			<form method="post" action="/gdpr">
				@csrf
				@method('put')
				<p class="mt-0">{{ getsetting('gdpr_prompt') }}</p>
		        <div class="d-flex align-items-center">
		            <div class="form-check ml-3">
		            	<input type="hidden" name="is_visible" value="0">
		                <input type="checkbox" class="form-check-input" name="is_visible" id="is_visible" value="1" {{ (!$user->is_hidden) ? 'checked' : '' }}>
		                <label for="is_visible">{{ getsetting('gdpr_checkbox_label') }}</label>
		            </div>
		        </div>
		       <button type="submit" class="btn btn-primary">Save</button>
			</form>

		</div>
		@endif

		@if(getsetting('are_group_codes_enabled'))
		<div class="card card-body">
			<h4 class="mb-2">@lang('groups.Join group with code')</h4>
			<form method="post" action="/groups/join">
				@csrf
				<div class="form-group w-50">
					<label for="code">@lang('groups.Code'):</label>
					<input required autocomplete="off" type="text" name="code" id="code" class="form-control">
				</div>
				<button type="submit" class="btn btn-primary">Join</button>
			</form>
		</div>
		@endif
		<div class="card">
			<div class="card-body">
				<form method="post" action="/account">
					@method('put')
					@csrf
					<h4 class="mb-2">@lang('messages.change-password')</h4>
					<div class="form-group" style="max-width: 400px;">
						<label for="new_password">@lang('messages.new-password')</label>
						<input type="password" name="new_password" id="new_password" class="form-control">
					</div>
					<div class="form-group" style="max-width: 400px;">
						<label for="new_password_confirm">@lang('messages.new-password-confirm')</label>
						<input type="password" name="new_password_confirm" id="new_password_confirm" class="form-control">
					</div>
					<button type="submit" class="btn btn-primary">@lang('messages.save')</button>
					<div id="loader">
                        <img src="https://media3.giphy.com/media/3oEjI6SIIHBdRxXI40/giphy.gif?cid=ecf05e47nz5seyqeu5xp0r72usry7m24bw3kq6hwc78wy9xy&rid=giphy.gif&ct=g" alt="Loading..." />
                    </div>
				</form>
			</div>
		</div>

		<div class="card">
			<div class="card-body">
				<form method="post" action="/account/email">
					@method('put')
					@csrf
					<h4 class="mb-2">@lang('messages.change-email')</h4>
					<div class="form-group" style="max-width: 400px;">
						<label for="email">@lang('messages.new-email')</label>
						<input type="email" name="email" id="email" class="form-control">
					</div>
					<button type="submit" class="btn btn-primary">@lang('general.save')</button>
				</form>
			</div>
		</div>
	</div>

</div>

<!-- Modal HTML -->
<div id="myModal" class="modal fade">
    <div class="modal-dialog modal-confirm">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">Permanently Delete Account</h4>
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        </div>
        <div class="modal-body">
          <p>Your account has been deactivated from the site and will be permanently deleted within 30 days. if you log into your account within the next 30 days,you will have the option to cancel this deletion.</p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-primary" data-dismiss="modal">Cancel</button>
          <a href="/delete-account/{{ $user->id }}"> <button type="button" class="btn btn-outline-danger">Delete </button></a>
        </div>
      </div>
    </div>
  </div>
@endsection

@section('scripts')
	<script>
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
		$('#radioSMS').on('change', function (event) {
			if (event.target.checked)
				$('#cellPhoneForm').removeClass('d-none');
		});
		$('#radioEmail').on('change', function(event) {
			if(event.target.checked)
				$('#cellPhoneForm').addClass('d-none');
		});
		
		$(document).on('change', '.custom-control-input', function(event) {
			@if(((new \Jenssegers\Agent\Agent)->isMobile()))
				$("#loader").show();
			@endif
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
						@if(((new \Jenssegers\Agent\Agent)->isMobile()))
							$("#loader").hide();
						@endif
					}
				});
			}
			else{
				@if(((new \Jenssegers\Agent\Agent)->isMobile()))
					$("#loader").show();
				@endif
				$.ajax({
					url: url,
					type: 'POST',
					data: {_token: token, is_enabled: checked, type: 'add_device'},
				
					success: function(data) {
						@if(((new \Jenssegers\Agent\Agent)->isMobile()))
								$("#loader").hide();
						    	@endif
						if (!firebase.messaging.isSupported()) {
							@if(!((new \Jenssegers\Agent\Agent)->isMobile()))
							$('#push-notificaitons-unsupported').removeClass('d-none');
							@endif
							
							var messaging = false;
							var isMessagingSupported = false;
						}
						else {
							var messaging = firebase.messaging();
							var isMessagingSupported = true;
						}

						function initFirebaseMessagingRegistration() {
							@if(((new \Jenssegers\Agent\Agent)->isMobile()))
								$("#loader").show();
							@endif
							if(!isMessagingSupported)
								return false;
								
							messaging.requestPermission().then(function () {
								return messaging.getToken()
								@if(((new \Jenssegers\Agent\Agent)->isMobile()))
								$("#loader").show();
						    	@endif
							}).then(function(token) {
								$.ajax({
									type: "POST",
									url: "{{ route('save-token') }}",
									data: {
										token: token,
										'type': 'this_device',
									},
									success: function(data) {
										
										@if(((new \Jenssegers\Agent\Agent)->isMobile()))
											$("#loader").hide();
										@endif
							
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
			@if(((new \Jenssegers\Agent\Agent)->isMobile()))
			 $("#loader").show();
			@endif
			var id = $(this).data('device-id');
			var token = '{{ csrf_token() }}';
			var url = '/account/push-notification/' + id;
				$.ajax({
					url: url,
					type: 'POST',
					data: {_token: token, type: 'remove_device'},
					success: function(data) {
						
						@if(((new \Jenssegers\Agent\Agent)->isMobile()))
							$("#loader").hide();
							window.location.reload();
						@endif
					
						context.closest('table tr').remove();
					}
					
				});
		});
		// $('#add_this_device').on('click', function(event) {

		// });

		function requestMobilePushPermissions(event) {

			@if(((new \Jenssegers\Agent\Agent)->isMobile()))
					$("#loader").show();
				@endif	
				window.location.reload();		
				event.preventDefault();
			confirm('@lang('messages.push-notification-confirmation')');
		};
		function requestPushPermissions(event) {
			event.preventDefault();
			if (!firebase.messaging.isSupported()) {
				@if(!((new \Jenssegers\Agent\Agent)->isMobile()))
				$('#push-notificaitons-unsupported').removeClass('d-none');
				@endif
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
						var html = '<tr><td>%name</td><td>%type</td><td><div class="custom-control custom-checkbox-switch"><input type="checkbox" class="custom-control-input" checked="%checked" data-device-id="%id" id="switch_%id"><label class="custom-control-label" for="switch_%id"></label></div></td><td><button type="button" class="btn btn-danger btn-sm removed-device" data-device-id="%id"><i class="fa fa-trash"></i></button></td></tr>'
                        html = html.replaceAll('%name', data.data.device_name);
                        html = html.replaceAll('%type', data.data.device_type);
                        html = html.replaceAll('%id', data.data.id);
                        html = html.replaceAll('%checked', data.data.active);
					
                        $("#appendDevice").append(html);
                        $("#add_this_device").hide();

					},
					error: function(data) {
						window.location.reload();
					}
				});

			}).catch(function (err) {
				console.log(`Token Error :: ${err}`);
			});
		}
	</script>
@endsection
