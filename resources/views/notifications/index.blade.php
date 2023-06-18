@extends('layouts.app')

@push('stylestack')
<link href="/assets/css/notification.css" rel="stylesheet" type="text/css" media="all" />
@endpush

@section('content')


<div class="container-fluid pt-3 notifications-sect pb-3">
	<a style="font-size:0.9em;" href="/home"><i class="icon-chevron-small-left"></i> @lang('messages.my-dashboard')</a>
	<div class="col-md-9 mx-md-auto mb-3 notification-container">
		<div class="d-flex justify-content-between mb-2">
			<h4 class="mb-0">@lang('notifications.Notifications')</h4>
			@if($authUser->unreadNotifications()->count())
			<form action="/notifications/mark-all-as-read" method="post">
				@csrf
				<button type="submit" class="btn btn-sm btn-primary">@lang('notifications.Mark all as read')</button>
			</form>
			@endif
		</div>
		<div id="refreshNotifications" style="cursor: pointer;" class="alert alert-success d-none">
			<span>You have new notifications. Click here to refresh.</span>
		</div>
		<div class="card p-0 notification-card notification-invitation " id="feedDiv">
			<table class="table mb-0">
				<tbody>
					@foreach($notifications as $notification)

					<tr>
						@if($notification->notifiable instanceof App\MessageThread)

						@if($notification->notifiable->participants()->first()->gender_pronouns == "She/Her/Hers")
						<?php $avatar = "/assets/img/avatar-female.png"; ?>
						@else
						<?php $avatar = "/assets/img/avatar-male.png"; ?>
						@endif

						<td style="vertical-align: middle;">
							<div class="card-title mb-0 d-flex align-items-center pt-1 @if($notification->viewed_at) gap-left @endif">
								@if(!$notification->viewed_at)
								<span class="notification-dot"></span>
								@endif
								<div class="img-box">
									@if(empty($notification->notifiable->participants()->first()->photo_path))
									<img style="width:100%; border-radius: 5px;" src="{{$avatar}}" />
									@else
									<div class="mx-auto mb-2 mt-1" style="margin:0px!important; width: 100%;border-radius: 5px; height: 50px; background-image: url('{{ $notification->notifiable->participants()->first()->photo_path }}'); background-size: cover; background-position: center; overflow: hidden;">
									</div>
									@endif
								</div>
								<div class="content-sect">
									<div class="mb-0 align-items-center text-weight max-text">
										<a href="/users/{{$notification->notifiable->participants()->first()->id}}">{{$notification->notifiable->participants()->first()->name}}</a> just sent you a message!
									</div>
									<span class="text-light-weight">{{ $notification->created_at->diffForHumans() }}</span>
								</div>
							</div>
						</td>

						@elseif($notification->notifiable instanceof App\Event)
						@if($notification->notifiable->group)

						@if($notification->notifiable->user()->first()->gender_pronouns == "She/Her/Hers")
						<?php $avatar = "/assets/img/avatar-female.png"; ?>
						@else
						<?php $avatar = "/assets/img/avatar-male.png"; ?>
						@endif

					<tr>
						<td style="vertical-align: middle;">
							<div class="card-title mb-0 d-flex align-items-center pt-1 @if($notification->viewed_at) gap-left @endif">
								@if(!$notification->viewed_at)<span class="notification-dot"></span>@endif
								<div class="img-box">
									@if(!$notification->notifiable->user()->first()->first()->photo_path)
									<img style="width:100%; border-radius: 5px;" src="{{$avatar}}" />
									@else
									<div class="mx-auto mb-2 mt-1" style="margin:0px!important; width: 100%;border-radius: 5px; height: 50px; background-image: url('{{ $notification->notifiable->user()->first()->photo_path }}'); background-size: cover; background-position: center; overflow: hidden;">
									</div>
									@endif
								</div>
								<div class="content-sect">
									<div class="mb-0 align-items-center text-weight max-text">
										<a href="/users/{{$notification->notifiable->user()->where('users.id', '!=', request()->user()->id)->first()->id}}">{{$notification->notifiable->user()->where('users.id', '!=', request()->user()->id)->first()->name}}</a>
										just posted a <a href="/groups/{{ $notification->notifiable->group->slug }}/discussions/{{ $notification->notifiable->slug }}" class="light-gray"><b> new event</b></a>
										 <a href="/groups/{{ $notification->notifiable->group->slug }}/events/{{ $notification->notifiable_id }}"> “{{ str_limit($notification->notifiable->name, 75) }}”  in <a href="/groups/{{ $notification->notifiable->listing->group->slug  }}"> @if(isset($notification->notifiable->listing->group->name)) {{str_limit(($notification->notifiable->listing->group->name), 75)}} @endif !</a>  RSVP and don't miss out!
									</div>
									<span class="text-light-weight">{{ $notification->created_at->diffForHumans() }}</span>
								</div>
							</div>
						</td>

					</tr>
					@else
					<?php $notification->delete(); ?>
					@endif
					@elseif($notification->notifiable instanceof App\Ideation)
					@if($notification->notifiable->participants()->first()->gender_pronouns == "She/Her/Hers")
					<?php $avatar = "/assets/img/avatar-female.png"; ?>
					@else
					<?php $avatar = "/assets/img/avatar-male.png"; ?>
					@endif
					<tr>
						<td style="vertical-align: middle;">
							<div class="card-title mb-0 d-flex align-items-center pt-1  @if($notification->viewed_at) gap-left @endif">
								@if(!$notification->viewed_at)<span class="notification-dot"></span>@endif

								<div class="img-box">
									@if(empty($notification->notifiable->participants()->first()->photo_path))
									<img style="width:100%; border-radius: 5px;" src="{{$avatar}}" />
									@else
									<div class="mx-auto mb-2 mt-1" style="margin:0px!important; width: 100%;border-radius: 5px; height: 50px; background-image: url('{{ $notification->notifiable->participants()->first()->photo_path }}'); background-size: cover; background-position: center; overflow: hidden;">
									</div>
									@endif
								</div>
								<div class="content-sect">
									<div class="mb-0 align-items-center text-weight max-text">
										<a href="/users/{{$notification->notifiable->participants()->first()->id}}">{{$notification->notifiable->participants()->first()->name}}</a>
										invites you to a new <a href="/ideations/{{ $notification->notifiable->slug }}">Focus Group</a>!
									</div>
									<span class="text-light-weight">{{ $notification->created_at->diffForHumans() }}</span>
								</div>
							</div>
						</td>

					</tr>

					@elseif($notification->notifiable_type == 'App\Ideation' && $notification->action == "Ideation Not Accepted")

					<tr>
						<td style="vertical-align: middle;">
							<div class="card-title mb-0 d-flex align-items-center pt-1 @if($notification->viewed_at) gap-left @endif">
								@if(!$notification->viewed_at)<span class="notification-dot"></span>@endif
								<div class="img-box">
									<img style="width:100%; border-radius: 5px;" src="/assets/img/avatar-male.png" />
								</div>
								<div class="content-sect">
									<div class="mb-0 align-items-center text-weight max-text">
										Unfortunately, your request to join the <b>{{ str_limit($notification->notifiable()->withTrashed()->first()->name, 75) }} </b> {{ $notification->message }} has been declined. Better luck next time.
									</div>
									<span class="text-light-weight">{{ $notification->created_at->diffForHumans() }}</span>
								</div>

							</div>
						</td>

					</tr>
					@elseif($notification->notifiable instanceof App\DiscussionThread)
					@if($notification->notifiable->group)
					@if($notification->notifiable->user()->first()->gender_pronouns == "She/Her/Hers")
					<?php $avatar = "/assets/img/avatar-female.png"; ?>
					@else
					<?php $avatar = "/assets/img/avatar-male.png"; ?>
					@endif
					<tr>
						<td style="vertical-align: middle;">
							<div class="card-title mb-0 d-flex align-items-center pt-1 @if($notification->viewed_at) gap-left @endif">
								@if(!$notification->viewed_at)<span class="notification-dot"></span>@endif
								<div class="img-box">
									@if(empty($notification->notifiable->user()->where('users.id', '!=', request()->user()->id)->first()->photo_path))
									<img style="width:100%; border-radius: 5px;" src="{{$avatar}}" />
									@else
									<div class="mx-auto mb-2 mt-1" style="margin:0px!important; width: 100%;border-radius: 5px; height: 50px; background-image: url('{{ $notification->notifiable->user()->where('users.id', '!=', request()->user()->id)->first()->photo_path }}'); background-size: cover; background-position: center; overflow: hidden;">
									</div>
									@endif
								</div>

								<div class="content-sect">
									<div class="mb-0 align-items-center text-weight max-text">
										<a href="/users/{{$notification->notifiable->user()->first()->id}}">{{$notification->notifiable->user()->first()->name}} </a>
										just started a <a href="/groups/{{ $notification->notifiable->group->slug }}/discussions/{{ $notification->notifiable->slug }}" class="light-gray"><b> new discussion</b>
									</a> <a href="/groups/{{ $notification->notifiable->group->slug }}/discussions/{{ $notification->notifiable->slug }}"> “{{ str_limit($notification->notifiable->name, 75) }}”</a>
										in   <a href="/groups/{{ $notification->notifiable->listing->group->slug  }}"> @if(isset($notification->notifiable->listing->group->name)) {{str_limit(($notification->notifiable->listing->group->name), 75)}} @endif !</a> Join in and share your thoughts.
									</div>
									<span class="text-light-weight">{{ $notification->created_at->diffForHumans() }}</span>
								</div>
							</div>
						</td>

					</tr>
					@else
					<?php $notification->delete(); ?>
					@endif

					@elseif($notification->notifiable instanceof App\TextPost && $notification->notifiable->listing()->exists())
					@if($notification->notifiable->user()->first()->gender_pronouns == "She/Her/Hers")
					<?php $avatar = "/assets/img/avatar-female.png"; ?>
					@else
					<?php $avatar = "/assets/img/avatar-male.png"; ?>
					@endif
					<tr>
						<td style="vertical-align: middle;">
							<div class="card-title mb-0 d-flex align-items-center pt-1 @if($notification->viewed_at) gap-left @endif">
								@if(!$notification->viewed_at)<span class="notification-dot"></span>@endif
								<div class="img-box">
									@if(empty($notification->notifiable->user()->first()->photo_path))
									<img style="width:100%; border-radius: 5px;" src="{{$avatar}}" />
									@else
									<div class="mx-auto mb-2 mt-1" style="margin:0px!important; width: 100%;border-radius: 5px; height: 50px; background-image: url('{{ $notification->notifiable->user()->first()->photo_path}}'); background-size: cover; background-position: center; overflow: hidden;">
									</div>
									@endif
								</div>
								<div class="content-sect">
									<div class="mb-0 align-items-center text-weight max-text">
										<a href="/users/{{$notification->notifiable->user()->first()->id}}">{{$notification->notifiable->user()->first()->name}}</a>
										just added a new <a href="/groups/{{ $notification->notifiable->listing->group->slug }}/posts/{{ $notification->notifiable->listing->id }}" class="light-gray"><b>text post</b></a> in <a href="/groups/{{ $notification->notifiable->listing->group->slug  }}"> @if(isset($notification->notifiable->listing->group->name)) {{str_limit(($notification->notifiable->listing->group->name), 75)}} @endif </a> group!
									</div>
									<span class="text-light-weight">{{ $notification->created_at->diffForHumans() }}</span>
								</div>
							</div>
						</td>

					</tr>
					@elseif($notification->notifiable instanceof App\Introduction && $notification->notifiable->other_user && $notification->notifiable->invitee)
					<tr>
						<td style="vertical-align: middle;">
							<div class="card-title mb-0 d-flex align-items-center pt-1 @if($notification->viewed_at) gap-left @endif">
								@if(!$notification->viewed_at)<span class="notification-dot"></span>@endif
								<div class="img-box">
									<img style="width:100%; border-radius: 5px;" src="/assets/img/avatar-male.png" />
								</div>
								<div class="content-sect">
									<div class="mb-0 align-items-center text-weight max-text">
										<a class="mr-1" href="/introductions/{{ $notification->notifiable->id }}">{{ $notification->notifiable->other_user->name }}</a>
										<a href="/introductions/{{ $notification->notifiable->id }}" class="light-gray"><b>introduces</b></a>
										<a class="ml-1" href="/introductions/{{ $notification->notifiable->invitee->id }}">{{ $notification->notifiable->invitee->name }}</a>
									</div>
									<span class="text-light-weight">{{ $notification->created_at->diffForHumans() }}</span>
								</div>
							</div>
						</td>
					</tr>
					@elseif($notification->notifiable instanceof App\Shoutout)
					@if($notification->notifiable->user()->where('users.id', '!=', request()->user()->id)->first()->gender_pronouns == "She/Her/Hers")
					<?php $avatar = "/assets/img/avatar-female.png"; ?>
					@else
					<?php $avatar = "/assets/img/avatar-male.png"; ?>
					@endif
					@if($notification->notifiable->listing->group != null)
					<tr>
						<td style="vertical-align: middle;">
							<div class="card-title mb-0 d-flex align-items-center pt-1 @if($notification->viewed_at) gap-left @endif">
								@if(!$notification->viewed_at)<span class="notification-dot"></span>@endif

								<div class="img-box">
									@if(!$notification->notifiable->user()->where('users.id', '!=', request()->user()->id)->first()->photo_path)
									<img style="width:100%; border-radius: 5px;" src="{{ $avatar}}" />
									@else<div class="mx-auto mb-2 mt-1" style="margin:0px!important; width: 100%;border-radius: 5px; height: 50px; background-image: url('{{ $notification->notifiable->user()->where('users.id', '!=', request()->user()->id)->first()->photo_path}}'); background-size: cover; background-position: center; overflow: hidden">
									</div>
									@endif

								</div>
								<div class="content-sect">
									<div class="mb-0 align-items-center text-weight max-text ">
										<a href="/users/{{$notification->notifiable->shouting->id}}">{{$notification->notifiable->shouting->name}}</a>
										just added a <a href="{{$notification->notifiable->shouting()->exists() ? '/shoutouts/received' : '/posts/' . $notification->notifiable->listing->id }}" class="light-gray"><b>shoutout </b></a> in <a href="/groups/{{ $notification->notifiable->listing->group->slug ? $notification->notifiable->listing->group->slug : '' }}">{{ str_limit($notification->notifiable->listing->group->name ?$notification->notifiable->listing->group->name : '', 75) }}</a> group! Check it out and share your support.
									</div>
									<span class="text-light-weight">{{ $notification->created_at->diffForHumans() }}</span>
								</div>
							</div>
						</td>
					</tr>
					@endif
					@elseif($notification->notifiable instanceof App\Post)
					@php
					$user = $notification->notifiable->comments()->first();
					$userId = isset($user->user_id) ? $user->user_id:0;
					$getuserinfo = App\User::withTrashed()->where('id',$userId)->first();
					@endphp
					@if(isset($getuserinfo->id))
					<tr>
						<td style="vertical-align: middle;">
							<div class="card-title mb-0 d-flex align-items-center pt-1 @if($notification->viewed_at) gap-left @endif">
								@if(!$notification->viewed_at)<span class="notification-dot"></span>@endif
								<div class="img-box">
									@if(isset($getuserinfo->photo_path))
									<div class="mx-auto mb-2 mt-1" style="margin:0px!important; width: 100%;border-radius: 5px; height: 50px; background-image: url('{{isset($getuserinfo->photo_path) ? $getuserinfo->photo_path:0  }}'); background-size: cover; background-position: center; overflow: hidden">
									</div>

									@else
									<img style="width:100%; border-radius: 5px;" src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQ8998L1n4kR9NC61La_R2oiM5gxqq1QaQJb8LHxuH4Z9_o3SmxRYOcEd5JkTTDohBPBgI&usqp=CAU" />
									@endif
								</div>
								<div class="content-sect">
									<div class="mb-0 align-items-center text-weight max-text">
										<a class="mr-1" href="/users/{{isset($getuserinfo->id) ? $getuserinfo->id:0 }}">{{isset($getuserinfo->name) ? $getuserinfo->name:0  }}</a>
										just commented on the post in <a class="mr-1" href="/posts/{{ $notification->notifiable_id }}">{{$notification->notifiable->group()->first()->name }}</a> <a href="/posts/{{ $notification->notifiable_id }}" class="light-gray"></a>
									</div>
									<span class="text-light-weight">{{ $notification->created_at->diffForHumans() }}</span>
								</div>
							</div>
						</td>

					</tr>
					@endif
					@else
					{{ $notification->markAsRead() }}
					@endif
					
					</tr>
					@endforeach
					<tr id="append-data"></tr>
					<tr class="skeleton" id="skelton-loader">
			<td style="vertical-align: middle;">
				<div class="card-title mb-0 d-flex align-items-center pt-1  gap-left ">
					<div class="square"></div>
					<div class="content-sect">
						<div class="mb-0 align-items-center text-weight max-text pb-1">
							<div class="line w75"></div>
						</div>
						<span class="text-light-weight">
							<div class="line h12 w25 m10"></div>
						</span>
					</div>
				</div>
			</td>

		</tr>
		<tr class="skeleton" id="skelton-loader">
			<td style="vertical-align: middle;">
				<div class="card-title mb-0 d-flex align-items-center pt-1  gap-left ">
					<div class="square"></div>
					<div class="content-sect">
						<div class="mb-0 align-items-center text-weight max-text pb-1">
							<div class="line w75"></div>
						</div>
						<span class="text-light-weight">
							<div class="line h12 w25 m10"></div>
						</span>
					</div>
				</div>
			</td>

		</tr>
		<tr class="skeleton" id="skelton-loader">
			<td style="vertical-align: middle;">
				<div class="card-title mb-0 d-flex align-items-center pt-1  gap-left ">
					<div class="square"></div>
					<div class="content-sect">
						<div class="mb-0 align-items-center text-weight max-text pb-1">
							<div class="line w75"></div>
						</div>
						<span class="text-light-weight">
							<div class="line h12 w25 m10"></div>
						</span>
					</div>
				</div>
			</td>

		</tr>
		<tr class="skeleton" id="skelton-loader">
			<td style="vertical-align: middle;">
				<div class="card-title mb-0 d-flex align-items-center pt-1  gap-left ">
					<div class="square"></div>
					<div class="content-sect">
						<div class="mb-0 align-items-center text-weight max-text pb-1">
							<div class="line w75"></div>
						</div>
						<span class="text-light-weight">
							<div class="line h12 w25 m10"></div>
						</span>
					</div>
				</div>
			</td>

		</tr>

		<tr class="skeleton" id="skelton-loader">
			<td style="vertical-align: middle;">
				<div class="card-title mb-0 d-flex align-items-center pt-1  gap-left ">
					<div class="square"></div>
					<div class="content-sect">
						<div class="mb-0 align-items-center text-weight max-text pb-1">
							<div class="line w75"></div>
						</div>
						<span class="text-light-weight">
							<div class="line h12 w25 m10"></div>
						</span>
					</div>
				</div>
			</td>

		</tr>
				</tbody>
			</table>
			
		</div>
	</div>


	<!-- <div class="d-flex align-items-center justify-content-around">
			{{ $notifications->nextPageUrl() }}
		</div> -->
</div>
</div>
<!-- <input type="text" value="{{$notifications->nextPageUrl()}}" id="nextPageUrl"> -->

@endsection

@section('scripts')
<script>
	let time = new Date();
	window.setInterval(checkNewNotifications, 10000, time);

	function checkNewNotifications(startTime) {
		$.ajax({
			type: "get",
			url: "/api/new-notifications/" + startTime.toISOString(),
			async: true,
			success: function($result) {
				if ($result == true) {
					$('#refreshNotifications').removeClass('d-none');
				}
			},
		});
	}
	var page = 1;
	$('#refreshNotifications').click(function() {
		window.location.reload();
	});
	$(window).scroll((e) => {
		//e.preventDefault();

		if ($(window).scrollTop() + $(window).height() === $(document).height()) {
			var pageid = $('#nextPageUrl').val();
			e.preventDefault();
			$.ajax({
				url: '/notifications?page=' + ++page,
				type: "GET",
				beforeSend: function() {
					//alert('beforeSend');
					$('tr#skelton-loader').show();
				},
				complete: function() {

					$('tr#skelton-loader').hide();
				},
				success: function(response) {
					$("#append-data").append(response.html);
					$('tr#skelton-loader').hide();
					// setTimeout(function() { 
					// 	$('tr#skelton-loader').show();
					// }, 1000);

					// setTimeout(function() { 
					// 	$('tr#skelton-loader').hide();
					// 	$("#append-data").append(response.html);
					// }, 2000);

				},
				error: function() {}
			});
		}
	});
</script>
@endsection