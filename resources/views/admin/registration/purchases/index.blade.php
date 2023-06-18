@extends('admin.registration.layout')

@section('inner-page-content')
<div class="container">

	<div class="text-right">
		<a href="/admin/registration/{{ $page->id }}/purchases/export" class="btn btn-primary btn-sm mb-2" id="download_receipts">Download all receipts</a>
	</div>
	<table class="table">
		<thead style="background-color: #ededed">
			<tr>
                <th>#</th>
				<th>Receipt id</th>
				<th>Date</th>
				<th>User</th>
				<th>Type</th>
				<th>Amount</th>
				<th class="text-right"></th>
			</tr>
		</thead>
		<tbody>
			@foreach($receipts as $receipt)

				@php
					$type = [];
					if(isset($receipt->details['ticket'])){
                        $type[] = 'Ticket';
                    }
                    if(isset($receipt->details['addons'])){
                        $type[] = 'Add-ons';
					}
                    if(isset($receipt->details['coupon'])){
                        $type[] = 'Coupon Code';
					}
					$type = implode(', ', $type);
				@endphp
				<tr>
					<td><input type="checkbox" name="filterCheckbox[]" value="{{ $receipt->id }}"></td>
					<td><a href="/admin/registration/{{ $page->id }}/purchases/{{ $receipt->id }}">{{ $receipt->id }}</a></td>
					<td>{{ $receipt->created_at->format('m/d/y') }}</td>
					<td><a href="/admin/users/{{ $receipt->user->id }}" target="_blank">{{ $receipt->user->name }}</a></td>
                    <td>
						{{$type}}
                    </td>
					<td>${{ $receipt->amount_paid }}</td>
					<td class="text-right"><a href="/admin/registration/{{ $page->id }}/purchases/{{ $receipt->id }}">view</a></td>
				</tr>
			@endforeach
		</tbody>
	</table>

</div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            $(document).on('click', '#download_receipts', function(e) {
                e.preventDefault();
                var href = $(this).attr('href');
                var checkbox = document.getElementsByName('filterCheckbox[]');
                var ids = [];
                var param = 'purchases/export?ids=';
                checkbox.forEach(item => {
                    if(item.checked)
                       param += item.value+',';
                });
                location.href = param.slice(0,-1);
            })
        });
    </script>

@endsection
