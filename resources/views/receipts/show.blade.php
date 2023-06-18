@extends('layouts.app')

@section('content')


<div class="container pt-3">
	<a style="font-size:0.9em;" href="/purchases"><i class="icon-chevron-small-left"></i> Purchases</a>
	<div class="col-md-9 mx-md-auto mb-3">
		<div class="receipts-container">
			@include('components.receipts.show', ['receipt' => $receipt, 'isSimple' => false, 'showGroups' => false])
		</div>
	</div>
</div>

@endsection