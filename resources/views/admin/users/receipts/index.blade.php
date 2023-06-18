@extends('admin.users.layout')

@section('inner-page-content')
<div class="container">
    <h5>{{ $user->name }}'s Purchases</h5>
    @include('components.receipts.index', ['receipts' => $user->receipts()->orderBy('created_at', 'desc')->get(), 'isSimple' => true, 'showGroups' => false, 'showLink' => '/admin/users/' . $user->id . '/purchases/'])
</div>
@endsection