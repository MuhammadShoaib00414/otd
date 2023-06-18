@extends('layouts.app')

@section('content')


<div class="container pt-3">
	<a style="font-size:0.9em;" href="/home"><i class="icon-chevron-small-left"></i> @lang('messages.my-dashboard')</a>
	<div class="col-md-9 mx-md-auto mb-3">
		<h4>Purchases</h4>
		<div class="receipts-container">
			@include('components.receipts.index', ['receipts' => $receipts, 'showLink' => '/purchases/', 'showGroups' => false, 'isSimple' => true, 'showLinks' => true])
		</div>
	</div>
</div>

@endsection