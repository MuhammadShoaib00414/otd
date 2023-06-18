@extends('admin.registration.layout')

@section('inner-page-content')
<div class="container">
    <div class="d-flex justify-content-between">  
        <h5>Tickets available for {{ $page->name }}</h5> 
      
        @if($page->deleted_at == null)<a href="/admin/registration/{{ $page->id }}/tickets/new" class="btn btn-primary btn-sm">New Ticket</a>@endif
    </div>
    @if($page->tickets()->exists())
	<table class="table mt-2">
        <thead>
            <tr>
                <th>name</th>
                <th>price</th>
                <th class="text-right"></th>
            </tr>
        </thead>
        <tbody>
            @foreach($page->tickets as $ticket)
             @if(empty($ticket->deleted_at))
            <tr>
                <td><a href="/admin/registration/{{ $page->id }}/tickets/{{ $ticket->id }}">{{ $ticket->name }}</a></td>
                <td>{{ $ticket->display_price }}</td>
                <td class="text-right">
                    <a href="/admin/registration/{{ $page->id }}/tickets/{{ $ticket->id }}">view</a>
                </td>
            </tr>
            @endif
            @endforeach
        </tbody>    
    </table>
    @else
        <p class="text-center">No Tickets</p>
    @endif
</div>
@endsection