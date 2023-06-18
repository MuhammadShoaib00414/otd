@extends('admin.layout')

@section('page-content')
    @component('admin.partials.breadcrumbs', ['links' => [
        'Registration Pages' => '/admin/registration',
        $page->name => '',
    ]])
    @endcomponent

<div style="background-color: #fafbfd; margin: -1em -1em 1em -1em; padding: 1em 0 0; border-left: 1px solid #e0e2e7;">
	<div class="d-flex justify-content-between mb-3 align-items-center mx-4 py-2">
		<h4>{{ $page->name }} Registration Page</h4>
		<div class="input-group input-group-sm" style="max-width: 25%">
			<input id="link" class="form-control" type="text" value="{{ config('app.url') }}/register/{{ $page->slug }}">
			<div class="input-group-append">
				<button id="copyLink" class="btn btn-primary">Copy</button>
			</div>
		</div>
		@if ($page->deleted_at == null)<a href="/admin/registration/{{ $page->id }}/edit" class="btn btn-outline-primary btn-sm">Edit</a>@endif
	</div>
	<ul class="nav nav-tabs mb-4 px-3">
	    <li class="nav-item">
	        <a class="nav-link{{ (Request::is('admin/registration/' . $page->id)) ? ' active' : '' }}" href="/admin/registration/{{ $page->id }}">Overview</a>
	    </li>
	    @if(is_stripe_enabled())
	    <li class="nav-item">
	    	<a class="nav-link{{ (Request::is('*tickets*')) ? ' active' : '' }}" href="/admin/registration/{{ $page->id }}/tickets">Tickets</a>
	    </li>
	    <li class="nav-item">
	    	<a class="nav-link{{ (Request::is('*purchases*')) ? ' active' : '' }}" href="/admin/registration/{{ $page->id }}/purchases">Purchases</a>
	    </li>
        <li class="nav-item">
	    	<a class="nav-link{{ (Request::is('*report*')) ? ' active' : '' }}" href="/admin/registration/{{ $page->id }}/report">Registration Report</a>
	    </li>
	    @endif
	</ul>
</div>
@yield('inner-page-content')

@endsection

@section('scripts')
<script>
	$('#copyLink').click(function() {
		$('#link').focus().select();
		document.execCommand('copy');
		$(this).html('Copied!');
	});
</script>
@endsection
