@extends('admin.layout')

@push('stylestack')
    <link rel="stylesheet" href="/revolvapp-1_0_7/css/revolvapp.min.css" />
@endpush

@section('page-content')
    @component('admin.partials.breadcrumbs', ['links' => [
        'Pages' => '/admin/pages',
        'Preview' => '',
    ]])
    @endcomponent

<div>
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h5 class="mr-3 mb-0">{{ $page->title }}</h5>
        </div>
        <div>
            <a href="/admin/pages/{{ $page->id }}/edit" class="btn btn-light mr-2">Edit</a>
            
            <a href data-toggle="modal" class="cursor-pointer btn btn-light mr-2" data-target="#exampleModalCenter">Delete</a>
        </div>
    </div>  
    <hr>
    <div class="container">
        <div class="col-md-12 mb-5">
            <div>
               
                <iframe src="/admin/pages/{{ $page->id }}/{{ $page->slug }}" frameBorder="0" style="width: 100%; height: 700px;"></iframe>
            </div>
        </div>
    </div>
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
                <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary "> <a href="/admin/pages/{{ @$page->id }}/destroy" class="text-white cursor-pointer"> Yes, delete it!</a></button>
            </div>
        </div>
    </div>
</div>
</div>
@endsection