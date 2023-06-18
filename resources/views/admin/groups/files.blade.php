@extends('admin.groups.layout')

@section('inner-page-content')

    <div class="d-flex justify-content-between">
        <h5>
            @if($folder)
            <a href="/admin/groups/{{ $group->id }}/files">Files</a> <i class="fas fa-angle-right"></i> {{ $folder->name }}
            @else
            Files
            @endif
        </h5>
    </div>
    
    <table class="table mt-2">
        <thead>
            <tr>
                <th scope="col"><b>Name</b></th>
                <th></th>
            </tr>
        </thead>
        @foreach($items as $item)
        <tr>
            @if($item instanceOf App\Folder)
            <td>
                <a href="/admin/groups/{{ $group->id }}/files/{{ $item->id }}">
                    <i class="fas fa-folder mr-2"></i>
                    {{ $item->name }}
                </a>
            </td>
            <td class="text-right">
                <a href="/admin/groups/{{ $group->id }}/files/{{ $item->id }}">Open</a>
            </td>
            @else
            <td>
                <a href="{{ $item->url }}">
                    <i class="fas fa-file mr-2"></i>
                    {{ $item->name }}
                </a>
            </td>
            <td class="text-right">
                <a href="/admin/groups/{{ $group->id }}/files/{{ $item->id }}/download">Download</a>
            </td>
            @endif
        </tr>
        @endforeach
    </table>
@endsection