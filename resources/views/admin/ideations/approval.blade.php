@extends('admin.ideations.layout')

@section('inner-page-content')
    <table class="table mt-2">
        <thead>
            <tr>
                <th scope="col"><b>Name</b></th>
                <th scope="col"><b>Owner</b></th>
                <th class="text-center" scope="col"><b>Approve</b></th>
                <th colspan="2"></th>
            </tr>
        </thead>
        @foreach($ideations as $ideation)
        <tr>
            <td><a href="/admin/ideations/{{ $ideation->id }}">{{ $ideation->name }}</a></td>
            <td><a href="/admin/users/{{ $ideation->owner()->pluck('id')->first() }}">{{ $ideation->owner()->pluck('name')->first() }}</a></td>
            <td class="d-flex justify-content-around">
            	<form action="/admin/ideations/approve" method="post">
                    @csrf
                    @method('put')
                    <input type="hidden" name="ideation" value="{{ $ideation->id }}">
                    <button type="submit" class="btn btn-sm btn-primary">Approve</button>
                </form>
                <button id="denyButton" data-target="#exampleModal" data-toggle="modal" type="button" class="btn btn-sm btn-outline-primary">Deny</button>
                    <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <form action="/admin/ideations/reject" method="post">
                    @csrf
                    @method('put')
                      <div class="modal-dialog" role="document">
                        <div class="modal-content">
                          <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">{{ $ideation->name }}</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                              <span aria-hidden="true">&times;</span>
                            </button>
                          </div>
                          <div class="modal-body">
                            <input type="hidden" name="ideation" value="{{ $ideation->id }}">
                            <div class="form-group">
                                <label for="message">Reason: <small class="text-muted">(optional)</small></label>
                                <input type="text" class="form-control" name="message" id="message">
                            </div>
                          </div>
                          <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Deny</button>
                          </div>
                        </div>
                      </div>
                    </form>
                    </div>
            </td>
            <td class="text-right"><a href="/admin/ideations/{{ $ideation->id }}">Details</a></td>
        </tr>
        @endforeach
    </table>
@endsection