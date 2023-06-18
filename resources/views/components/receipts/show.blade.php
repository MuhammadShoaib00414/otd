@once
@push('stylestack')
	 <style>
	 	td{
	 		max-width: 400px;
	 	}

	 	tr.spaceUnder > td {
	 		padding-bottom: 1em;
	 	}
	 	a.card {
	 		transition: none;
	 	}
	 	a.card:hover {
	 		transform: none;
	 		box-shadow: none;
	 	}
	 	* {
	 		color: {{ getThemeColors()->primary['900'] }};
	 	}
	 	@if(!$isSimple)
	 		a.card {
	 			cursor: initial;
	 		}
	 	@endif

	 </style>
@endpush
@endonce

@if(isset($showLink))
<a class="card card-body" href="{{ isset($showLink) ? $showLink : '' }}" style="text-decoration: none; margin-bottom: 100px">
@else
<div class="card card-body" style="margin-bottom: 50px">
@endif
<?php  $i = 1;?>
@if ($i = 1)
   
	<div class="d-flex justify-content-between">
		<div class="d-flex flex-column w-100">
			@if(!empty($page->image_url))
			<div class="row d-flex justify-content-between align-items-center">
			    <div class="col-md-12 text-center" style="text-align: center;padding-bottom: 50px;">
		            <img src="{{$page->image_url}}" class="img-fluid" alt="Responsive image" style="width: 70%;">
         		</div>
			</div>
			@endif
	
			<div class="d-flex justify-content-between align-items-center">
				<span class="font-weight-bold pb-3">Receipt <span class="text-muted">#{{ sprintf("%04d", $receipt->id) }} </span></span>
				<div class="pb-3">

				@if($receipt->status == "Approved")
					<span class="badge badge-success">Approved</span>
				@elseif($receipt->status == "Refunded")
					<span class="badge badge-warning">Refunded</span>
				@elseif($receipt->status == "Cancelled")
					<span class="badge badge-danger">Cancelled</span>
				@endif
				@if($authUser->is_admin && (!isset($is_editable) || $is_editable) && request()->is('*admin*'))
					<i class="fas fa-lg fa-edit" data-toggle="modal" data-target="#changeStatusModal" style="color: #404b53; cursor: pointer"></i>
				@endif
				</div>
			</div>

			<div class="row pb-3">
				<div class="col-md-6">
					@if(!empty($page->event_name))
					<span class="font-weight-bold pt-1">Event Name<span class="text-muted">: {{ $page->event_name }} </span></span><br>
					@endif
					<span class="font-weight-bold">Name of the guest<span class="text-muted">: {{ $receipt->user->name }} </span></span>
					<br>
					<span class="text-muted pb-3">Purchased on {{ $receipt->created_at->tz(request()->user()->timezone)->format('m/d/Y h:i a') }} </span>
				</div>
				<div class="col-md-6 text-right">
					@if(isset($showLinks) && $showLinks)
					<form action="/purchases/{{ $receipt->id }}/export" method="GET">
						@csrf
						<button class="btn btn-sm btn-primary">Download invoice</button>
					</form>
				@endif
				
				@if(!empty($page->event_date))
					<span class="font-weight-bold pt-1">Date of Event: <span class="text-muted">
					 {{ ($page->event_date) ? $page->event_date->format('d-M-Y') : '-' }} </span></span>
				@endif

				</div>

			</div>


		</div>
		@if($isSimple && !(isset($showLinks) && $showLinks))
		<span>more details</span>
		@endif
	</div>
	<table class="table table-bordered" style="{{ isset($showLink) ? 'width: 100%' : 'width: 100%' }}" >
		<thead style="background: #ededed">
			 <tr>
		        <th style="padding-top:10px;padding-bottom: 10px;">Type</th>
		        <th style="max-width: 200px;padding-top:10px;padding-bottom: 10px;">Item</th>
		        <!-- <th style="width: 300px;">coupon used</th> -->
		        <th style="max-width: 200px;padding-top:10px;padding-bottom: 10px;">Description</th>
		        <th class="text-right" style="padding-top:10px;padding-bottom: 10px;padding-right: 5px">Amount</th>
		      </tr>
		</thead>
		<tbody>
			@if(array_key_exists('ticket', $receipt->details))
			<tr class="{{ isset($showLink) ? '' : 'spaceUnder' }}" style="border-top: 1px solid #80808047;border-bottom: 1px solid #80808047;max-width: 350px;">
				<td >Ticket</td>
				<td class="text-center;text-primary-900" style="padding: 10px;word-wrap: break-word;display: inline-block;border: 0px;inline-size: 80%;width: 250px;">{{ $receipt->details['ticket']['name'] }}</td>

				@if($receipt->details['ticket']['description'] != '')
				<td class="text-primary-900" style="width: 250px;">
					{{ $receipt->details['ticket']['description'] }}
					@if(array_key_exists('add_to_groups', $receipt->details['ticket']) && $receipt->details['ticket']['add_to_groups'] && (isset($showGroups) && $showGroups))
					<br>
					<i>Added to groups:</i>
						@foreach($receipt->details['ticket']['add_to_groups'] as $groupId)
						<br>
							@if(isset($showLinks) && $showLinks)
								<a href="/groups/{{ \App\Group::withTrashed()->find($groupId)->slug }}">{{ \App\Group::withTrashed()->find($groupId)->name }}</a>
							@else
								<p>{{ \App\Group::withTrashed()->find($groupId)->name }}</p>
							@endif
						@endforeach
					@endif
				</td>
				@else
				<td></td>
				@endif
				<td class="text-right">${{ $receipt->details['ticket']['price'] }}</td>
			</tr>
			@endif

			@if(isset($isSimple) && !$isSimple)
				@if(array_key_exists('addons', $receipt->details))
					@foreach($receipt->details['addons'] as $addon)
					<tr  style="border-top: 1px solid #80808047;border-bottom: 1px solid #80808047; width: 350px;">
						<td>Add-on</td>
						
                         @if(isset($addon['name']))
                         <td class="text-center" style="inline-size: 80%;word-wrap: break-word;display: inline-block;border: 0px;width: 250px;">{{ $addon['name'] }}</td>
                         @else
                              <td></td>
                         @endif
                          @if(isset($addon['description']))
                        <td class="text-center" >{{ $addon['description'] }}</td>
                         @else
                              <td></td>
                         @endif
						<td class="text-right">${{ $addon['price'] / 100 }}</td>
					</tr>
					@endforeach
				@endif
				@if(array_key_exists('coupon', $receipt->details))
					<tr>
						<td></td>
						<td></td>
						<td class="text-right">Coupon Code: <i>{{ $receipt->details['coupon']['code'] }}</i></td>
						<td class="text-right">{{ $receipt->details['coupon']['label'] }}</td>
					</tr>
				@endif
			@endif

			<tr>
				<td></td>
				<td></td>
				<td class="text-right"><strong>Total Paid</strong>:</td>
				<td class="text-right"><b>${{ $receipt->amount_paid }}</b></td>
			</tr>
		</tbody>
	</table>


    <br><br><br><br>
	<div class="row">
		<div class="col-md-12 text-center">
			<div style="text-align: center;">
				<strong>THANK YOU FOR YOUR PARTICIPATION IN {{ config('app.name') }} </strong>
			</div>
			<div style="text-align: center;">
				<strong>Questions or inquiry regarding this invoice,Please contact <a href="mailto:{{ config('app.email')}}" class="text-primary">{{ config('app.email')}}</a></strong>
			</div>
		</div>
	</div>

 <p style="page-break-after: always;"></p>
@endif

@if(isset($showLink))
</a>
@else
</div>
@endif

@if($authUser->is_admin && (!isset($is_editable) || $is_editable))
<div class="modal fade" id="changeStatusModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	  <div class="modal-dialog modal-dialog-centered" role="document">
	    <div class="modal-content">
	    <form method="post" action="/admin/purchases/{{ $receipt->id }}/status">
		@method('put')
		@csrf
	      <div class="modal-header">
	        <h5 class="modal-title" id="exampleModalLabel">Change Receipt Status</h5>
	        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
	          <span aria-hidden="true">&times;</span>
	        </button>
	      </div>
	      <div class="modal-body">
	      	<p class="font-weight-bold">Change receipt status to...</p>
	      	@if($receipt->status == "Approved")
	      	<div class="form-check mb-3">
			  <input class="form-check-input" type="radio" value="Refunded" id="statusRefund" name="status">
			  <label class="form-check-label" for="statusRefund">
			    Refunded
			  </label>
			</div>
			<div class="form-check mb-3">
			  <input class="form-check-input" type="radio" value="Cancelled" id="statusCancel" name="status">
			  <label class="form-check-label" for="statusCancel">
			    Cancelled
			  </label>
			</div>
	      	@elseif($receipt->status == "Refunded")
	      	<div class="form-check mb-3">
			  <input class="form-check-input" type="radio" value="Approved" id="statusApprove" name="status">
			  <label class="form-check-label" for="statusApprove">
			    Approved
			  </label>
			</div>
			<div class="form-check mb-3">
			  <input class="form-check-input" type="radio" value="Cancelled" id="statusCancel2" name="status">
			  <label class="form-check-label" for="statusCancel2">
			    Cancelled
			  </label>
			</div>
	      	@elseif($receipt->status == "Cancelled")
	      	<div class="form-check mb-3">
			  <input class="form-check-input" type="radio" value="Approved" id="statusApprove2" name="status">
			  <label class="form-check-label" for="statusApprove2">
			    Approved
			  </label>
			</div>
			<div class="form-check mb-3">
			  <input class="form-check-input" type="radio" value="Refunded" id="statusRefund2" name="status">
			  <label class="form-check-label" for="statusRefund2">
			    Refunded
			  </label>
			</div>
	      	@endif
	      	<hr>
	      	<div class="form-check mb-3">
			  <input class="form-check-input" type="checkbox" value="1" id="remove_access" name="remove_access">
			  <label class="form-check-label" for="remove_access">
			    Remove user from groups granted from this ticket
			  </label>
			</div>
	      </div>
	      <div class="modal-footer">
	        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
	        <button type="submit" class="btn btn-primary">Save changes</button>
	      </div>
	      </form>
	    </div>
	</div>
</div>
@if(isset($hr) && $hr)
<hr>
@endif
@endif

