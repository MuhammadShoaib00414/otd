@extends('admin.layout')

@push('stylestack')
<link rel="stylesheet" href="/redactorx-1-2-0/redactorx.min.css" />
@endpush

@section('page-content')
@component('admin.partials.breadcrumbs', ['links' => [
'Pages' => '/admin/pages',
'Update Page' => '',
]])
@endcomponent


 
<div class="row">
  <div class="col-lg-12">
    <div class="margin-top-40"></div>
  </div>
  <div class="col-lg-12 mt-5">
    <div class="card">
      <div class="card-body">
        @if ($errors->any())
        <div class="alert alert-danger mb-3">
          <ul>
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
        @endif
        <form action="/admin/pages/{{$page->id}}/edit" id="form" method="post" enctype="multipart/form-data">
          <div class="header-wrap d-flex justify-content-between">
            <h4 class="header-title">Update Page</h4>
            <div class="row">
              <div class="col-md-6">
                <input type="submit" id="saveButton" class="btn btn btn-danger" name="draft" value="@lang('general.draft')">
              </div>
              <div class="col-md-6">
                <input type="submit" id="saveButton" class="btn btn btn-primary" name="published" value="@lang('general.save')">
              </div>
            </div>
          </div>
          {{ csrf_field() }}
          <div class="row">
            <div class="col-lg-8">

              <div class="form-group">
                <label for="title">Title</label>
                <input type="text" name="title" id="title" class="form-control" value="{{$page->title}}" onload="convertToSlug(this.value)" onkeyup="convertToSlug(this.value)" required>
                <small class="text-danger p-1" style="display:none;" id="urlTitle">You have entered an invalid url</small>
              </div>

              <div class="form-group classic-editor-wrapper">
                <label>Content</label>
                <textarea id="content" rows="8" class="form-control mb-3 content" height="500" name="content" required>{{$page->content}}</textarea>

              </div>

            </div>
            <div class="col-lg-4">
            <label for="title">Page Url</label>
                  <div class="form-group input-group input-group-sm">
                    <input id="link" class="form-control" type="text" readonly  value="{{ config('app.url') }}/pages/{{$page->id}}/{{$page->slug}}" onload="convertToSlug(this.value)" onkeyup="convertToSlug(this.value)">
                    <div class="input-group-append">
                      <button id="copyLink" class="btn btn-primary">Copy</button>
                    </div>
                  </div>
              <div class="form-group">
                <label for="slug">Slug</label>
                <input type="text" name="slug" class="form-control" id="slug" value="{{$page->slug}}" required>
              </div>
              <div class="form-group">
                <label>Status</label>
                <select name="is_active" id="is_active" class="form-control">
                  <option value="1" {{ $page->is_active == '1' ? 'selected' : '' }}>Publish</option>
                  <option value="2" {{  $page->is_active == '2' ? 'selected' : '' }}>Inactive</option>
                 
                </select>
              </div>
              <div class="form-group">
                <label>Visibility</label>
                <select name="visibility" class="form-control">
                  <option value="1" {{ $page->visibility == '1' ? 'selected' : '' }}>All</option>
                  <option value="0" {{ $page->visibility == '0' ? 'selected' : '' }}>Only Admins</option>
                </select>
              </div>
              <div class="form-group">
                <div class="d-flex justify-content-between mt-2">
                  <div class="form-check ml-2">
                    <input type="checkbox" class="form-check-input" id="displayed_show" name="displayed_show" {{ $page->displayed_show == 'on' ? 'checked' : '' }}>
                    <label class="form-check-label" for="displayed_show" style="font-size: 16px;">Show on Dashboard</label>
                  </div>
                </div>
                @if($page->show_in_groups != null)
                <div class="d-flex justify-content-between mt-2">
                  <div class="form-check ml-2">
                    <input type="checkbox" class="form-check-input" id="show_in_groups" name="show_in_groups" {{ $page->show_in_groups != '0' ? 'checked' : '' }}>
                    <label class="form-check-label" for="show_in_groups" style="font-size: 16px;">Show on Groups</label>
                  </div>
                </div>
                <div class="d-flex justify-content-between mt-2">
                  <div class="form-check ml-2">
                    <div>
                      <label for="groups[]"></label>
                      @foreach($groups as $group) @include('admin.posts.partials.getShareableGroups', ['group' => $group,'checkedGroups' => collect(json_decode($page->show_in_groups))]) @endforeach
                    </div>
                  </div>
                </div>

                @else
                <div class="d-flex justify-content-between mt-2">
                  <div class="form-check ml-2">
                    <input type="checkbox" class="form-check-input" id="show_in_groups" name="show_in_groups" {{ $page->show_in_groups != '0' ? 'checked' : '' }} data-toggle="collapse" href="#collapseExample" role="button" aria-expanded="false" aria-controls="collapseExample">
                    <label class="form-check-label" for="show_in_groups" style="font-size: 16px;">Show on Groups</label>
                  </div>
                </div>
                <div class="d-flex justify-content-between mt-2">
                  <div class="form-check ml-2">
                    <div class="collapse" id="collapseExample">
                      <label for="groups[]"></label>
                      @foreach($groups as $group) @include('admin.posts.partials.getShareableGroups', ['group' => $group]) @endforeach
                    </div>
                  </div>
                </div>
                @endif
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>

  </div>

</div>

</div>
@endsection
<script>
    function convertToSlug(str) {
    str = str.replace(/[`~!@#$%^&*()_\-+=\[\]{};:'"\\|\/,.<>?\s]/g, ' ')
      .toLowerCase();
    str = str.replace(/^\s+|\s+$/gm, '');
    str = str.replace(/\s+/g, '-');

    var url = "{{ config('app.url') }}/pages/{{$page_id}}/";
    document.getElementById("link").value = url + str;
    document.getElementById("slug").value = str;
  }
</script>
@include('admin.pages.pages-footer')