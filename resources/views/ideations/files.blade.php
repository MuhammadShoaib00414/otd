@extends('ideations.show.layout')

@section('stylesheets')
<style>
    .btn-grey {
        background-color: #dadcdf;
        border-color: #dadcdf;
        color: #645f5f;
    }
    .btn-grey:hover {
        background-color: #ced1d5;
        border-color: #ced1d5;
        color: #645f5f;
    }
    .hover-hand:hover { cursor: pointer; }
    .nav-tabs .nav-item .nav-link:not(.active) {
      color: #515457;
    }
    .nav-item .nav-link.active {
      border-color: #1a2b40 !important;
      color: #1a2b40;
      font-weight: bold;
    }
</style>
@endsection

@section('inner-content')
<div class="row">
    <div class="col-md-12">
      @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
              @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
              @endforeach
            </ul>
        </div>
      @endif
        <div class="card mb-2">
            <div class="card-body py-2">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h5 class="mb-0">@lang('general.files')</h5>
                    <button type="button" class="btn btn-sm btn-secondary" data-toggle="modal" data-target="#uploadModal"><i class="icon-upload"></i> @lang('ideations.upload-file')</button>
                </div>
                <table class="table">
                    @forelse($files as $file)
                      <tr>
                        <td class="pl-2" style="vertical-align: middle;">
                          <a href="{{ $file->url }}" target="_blank" class="d-block">
                            <i class="icon-text-document mr-1"></i>
                            <span>{{ $file->name }}</span>
                          </a>
                        </td>
                        <td class="text-right" style="vertical-align: middle;">
                          <div class="dropdown">
                            <button class="btn btn-link btn-sm dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            </button>
                            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                              <a class="dropdown-item" href="/ideations/{{ $ideation->slug }}/files/{{ $file->id }}/download">@lang('general.download')</a>
                              <form action="/ideations/{{ $ideation->slug }}/files/{{ $file->id }}" method="post">
                                @csrf
                                @method('delete')
                                <button type="submit" class="btn dropdown-item" href="#">@lang('general.delete')</button>
                              </form>
                            </div>
                          </div>
                        </td>
                      </tr>
                    @empty
                        @include('partials.empty')
                    @endforelse
                </table>
            </div>
        </div>
    </div>
</div>
<div class="modal" tabindex="-1" role="dialog" id="uploadModal">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <form class="modal-content" action="/ideations/{{ $ideation->slug }}/files" method="post" enctype="multipart/form-data">
      @csrf
      <div class="modal-header">
        <h5 class="modal-title">@lang('general.upload')</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="form-group">
          <label for="name">@lang('general.file')</label>
          <input class="form-control-file form-control-lg" name="document" type="file" required/>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary-outline" data-dismiss="modal">@lang('general.close')</button>
        <button type="submit" class="btn btn-primary" id="uploadButton" disabled>@lang('general.upload')</button>
      </div>
    </form>
  </div>
</div>
@endsection

@section('scripts')
  <script>
    $('.form-control-file').on("change", function (e) {
      if (this.files[0] != null) {
          var fileSize = this.files[0].size;
          console.log(this.files[0].size);
          if (fileSize > 50000000) {
              alert("File too large!");
              return
          }
          else {
              $('#uploadButton').removeAttr('disabled');
          }
      }
  });

  </script>
@endsection