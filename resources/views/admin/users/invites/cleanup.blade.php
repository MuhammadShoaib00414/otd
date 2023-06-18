@extends('admin.layout')

@section('page-content')

    @component('admin.partials.breadcrumbs', ['links' => [
        'Users' => '/admin/users',
        'Invitations' => '/admin/users/invites',
        'Cleanup Tool' => '',
    ]])
    @endcomponent

    @if(Session::has('message'))
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            {!! Session::get('message') !!}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <h5>Invites Cleanup Tool</h5>

    <form method="post" action="/admin/users/invites/cleanup">
        @csrf
        
        <div class="mb-5">
            <button type="submit" class="btn btn-primary">Remove duplicate invites</button>
        </div>
    </form>

    <hr>

     <h5>Bulk remove invites</h5>

     <p>This will remove all unaccepted invitations initially sent out between the inputted dates.</p> 

    <form method="post" action="/admin/users/invites/cleanup">
        @csrf

        <div class="form-row mb-3" style="max-width: 550px;">
            <div class="col-6">
                <label for="start_date">Start Date</label>
                <input type="text" class="form-control" name="start_date" id="start_date" placeholder="ex. 1/1/20" required>
            </div>
            <div class="col-6">
                <label for="end_date">End Date</label>
                <input type="text" class="form-control" name="end_date" id="end_date" placeholder="ex. 2/1/20" required>
            </div>
        </div>
        
        <div class="mb-5">
            <button type="submit" class="btn btn-primary">Remove matching invites</button>
        </div>
    </form>
@endsection