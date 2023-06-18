@extends('admin.layout')

@section('page-content')
@component('admin.partials.breadcrumbs', ['links' => [
'Pages' => '',
]])
@endcomponent

<div class="d-flex justify-content-between">
    <h5>Pages</h5>
    <div class="text-right">
        <a class="btn btn-primary btn-sm" href="/admin/pages/create">
            Add Pages
        </a>
    </div>
</div>

<table class="table mt-2">
    <thead>
        <tr>


            <th scope="col"><b>Title</b></th>
            <th scope="col"><b>Slug</b></th>
            <th scope="col"><b>Created date</b></th>
            <th scope="col"><b>Created by</b></th>
            <th scope="col" style="text-align: right;"><b>Action</b></th>

        </tr>
    </thead>
    @if(isset($pages))
    @foreach ($pages as $page)
  
    <tr>
        <td>{{ $page->title }}</td>
        <td>{{ $page->slug }}</td>
        <td>{{ $page->created_at }}</td>
        <td>{{$page->user->name}}</td>
        <td style="text-align: right;"><a href="/admin/pages/{{ $page->id }}/edit" class="cursor-pointer">Edit</a> | <a data-id='{{$page->id}}' onclick="attachedPageIdP(event,this)" href data-toggle="modal" class="cursor-pointer trash-data" data-target="#exampleModalCenter">Delete</a> | <a href="/admin/pages/{{$page->id}}" class="cursor-pointer">View</a> </td>

    </tr>
    @endforeach
    </tr>
    @endif
</table>
<div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-center" style="width: 100%;font-size: 42px;color: red;" id="exampleModalLongTitle"><i class="fa fa-exclamation-circle" class="text-center" aria-hidden="true"></i></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <h3 class="modal-title text-center" id="exampleModalLongTitle"> Are you sure?</h3>
                <p class="text-center">You won't be able to revert this change!</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger cursor-pointer" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary"> <a href="javascript:void(0);" class="text-white cursor-pointer" id="page-id"> Yes, delete it!</a></button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scriptstack')
@if(Session::has('success'))
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@8"></script>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js" type="text/javascript"></script>
<script>
    Swal.fire({
        title: 'Success!',
        text: '<?php echo Session::get('success'); ?>',
        type: 'success',
        confirmButtonText: 'Close'
    })
</script>
@endif
@php
Session::forget('success');
@endphp
<script>
    function attachedPageIdP(event,obj){
        var id =$(obj).attr("data-id");
        $('#page-id').attr('href','/admin/pages/'+id+'/destroy');
    }
   
</script>
@endpush