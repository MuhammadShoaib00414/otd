@extends('groups.layout')

@section('inner-content')

  @if (session('error'))
    <div class="alert alert-danger" role="alert">
      {{ session('error') }}
    </div>
  @endif

  @if($folder)
    <a href="/groups/{{ $group->slug }}/files" class="d-inline-block mb-2" style="font-size: 14px;"><i class="icon-chevron-small-left"></i> @lang('files.All Folders')</a>
  @endif
  <div class="d-flex justify-content-between align-items-center mb-2">
    <h3 class="font-weight-bold mb-0">
      {{ (!$folder) ? $group->files_alias : $folder->name }}
    </h3>
    @if($group->isUserAdmin($authUser->id) || $group->can_users_upload_files)
    <div>
      @if(!$folder)
      <button type="button" class="btn btn-sm btn-secondary" data-toggle="modal" data-target="#newFolderModal"><i class="icon-folder"></i> @lang('files.New Folder')</button>
      @endif
      <button type="button" class="btn btn-sm btn-secondary" data-toggle="modal" data-target="#uploadModal"><i class="icon-upload"></i> {{ $group->files_alias ? __('general.new') . ' ' . $group->files_alias : __('files.Upload file') }}</button>
    </div>
    @endif
  </div>
  @if ($errors->any())
      <div class="alert alert-danger">
          <ul>
            @foreach ($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
          </ul>
      </div>
    @endif
  <div class="card">
    <table class="table mb-0">
      @forelse($items as $item)
      <tr>
        @if($item instanceOf App\Folder)
        <td class="pl-2" style="vertical-align: middle;">
          <a href="/groups/{{ $group->slug }}/files/{{ $item->id }}" class="d-block">
            <i class="icon-folder mr-1"></i>
            <span style="word-break: break-all;">{{ $item->name }}</span>
          </a>
        </td>
        <td class="text-right" style="vertical-align: middle;">
          <div class="dropdown">
            <button class="btn btn-link btn-sm dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            </button>
            @if($group->isUserAdmin($authUser->id) || $group->can_users_upload_files)
            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
              <form action="/groups/{{ $group->slug }}/folders/{{ $item->id }}" method="post">
                @csrf
                @method('delete')
                <button type="submit" class="btn dropdown-item" href="#">@lang('general.delete')</button>
              </form>
            </div>
            @endif
          </div>
        </td>
        @else
        <td class="pl-2" style="vertical-align: middle;">
          <a href="{{ $item->url }}" class="d-block">
            <i class="icon-text-document mr-1"></i>
            <span style="word-break: break-all;">{{ $item->name }}</span>
          </a>
        </td>
        <td class="text-right" style="vertical-align: middle;">
          <div class="dropdown">
            <button class="btn btn-link btn-sm dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            </button>
            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
              <a class="dropdown-item" href="/groups/{{ $group->slug }}/files/{{ $item->id }}/download">@lang('general.download')</a>
              @if($group->isUserAdmin($authUser->id) || $group->can_users_upload_files)
              <form action="/groups/{{ $group->slug }}/files/{{ $item->id }}" method="post">
                @csrf
                @method('delete')
                <button type="submit" class="btn dropdown-item" href="#">@lang('general.delete')</button>
              </form>
              @endif
            </div>
          </div>
        </td>
        @endif
      </tr>
      @empty
        @include('partials.empty')
      @endforelse
    </table>
  </div>

@if($group->isUserAdmin($authUser->id) || $group->can_users_upload_files)
<div class="modal" tabindex="-1" role="dialog" id="newFolderModal">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <form class="modal-content" action="/groups/{{ $group->slug }}/files/folders" method="post">
      @csrf
      <div class="modal-header">
        <h5 class="modal-title">@lang('files.New Folder')</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="form-group">
          <label for="name">@lang('files.Folder name')</label>
          <input type="text" name="name" class="form-control">
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary-outline" data-dismiss="modal">@lang('general.close')</button>
        <button type="submit" class="btn btn-primary">@lang('general.create')</button>
      </div>
    </form>
  </div>
</div>

<div class="modal" tabindex="-1" role="dialog" id="uploadModal">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <form class="modal-content" action="/groups/{{ $group->slug }}/files/upload" method="post" enctype="multipart/form-data">
      @csrf
      <div class="modal-header">
        <h5 class="modal-title">@lang('general.upload')</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="form-group">
          <label for="name">@lang('files.Upload into folder')</label>
          <select class="custom-select" name="folder">
            <option value=""{{ (!$folder) ? ' selected' : '' }}>@lang('general.none')</option>
            @foreach($group->folders as $folderItem)
              <option value="{{ $folderItem->id }}"{{ ($folder && $folder->id == $folderItem->id) ? ' selected' : '' }}>{{ $folderItem->name }}</option>
            @endforeach
          </select>
        </div>
        <div class="form-group">
          <label for="name">@lang('general.file')</label>
          <input class="form-control-file form-control-lg" name="document" type="file" id="fileToUpload" />
          <span id="fileMessage" class="d-none" style="color: red; text-align: center;">Error: Max filesize is 50 MB</span>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary-outline" data-dismiss="modal">@lang('general.close')</button>
        <button type="submit" class="btn btn-primary" id="uploadFileSubmitButton">@lang('general.upload')</button>
      </div>
    </form>
  </div>
</div>
@endif
@endsection

@push('scriptstack')
<script>
  $('#fileToUpload').on('change', function (e) {
    console.log(this.files[0].size > 50000000);
    if (this.files[0].size > 50000000) {
      $('#fileMessage').removeClass('d-none');
      $('#uploadFileSubmitButton').prop('disabled', true);
    } else {
      $('#fileMessage').addClass('d-none');
      $('#uploadFileSubmitButton').prop('disabled', false);
    }
  });
</script>
@endpush