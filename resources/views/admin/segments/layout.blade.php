@extends('admin.layout')

@section('page-content')

    @component('admin.partials.breadcrumbs', ['links' => [
        'Reports' => '/admin/segments',
        $segment->name => '',
    ]])
    @endcomponent

@if(session()->has('success'))
    <div class="alert alert-success text-center">
        {{ session('success') }}
    </div>
@endif

@if(count(request()->segments()) < 5)
<div style="background-color: #fafbfd; margin: -1em -1em 1em -1em; padding: 1em 0 0; border-left: 1px solid #e0e2e7;">
    <div class="d-flex justify-content-between align-items-center mb-3 px-3">
        <h5>Segment: {{ $segment->name }} <small>(Total: {{ $totalCount }})</small></h5>
        <div>
            @include('messages.partials.create', ['users' => $segment->users])
            <button id="exportButton" class="btn btn-sm btn-outline-primary" data-toggle="modal" data-target="#ExportModal"><i class="fas fa-download"></i> Export</button>
            <form action="/admin/segments/{{ $segment->id }}" method="post" class="d-inline-block">
                @method('delete')
                @csrf
                <button type="submit" class="btn btn-sm btn-outline-primary mx-1" id="deleteSegment" class="d-inline-block"><i class="fa fa-trash"></i></button>
            </form>
            <a href="/admin/segments/{{ $segment->id }}/edit" class="btn btn-sm btn-primary">Edit</a>
        </div> 
    </div>

    <ul class="nav nav-tabs mb-4 px-3">
        <li class="nav-item">
            <a class="nav-link{{ (Request::is('*demographics*')) ? ' active' : '' }}" href="/admin/segments/{{ $segment->id }}/demographics">Demographics</a>
        </li>
        <li class="nav-item">
            <a class="nav-link{{ (Request::is('*behavior*')) ? ' active' : '' }}" href="/admin/segments/{{ $segment->id }}/behavior">Behavior</a>
        </li>
        <li class="nav-item">
            <a class="nav-link{{ (Request::is('*members*')) ? ' active' : '' }}" href="/admin/segments/{{ $segment->id }}/members">Members</a>
        </li>
    </ul>
</div>


<div class="modal fade" id="ExportModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content" id="">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Request an export</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <div class="modal-body">
        <p>Exports usually take quite a bit of time (typically 10-20 minutes), as your data is processed and cleaned. Enter an email, and a download link will be sent to you as soon as it's ready.</p>

        <form action="/admin/segments/{{ $segment->id }}/export/start" method="post">
            @csrf
            <div class="form-group">
                <label>Email address</label>
                <input type="text" name="send_to_email" value="{{ $authUser->email }}" class="form-control">
            </div>
            <div class="text-center">
                <button type="submit" class="btn btn-primary">Request Export</button>
            </div>
        </form>
      </div>

    </div>
  </div>
</div>
@endif

    @yield('inner-page-content')

@endsection

@section('scripts')
    <script>
    $(function () {
        $('[data-toggle="popover"]').popover({
            trigger: 'focus'
        });
        $('#deleteSegment').on('click', function(event) {
          event.preventDefault();
          if (confirm('Delete this segment?'))
            $('#deleteSegment').parent().submit();
        });

        function checkIfExportCompleted(exportId)
        {
            $.ajax({
                url: "/admin/exports/" + exportId + "/check",
                method: 'GET',
                async: true,
                data: {
                    "_token": "{{ csrf_token() }}",
                },
                success: function (response) {
                    console.log(response);
                    if(response != '')
                    {
                        $('.fa-spinner').addClass('d-none');
                        $('#downloadExportButton').attr('href', '/admin/exports/'+exportId+'/download');
                        $('#downloadExportButton').removeClass('d-none');
                        $('#successMessage').removeClass('d-none');
                    }
                },
                error: function (response) {
                    console.log('There was an error!');
                }
            });
        }
    });
    </script>
@endsection