@extends('admin.layout')

@push('stylestack')
    <link rel="stylesheet" href="/revolvapp-1_0_7/css/revolvapp.min.css" />
@endpush

@section('page-content')
    @component('admin.partials.breadcrumbs', ['links' => [
        'Onboarding Emails' => '/admin/emails/welcome',
        $email->email_subject => '',
    ]])
    @endcomponent

<div>
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h5 class="mr-3 mb-0">{{ $email->email_subject }}</h5>
            @if($email->enabled)
                <span class="badge badge-primary">active</span>
            @else
                <span class="badge badge-secondary">not active</span>
            @endif
        </div>
        <div>
            <a href="/admin/emails/welcome/{{ $email->id }}/edit" class="btn btn-light mr-2">Edit</a>
        </div>
    </div>
    <hr>
    <div class="row">
        <div class="col-md-10 mb-5">
            <div>
                <p>
                    <b>Email Preview</b>
                </p>
                <iframe src="/admin/emails/welcome/{{ $email->id }}/html" style="width: 100%; height: 500px;"></iframe>
            </div>
        </div>
    </div>
</div>
@endsection