@once
@push('stylestack')
   <style>

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
    @media print{
        body {
           font-size:10pt;
           background-color: red;
        }
    }
   </style>
@endpush
@endonce

@if(isset($showLink))

<a class="card card-body" href="{{ isset($showLink) ? $showLink : '' }}" style="text-decoration: none; margin-bottom: 100px">
@else
<div class="card card-body" style="margin-bottom: 50px">
@endif
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
        <span class="font-weight-bold pb-3" style="padding-bottom: 1rem !important;font-weight: 700 !important;">Receipt <span class="text-muted"  style="color: #6c757d !important;">#{{ sprintf("%04d", $receipt->id) }} </span></span>
        <div class="pb-3"  style="float: right">

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
          <span class="font-weight-bold pt-1"><span style="font-weight: 700 !important;">Event Name </span><span class="text-muted" style="color: #6c757d !important;">: {{ $page->event_name }} </span></span><br>
          @endif
          <span class="font-weight-bold"><span style="font-weight: 700 !important;">Name of the guest </span><span class="text-muted"  style="color: #6c757d !important;">: {{ $receipt->user->name }} </span></span>
          <br>
          <span class="text-muted pb-3" style="font-size: 13px;color: #6c757d !important;">Purchased on {{ $receipt->created_at->tz(request()->user()->timezone)->format('m/d/Y h:i a') }} </span>
        </div>
        @if(!empty($page->event_date))
        <div class="col-md-6 text-right" style="text-align: right; position: relative;top: -50px">
          <span class="font-weight-bold pt-1" style="text-align: right; position: relative;left: 15px"><span style="font-weight: 700 !important;"> Date of Event </span> <span class="text-muted" style="color: #6c757d !important;">
          <br> {{ $page->event_date->format('d-M-Y') }} </span></span>
        </div>
        @endif

      </div>


    </div>
    @if($isSimple && !(isset($showLinks) && $showLinks))
    <span>more details</span>
    @endif
  </div>
  <table class="table table-bordered table table-responsive" style="{{ isset($showLink) ? '' : 'width: 100%' }};border: 1px solid #dee2e6;border-collapse: collapse;">
    <thead style="background: #ededed">
      <tr>
        <th style="padding-top:10px;padding-bottom: 10px;">Type</th>
        <th style="max-width: 300px;padding-top:10px;padding-bottom: 10px;">Item</th>
        <!-- <th style="width: 300px;">coupon used</th> -->
        <th style="max-width: 300px;padding-top:10px;padding-bottom: 10px;">Description</th>
        <th class="text-right" style="padding-top:10px;padding-bottom: 10px;padding-right: 5px">Amount</th>
      </tr>
    </thead>
    <tbody style="border: 1px solid #dee2e6;" style="width: 100%">
      @if(array_key_exists('ticket', $receipt->details))
      <tr class="{{ isset($showLink) ? '' : 'spaceUnder' }}">
        <td  style="border: 1px solid #dee2e6;border: 1px solid #dee2e6;padding: 10px;padding: 10px">Ticket</td>
        <td style="max-width: 200px;overflow-wrap: break-word;inline-size: 150px;border: 1px solid #dee2e6;border: 1px solid #dee2e6;padding: 10px;" class="text-primary-900">
         {{ $receipt->details['ticket']['name'] }}
         </td>

        @if($receipt->details['ticket']['description'] != '')
        <td style="max-width: 300px;border: 1px solid #dee2e6;border: 1px solid #dee2e6;padding: 10px;" class="text-primary-900">
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
        <td style="border: 1px solid #dee2e6;border: 1px solid #dee2e6;padding: 10px;"></td>
        @endif
        <td style="border: 1px solid #dee2e6;border: 1px solid #dee2e6;padding: 10px;" class="text-right">${{ $receipt->details['ticket']['price'] }}</td>
      </tr>
      @endif

      @if(isset($isSimple) && !$isSimple)
        @if(array_key_exists('addons', $receipt->details))
          @foreach($receipt->details['addons'] as $addon)
          <tr>
            <td style="border: 1px solid #dee2e6;padding: 10px;">Add-on</td>
            <td style="border: 1px solid #dee2e6;padding: 10px;word-wrap: break-word;display: inline-block;inline-size: 80%;max-width: 350px;" class="text-center">{{ $addon['name'] }}</td>
            <td style="border-bottom: 1px solid #dee2e6;padding: 10px;max-width: 350px;" class="text-center">{{ $addon['description'] }}</td>
            <td style="border: 1px solid #dee2e6;border: 1px solid #dee2e6;padding: 10px;" class="text-right">${{ $addon['price'] / 100 }}</td>
          </tr>
          @endforeach
        @endif
        @if(array_key_exists('coupon', $receipt->details))
          <tr>
            <td style="border: 1px solid #dee2e6;border: 1px solid #dee2e6;padding: 10px;"></td>
            <td style="border: 1px solid #dee2e6;border: 1px solid #dee2e6;padding: 10px;"></td>
            <td style="border: 1px solid #dee2e6;border: 1px solid #dee2e6;padding: 10px;" class="text-right">Coupon Code: <i>{{ $receipt->details['coupon']['code'] }}</i></td>
            <td style="border: 1px solid #dee2e6;border: 1px solid #dee2e6;padding: 10px;" class="text-right">{{ $receipt->details['coupon']['label'] }}</td>
          </tr>
        @endif
      @endif

      <tr>
        <td style="border: 1px solid #dee2e6;border: 1px solid #dee2e6;padding: 10px;"></td>
        <td style="border: 1px solid #dee2e6;border: 1px solid #dee2e6;padding: 10px;"></td>
        <td style="border: 1px solid #dee2e6;border: 1px solid #dee2e6;padding: 10px;" class="text-right"><strong>Total Paid</strong>:</td>
        <td style="border: 1px solid #dee2e6;border: 1px solid #dee2e6;padding: 10px;" class="text-right"><b>${{ $receipt->amount_paid }}</b></td>
      </tr>
    </tbody>
  </table>



  <div class="row" style="padding-top: 50px">
    <div class="col-md-12 text-center" style="text-align: center">
      <div style="padding-bottom: 20px">
        <strong>THANK YOU FOR YOUR PARTICIPATION IN THE {{ config('app.name')}}.</strong>
      </div>
      <div>
        <strong>Questions or inquiry regarding this invoice,Please contact <a href="mailto:{{ config('app.email')}}" class="text-primary">{{ config('app.email')}}. </a></strong>
      </div>
    </div>
  </div>




@if(isset($showLink))
</a>
@else
</div>
@endif


@if(isset($hr) && $hr)
<hr>
@endif
