@extends('admin.layout')

@push('stylestack')
<link rel="stylesheet" href="/redactorx-1-2-0/redactorx.min.css" />
@endpush

@section('page-content')
@component('admin.partials.breadcrumbs', ['links' => [

'Update Terms and Conditions' => '',
]])
@endcomponent

<div class="row">
    <div class="col-lg-12">
        <div class="margin-top-40"></div>
    </div>
    <div class="col-lg-12 mt-5">
        <div class="card">
            <div class="card-body">
                @if ($errors->any())
                <div class="alert alert-danger mb-3">
                    <ul>
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif
                <form action="/admin/terms-and-conditions/{{$is_terms_and_conditions->id}}/update" id="form" method="post" enctype="multipart/form-data">
                    <div class="header-wrap d-flex justify-content-between">
                        <h4 class="header-title">Update Terms and Conditions</h4>

                    </div>
                    {{ csrf_field() }}
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="form-group classic-editor-wrapper">
                                <label>Content</label>
                                <textarea id="content" rows="8" class="form-control mb-3 content" height="500" name="content" required>{!! $is_terms_and_conditions->value !!}</textarea>
                            </div>
                        </div>
                        <input type="hidden" name="id" value="{{$is_terms_and_conditions->id}}">
                        <div class="col-md-12 text-right">
                            <input type="submit" id="saveButton" class="btn btn btn-primary" name="published" value="@lang('general.save')">
                        </div>
                    </div>

                </form>
            </div>
        </div>

    </div>

</div>

</div>
@endsection
<script>
    Swal.fire({
        title: 'Success!',
        text: '<?php echo Session::get('success'); ?>',
        type: 'success',
        confirmButtonText: 'Close'
    })
</script>
@include('admin.pages.pages-footer')