@extends('admin.ideations.show.layout')

@section('inner-page-content')
<div class="col-md-6 mx-md-auto">
  <div class="card">
    <table class="table">
      @foreach($ideation->files as $file)
        <tr>
          <td><a target="_blank" href="{{ $file->url }}">{{ $file->name }}</a></td>
          <td>
            <form action="/admin/ideations/{{ $ideation->id }}/files/{{ $file->id }}" method="post">
              @csrf
              @method('delete')
              <button onclick="return confirm('Are you sure you want to delete this file?');" type="submit" class="btn btn-primary">Delete</button>
            </form>
          </td>
        </tr>
      @endforeach
    </table>
    @if(!$ideation->files()->count())
      <span class="text-center mb-3">No files...</span>
    @endif
  </div>
</div>
@endsection