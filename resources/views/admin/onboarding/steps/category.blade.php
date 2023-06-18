@extends('admin.layout')

@section('page-content')

    @component('admin.partials.breadcrumbs', ['links' => [
        'Onboarding Configuration' => '/admin/system/onboarding',
        'Category: ' . $taxonomy->name => '',
    ]])
    @endcomponent

    <div class="card" style="max-width: 600px; margin: 3em auto 1em auto;">
        <div class="card-body pb-0">
            <p><b>{{ $taxonomy->name }}</b></p>
            <p>Users are prompted to choose their options from this category in the onboarding because it is <i>enabled</i> and <i>editable</i>, as determined by it's settings.</p>
            <p>To change this, edit the <a href="/admin/categories/{{ $taxonomy->id }}/edit" style="text-decoration: underline;">Category Settings</a>.</p>
            <hr>
            <p>To change the order categories show in onboarding and when a user is editing their profile, edit the <a href="/admin/categories/sort" style="text-decoration: underline;">Profile Categories Order</a>.</p>
        </div>
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