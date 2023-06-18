@extends('admin.registration.layout')

@section('inner-page-content')
<div class="container" style="overflow: hidden;">
	<div class="text-right">
        <a href="/admin/report/{{ $page->id }}/export" class="btn btn-outline-dark btn-sm mr-2"><i class="fas fa-download"></i> Export Report</a>
    </div>
    <br>

	<table class="table table-bordered table-responsive">
		<thead style="background-color: #ededed">
			<tr>
				<th rowspan="2">Name</th>
				<th rowspan="2">Email</th>
				<th rowspan="2">Ticket Type</th>
                <th rowspan="2">Coupon Code</th>
				<th rowspan="2">Coupon Type</th>
				<th rowspan="2">Coupon Amount</th>
                <th colspan="2" class="text-center">Add-ons</th>
				<th rowspan="2">Paid Amount</th>
				<th rowspan="2">Date</th>
			</tr>
            <tr>
                <th>Name</th>
                <th>Price</th>
            </tr>
		</thead>
		<tbody>
			@foreach($receipts as $key => $receipt)
            @php
            $couponExists = isset($receipt->details['coupon']);
            $addonExists = isset($receipt->details['addons']);
            $addonticket = isset($receipt->details['ticket']);

            @endphp


                @if(isset($receipt->details['coupon']))
                    @php
                        $couponType = strpos($receipt->details['coupon']['label'], '%') !== false ? 'Percentage' : (strpos($receipt->details['coupon']['label'], '$') !== false ? 'Fixed' : '---');
                    @endphp
                @else
                    @php
                        $couponType = '---';
                    @endphp
                @endif
				<tr>
                    <td><a href="/admin/users/{{ $receipt->user->id }}" target="_blank">{{ $receipt->user->name }}</a></td>
                    <td>{{ $receipt->user->email }}</td>
                    <td>
                        {{ ($receipt->details['ticket']['name']) ?? '-'}}

                     </td>
                    <td>
                       @if(isset($receipt->details['coupon']['code']))
                       {{$receipt->details['coupon']['code']}}
                       @else
                       ---
                       @endif
                    </td>
                    <td>
                       {{$couponType}}
                    </td>
                    <td>
                       @if(isset($receipt->details['coupon']['label'] ))
                       {{ trim($receipt->details['coupon']['label']," - ") }}
                       @else
                       ---
                       @endif
                    </td>
                    <td colspan="2" class="p-0">
                       <table style="width:100%; border-" class="table mb-0 table-sm text-center">
                          @if(array_key_exists('addons', $receipt->details))
                          @forelse($receipt->details['addons'] as $addon)
                          <tr>
                             <td>{{$addon['name']}}</td>
                             <td>{{$addon['price']}}</td>
                          </tr>
                          @empty
                          @endforelse
                          @else
                          @endif
                       </table>
                    </td>
                    <td> {{ number_format($receipt->amount_paid,2)}}</td>
                    <td>{{ $receipt->created_at->format('m/d/y') }}</td>
                 </tr>

			@endforeach
		</tbody>
	</table>



</div>
@endsection
