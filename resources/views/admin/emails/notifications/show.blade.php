@extends('admin.layout')

@section('page-content')
    @component('admin.partials.breadcrumbs', ['links' => [
        'Email Notifications' => '/admin/emails/notifications',
        $email->name => '/admin/emails/notifications/'.$email->id,
    ]])
    @endcomponent

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <h5>{{ $email->name }}</h5>
            <form method="get">
                <select class="custom-select" name="locale" onchange="this.form.submit()">
                    <option {{ !isset(request()->locale) ? 'selected' : '' }} value="en">English</option>
                    <option {{ isset(request()->locale) && request()->locale == 'es' ? 'selected' : '' }} value="es">Spanish</option>
                </select>
            </form>
            <a href="/admin/emails/notifications/{{ $email->id }}/edit{{ request()->has('locale') ? '?locale='. request()->locale : '' }}" class="btn btn-sm btn-outline-primary">Edit</a>
        </div>
        <div class="card">
            <div class="card-body">
                <p><b>Subject:</b> {{ $email->subjectWithLocale(request()->locale) }}</p>
                <iframe src="/admin/emails/notifications/{{ $email->id }}/html{{ request()->has('locale') ? '?locale='. request()->locale : '' }}" style="width: 100%; height: 500px;"></iframe>
            </div>
        </div>
    </div>
</div>
@endsection