@extends('admin.layout')

@section('page-content')

    @component('admin.partials.breadcrumbs', ['links' => [
        'Users' => '/admin/users'
    ]])
    @endcomponent

    <div class="d-flex justify-content-between">
        <div class="d-flex">
            <h5 class="mr-3">Users</h5>
            @include('partials.tutorial', ['tutorial' => \App\Tutorial::where('name', 'Inviting & Managing Users')->first()])
        </div>
        <form class="form-inline">
            <div class="input-group input-group-sm">
                <input type="text" name="q" placeholder="Search..." class="form-control form-control-sm" value="{{ Request::input('q') }}">
                <input type="hidden" name="filter" placeholder="Search..." class="form-control form-control-sm" value="{{ Request::input('filter') }}">
                <div class="input-group-append">
                    <button type="submit" class="btn btn-sm btn-outline-secondary"><i class="fas fa-search"></i></button>
                </div>
            </div>
        </form>
        <div class="text-right row">
            @if(false)
               @include('messages.partials.create', ['users' => $allUsers])
            @endif
            <a href="/admin/users/export" class="btn btn-outline-dark btn-sm mr-2"><i class="fas fa-download"></i> Export</a>
              <div class="dropdown px-3">
                <a class="btn btn-outline-dark btn-sm dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Manage Users
                </a>

                <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                  <a class="dropdown-item d-flex align-items-center" href="/admin/users/invites">
                      <span>Invite Users</span>
                  </a>
                  <a class="dropdown-item d-flex align-items-center" href="/admin/users/bulk-delete">
                    <span>Delete Users</span>
                </a>


                </div>
              </div>
        </div>

    </div>

    <div>
        <div class="dropdown">
          <a class="btn btn-outline-dark btn-sm dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            Filter by
          </a>

          <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
            <a class="dropdown-item d-flex align-items-center" href="/admin/users">
                @if (!Request::has('filter'))
                    <i class="far fa-check-square mr-2" style="font-size: 12px;"></i>
                @else
                    <i class="far fa-square mr-2" style="font-size: 12px;"></i>
                @endif
                <span>All users</span>
            </a>
            <a class="dropdown-item d-flex align-items-center" href="?filter=enabled">
                @if (Request::has('filter') && Request::input('filter') == 'enabled')
                    <i class="far fa-check-square mr-2" style="font-size: 12px;"></i>
                @else
                    <i class="far fa-square mr-2" style="font-size: 12px;"></i>
                @endif
                <span>Enabled users</span>
            </a>
            <a class="dropdown-item d-flex align-items-center" href="?filter=disabled">
                @if (Request::has('filter') && Request::input('filter') == 'disabled')
                    <i class="far fa-check-square mr-2" style="font-size: 12px;"></i>
                @else
                    <i class="far fa-square mr-2" style="font-size: 12px;"></i>
                @endif
                <span>Disabled users</span>
            </a>
            <a class="dropdown-item d-flex align-items-center" href="?filter=deleted">
                @if (Request::has('filter') && Request::input('filter') == 'deleted')
                    <i class="far fa-check-square mr-2" style="font-size: 12px;"></i>
                @else
                    <i class="far fa-square mr-2" style="font-size: 12px;"></i>
                @endif
                <span>Deleted users</span>
            </a>
            @if(config('app.gdpr_opted') == true)
            <a class="dropdown-item d-flex align-items-center" href="?filter=gdpr">
                @if (Request::has('filter') && Request::input('filter') == 'gdpr')
                    <i class="far fa-check-square mr-2" style="font-size: 12px;"></i>
                @else
                    <i class="far fa-square mr-2" style="font-size: 12px;"></i>
                @endif
                <span>GDPR opted</span>
            </a>
            @endif
          </div>
        </div>
    </div>
    <table class="table mt-2">
        <thead>
            <tr>
                <th scope="col"><span class="d-inline-block" style="width: 1.2em;"></span><b>Name</b></th>
                <th scope="col"><b>Job title</b></th>
                <th scope="col"></th>
                <th></th>
            </tr>
        </thead>
        @foreach($users as $user)
        <tr>
            <td>
                <span class="d-inline-block" style="width: 1.2em;">
                    @if($user->deleted_at)
                        <i class="fas fa-circle" style="color: #ff0000; font-size: 0.8em;" data-toggle="tooltip" data-placement="top" title="deleted"></i>
                    @elseif($user->is_enabled)
                        <i class="fas fa-circle" style="color: #15bd15; font-size: 0.8em;" data-toggle="tooltip" data-placement="top" title="enabled"></i>
                    @else
                        <i class="fas fa-circle" style="color: #d2d2d2; font-size: 0.8em;" data-toggle="tooltip" data-placement="top" title="disabled"></i>
                    @endif
                </span>

                <a href="/admin/users/{{ $user->id }}"> {{ $user->name }}</a>
            </td>
            <td>{{ $user->job_title }}</td>
            <td>
                @if($user->is_admin)<span class="badge badge-info">Admin</span>@endif
            </td>
            @if($user->deleted_at)
                <td class="text-right">
                    <form action="/admin/users/{{ $user->id }}/restore" method="post" id="restoreForm_{{$user->id}}">
                        @csrf
                        <a href="#" onclick="if(confirm('Are you sure you want to restore {{ $user->name }}?')){document.getElementById('restoreForm_{{$user->id}}').submit();}">Restore</a>
                    </form>
                </td>
            @else
                <td class="text-right"><a href="/admin/users/{{ $user->id }}">Manage</a></td>
            @endif
        </tr>
        @endforeach
    </table>
    <div class="d-flex justify-content-center">
        {{ (Request::has('filter')) ? $users->appends(['filter' => Request::input('filter')])->links() :  $users->links()}}
    </div>
    <div class="modal fade" id="accessLink" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="my-auto mx-auto">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="col-12 modal-title text-center" id="exampleModalLabel">Registration Link
                        <button type="button" class="close float-right" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </h5>
                </div>
                <div class="modal-body mb-3">
                    <div class="input-group col-12">
                        <input class="form-control" id="copyLink" value="{{ $registration_link }}">
                        <div class="input-group-append">
                            <button id="copyButton" class="btn btn-primary" data-clipboard-target="#copyLink" onclick="switchToCopied()">Copy</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
<script src="//cdnjs.cloudflare.com/ajax/libs/clipboard.js/2.0.6/clipboard.min.js"></script>
<script>
    (function(){
        new ClipboardJS('#copyButton');
    })();

    $(function () {
      $('[data-toggle="tooltip"]').tooltip()
    })

    function switchToCopied()
    {
        document.getElementById('copyButton').innerHTML = "Copied!";
    }

</script>
@endsection
