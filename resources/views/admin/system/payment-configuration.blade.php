@extends('admin.layout')

@push('stylestack')
    <link href="https://unpkg.com/gijgo@1.9.13/css/gijgo.min.css" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.9/dist/css/bootstrap-select.min.css">
    <style>
      .select-picker > .dropdown-toggle { border: 1px solid #ced4da; }
    </style>
@endpush

@section('page-content')

    @component('admin.partials.breadcrumbs', ['links' => [
        'Payment Configuration' => '',
    ]])
    @endcomponent

    <div class="mb-5">

        @foreach($errors->all() as $message)
            <div class="alert alert-dismissible alert-danger" style="max-width:450px;">
                <button type="button" class="close" data-dismiss="alert">×</button>
                <strong>{!! $message !!}</strong>
            </div>
        @endforeach

        <h5>Payment Configuration</h5>

        <hr>

        <form method="post" action="/admin/system/payment-configuration" id="form">
            @csrf

            <div class="form-group">
                <label for="stripe_key">Stripe Key <small class="text-muted">(Required for payments under registration pages)</small></label>
                <input type="text" class="form-control" name="stripe_key" id="stripe_key" value="{{ get_stripe_credentials()['key'] }}">
              </div>
              <div class="form-group">
                <label for="stripe_secret">Stripe Secret</label>
                <input type="password" class="form-control" name="stripe_secret" id="stripe_secret" value="{{ get_stripe_credentials()['secret'] }}">
              </div>

            <hr>

            <button type="submit" class="btn btn-info">Save changes</button>
        </form>
    </div>
@endsection


@push('scriptstack')
    @if(Session::has('success'))
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@8"></script>
        <script>
            Swal.fire({
              title: 'Success!',
              text: 'Changes saved.',
              type: 'success',
              confirmButtonText: 'Close'
            })
        </script>
    @endif
@endpush