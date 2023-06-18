<div style="display: flex; flex-direction: column; justify-content: between;">
	@include('components.receipts.index', ['receipts' => $receipts, 'isSimple' => false, 'showGroups' => request()->user()->is_admin, 'showLinks' => false, 'hr' => true])
</div>