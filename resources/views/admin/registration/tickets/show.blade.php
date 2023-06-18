@extends('admin.registration.layout')

@section('inner-page-content')
<div class="container col-6 mx-auto">
    <div class="d-flex justify-content-between mb-2">  
    <div class="card card-body">
        <div class="d-flex flex-column"> 
        <h5>Ticket: {{ $ticket->name }}</h5>
        </div>
        
    </div>
     
    </div>
    <div class="card card-body">
        <div class="d-flex flex-column">
            <div><b>Price:</b> {{ $ticket->display_price }}</div>
            @if($ticket->description)
            <div><b>Description</b><br>{{ $ticket->description }}</div>
            @endif
            @if($ticket->add_to_groups)
            <div><b>Add to groups:</b>
                <ul>
                @foreach($ticket->groups as $group)
                    <li>{{ $group->name }}</li>
                @endforeach
                </ul>
            </div>
            @endif
        </b>
    </div>
    @if($page->deleted_at == null)<a href="/admin/registration/{{ $page->id }}/tickets/{{ $ticket->id }}/edit" class="btn btn-primary btn-sm">Edit Ticket</a>@endif
</div>
@endsection
