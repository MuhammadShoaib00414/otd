@extends('admin.layout')

@section('page-content')
    @component('admin.partials.breadcrumbs', ['links' => [
        'Users' => '/admin/users',
        'Invitations' => '/admin/users/invites',
        'Bulk Resend Tool' => '',
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

    @if($invitations->count())

        <h5>Bulk Resend Invitation Emails To:</h5>

        <div class="mb-3 d-flex justify-content-start align-items-center">
            <span>Show only those last sent after date:</span>
            <form method="get" action="/admin/users/invites/bulk-resend" class="d-inline-block ml-2" style="max-width: 200px;">
                <input type="text" class="form-control form-control-sm" name="after" value="{{ (request()->has('after')) ? request()->after : '1/1/19' }}">
            </form>
        </div>

        <form method="post" action="/admin/users/invites/bulk-resend">
            @csrf
            <table class="table">
                <thead>
                    <tr>
                        <th style="width: 2em; text-align: right;">
                            <input type="checkbox" id="checkAll">
                        </th>
                        <th>
                            @if(request()->has('sort'))
                                <a href="/admin/users/invites/bulk-resend{{ (request()->has('show')) ? '?show=' . request()->show : '' }}">email</a>
                            @else
                                <a href="/admin/users/invites/bulk-resend?sort=email{{ (request()->has('show')) ? '&show=' . request()->show : '' }}">email</a>
                            @endif
                        </th>
                        <th>invited</th>
                        <th>last sent</th>
                    </tr>
                </thead>
                @foreach ($invitations as $invitation)
                <tr>
                    <td style="width: 2em; text-align: right;"><input type="checkbox" name="invitations[]" value="{{ $invitation->id }}" id="invitation{{ $invitation->id }}"></td>
                    <td><label for="invitation{{ $invitation->id }}" class="mb-0">{{ $invitation->email }}</label></td>
                    <td>{{ $invitation->sent_at->format('n/d/y') }}</td>
                    <td>{{ optional($invitation->last_sent_at)->format('n/d/y') }}</td>
                </tr>
                @endforeach
            </table>
            <div class="mb-5">
                <button type="submit" class="btn btn-lg btn-primary">Resend</button>
            </div>
        </form>
    @endif
@endsection

@section('scripts')
<script>
    $('#checkAll').on('click', function (event) {
        $('input[type="checkbox"]').prop('checked', event.currentTarget.checked);
    });
</script>
@endsection