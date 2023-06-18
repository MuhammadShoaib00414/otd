@extends('admin.layout')

@section('page-content')
    <div class="d-flex justify-content-between">
        <div class="d-flex align-items-center justify-content-start">
            <h5 class="mr-4 mb-0">Tickets</h5>
        </div>
        <div class="text-right">
            <a class="btn btn-primary btn-sm" href="/admin/tickets/create">
            	<i class="fa fa-plus mr-1"></i>
              New Ticket
            </a>
        </div>
    </div>
    
    <table class="table mt-2">
        <thead>
            <tr>
                <th scope="col"><b>Name</b></th>
                <th></th>
            </tr>
        </thead>
        @foreach($tickets as $ticket)
            <tr>
                <td>{{ $ticket->name }}</td>
                <td class="text-right"><a href="/admin/tickets/{{ $ticket->id }}">View</a></td>
            </tr>
        @endforeach
    </table>
@endsection