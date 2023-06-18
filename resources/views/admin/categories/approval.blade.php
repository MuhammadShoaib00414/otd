@extends('admin.categories.layout')

@section('inner-page-content')
    <div class="col-md-8">
        <div class="d-flex justify-content-between">
            <h2>
                Approval Queue
            </h2>
        </div>
    </div>
   <div class="col-md-8">
    <table class="table mt-2">
        <thead>
            <tr>
                <th scope="col"><b>Name</b></th>
                <th scope="col"><b>Type</b></th>
                <th scope="col"><b>Grouping</b></th>
                <th scope="col"><b>Created by</b></th>
                <th></th>
            </tr>
        </thead>
        @foreach($queue as $item)
            @if($item->taxonomy()->exists())
                <tr>
                    <td>{{ $item->name }}</td>
                    <td>{{ $item->taxonomy->name }}</td>
                    <td>{{ $item->parent }}</td>
                    <td><a href="/admin/users/{{ $item->created_by }}">{{ \App\User::where('id', $item->created_by)->pluck('name')->first() }}</a></td>
                    <td class="text-right d-flex flex-nowrap justify-content-around">
                        <form method="post" action="/admin/categories/approve">
                            @method('put')
                            @csrf
                            <input type="hidden" name="type" value="{{ get_class($item) }}">
                            <input type="hidden" name="action" value="approve">
                            <input type="hidden" name="id" value="{{ $item->id }}">
                            <button style="background-color:#7bec7f" type="submit" class="btn btn-sm btn-sm-secondary">Approve</button>
                        </form>
                        <form method="post" action="/admin/categories/approve">
                            @method('put')
                            @csrf
                            <input type="hidden" name="type" value="{{ get_class($item) }}">
                            <input type="hidden" name="action" value="deny">
                            <input type="hidden" name="id" value="{{ $item->id }}">
                            <button style="background-color:#ff7575" type="submit" class="btn btn-sm btn-sm-secondary">Deny</button>
                        </form>
                    </td>
                </tr>
            @endif
        @endforeach
    </table>
   </div>
@endsection
