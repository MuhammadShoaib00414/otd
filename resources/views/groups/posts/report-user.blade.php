@extends('layouts.app')

@section('content')
<div class="container">
	<div class="col-md-9 mx-md-auto py-3">
		<h3>@lang('messages.report-user')</h3>
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
                <div class="d-flex gap-2 text-black">
                    <div class="flex-shrink-0">
                        <img src="{{($userToReport->photo_path) ?? public_path('images/profile-icon-empty.png')}}"
                            alt="Generic placeholder image" class="img-fluid"
                            style="width: 70px; border-radius: 50%;">
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h5 class="mb-1">{{$userToReport->name}}</h5>
                        <p class="mb-2 pb-1" style="color: #2b2a2a;">{{$userToReport->summary}}</p>
                        
                    </div>
                </div>
                <hr>
				<form method="post" action="{{route('post-report-user', $userToReport->id)}}">
					@method('post')
					@csrf
					<input type="hidden" name="user_id" value="{{$userToReport->id}}">
					<input type="hidden" name="reported_by" value="{{$userId}}">
					<input type="hidden" name="postId" value="{{$postId}}">
                    <div class="form-group">
                        <label for="report-reason">@lang('messages.report-reason')</label>
                        <textarea class="form-control" id="report-reason" name="report-reason" placeholder="@lang('messages.report-reason-placeholder')" rows="5" required></textarea>
                    </div>
                    <div class="form-group">
                        <input class="" type="checkbox" value="1" id="report-description" name="block">
                        <label for="report-description">@lang('messages.block-user')</label>
                    </div>
					<button type="submit" class="btn btn-primary">@lang('general.save')</button>
				</form>
			</div>
		</div>
	</div>
</div>

@endsection

@section('scripts')
	<script>
		$('#radioSMS').on('change', function (event) {
			if (event.target.checked)
				$('#cellPhoneForm').removeClass('d-none');
		});
		$('#radioEmail').on('change', function(event) {
			if(event.target.checked)
				$('#cellPhoneForm').addClass('d-none');
		});
	</script>
@endsection