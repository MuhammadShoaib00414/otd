@extends('admin.users.layout')

@section('inner-page-content')
<div class="container">
    <a class="mb-2" href="/admin/users/{{ $user->id }}/purchases"> < Purchases</a>
    <h5>{{ $user->name }}'s Purchases</h5>
    @include('components.receipts.index', ['receipts' => $user->receipts()->orderBy('created_at', 'desc')->get(), 'isSimple' => false, 'showGroups' => true])
</div>
@endsection