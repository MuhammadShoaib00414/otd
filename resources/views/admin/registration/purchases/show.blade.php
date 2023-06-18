@extends('admin.registration.layout')

@section('inner-page-content')
<div class="container">
	<a href="/admin/registration/{{ $page->id }}/purchases"> < Purchases </a>
	@include('components.receipts.show', ['receipt' => $receipt,'page' => $page, 'isSimple' => false, 'showGroups' => true,'showLinks' => true])
</div>
@endsection
