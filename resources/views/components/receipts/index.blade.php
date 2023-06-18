@foreach($receipts as $receipt)
	@include('components.receipts.show', ['receipt' => $receipt, 'isSimple' => $isSimple, 'showLink' => isset($showLink) ? $showLink . $receipt->id : false, 'showLinks' => isset($showLinks) ? $showLinks : true, 'hr' => isset($hr) ? $hr : false])
@endforeach