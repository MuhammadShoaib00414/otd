@extends('layouts.app')

@push('stylestack')
<style>
    #backlink {
            z-index: 100000;
        }

    .form-check-label {
        font-size: 1em !important;
    }

    .registrationpage {
      position: relative;
      transition: all 0.3s ease-in-out;
      cursor: pointer;
      text-decoration: none;
      color: black;
      font-size: 1.2em;
      padding: 15px;
    }

    .registrationpage::after {
      content: '';
      position: absolute;
      z-index: -2;
      width: 100%;
      height: 100%;
      opacity: 0;
      border-radius: 5px;
      box-shadow: 0 5px 15px rgba(0,0,0,0.3);
      transition: opacity 0.3s ease-in-out;
      top: 0;
      left: 0;
    }

    .registrationpage:hover {
      color: black;
      text-decoration: none;
      transform: scale(1.02, 1.02);
    }

    .registrationpage:hover::after {
      opacity: 1;
    }

    .ticket-label {
        cursor:  pointer;
    }

    .ticketInput:checked + label {
        border: 1px solid {{ getThemeColors()->primary['500'] }};
        color: black;
        text-decoration: none;
        transform: scale(1.02, 1.02);
    }

    .ticketInput:checked + label::after {
      opacity: 1;
    }
</style>
@endpush

@section('content')
<div class="col-lg-6 mx-auto py-2">
	<a href="{{ route('spa') }}" style="font-size: 0.9em" onclick="return confirm('Are you sure you want to leave? Any changes will be lost.');"> < Dashboard</a>
	<div class="d-flex justify-content-between mt-3 mb-2">
		<h4>Register to view {{ $group->name }}</h4>
	</div>
	<div class="card card-body">

		@if($page->tickets()->count())
		<div id="ticketForm" class="step">
            <span class="text-primary-600">{{ $page->ticket_prompt }}</span>

            @foreach($page->tickets as $ticket)
                <div class="form-check pl-0">
                  <input class="form-check-input d-none ticketInput" type="radio" name="ticket" id="ticket{{ $ticket->id }}" value="{{ $ticket->id }}">
                  <label for="ticket{{ $ticket->id }}" class="ticket-label text-primary-600 registrationpage ticketlabel d-flex flex-row justify-content-between card card-body nowrap">
                    {{ $ticket->name }} <b class="text-primary-600">${{ $ticket->price }}</b>
                  </label>
                </div>
            @endforeach
            <button type="button" class="btn btn-primary" id="nextButton2">Next</button>
        </div>
        @endif

        @if($page->addons)
        <div id="addonForm" class="step d-none">
            <span class="text-primary-600">{{ $page->addon_prompt }}</span>

            @foreach($page->addons as $addon)
            <div class="form-check mt-3 pl-0">
              <input class="form-check-input d-none ticketInput" type="checkbox" value="{{ $addon['id'] }}" name="addons[]" id="addon{{ $addon['id'] }}">
              <label class="ticket-label text-primary-600 registrationpage ticketlabel d-flex flex-row justify-content-between card card-body nowrap" for="addon{{ $addon['id'] }}">
                {{ $addon['name'] }} <b class="text-primary-600">${{ $addon['price'] }}</b>
              </label>
            </div>
            @endforeach
            <button class="btn btn-primary" type="button" id="nextButton3">Proceed to payment</button>
        </div>
        @endif

        <div id="paymentForm" class="d-none">
            stripe goes here (WIP)
        </div>
	</div>
</div>
@endsection

@section('scripts')
<script>
	$('#nextButton').click(function(e) {
        e.preventDefault();
        $('#signup').addClass('d-none');
        $('#ticketForm').removeClass('d-none');
    });

    $('#nextButton2').click(function(e) {
        e.preventDefault();
        if(!$('.ticketInput:checked').length)
            return false;
        $('#ticketForm').addClass('d-none');
        $('#addonForm').removeClass('d-none');
    });

    $('#nextButton3').click(function(e) {
        e.preventDefault();
        $('#addonForm').addClass('d-none');
        $('#paymentForm').removeClass('d-none');

        $.ajax({
            url: "/users/getPrice",
            type: "get", 
            data: {
              _token: '{{ csrf_token() }}',
              addons: $('.ticketInput:checked').serialize(),
              page: {{ $page->id }},
            }
          });
    });
</script>
@endsection